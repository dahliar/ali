<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Species;
use App\Models\Transaction;
use App\Models\DetailTransaction;
use App\Models\Countries;

use DB;
use Carbon\Carbon;


class StockController extends Controller
{
    //digunakan untuk akses melihat data seluruh stock
    public function index()
    {
        $species = Species::orderBy('name')->get();
        return view('stock.stocklist', compact('species'));
    }
    public function indexTransactionBarcode($transactionId)
    {
        return view('stock.stockDetailBarcodeTransactionList', compact('transactionId'));
    }
    public function indexTransactionBarcodeList($transactionId)
    {
        $transactions =  DB::table('codes as c')
        ->select(
            'c.id as id',
            'cu.fullcode as fullcode',
            'c.productionDate as productionDate',
            'cu.packagingDate as packagingDate',
            'cu.storageDate as storageDate',
            'cu.loadingDate as loadingDate',
            'cu.expireDate as expiringDate',
            'vid.nameBahasa as name'
        )
        ->join('code_usages as cu', 'cu.codeId', '=', 'c.id')
        ->join('view_item_details as vid', 'c.itemId', '=', 'vid.itemId')
        ->where('cu.transactionId', '=', $transactionId)
        ->orderBy('cu.fullcode', 'asc')
        ->get();
        return view('stock.transactionBarcodeList', compact('transactions'));
    }



    public function indexTransaction()
    {
        return view('stock.stockTransactionList');    
    }

    public function indexScanMasuk()
    {
        return view('stock.stockRecapMasuk');    
    }
    public function indexScanKeluar()
    {
        return view('stock.stockRecapKeluar');    
    }
    public function indexScanMasukHari($storageDate)
    {
        return view('stock.stockRecapMasukHari', compact('storageDate'));    
    }
    public function indexScanKeluarHari($transactionId, $loadingDate)
    {
        $transaction =  DB::table('transactions as t')
        ->select(
            't.id as id',
            't.transactionNum as transactionNum',
            't.pinum as pinum',
            't.loadingDate as loadingDate',
            'c.name as companyName'
        )
        ->join('companies as c', 'c.id', '=', 't.companyId')
        ->where('t.id', '=', $transactionId)
        ->first();
        return view('stock.stockRecapKeluarHari', compact('transaction','loadingDate'));    
    }
    public function indexScanMasukHariBarcodeList($storageDate, $itemId)
    {
        $itemName = DB::table('view_item_details')->select('name as itemName')->where('itemId', '=', $itemId)->first()->itemName;
        return view('stock.stockRecapMasukHariBarcodeList', compact('storageDate', 'itemId', 'itemName'));    
    }
    public function indexScanKeluarBarcodeList($transactionId, $loadingDate, $itemId)
    {
        $transaction =  DB::table('transactions as t')
        ->select(
            't.id as id',
            't.transactionNum as transactionNum',
            't.pinum as pinum',
            't.loadingDate as loadingDate',
            'c.name as companyName'
        )
        ->join('companies as c', 'c.id', '=', 't.companyId')
        ->where('t.id', '=', $transactionId)
        ->first();
        $itemName = DB::table('view_item_details')->select('name as itemName')->where('itemId', '=', $itemId)->first()->itemName;
        return view('stock.stockRecapKeluarBarcodeList', compact('transaction', 'loadingDate', 'itemId', 'itemName'));    
    }

    public function create()
    {
        return view('stock.stockMasukAdd');
    }


    public function show($id)
    {
        $barcode = DB::table('code_usages as cu')
        ->join('codes as c', 'c.id', '=', 'cu.codeId')
        ->join('view_item_details as vid', 'vid.itemId', '=', 'c.itemId')
        ->where('cu.id', $id)
        ->select(
            'cu.id as id',
            'c.productionDate as productionDate',
            'cu.packagingDate as packagingDate',
            'cu.storageDate as storageDate',
            'cu.loadingDate as loadingDate',
            'cu.fullcode as barcode',
            'cu.status as status', 
            'vid.name as name'
        )->first();

        return view('stock.stockEdit', compact('barcode'));
    }

