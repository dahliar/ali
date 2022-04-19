<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Item;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use DB;
use Carbon\Carbon;



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
        $this->transaction = new TransactionController();
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
    public function indexApproval()
    {
        $speciesList = Species::orderBy('nameBahasa')->get();
        return view('item.itemStockApproval', compact('speciesList'));
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
        $request->validate([
            'unpackedPerubahan' => 'required|gt:0'
        ]);

        //update table item, untuk update jumlah amount dan amountUnpacked
        $affected = DB::table('items')
        ->where('id', $request->itemId)
        ->update([
            'amountUnpacked' => $request->unpackedAkhir
        ]);


        $this->transaction->stockChangeLog(2, "Update opname stock unpacked", $request->itemId, $request->unpackedPerubahan);

        $teks1 = $request->speciesName." ".$request->sizeName." ".$request->gradeName;
        return redirect('itemStockList')
        ->with('status','Item unpacked berhasil diubah.');
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
            'isApproved' => 0
        ];
        DB::table('stores')->insert($data);
        
        $teks1 = $request->speciesName." ".$request->sizeName." ".$request->gradeName;
        return redirect('itemStockList')
        ->with('status','Stock item '.$teks1.' berhasil ditambahkan, menunggu approval.');
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
            'amountPacked' => 'required|gte:0', 
            'amountUnpacked' => 'required|gte:0',
            'tanggalPacking' => 'required|date|after_or_equal:tanggalProses'
        ]);

        $affected = DB::table('stores')
        ->where('id', $request->storeId)
        ->update([
            'amountPacked'      => $request->amountPacked,
            'amountUnpacked'    => $request->amountUnpacked,
            'isApproved'        => '0'
        ]);


        $teks1 = $request->speciesName." ".$request->sizeName." ".$request->gradeName;
        return redirect('itemStockView/'.$request->itemId)
        ->with('status','Item '.$teks1.' berhasil diubah.');
    }

    public function getItemStoreHistory($itemId, $start, $end, $opsi){
        $start= Carbon::parse($start);
        $end= Carbon::parse($end);

        $query = DB::table('stores as str')
        ->select('str.id', 
            'i.name as item', 
            's.name as size',
            'g.name as grade',
            'sp.nameBahasa as species',
            'p.name as packing',
            'f.name as freezing',
            'p.shortname as pShortname', 
            'str.isApproved as stat',
            'i.amount as currentAmount',
            'i.amountUnpacked as currentAmountUnpacked',
            'str.datePackage as datePackage',
            'ui.name as userInputName',
            'ua.name as userApproveName',
            'str.amountPacked as amountPacked',
            'str.amountUnpacked as amountUnpacked',
            'i.weightbase as wb',
            DB::raw('(CASE 
                WHEN str.isApproved="0" THEN "Belum" 
                WHEN str.isApproved="1" THEN "Setuju" 
                WHEN str.isApproved="2" THEN "Tolak" 
                END) AS isApproved')
        )
        ->join('users as ui', 'ui.id', '=', 'str.userId')
        ->leftjoin('users as ua', 'ua.id', '=', 'str.approvedBy')
        ->join('items as i', 'i.id', '=', 'str.itemId')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->whereBetween('str.datePackage', [$start->startOfDay(), $end->endOfDay()])
        ->where('i.isActive','=', 1)
        ->where('i.id','=', $itemId)
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderByRaw('s.name+0', 'asc');

        if($opsi == 0){
            $query = $query->where('isApproved', '=', '0');
        } else if($opsi == 1){
            $query = $query->where('isApproved', '=', '1');
        } else if($opsi == 2){
            $query = $query->where('isApproved', '=', '2');
        }

        $query = $query->get();  

        return datatables()->of($query)
        ->editColumn('itemName', function ($row) {
            $name = $row->species." ".$row->grade. " ".$row->size. " ".$row->packing. " ".$row->freezing." ".$row->wb." Kg";
            return $name;
        })
        ->editColumn('amountPacked', function ($row) {
            $name = $row->amountPacked." ".$row->pShortname;
            return $name;
        })
        ->editColumn('amountUnpacked', function ($row) {
            $name = $row->amountUnpacked." Kg";
            return $name;
        })
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->stat != 1){
                $html .= '
                <button class="btn btn-xs btn-light" type="button" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit penyimpanan" onclick="editStoreDetail('."'".$row->id."'".')"><i class="fa fa-edit"></i></button>              
                ';
            }
            if ($row->stat == 0){
                $html .= '
                <button class="btn btn-xs btn-light" type="button" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus penyimpanan" onclick="deleteStoreDetail('."'".$row->id."'".')"><i class="fa fa-trash"></i></button>                
                ';
            }

            return $html;
        })
        ->addIndexColumn()->toJson();    
    }




    public function getStoresRecord(Request $request)
    {
        //dd($request);
        $start= Carbon::parse($request->start);
        $end= Carbon::parse($request->end);

        $query = DB::table('stores as s')
        ->select(
            'i.name as itemName', 
            'f.name as fName', 
            'g.name as gName', 
            'p.name as pName', 
            'p.shortname as pShortname', 
            'i.weightbase as wb',
            'si.name as sName',
            'sp.nameBahasa as spName',
            's.id as id',
            's.itemId as itemId',
            's.isApproved as stat',
            'i.amount as currentAmount',
            'i.amountUnpacked as currentAmountUnpacked',
            's.amountPacked as amountPacked',
            's.amountUnpacked as amountUnpacked',
            's.datePackage as datePackage',
            'ui.name as userInputName',
            'ua.name as userApproveName',
            's.approvedDate as approvedDate',
            DB::raw('(CASE 
                WHEN s.isApproved="0" THEN "Belum" 
                WHEN s.isApproved="1" THEN "Setuju" 
                WHEN s.isApproved="2" THEN "Tolak" 
                END) AS isApproved')
        )
        ->join('users as ui', 'ui.id', '=', 's.userId')
        ->leftjoin('users as ua', 'ua.id', '=', 's.approvedBy')
        ->join('items as i', 'i.id', '=', 's.itemId')
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('sizes as si', 'i.sizeid', '=', 'si.id')
        ->join('species as sp', 'si.speciesId', '=', 'sp.id')
        ->whereBetween('s.datePackage', [$start->startOfDay(), $end->endOfDay()]);

        if($request->opsi == 0){
            $query = $query->where('isApproved', '=', '0');
        } else if($request->opsi == 1){
            $query = $query->where('isApproved', '=', '1');
        } else if($request->opsi == 2){
            $query = $query->where('isApproved', '=', '2');
        }

        if($request->speciesId != 0){
            $query = $query->where('sp.id', '=', $request->speciesId);
        }
        $query = $query->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderByRaw('si.name+0', 'asc')
        ->get();


//{{number_format($totalGrossWeight, 2, ',', '.').' Kg'}}

        return datatables()->of($query)
        ->editColumn('amountPacked', function ($row) {
            if ($row->stat == 0){
                $name = number_format($row->currentAmount, 1, ',', '.')." + ".number_format($row->amountPacked, 1, ',', '.')." ".$row->pShortname;
            } else{
                $name = number_format($row->amountPacked, 1, ',', '.')." ".$row->pShortname;
            }
            return $name;
        })
        ->editColumn('amountUnpacked', function ($row) {
            if ($row->stat == 0){
                $name = number_format($row->currentAmountUnpacked, 1, ',', '.')." + ".number_format($row->amountUnpacked, 1, ',', '.')." Kg";
            } else{
                $name = number_format($row->amountUnpacked, 1, ',', '.')." Kg";
            }
            return $name;
        })
        ->editColumn('itemName', function ($row) {
            $name = $row->spName." ".$row->gName. " ".$row->sName. " ".$row->fName." ".$row->wb." Kg";
            return $name;
        })
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->stat==0){
                $html .= '
                <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Setujui" onclick="approveStore('."'".$row->id."',".')">
                <i class="fa fa-check"></i>
                </button>
                <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Tolak perubahan" onclick="tolakStore('."'".$row->id."',".')">
                <i class="fa fa-times"></i>
                </button>
                ';
            }
            return $html;
        })
        ->addIndexColumn()
        ->toJson();
    }

    public function stockChange(Request $request)
    {
        if($request->approveStore == 1){
            try{
                DB::beginTransaction();
                $approved = DB::table('stores')
                ->where('id', $request->storeId)
                ->update([
                    'isApproved' => 1,
                    'approvedBy' => auth()->user()->id,
                    'approvedDate' => date('Y-m-d')
                ]);

                $store = DB::table('stores')
                ->where('id', $request->storeId)
                ->first();

                $affected = DB::table('items')
                ->where('id', $store->itemId)
                ->update([
                    'amount' => DB::raw('amount + '.$store->amountPacked),
                    'amountUnpacked' => DB::raw('amountUnpacked + '.$store->amountUnpacked),
                ]);
                $this->transaction->stockChangeLog(1, "Approval stock tanggal ".$store->datePackage, $store->itemId, $store->amountPacked);
                DB::commit();
                return "Data berhasil diupdate";
            }
            catch(\Exception $e){
                DB::rollBack();
                return "Gagal Update, kontak administrator";
            }
        } else if($request->approveStore == 2){
            $approved = DB::table('stores')
            ->where('id', $request->storeId)
            ->update([
                'isApproved' => 2,
                'approvedBy' => auth()->user()->id,
                'approvedDate' => date('Y-m-d')
            ]);
            return "data berhasil ditolak";
        }
    }
    public function stockChangeDelete(Request $request)
    {
        $approved = DB::table('stores')
        ->where('id', $request->storeId)
        ->delete();
        return "data berhasil dihapus";
    }
}
