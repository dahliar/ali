<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaction;
use App\Models\Transaction;
use App\Models\Species;
use App\Models\Grade;
use App\Models\Packing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;

class DetailTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->detailTransaction = new DetailTransaction();
    }

    public function index($transactionId)
    {
        $tranStatus=Transaction::select('status')->where('id', $transactionId)->value('status');
        return view('detail.detailList', compact('transactionId', 'tranStatus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Transaction $transaction)
    {
        $species = Species::orderBy('name')->get();
        $grades = Grade::all();
        $packings = Packing::all();

        $transactionId = $transaction->id;
        $valutaType = $transaction->valutaType;
        $marker="";
        switch ($valutaType){
            case 1 : $marker = "Rp"; break;
            case 2 : $marker = "USD"; break;
            case 3 : $marker = "Rmb"; break;
        }


        return view('detail.detailAdd', compact('marker','transactionId', 'species', 'grades', 'packings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    /*
        0. validation
        1. insert ke detailTransaction
        2. update jumlah stock di tabel items
        3. update payment di table transactions
    */

        //0. validation
        $request->validate([
            'transactionId' => ['required',
            Rule::exists('transactions', 'id')->where('id', $request->transactionId),], 
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

        //1. insert ke detailTransaction
        //2. update jumlah stock di tabel items
        
        $data = [
            'transactionId' => $request->transactionId,
            'itemId' => $request->item,
            'amount' =>  $request->amount,
            'price' =>  $request->harga,
            'info' =>  ''
        ];

        DB::table('detail_transactions')->insert($data);
        //3. update payment di table transactions
        $weightbase = DB::table('items')
        ->select('weightbase')
        ->where('id', $request->item)
        ->first();

        $addPayment = ($request->amount * $request->harga * $weightbase->weightbase);
        $affected = DB::table('transactions')
        ->where('id', $request->transactionId)
        ->increment('payment', $addPayment);

        $transaction = $request->transactionId;
        return redirect()->route('detailtransactionList',
            ['transaction'=>$transaction])
        ->with('status','Item berhasil ditambahkan.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DetailTransaction  $detailTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(DetailTransaction $detailTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DetailTransaction  $detailTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(DetailTransaction $detailTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DetailTransaction  $detailTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DetailTransaction $detailTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DetailTransaction  $detailTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(DetailTransaction $detailTransaction)
    {
    /*
        1. Hapus dari detailTransaction
        2. Kembalikan jumlah stok di tabel items
        3. Kurangi jumlah payment di tabel transactions
    */

        //1. Hapus dari detailTransaction
        //2. Kembalikan jumlah stok di tabel items
        $oneItemStore = $this->detailTransaction->deleteOneItemDetail($detailTransaction);

        //3. Kurangi jumlah payment di tabel transactions
        //3. update payment di table transactions
        $weightbase = DB::table('items')
        ->select('weightbase')
        ->where('id', $detailTransaction->itemId)
        ->first();

        $reducePayment = ($detailTransaction->amount * $detailTransaction->price * $weightbase->weightbase);
        $affected = DB::table('transactions')
        ->where('id', $detailTransaction->transactionId)
        ->decrement('payment', $reducePayment);
        return "sukses";
    }

    public function getAllDetail($transactioId){
        return $this->detailTransaction->getAllDetail($transactioId);
    }
}