    public function storeMasuk(Request $request)
    {        
        $tanggal = Carbon::now()->toDateString();
        $arr = array();


        if (!empty($request->barcode)){
            foreach ($request->barcode as $barcode){            
                //DB::table('code_usages as cu')
                //->where('cu.fullcode', '=', $barcode)
                //->where('cu.status', '=', 0)
                //->update(['cu.status' => 1, 'cu.storageDate' => $tanggal]);

                array_push($arr,$barcode);
            }
        }

        DB::table('code_usages as cu')
        ->whereIn('cu.fullcode', $arr)
        ->where('cu.status', '=', 0)
        ->update(['cu.status' => 1, 'cu.storageDate' => $tanggal, 'cu.packagingDate' => $tanggal]);


        $query = DB::table('code_usages as cu')
        ->join('codes as c', 'c.id', '=', 'cu.codeId')
        ->whereIn('cu.fullcode', $arr)
        ->select(
            'itemId', 
            DB::raw('count(cu.id) AS jumlah')
        )
        ->groupBy('c.itemId')
        ->get();

        $tran = new TransactionController();
        foreach ($query as $item){ 
            $query = DB::table('items as i')
            ->where('i.id','=', $item->itemId)
            ->increment('i.amount', $item->jumlah);

            $tran->stockChangeLog(1, "Scan barcode masuk barang ".$item->itemId." tanggal ".$tanggal, $item->itemId, $item->jumlah);
        }


        $species = Species::orderBy('name')->get();
        return redirect('scanList')
        ->with('species',$species)   
        ->with('status',"Data disimpan");   
    }

    public function storeKeluar(Request $request){
        $tanggal = Carbon::now()->toDateString();
        $arr = array();
        if (!empty($request->barcode)){
            foreach ($request->barcode as $barcode){            
                DB::table('code_usages as cu')
                ->where('cu.fullcode', '=', $barcode)
                ->where('cu.status', '=', 1)
                ->update(['cu.status' => 2, 'cu.loadingDate' => $tanggal, 'cu.transactionId' => $request->transactionId]);

                array_push($arr,$barcode);
            }
        }
        $query = DB::table('code_usages as cu')
        ->join('codes as c', 'c.id', '=', 'cu.codeId')
        ->whereIn('cu.fullcode', $arr)
        ->where('cu.transactionId','=', $request->transactionId)
        ->select(
            'c.itemId as itemId', 
            DB::raw('count(cu.id) AS jumlah')
        )
        ->groupBy('c.itemId')
        ->get();

        foreach ($query as $item){
            $inventory = DetailTransaction::firstOrNew(['transactionId' => $request->transactionId, 'itemId' => $item->itemId]);
            $inventory->itemId = $item->itemId;
            $inventory->amount = ($inventory->amount + $item->jumlah);
            //dd($inventory);
            $inventory->save();
        }

        $species = Species::orderBy('name')->get();
        return redirect('scanTransactionList')
        ->with('transaction', $request->transactionId)
        ->with('status','Item berhasil ditambahkan.');
    }

    public function scanStoreBarcodeKeluar(Request $request){
        //dd($request);
        $tanggal = Carbon::now()->toDateString();

        $query = DB::table("code_usages as cu")
        ->where('cu.fullcode', '=', $request->barcode)
        ->join('codes as c', 'c.id', '=', 'cu.codeId')
        ->join('view_item_details as vid', 'vid.itemId', '=', 'c.itemId')
        ->select('vid.name as name', 'cu.status as status', 'vid.itemId as itemId')->first();

        $message = "";
        $itemId = -1;
        $itemName = -1;

        if (!$query){
            $message = "Barcode tidak ditemukan: ".$request->barcode;
        } else{
            $itemId = $query->itemId;
            $itemName = $query->name;

            $updateCU = DB::table('code_usages as cu')
            ->where('cu.fullcode', '=', $request->barcode);
            $status = $query->status;
            $message = "Barcode tidak ditemukan: ".$request->barcode;


            switch ($query->status){
                case 0 : 
                $message = "Update: Package, Simpan Storage dan Loading: ".$request->barcode;
                $updateCU->update([
                    'cu.status' => 2, 
                    'cu.packagingDate' => $tanggal, 
                    'cu.storageDate' => $tanggal, 
                    'cu.loadingDate' => $tanggal, 
                    'cu.transactionId' => $request->transactionId
                ]);
                break;
                case 1 : 
                $message = "Update: Loading";
                $updateCU->update([
                    'cu.status' => 2, 
                    'cu.storageDate' => $tanggal, 
                    'cu.loadingDate' => $tanggal, 
                    'cu.transactionId' => $request->transactionId
                ]);
                break;
                case 2 : 
                $message = "Barang telah terloading: ".$request->barcode;
                break;

            }
        }

        return response()->json(["name" => $itemName, "message" => $message, "itemId" => $itemId]);

    }

