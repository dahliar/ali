<?php

namespace App\Http\Controllers;

use App\Models\DetailPurchase;
use App\Models\Purchase;
use App\Models\Species;
use App\Models\Grade;
use App\Models\Packing;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use DB;

class DetailPurchaseController extends Controller
{

    public function __construct(){
        $this->detailPurchase = new DetailPurchase();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Purchase $purchase)
    {
        return view('detail.detailPurchaseList', compact('purchase'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Purchase $purchase)
    {
        $species = Species::orderBy('name')->get();
        $grades = Grade::all();
        $packings = Packing::all();

        $marker="";
        switch ($purchase->valutaType){
            case 1 : $marker = "Rp"; break;
            case 2 : $marker = "USD"; break;
            case 3 : $marker = "Rmb"; break;
        }


        return view('detail.detailPurchaseCreate', compact('marker','purchase', 'species', 'grades', 'packings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);

        $request->validate([
            'purchaseId' => ['required',
            Rule::exists('purchases', 'id')->where('id', $request->purchaseId),], 
            'species' => 'required|integer|gt:0', 
            'item' => 'required|integer|gt:0', 
            'amount' => 'required|numeric|gt:0',
            'harga' => 'required|numeric|gt:0',
        ],[
            'species.gt'=> 'Pilih satu Species',
            'item.gt'=> 'Pilih jenis Item',
            'amount.gt' => 'Amount harus lebih dari 0', 
            'harga.gt' => 'Harga harus lebih dari 0', 
            'harga.integer' => 'Harga harus berupa angka',
        ]);
        
        $data = [
            'purchasesId' => $request->purchaseId,
            'itemId' => $request->item,
            'amount' =>  $request->amount,
            'price' =>  $request->harga,
            'info' =>  '',
            'status' =>  1
        ];
        $oneItemStore = $this->detailPurchase->purchaseItemAddStore($data); 

        $addPayment = ($request->amount * $request->harga);

        $purchase =  DB::table('purchases')->find($request->purchaseId);

        $tax = $addPayment * $purchase->taxPercentage / 100;

        $affected = DB::table('purchases')
        ->where('id', $request->purchaseId)
        ->update([
            'paymentAmount' => DB::raw('paymentAmount+'.$addPayment), 
            'tax'           => DB::raw('tax+'.$tax)
        ]);

        return redirect()->route('purchaseItems',
            ['purchase'=>$request->purchaseId])
        ->with('status','Item pembelian berhasil ditambahkan.');
    }

    public function getAllPurchaseItems($purchaseId){
        $query = DB::table('detail_purchases as dp')
        ->select(
            //'sp.name as speciesName', 
            'dp.id as id', 
            'dp.purchasesId as purchasesId', 
            'vid.nameBahasa as itemName',
            /*
            'i.name as itemName', 
            'f.name as freezingName', 
            'g.name as gradeName', 
            'p.name as packingName',
            'p.shortname as pShortname',
            's.name as sizeName', 
            */
            'vid.pshortname as packingName',
            'pur.status as status',
            'vid.weightbase as wb',
            DB::raw('(CASE   WHEN pur.valutaType="1" THEN "Rp. " 
                WHEN pur.valutaType="2" THEN "USD. " 
                WHEN pur.valutaType="3" THEN "Rmb. " 
                END) as valuta'
            ), 
            'dp.amount as amount',
            DB::raw('(dp.amount * dp.price) as bayar'),
            'dp.price as price'
        )
        ->join('purchases as pur', 'pur.id', '=', 'dp.purchasesId')
        ->join('view_item_details as vid', 'vid.itemId', '=', 'dp.itemId')
        /*
        ->join('items as i', 'i.id', '=', 'dp.itemId')
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        */
        ->where('pur.id','=', $purchaseId)
        ->orderBy('vid.speciesName')
        ->orderBy('vid.gradeName', 'desc')
        ->orderBy('vid.sizeName', 'asc')
        ->orderBy('vid.freezingName');
        $query->get();  
        return datatables()->of($query)
        ->editColumn('amount', function ($row) {
            $html = number_format($row->amount, 2, ',', '.').' Kg';
            return $html;
        })
        ->editColumn('price', function ($row) {
            $html = 'Rp.'. number_format($row->price, 2, ',', '.');
            return $html;
        })
        ->editColumn('bayar', function ($row) {
            $html = 'Rp.'.number_format($row->price, 2, ',', '.');
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Detail" onclick="deleteItem('."'".$row->id."'".')">
            <i class="fa fa-trash" style="font-size:20px"></i>
            </button>
            ';

            if ($row->status == '1') return $html;
            if ($row->status != '2') return ;

            //return $html;
        })->addIndexColumn()->toJson();
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DetailPurchase  $detailPurchase
     * @return \Illuminate\Http\Response
     */
    public function show(DetailPurchase $detailPurchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DetailPurchase  $detailPurchase
     * @return \Illuminate\Http\Response
     */
    public function edit(DetailPurchase $detailPurchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DetailPurchase  $detailPurchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DetailPurchase $detailPurchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DetailPurchase  $detailPurchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //dd($request);
        /*
            1. get paymentAmount dan tax nya
            2. kurangi tabel purchase paymentAmount dan tax nya
            3. update table purchases
            4. hapus tabel detail_purchases
        */
            //1
            $dp = DB::table('detail_purchases as dp')->select('purchasesId', 'amount', 'price', 'taxPercentage')
            ->join('purchases as p', 'p.id', '=', 'dp.purchasesId')
            ->where('dp.id', '=', $request->dpid)
            ->first();

            $deductedPayment = ($dp->amount * $dp->price);
            $tax = $deductedPayment * $dp->taxPercentage / 100;


            //2 dan 3
            $affected = DB::table('purchases')
            ->where('id', $dp->purchasesId)
            ->update([
                'paymentAmount' => DB::raw('paymentAmount-'.$deductedPayment), 
                'tax'           => DB::raw('tax-'.$tax)
            ]);

            //4
            $jumlahPaid = DB::table('detail_purchases')
            ->where('id', '=', $request->dpid)
            ->delete();

            return true;
        }
    }
