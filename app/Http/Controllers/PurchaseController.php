<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Countries;
use App\Models\Company;

use Illuminate\Http\Request;
use DB;

class PurchaseController extends Controller
{
    public function __construct(){
        $this->purchase = new Purchase();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nations = Countries::where('isActive',1)->orderBy('name')->get();
        return view('purchase.purchaseList', compact('nations'));
    }

    public function getPurchaseList()
    {
        $query = DB::table('purchases as p')
        ->select(
            'p.id as id', 
            'p.purchasingnum as nosurat', 
            'c.name as name', 
            'n.name as nation', 
            'p.purchasedate as purchasedate',
            'p.arrivaldate as arrivaldate',
            'p.paymentAmount as paymentAmount',
            'p.status as statusCheck',
            DB::raw('(CASE WHEN p.status ="0" THEN "New Submission"
                WHEN p.status ="1" then "On Progress"
                WHEN p.status ="2" then "Finished"
                ELSE "Cancelled" END) AS status')
        )
        ->join('companies as c', 'c.id', '=', 'p.companyid')
        ->join('countries as n', 'n.id', '=', 'c.nation');
        $query->get();  


        return datatables()->of($query)
        ->addColumn('action', function ($row) {

            $html='
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar beli item" onclick="purchaseItems('."'".$row->id."'".')">
            <i class="fa fa-list" style="font-size:20px"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit data pembelian " onclick="purchaseEdit('."'".$row->id."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Print Invoice" onclick="purchaseInvoice('."'".$row->id."'".')">Inv
            </button>
            ';
            return $html;
        })->addIndexColumn()->toJson();
    }


        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function create()
        {
            $companies = Company::orderBy('name')->get();
            return view('purchase.purchaseAdd', compact('companies'));

        }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'company' => 'required|gt:0',
                'valutaType' => 'required|gt:0',
                'purchaseDate' => 'required|date|before_or_equal:today',
                'arrivalDate' => 'required|date|before_or_equal:purchaseDate',
                'taxPercentage' => 'required|gt:0'
            ],
            [
                'company.gt'=> 'Pilih salah satu perusahaan',
                'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
                'company.gt'=>'Pilih salah satu perusahaan'
            ]
        );

        $company=Company::select('name', 'taxIncluded')->where('id', $request->company)
        ->first();

        $data = [
            'userId' => auth()->user()->id,
            'companyId' =>  $request->company,
            'valutaType' => $request->valutaType,
            'created_at' =>  date('Y-m-d'),
            'paymentTerms' => $request->paymentTerms,
            'purchaseDate' => $request->purchaseDate,
            'arrivalDate' => $request->arrivalDate,
            'taxIncluded' => $company->taxIncluded,
            'taxPercentage' => $request->taxPercentage,
            'status' =>  1
        ];

        $lastPurchaseIdStored = $this->purchase->storeOnePurchase($data);

        //create Purchase Number
        $this->inv = new InvoiceController();
        $purchaseNum = $this->inv->getPurchaseNumber($lastPurchaseIdStored);
        $purchase = Purchase::find($lastPurchaseIdStored);
        $purchase->purchasingNum = $purchaseNum;
        $purchase->save();

        //$companyName=Company::select('name')->where('id', $request->company)->value('name');

        return redirect('purchaseList')
        ->with('status','Transaksi pembelian ke '.$company->name.' berhasil ditambahkan.');
    }

        /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
        public function show(Purchase $purchase)
        {
        }

        /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
        public function edit(Purchase $purchase)
        {
            $companyName = DB::table('companies')->select('name as name')->where('id','=', $purchase->companyId)->first();

            return view('purchase.purchaseEdit', compact('purchase', 'companyName'));
        }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
        public function update(Request $request)
        {
            $request->validate(
                [
                    'progressStatus' => 'required|gt:0',
                    'purchaseDate' => 'required|date|before_or_equal:today',
                    'arrivalDate' => 'required|date|before_or_equal:purchaseDate'
                ],
                [
                ]
            );

            $purchase = Purchase::find($request->purchaseId);
            $purchase->arrivalDate = $request->arrivalDate;
            $purchase->purchaseDate = $request->purchaseDate;
            $purchase->status = $request->progressStatus;
            $purchase->paymentTerms = $request->paymentTerms;
            $purchase->save();

            return redirect('purchaseList')
            ->with('status','Transaksi pembelian ke '.$request->companyName.' berhasil diperbaharui.');
        }

        /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
        public function destroy(Purchase $purchase)
        {
        //
        }
    }