    public function checkStatusBarcodeBarang(Request $request)
    {
        $status = -1;
        $itemName = "";
        $message = "";
        $itemId = "";
        $query = DB::table("code_usages as cu")
        ->where('cu.fullcode', '=', $request->barcode);

        $isExist = $query->count('cu.id');

        $query = $query->join('codes as c', 'c.id', '=', 'cu.codeId')
        ->join('view_item_details as vid', 'vid.itemId', '=', 'c.itemId')
        ->select('vid.name as name', 'cu.status as status')->first();
        /*
        ->select('vid.name as name', 'cu.status as status'/*, 'vid.itemId as itemId')->first();
        */
        if ($isExist >= 1){
            switch ($query->status){
                case 0 : 
                $status = 0;
                $itemName = $query->name;
                //$itemId = $query->itemId;
                $message = "Barcode/barang baru tercetak";
                break;
                case 1 : 
                $status = 1;
                $itemName = $query->name;
                //$itemId = $query->itemId;
                $message = "Barcode/barang tersimpan di storage";
                break;
                case 2 : 
                $status = 2;
                $itemName = $query->name;
                //$itemId = $query->itemId;
                $message = "Barcode/barang terloading";
                break;
            }
        } else {
            $status = -1;
            $message = "Barcode tidak terdaftar";
        }
        return response()->json(["found" => $status, "name" => $itemName, "message" => $message, "itemId" => $itemId]);
    }
    public function getItemsForScanPage($speciesId){
        $query = DB::table('view_item_details as vid')
        ->where('vid.speciesId','=', $speciesId)
        ->where('vid.itemStatus', '=', 1)
        ->orderBy('vid.shapesName', 'asc')
        ->orderBy('vid.gradeName', 'asc')
        ->orderBy('vid.sizeName', 'asc')
        ->orderBy('vid.weightbase', 'asc')
        ->orderBy('vid.freezingName')
        ->select(
            'vid.itemId as itemId', 
            'vid.nameBahasa as itemName'
        );
        return $query->get();  
    }


