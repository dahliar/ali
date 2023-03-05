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
use Illuminate\Support\Facades\Validator;

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
        $t=Transaction::where('id', $transactionId);
        $tranStatus =   $t->value('status');
        $marker="";

        switch ($t->value('valutaType')){
            case 1 : $marker = "Rp"; break;
            case 2 : $marker = "USD"; break;
            case 3 : $marker = "Rmb"; break;
        }

        return view('detail.detailList', compact('transactionId', 'tranStatus', 'marker'));
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
            'amount' => 'required|numeric|gt:0'
        ],[
            'species.gt'=> 'Pilih satu Species',
            'item.gt'=> 'Pilih jenis Item',
            'amount.gt' => 'Amount harus lebih dari 0'
        ]);

        $inventory = DetailTransaction::firstOrNew(['transactionId' => $request->transactionId, 'itemId' => $request->item]);
        $inventory->itemId = $request->item;
        $inventory->amount = ($inventory->amount + $request->amount);
        $inventory->save();

        $transaction = $request->transactionId;
        return redirect()->route('detailtransactionList',
            ['transaction'=>$transaction])
        ->with('status','Item berhasil ditambahkan.');

    }


    public function updatePrice($detailTransactionId, $harga){        
        /*
        $weightbase = DB::table('items')
        ->select('weightbase')
        ->where('id', $itemId)
        ->first();

        $addPayment = ($amount * $harga * $weightbase);
        
        $affected = DB::table('transactions')
        ->where('id', $transactionId)
        ->increment('payment', $addPayment);
        */
        $dt = DetailTransaction::find(['detailTransactionId' => $detailTransactionId]);
        $dt->price = $request->amount;
        $dt->save();

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
    function storePerubahanHargaDetailTransaksi(Request $request){

        $retValue="";

        $validator = Validator::make($request->all(), [
            'detailId' => ['required',
            Rule::exists('detail_transactions', 'id')->where('id', $request->detailId),], 
            'harga' => 'required|integer|gt:0'
        ]);

        if ($validator->fails()) {
            $retValue = [
                'message'       => "Data gagal disimpan, cek harga dulu",
                'isError'       => "1"
            ];
            return $retValue;
        }

        $dt = DetailTransaction::where('id', $request->detailId)->first();
        $dt->price = $request->harga;

        $transactionId = $dt->transactionId;
        $dt->save();

        $transaction = new TransactionController();
        
        $totalPayment = $transaction->getExportTotalPayment($transactionId);

        $t = Transaction::where('id', $transactionId)->first();
        $t->payment = $totalPayment;
        $t->save();


        $retValue = [
            'message'       => "Data berhasil disimpan ",
            'isError'       => "0"
        ];

        return $retValue;        
    }
}
