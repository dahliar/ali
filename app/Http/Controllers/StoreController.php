<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Item;
use App\Models\Species;
use App\Models\StockSubtract;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Exports\StockOpnameExport;
use App\Imports\StockOpnameImport;
use Maatwebsite\Excel\Facades\Excel;


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
    public function indexApprovalPenambahan()
    {
        $speciesList = Species::orderBy('nameBahasa')->get();
        return view('item.itemStockApproval', compact('speciesList'));
    }
    public function indexApprovalPengurangan()
    {
        $speciesList = Species::orderBy('nameBahasa')->get();
        return view('item.itemStockSubtractApproval', compact('speciesList'));
    }

    public function create($speciesId)
    {
        $oneItem = $this->item->getOneItem($speciesId);
        return view('item.itemStockAdd', compact('oneItem'));
    }
    public function subtract($itemId)
    {
        $oneItem = $this->item->getOneItem($itemId);
        return view('item.itemStockSubtract', compact('oneItem'));
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
    public function storeSubtract(Request $request)
    {
        $request->validate([
            'packedKurang' => 'required|gt:0', 
            'tanggal' => 'required|date|before_or_equal:today',
            'alasan' => 'required|string|min:20|max:255'
        ],
        [
            'packedKurang.*'=> 'Jumlah pengurangan harus lebih besar dari 0',
            'tanggal.*'=> 'Tanggal harus sebelum hari ini',
            'alasan.*'=> 'Alasan harus antara 30-300 karakter'
        ]);

        $data = [
            'itemId' => $request->itemId,
            'amountSubtract' =>  $request->packedKurang,
            'alasan' =>  $request->alasan,
            'tanggal' =>  $request->tanggal,
            'userId' =>  auth()->user()->id,
            'isApproved' => 0
        ];
        DB::table('stock_subtracts')->insert($data);
        
        return redirect('itemStockList')
        ->with('status','Stock item '.$request->itemName.' berhasil diinput, menunggu approval.');
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
    public function subtractEdit(StockSubtract $stockSubtract)
    {
        //
        $data = DB::table('items as i')
        ->select(
            'i.name as itemName', 
            'f.name as freezingName', 
            'g.name as gradeName', 
            'p.name as packingName', 
            'p.shortname as pShortname', 
            's.name as sizeName',
            'sp.nameBahasa as speciesName',
            'i.amount as amount',
            'i.weightbase as weightbase'
        )
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('i.id','=', $stockSubtract->itemId)
        ->first();

        return view('item.itemStockSubtractEdit', compact('stockSubtract', 'data'));
    }




    public function update(Request $request)
    {
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

    public function subtractUpdate(Request $request)
    {
        $request->validate([
            'amountSubtract' => 'required|gt:0|different:oldAmountsubtract', 
            'tanggal' => 'required|date|after_or_equal:today'
        ],[
            'amountSubtract.different' => 'Jumlah lama dan baru harus berbeda', 

        ]);

        $affected = DB::table('stock_subtracts')
        ->where('id', $request->stockSubtractId)
        ->update([
            'amountSubtract'    => $request->amountSubtract,
            'tanggal'           => $request->tanggal,    
            'isApproved'        => '0'
        ]);


        return redirect('itemStockSubtractView/'.$request->itemId)
        ->with('status','Perubahan pengurangan stok item '.$request->item.' berhasil dilakukan, menunggu approval.');
    }

    public function getItemStoreHistory($itemId, $start, $end, $opsi){
        $start= Carbon::parse($start);
        $end= Carbon::parse($end);

        $query = DB::table('stores as str')
        ->select('str.id', 
            'i.name as item', 
            's.name as size',
            'sh.name as shape',
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
        ->join('shapes as sh', 'i.shapeId', '=', 'sh.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->whereBetween('str.datePackage', [$start->startOfDay(), $end->endOfDay()])
        ->where('i.isActive','=', 1)
        ->where('i.id','=', $itemId)
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderBy('s.name', 'asc');

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
            $name = $row->species." ".$row->grade. " ".$row->shape. " ".$row->size. " ".$row->freezing." ".$row->wb." Kg/".$row->pShortname." - ".$row->item;
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


    public function getItemSubtractHistory($itemId, $start, $end, $opsi){
        $start= Carbon::parse($start);
        $end= Carbon::parse($end);

        $query = DB::table('stock_subtracts as str')
        ->select('str.id', 
            'i.name as item', 
            's.name as size',
            'sh.name as shape',
            'g.name as grade',
            'sp.nameBahasa as species',
            'p.name as packing',
            'f.name as freezing',
            'p.shortname as pShortname', 
            'str.isApproved as stat',
            'i.amount as currentAmount',
            'i.amountUnpacked as currentAmountUnpacked',
            'str.tanggal as tanggal',
            'ui.name as userInputName',
            'ua.name as userApproveName',
            'str.amountSubtract as amountSubtract',
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
        ->join('shapes as sh', 'i.shapeId', '=', 'sh.id')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->whereBetween('str.tanggal', [$start->startOfDay(), $end->endOfDay()])
        ->where('i.isActive','=', 1)
        ->where('i.id','=', $itemId)
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderBy('s.name', 'asc');

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
            $name = $row->species." ".$row->grade. " ".$row->shape. " ".$row->size. " ".$row->freezing." ".$row->wb." Kg/".$row->pShortname." - ".$row->item;
            return $name;
        })
        ->editColumn('amountSubtract', function ($row) {
            $name = $row->amountSubtract." ".$row->pShortname;
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
        ->orderBy('si.name', 'asc')
        ->get();

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
            $name = $row->spName." ".$row->gName. " ".$row->sName. " ".$row->fName." ".$row->wb." Kg/".$row->pShortname." - ".$row->itemName;
            return $name;
        })
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->stat==0){
                $html .= '
                <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Setujui perubahan" onclick="approveStore('."'".$row->id."',".')">
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

    public function getStorekSubtractRecord(Request $request)
    {
        //dd($request);
        $start= Carbon::parse($request->start);
        $end= Carbon::parse($request->end);

        $query = DB::table('stock_subtracts as s')
        ->select(
            'i.name as itemName', 
            'f.name as fName', 
            'g.name as gName', 
            'p.name as pName', 
            's.alasan as alasan',
            'p.shortname as pShortname', 
            'i.weightbase as wb',
            'si.name as sName',
            'sp.nameBahasa as spName',
            's.id as id',
            's.itemId as itemId',
            's.isApproved as stat',
            'i.amount as currentAmount',
            's.amountSubtract as amountSubtract',
            's.tanggal as tanggal',
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
        ->whereBetween('s.tanggal', [$start->startOfDay(), $end->endOfDay()]);

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
        ->orderBy('si.name', 'asc')
        ->get();

        return datatables()->of($query)
        ->editColumn('amountSubtract', function ($row) {
            if ($row->stat == 0){
                $name = number_format($row->currentAmount, 1, ',', '.')." - ".number_format($row->amountSubtract, 1, ',', '.')." ".$row->pShortname;
            } else{
                $name = number_format($row->amountSubtract, 1, ',', '.')." ".$row->pShortname;
            }
            return $name;
        })
        ->editColumn('itemName', function ($row) {
            $name = $row->spName." ".$row->gName. " ".$row->sName. " ".$row->fName." ".$row->wb." Kg/".$row->pShortname." - ".$row->itemName;
            return $name;
        })
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->stat==0){
                $html .= '
                <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Setujui perubahan" onclick="approveStockSubtract('."'".$row->id."',".')">
                <i class="fa fa-check"></i>
                </button>
                <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Tolak perubahan" onclick="tolakStockSubtract('."'".$row->id."',".')">
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
    public function stockSubtractChange(Request $request)
    {
        if($request->approveStockSubtract == 1){
            try{
                DB::beginTransaction();
                $approved = DB::table('stock_subtracts')
                ->where('id', $request->stockSubtractId)
                ->update([
                    'isApproved' => 1,
                    'approvedBy' => auth()->user()->id,
                    'approvedDate' => date('Y-m-d')
                ]);

                $stock_subtract = DB::table('stock_subtracts')
                ->where('id', $request->stockSubtractId)
                ->first();

                $affected = DB::table('items')
                ->where('id', $stock_subtract->itemId)
                ->update([
                    'amount' => DB::raw('amount - '.$stock_subtract->amountSubtract)
                ]);
                $this->transaction->stockChangeLog(2, "Approval pengurangan stock tanggal ".$stock_subtract->tanggal, $stock_subtract->itemId, $stock_subtract->amountSubtract);
                DB::commit();
                return "Data pengurangan berhasil diupdate";
            }
            catch(\Exception $e){
                DB::rollBack();
                return "Gagal Update, kontak administrator";
            }
        } else if($request->approveStockSubtract == 2){
            $approved = DB::table('stock_subtracts')
            ->where('id', $request->stockSubtractId)
            ->update([
                'isApproved' => 2,
                'approvedBy' => auth()->user()->id,
                'approvedDate' => date('Y-m-d')
            ]);
            return "data pengurangan ditolak";
        }
    }

    public function stockChangeDelete(Request $request)
    {
        $approved = DB::table('stores')
        ->where('id', $request->storeId)
        ->delete();
        return "data berhasil dihapus";
    }
    public function deleteStockSubtractChange(Request $request)
    {
        $approved = DB::table('stock_subtracts')
        ->where('id', $request->subtractId)
        ->delete();
        return "data berhasil dihapus";
    }

    public function opname()
    {
        return view('opname.opname');
    }

    public function getOpnameData()
    {
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'sp.nameBahasa as speciesName', 
            'i.amount as jumlahPacked',
            DB::RAW('(i.amount * i.weightbase) as amount'),
            'amountUnpacked as jumlahUnpacked',
            'p.shortname as packingShortname',
            DB::raw('concat(sp.nameBahasa," ",g.name," ",sh.name," ",s.name, " ",f.name," ",weightbase," Kg/",p.shortname," - ",i.name) as itemName'),
            DB::raw('(select sum(dt.amount) from detail_transactions as dt join transactions t on dt.transactionId=t.id where t.status=4 and dt.itemId=i.id) as jumlahOnLoading'),
            'i.name as iname',
            's.name as sname',
            'sh.name as shname',
            'f.name as fname',
            'g.name as gname',
            'baseprice as baseprice',
            'weightbase as weightbase'
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('shapes as sh', 'i.shapeId', '=', 'sh.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->orderBy('sp.nameBahasa', 'asc')
        ->orderBy('g.name', 'asc')
        ->orderBy('s.name', 'asc')
        ->get();

        return datatables()->of($query) 
        ->addIndexColumn()
        ->toJson();
    }
    public function opnameImport()
    {
        return view('opname.opnameImport');
    }

    public function excelStockOpnameFileGenerator()
    {
        return Excel::download(new StockOpnameExport(), 'Stock Opname Tanggal '.Carbon::now()->toDateString().'.xlsx');
    }

    public function stockOpnameStore(Request $request)
    {
        $request->validate([
            'stockOpnameFile' => 'required', 
            'stockOpnameDate' => 'required|date|before_or_equal:today'
        ]);


        $import = new StockOpnameImport($request->stockOpnameDate);
        Excel::import($import, $request->stockOpnameFile);

        $message = $import->getImportResult();
        return redirect('opname')->with('status', $message);
    }
    public function historyPerubahanStock()
    {
        $speciesList = Species::orderBy('nameBahasa')->select('id','nameBahasa')->get();
        return view('item.itemStockHistories', compact('speciesList'));
    }

    public function getHistoryPerubahanStock($species, $start, $end){
        $query = DB::table('stock_histories as sh')
        ->select(
            DB::raw('(CASE WHEN sh.jenis="1" THEN "Tambah"
                WHEN sh.jenis="2" THEN "Kurang"
                WHEN sh.jenis="3" THEN "Opname"
                END) as jenis'
            ),
            'sh.userId as userPeubah',
            'sh.informasiTransaksi as informasiTransaksi',
            'sh.amount as realAmount',
            DB::raw('concat(i.weightbase, " Kg/", p.shortname) as weightbase'),
            DB::raw('concat(sp.nameBahasa," ",g.name," ",b.name," ",s.name, " ",f.name," ",weightbase," ",p.shortname," - ",i.name) as itemName'),
            DB::raw('concat((sh.amount*i.weightbase)," Kg") as amount'),
            DB::raw('concat(((sh.amount+sh.prevAmount)*i.weightbase)," Kg") as afterAmount'),
            DB::raw('concat((sh.prevAmount*i.weightbase)," Kg") as prevAmount')
        )
        ->join('items as i', 'i.id', '=', 'sh.itemId')
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('shapes as b', 'i.sizeid', '=', 'b.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->whereBetween('sh.created_at', [$start." 00:00:00", $end." 23:59:59"])
        ->orderBy('sp.name')
        ->orderBy('g.name', 'desc')
        ->orderBy('s.name', 'asc')
        ->orderBy('f.name');
        if ($species>0){
            $query->where('sp.id','=', $species);
        }
        $query->get();
        return datatables()->of($query)
        ->addIndexColumn()->toJson();
    }

}