    public function getAllBarcodeData(Request $request){
        //dd($request);
        $query = DB::table('codes as c')
        ->select(
            'cu.id as id', 
            'c.productionDate as productionDate', 
            'c.itemId as itemId', 
            'cu.fullcode as fullcode',
            'cu.status as status',
            DB::raw('(CASE WHEN 
                cu.status ="0" THEN "Created"
                WHEN cu.status ="1" then "Stored" 
                WHEN cu.status ="2" then "Loaded" 
                WHEN cu.status ="3" then "Hilang" 
                WHEN cu.status ="4" then "Dihapus" 
                END) AS statusText'),
            'cu.packagingDate as packagingDate', 
            'cu.storageDate as storageDate', 
            'cu.loadingDate as loadingDate',
            'vid.speciesName as speciesName',
            'vid.itemName as itemName'
        )
        ->join('code_usages as cu', 'c.id', '=', 'cu.codeId')
        ->join('view_item_details as vid', 'c.itemId', '=', 'vid.itemId')
        ->whereBetween('c.productionDate', [$request->start, $request->end]);

        if ($request->status >= 0){
            $query=$query->where('cu.status', '=', $request->status);
        }
        if ($request->speciesId >= 0){
            $query=$query->where('vid.speciesId', '=', $request->speciesId);
            if ($request->itemId >= 0){
                $query=$query->where('vid.itemId', '=', $request->itemId);
            }
        }
        $query->orderBy('cu.fullcode')->get();

        return datatables()->of($query)
        ->addColumn('speciesName', function ($row) {
            return ($row->speciesName ." - ". $row->itemName);
        })
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit data barcode" onclick="editBarcode('."'".$row->id."'".')">
            <i class="fas fa-edit"></i>
            </button>            
            ';
            if ($row->status < 2){
                $html .= '
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus barcode" onclick="hapusBarcode('."'".$row->id."'".', '."'".$row->itemId."'".')">
                <i class="fas fa-trash"></i>
                </button>            
                ';
            }
            return $html;
        })
        ->rawColumns(['action'])
        ->toJson();
    }

    public function getAllBarcodeExportTransaction(Request $request){
        $start=$request->start;
        $end=$request->end;
        $query = DB::table('transactions as t')
        ->select(
            't.id as id', 
            't.transactionnum as invnum', 
            't.pinum as pinum', 
            'c.name as name', 
            DB::raw('count(cu.id) as jumlahBarcode'),
            DB::raw('(CASE WHEN t.jenis ="1" THEN "Ekspor"
                WHEN t.jenis ="2" then "Lokal" END
            ) AS jenis'),
            DB::raw('(CASE WHEN t.status ="0" THEN "New Submission"
                WHEN t.status ="1" then "Offering"
                WHEN t.status ="2" then "Finished"
                WHEN t.status ="3" then "Canceled"
                WHEN t.status ="4" then "Sailing"
                END) AS status')
        )
        ->leftjoin('code_usages as cu', 't.id', '=', 'cu.transactionId')
        ->join('companies as c', 'c.id', '=', 't.companyid')
        ->where(function($query2) use ($start, $end){
            $query2->whereBetween('t.loadingDate', [$start, $end])
            ->orWhereBetween('t.transactionDate', [$start, $end])
            ->orWhereBetween('t.departureDate', [$start, $end])
            ->orWhereBetween('t.arrivalDate', [$start, $end]);
        });
        if($request->statusTransaksi != -1){
            $query->where('t.status', '=', $request->statusTransaksi);
        }
        $query->groupBy('t.id')
        ->orderBy('t.creationDate', 'desc')
        ->orderBy('t.status', 'desc')
        ->get();

        return datatables()->of($query)
        ->addColumn('number', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-4">PI</span>
            <span class="col-8 text-end">'.$row->pinum.'</span>
            </div>
            <div class="row form-group">
            <span class="col-4">INV</span>
            <span class="col-8 text-end">'.$row->invnum.'</span>
            </div>';
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html = '
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Detil Semua Barang" onclick="detilBarang('."'".$row->id."'".')">
            <i class="fa fa-list""></i>
            </button>
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar Barcode" onclick="detilBarcode('."'".$row->id."'".')">
            <i class="fas fa-barcode""></i>
            </button>
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Scan barang - keluar" onclick="functionStockKeluar('."'".$row->id."'".')">
            <i class="fas fa-satellite-dish"></i>
            </button>
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Scan barang - keluar V2" onclick="functionStockKeluarV2('."'".$row->id."'".')">
            <i class="fas fa-satellite-dish"></i>
            </button>';
            return $html;
        })
        ->rawColumns(['action','number'])
        ->toJson();
    }



    /*
    *   Keluar Barang
    *   Keluar Barang
    *   Keluar Barang
    *   Keluar Barang
    */

    public function createKeluar($id)
    {

        $transaction = DB::table('transactions as t')
        ->select(
            't.id as id',
            'c.id as companyId',
            'c.name as companyName',
            't.pinum as pinum'
        )
        ->join('companies as c', 'c.id', '=', 't.companyId')
        ->where('t.id', '=', $id)
        ->first();

        $company = $transaction->companyName;
        $pinum = $transaction->pinum;

        return view('stock.stockKeluarAdd', compact('transaction'));
    }
    public function createKeluarV2($id)
    {

        $transaction = DB::table('transactions as t')
        ->select(
            't.id as id',
            'c.id as companyId',
            'c.name as companyName',
            't.pinum as pinum'
        )
        ->join('companies as c', 'c.id', '=', 't.companyId')
        ->where('t.id', '=', $id)
        ->first();

        $company = $transaction->companyName;
        $pinum = $transaction->pinum;

        return view('stock.stockKeluarAddV2', compact('transaction'));
    }
    public function getAllBarcodeItemDetail($transactionId){
        $query = DB::table('detail_transactions as dt')
        ->select(
            'dt.id as id', 
            'dt.transactionId as transactionId', 
            'dt.amount as amount',
            'vid.itemId as itemId', 
            'vid.name as itemName', 
            'vid.weightbase as wb',
            'vid.pshortname as pshortname',
        )
        ->join('transactions as t', 't.id', '=', 'dt.transactionId')
        ->join('view_item_details as vid', 'vid.itemId', '=', 'dt.itemId')
        ->where('t.id','=', $transactionId)
        ->orderBy('vid.speciesName')
        ->orderBy('vid.gradeName', 'desc')
        ->orderBy('vid.sizeName')
        ->orderBy('vid.freezingName');
        
        $query->get();  


        return datatables()->of($query)
        ->addColumn('weight', function ($row) {
            $html = number_format(($row->amount * $row->wb), 2, ',', '.').' Kg';
            return $html;
        })
        ->editColumn('amount', function ($row) {
            $html = number_format($row->amount, 2, ',', '.').' '.$row->pshortname;
            return $html;
        })
        ->addIndexColumn()->toJson();
    }

    public function getScanMasukHarian(Request $request){
        $query = DB::table('codes as c')
        ->select(
            'cu.storageDate as storageDate',
            DB::raw('count(cu.id) as jumlahBarcode'),
        )
        ->join('code_usages as cu', 'c.id', '=', 'cu.codeId')
        ->whereBetween('cu.storageDate', [$request->start, $request->end])
        ->whereNotNull('cu.storageDate')
        ->groupBy('cu.storageDate')
        ->orderBy('cu.storageDate')->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '           
            <button  data-rowid="'.$row->storageDate.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar barang" onclick="barcodeItemList('."'".$row->storageDate."'".')">
            <i class="fas fa-list"></i>
            </button>            
            ';
            return $html;
        })
        ->rawColumns(['action'])
        ->toJson();
    }

    public function getScanMasukHarianTanggal(Request $request){
        $query = DB::table('code_usages as cu')
        ->select(
            'cu.storageDate as storageDate', 
            'c.itemId as itemId', 
            'vid.name as itemName', 
            'vid.speciesName as speciesName', 
            DB::raw('count(cu.id) as jumlahBarcode')
        )
        ->join('codes as c', 'c.id', '=', 'cu.codeId')
        ->join('view_item_details as vid', 'vid.itemId', '=', 'c.itemId')
        ->where('cu.storageDate','=', $request->tanggal)
        ->groupBy('c.itemId')
        ->orderBy('vid.speciesName')
        ->orderBy('vid.itemName')
        ->get();  

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '           
            <button  data-rowid="'.$row->itemId.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar barcode" onclick="barcodeList('."'".$row->storageDate."'".",'".$row->itemId."'".')">
            <i class="fas fa-list"></i>
            </button>            
            ';
            return $html;
        })
        ->rawColumns(['action'])
        ->toJson();
    }
    public function getScannedKeluarTransaksiHari(Request $request){
        $query = DB::table('code_usages as cu')
        ->select(
            't.id as transactionId',
            't.loadingDate as loadingDate', 
            'c.itemId as itemId', 
            'vid.name as itemName', 
            'vid.speciesName as speciesName', 
            DB::raw('count(cu.id) as jumlahBarcode')
        )
        ->join('codes as c', 'c.id', '=', 'cu.codeId')
        ->join('transactions as t', 't.id', '=', 'cu.transactionId')
        ->join('view_item_details as vid', 'vid.itemId', '=', 'c.itemId')
        ->where('t.loadingDate','=', $request->tanggal)
        ->where('cu.transactionId','=', $request->transactionId)
        ->groupBy('c.itemId')
        ->orderBy('vid.speciesName')
        ->orderBy('vid.itemName')
        ->get();  

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '           
            <button  data-rowid="'.$row->itemId.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar barcode" onclick="barcodeList('."'".$row->transactionId."'".",'".$row->loadingDate."'".",'".$row->itemId."'".')">
            <i class="fas fa-list"></i>
            </button>            
            ';
            return $html;
        })
        ->rawColumns(['action'])
        ->toJson();
    }
    

    public function getBarcodeListTanggalItem(Request $request){
        $query =  DB::table('codes as c')
        ->select(
            'c.id as id',
            'cu.fullcode as fullcode',
            DB::raw('(CASE WHEN 
                cu.status ="0" THEN "Created"
                WHEN cu.status ="1" then "Stored" 
                WHEN cu.status ="2" then "Loaded" 
                WHEN cu.status ="3" then "Hilang" 
                WHEN cu.status ="4" then "Dihapus" 
                END) AS status'),
            'c.productionDate as productionDate',
            'cu.packagingDate as packagingDate',
            'cu.storageDate as storageDate',
            'cu.loadingDate as loadingDate',
            'cu.expireDate as expiringDate',
            'vid.nameBahasa as name'
        )
        ->join('code_usages as cu', 'cu.codeId', '=', 'c.id')
        ->join('view_item_details as vid', 'c.itemId', '=', 'vid.itemId')
        ->where('cu.storageDate', '=', $request->tanggal)
        ->where('c.itemId', '=', $request->itemId)
        ->orderBy('cu.fullcode', 'asc')
        ->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '           
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Something something">
            <i class="fas fa-list"></i>
            </button>            
            ';
            return $html;
        })
        ->rawColumns(['action'])
        ->toJson();
    }

    public function getScanKeluarBarcodeList(Request $request){

        //dd($request);
        $query =  DB::table('codes as c')
        ->select(
            'c.id as id',
            'cu.fullcode as fullcode',
            DB::raw('(CASE WHEN 
                cu.status ="0" THEN "Created"
                WHEN cu.status ="1" then "Stored" 
                WHEN cu.status ="2" then "Loaded" 
                WHEN cu.status ="3" then "Hilang" 
                WHEN cu.status ="4" then "Dihapus" 
                END) AS status'),
            'c.productionDate as productionDate',
            'cu.packagingDate as packagingDate',
            'cu.storageDate as storageDate',
            't.loadingDate as loadingDate',
            'cu.expireDate as expiringDate',
            'vid.nameBahasa as name'
        )
        ->join('code_usages as cu', 'cu.codeId', '=', 'c.id')
        ->join('transactions as t', 't.id', '=', 'cu.transactionId')
        ->join('view_item_details as vid', 'c.itemId', '=', 'vid.itemId')
        ->where('cu.transactionId', '=', $request->transactionId)
        ->where('t.loadingDate', '=', $request->tanggal)
        ->where('c.itemId', '=', $request->itemId)
        ->orderBy('cu.fullcode', 'asc')
        ->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '           
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Something something">
            <i class="fas fa-list"></i>
            </button>            
            ';
            return $html;
        })
        ->rawColumns(['action'])
        ->toJson();
    }

    public function updateHapusBarcode(Request $request)
    {        
        $barcode = DB::table('code_usages as cu')
        ->join('codes as c', 'c.id', '=', 'cu.codeId')
        ->select(
            'cu.id as id',
            'c.itemId as itemId',
            'cu.fullcode as barcode',
            'cu.status as status'
        )
        ->where('cu.id', $request->barcodeId)
        ->first();

        //update data barang
        //update history perubahan barang
        if ($barcode->status == 1){
            DB::table('items')
            ->where('id', $request->itemId)
            ->decrement('amount', 1);
            $data = [
                'userId'    => auth()->user()->name,
                'jenis'     => 2,
                'informasiTransaksi' => "Hapus barcode ".$barcode->barcode,
                'itemId'    =>  $request->itemId,
                'amount'    =>  1             
            ];
            DB::table('stock_histories')->insert($data);
        }

        $query = DB::table('code_usages as cu')
        ->where('cu.id', $request->barcodeId)
        ->update(['cu.status' => 4]);
        return true;        

    }


    public function getScanKeluarData(Request $request){
        $query = DB::table('codes as c')
        ->select(
            't.loadingDate as loadingDate',
            'cu.transactionId as transactionId',
            't.transactionNum as transactionNum',
            't.pinum as pinum',
            'com.name as companyName',
            DB::raw('count(cu.id) as jumlahBarcode'),
        )
        ->join('code_usages as cu', 'c.id', '=', 'cu.codeId')
        ->join('transactions as t', 't.id', '=', 'cu.transactionId')
        ->join('companies as com', 'com.id', '=', 't.companyId')
        ->where('cu.status','=', 2)
        ->whereBetween('cu.loadingDate', [$request->start, $request->end])
        ->whereNotNull('cu.loadingDate')
        ->groupBy('cu.transactionId')
        ->orderBy('cu.loadingDate')->get();

        return datatables()->of($query)
        ->addColumn('number', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-4">PI</span>
            <span class="col-8 text-end">'.$row->pinum.'</span>
            </div>
            <div class="row form-group">
            <span class="col-4">INV</span>
            <span class="col-8 text-end">'.$row->transactionNum.'</span>
            </div>';
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html = '           
            <button  data-rowid="'.$row->loadingDate.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar barang" onclick="barcodeItemList('."'".$row->transactionId."'".', '."'".$row->loadingDate."'".')">
            <i class="fas fa-list"></i>
            </button>            
            ';
            return $html;
        })
        ->rawColumns(['action', 'number'])
        ->toJson();
    }

}
