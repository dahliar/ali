<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Species;
use App\Models\Store;
use DB;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->item = new Item();
    }

    public function getItemForSelectOption($tid, $pid, $speciesId){
        $something=$this->item->getItemForSelectOption($tid, $pid, $speciesId);
        return $something;
    }



    public function getAllStockItem($speciesId){
        return $this->item->getAllItemData($speciesId);
    }
    
    public function getItemHistory($itemId){
        return $this->item->getItemHistory($itemId);
    }
    public function getUnpackedItemHistory($itemId){
        return $this->item->getUnpackedItemHistory($itemId);
    }

    public function index(Request $request)
    {
        $speciesList = Species::orderBy('name')->get();
        return view('item.itemStockList', compact('speciesList'));
    }

    public function indexStockSpecies(Request $request)
    {
        return view('item.speciesStockList');
    }
    public function getSpeciesStock(){
        return $this->item->getSpeciesStock();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($itemId)
    {
        //
        return view('item.itemStockView', compact('itemId'));
    }
    public function showUnpacked($itemId)
    {
        //
        return view('item.itemStockViewUnpacked', compact('itemId'));
    }

    public function indexHarga(Request $request)
    {
        $speciesList = Species::orderBy('name')->get();
        return view('item.priceList', compact('speciesList'));
    }
    public function getPriceList($species, $start, $end){
        return $this->item->getPriceListByPurchasing($species, $start, $end);
    }

    public function indexHpp(Request $request)
    {
        $species = Species::orderBy('name')->get();
        return view('item.hppList', compact('species'));
    }
    public function getHpp(Request $request){
        $species = Species::orderBy('name')->get();
        $harian = DB::table('dailysalaries')
        ->select(
            DB::raw('sum(uangHarian + uangLembur) as total'),
            DB::raw('count(id) as jumlahOrang'),
        )
        ->whereBetween('presenceDate', [$request->start, $request->end])
        ->first();

        $dataHarian=[
            'total' => $harian->total,
            'orang' => $harian->jumlahOrang
        ];
        
        $borongan = DB::table('borongans as b')
        ->select(
            DB::raw('sum(db.netPayment) as total'),
            DB::raw('count(db.employeeId) as jumlahOrang'),
        )
        ->join('detail_borongans as db', 'db.boronganId', '=', 'b.id')
        ->whereBetween('b.tanggalKerja', [$request->start, $request->end])
        ->first();

        $dataBorongan=[
            'total' => $borongan->total,
            'orang' => $borongan->jumlahOrang
        ];

        $honorarium = DB::table('honorariums as h')
        ->select(
            DB::raw('sum(jumlah) as total'),            
            DB::raw('count(h.employeeId) as jumlahOrang'),
        )
        ->whereBetween('tanggalKerja', [$request->start, $request->end])
        ->first();

        $dataHonorarium=[
            'total' => $honorarium->total,
            'orang' => $honorarium->jumlahOrang
        ];

        $purchases= DB::table('purchases as pur')
        ->join('companies as c', 'c.id', '=', 'pur.companyId')
        ->join('detail_purchases as dp', 'dp.purchasesId', '=', 'pur.id')
        ->join('items as i', 'i.id', '=', 'dp.itemId')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->select(
            'c.name as perusahaan',
            DB::raw('concat(sp.name, " ", g.name, " ", s.name) as name'), 
            'pur.purchaseDate as tanggal',
            'dp.amount as amount',
            'dp.price as price'
        )
        ->whereBetween('pur.purchaseDate', [$request->start, $request->end]);
        if ($request->species > 0){
            $purchases = $purchases->where('sp.id', '=', $request->species);
        }
        if ($request->item > 0){
            $purchases = $purchases->where('i.id', '=', $request->item);
        }

        $purchases = $purchases->get();
        $start=$request->start;
        $end=$request->end;
        $speciesChoosen=$request->species;
        $itemChoosen=$request->item;
        $showDetail=$request->showDetail;

        return view('item.hppList', compact('showDetail','species','start','end','dataHarian', 'dataBorongan', 'dataHonorarium', 'purchases', 'speciesChoosen', 'itemChoosen'));    
    }
}
