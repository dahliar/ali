<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use DB;


class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->store = new Store();
        $this->item = new Item();
    }

    public function itemStoreDetail($storeId)
    {
        $oneStore = $this->store->getOneStore($storeId);
        return view('item.itemStoreDetail', compact('oneStore'));
    }
    public function getOneStore($storeId)
    {
        return $this->store->getOneStore($storeId);
    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($speciesId)
    {
        $oneItem = $this->item->getOneItem($speciesId);
        return view('item.itemStockAdd', compact('oneItem'));
    }
    
    public function editUnpacked($speciesId)
    {
        $oneItem = $this->item->getOneItem($speciesId);
        return view('item.itemStockEditUnpacked', compact('oneItem'));
    }

    public function unpackedUpdate(Request $request)
    {
        //dd($request->amountMetric);

        $request->validate([
            'itemId' => ['required',
            Rule::exists('items', 'id')->where('id', $request->itemId),], 
            'amountPacking' => 'required|gt:0|lte:maxUpdate',
            'tanggalPacking' => 'required|date|before_or_equal:today'
        ]);


        $data = [
            'itemId' => $request->itemId,
            'amountPacked' =>  $request->amountPacking,
            'amountUnpacked' =>  $request->amountMetric,
            'userId' =>  auth()->user()->id
        ];
        $oneItemStore = $this->store->unpackedUpdate($data);

        $teks1 = $request->speciesName." ".$request->sizeName." ".$request->gradeName;
        $teks2 = $request->amountPacking.' '.$request->input('packingName');
        return redirect('itemStockList')
        ->with('status','Item '.$teks1.' sebanyak '.$teks2.' berhasil di-pack dan disimpan.');
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'packedTambah' => 'required|gte:0', 
            'unpackedTambah' => 'required|gte:0',
            'tanggalProses' => 'required|date|before_or_equal:today',
            'tanggalPacking' => 'required|date|after_or_equal:tanggalProses|before_or_equal:today'
        ]);


        $data = [
            'itemId' => $request->itemId,
            'amountPacked' =>  $request->packedTambah,
            'amountUnpacked' =>  $request->unpackedTambah,
            'datePackage' =>  $request->tanggalPacking,
            'dateProcess' =>  $request->tanggalProses,
            'dateInsert' =>  date('Y-m-d'),
            'userId' =>  auth()->user()->id,
            'isApproved' => 1
        ];
        $oneItemStore = $this->store->storeOneItem($data);

        $teks1 = $request->speciesName." ".$request->sizeName." ".$request->gradeName;
        $teks2 = $request->amount.' '.$request->input('packingName');
        return redirect('itemStockList')
        ->with('status','Item '.$teks1.' sebanyak '.$teks2.' berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        //
        $data = DB::table('items as i')
        ->select(
            'i.name as itemName', 
            'f.name as freezingName', 
            'g.name as gradeName', 
            'p.name as packingName', 
            's.name as sizeName',
            'sp.name as speciesName',
            'i.amount as amount',
            'i.weightbase as weightbase'
        )
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('i.id','=', $store->itemId)
        ->first();

        return view('item.itemStockEdit', compact('store', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        //dd($request);
        $request->validate([
            //'amountPacked' => 'required', 
            //'amountUnpacked' => 'required',
            'tanggalPacking' => 'required|date|after_or_equal:tanggalProses'
        ]);


        $data = [
            'storeId' => $request->storeId,            
            'amount' =>  $request->amount,
            //'amountPacked' =>  $request->amountPacked,
            //'amountUnpacked' =>  $request->amountUnpacked,
            'datePackage' =>  $request->tanggalPacking,
            'dateProcess' =>  $request->tanggalProses,
            'dateInsert' =>  date('Y-m-d'),
            'userId' =>  auth()->user()->id,
            'isApproved' => 1
        ];

        $storeUpdateResult = $this->store->updateOneStore($request->itemId, $request->storeId, $request->amountPacked, $request->amountUnpacked, $request->pastAmount, $request->newAmount, $request->tanggalPacking);

        $teks1 = $request->speciesName." ".$request->sizeName." ".$request->gradeName;
        $teks2 = $request->newAmount.' '.$request->input('packingName');
        return redirect('itemStockList')
        ->with('status','Item '.$teks1.' sebanyak '.$teks2.' berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        //
    }
}
