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

        //dd($purchase);
        $tax = $addPayment * $purchase->taxPercentage / 100;

        $affected = DB::table('purchases')
        ->where('id', $request->purchaseId)
        ->update([
            'paymentAmount' => DB::raw('paymentAmount+'.$addPayment), 
            'tax'           => DB::raw('tax+'.$tax)
        ]);


        return redirect()->route('purchaseItems',
            ['purchase'=>$request->purchaseId])
        ->with('status','Item berhasil ditambahkan.');
    }

    public function getAllPurchaseItems($purchaseId){
        $query = DB::table('detail_purchases as dp')
        ->select(
            'dp.id as id', 
            'dp.purchasesId as purchasesId', 
            'i.name as itemName', 
            'f.name as freezingName', 
            'g.name as gradeName', 
            'p.name as packingName', 
            's.name as sizeName', 
            'pur.status as status', 
            DB::raw(
                'concat(dp.amount, " ",p.shortname) 
                as amount'),
            DB::raw('
                concat(dp.amount," Kg") as weight'),
            'dp.price as price',
        )
        ->join('purchases as pur', 'pur.id', '=', 'dp.purchasesId')
        ->join('items as i', 'i.id', '=', 'dp.itemId')
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('pur.id','=', $purchaseId)
        ->orderBy('sp.name');
        $query->get();  


        return datatables()->of($query)
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
    public function destroy(DetailPurchase $detailPurchase)
    {
        //
    }
}
