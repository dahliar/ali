<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Species;

use DB;
use Carbon\Carbon;


class StockController extends Controller
{
    //
    public function index()
    {
        $species = Species::orderBy('name')->get();
        return view('stock.stocklist', compact('species'));
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
                DB::table('code_usages as cu')
                ->where('cu.fullcode', '=', $barcode)
                ->where('cu.status', '=', 0)
                ->update(['cu.status' => 1, 'cu.storageDate' => $tanggal]);

                array_push($arr,$barcode);
            }
        }

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

    public function storeKeluar(Request $request)
    {
        $tanggal = Carbon::now()->toDateString();
        $arr = array();
        if (!empty($request->barcode)){
            foreach ($request->barcode as $barcode){            
                DB::table('code_usages as cu')
                ->where('cu.fullcode', '=', $barcode)
                ->where('cu.status', '=', 1)
                ->update(['cu.status' => 2, 'cu.loadingDate' => $tanggal]);

                array_push($arr,$barcode);
            }
        }

        /*
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

            $tran->stockChangeLog(2, "Scan barcode masuk barang ".$item->itemId." tanggal ".$tanggal, $item->itemId, $item->jumlah);
        }
        */

        $species = Species::orderBy('name')->get();
        return redirect('scanList')
        ->with('species',$species)   
        ->with('status',"Data disimpan");   
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
        $query = DB::table('codes as c')
        ->select(
            'c.id as id', 
            'c.productionDate as productionDate', 
            'c.itemId as itemId', 
            'cu.fullcode as fullcode',
            DB::raw('(CASE WHEN cu.status ="0" THEN "Created"
                WHEN cu.status ="1" then "Stored" WHEN cu.status ="2" then "Loaded" END) AS status'),
            'cu.packagingDate as packagingDate', 
            'cu.storageDate as storageDate', 
            'cu.loadingDate as loadingDate'
        )
        ->join('code_usages as cu', 'c.id', '=', 'cu.codeId')
        ->where('c.itemId', '=', $request->itemId)
        ->where('c.itemId', '=', $request->itemId)
        ->whereBetween('c.productionDate', [$request->start, $request->end]);

        if ($request->status >= 0){
            $query=$query->where('cu.status', '=', $request->status);
        }

        $query->orderBy('cu.fullcode')->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '           
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit data barcode" onclick="editBarcode('."'".$row->id."'".')">
            <i class="fas fa-edit"></i>
            </button>            
            ';
            return $html;
        })
        ->rawColumns(['action'])
        ->toJson();
    }



    /*
    *   Keluar Barang
    *   Keluar Barang
    *   Keluar Barang
    *   Keluar Barang
    */

    public function createKeluar()
    {
        return view('stock.stockKeluarAdd');
    }

}
