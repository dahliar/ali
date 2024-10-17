<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


use App\Models\Species;
use App\Models\Companies;
use App\Models\Company;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

use DB;
use Barryvdh\DomPDF\Facade\Pdf;


class BarcodeController extends Controller
{
    public function __construct(){
        $this->dns1d = new DNS1D();
        $this->dns2d = new DNS2D();
    }
    public function create()
    {

        $companies = Company::orderBy('name')->where('isActive','1')->whereNotNull('shortname')->get();
        $species = Species::orderBy('name')->get();
        return view('barcode.barcodeAdd', compact('species', 'companies'));
    }

    public function barcodeList()
    {
        $species = Species::orderBy('name')->get();
        return view('barcode.barcodeList', compact('species'));
    }

    public function getAllBarcodes($speciesId, $itemId){
        $query = DB::table('codes as c')
        ->select(
            'c.id as id',
            DB::raw('concat(c.id, "-", c.itemId) as identifier'),
            'c.productionDate as productionDate',
            'c.amountPrinted as amountPrinted',
            'c.created_at as created',
            'c.filename as filename',
            'c.printer as printer',
            'c.startFrom as startFrom',
            'vid.nameBahasa as name'
        )
        ->join('view_item_details as vid', 'c.itemId', '=', 'vid.itemId')
        ->orderBy('c.created_at', 'desc');

        if ($speciesId!=0){
            $query = $query->where('vid.speciesId','=', $speciesId);
            if($itemId!=0){
                $query = $query->where('c.itemId','=', $itemId);
            }
        }

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Tampilkan file" onclick="getFileDownload('."'".$row->filename."'".')">
            <i class="fa fa-file"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Delete Barcode dan File" onclick="deleteBarcode('."'".$row->id."'".')">
            <i class="fas fa-trash"></i>
            </button>';
            return $html;
        })
        ->editColumn('printer', function ($row) {
            $html="";
            if ($row->printer == 1){
                $html = "Postek";
            } else {
                $html = "Zebra ZT411CN";
            }
            return $html;
        })
        ->addIndexColumn()->toJson();
    }

    public function getBarcodeFileDownload($filename){
        $filepath = storage_path('/app/barcodes/'. $filename);
        $headers = ['Content-Type: application/pdf'];
        return \Response::download($filepath, $filename, $headers);
    }
    public function deleteBarcode($id){
        $check = DB::table('codes as c')
        ->where('c.id', '=', $id);

        if($check->count() > 0){
            $filename = $check->select('filename')->first()->filename;
            if (File::exists(storage_path('app/barcodes/'. $filename))){
                unlink(storage_path('app/barcodes/'.$filename));
            } 
            $deleted = DB::table('codes')->where('id', '=', $id)->delete();
            return redirect('barcodeList')
            ->with('status','Barcode sudah dihapus.');

        } else{
            return redirect('barcodeList')
            ->with('status','Barcode tidak ditemukan.');
        }
    }

    public function itemList($speciesId){
        $query = DB::table('view_item_details as vid')
        ->select(   
            'vid.itemId as itemId', 
            'vid.nameBahasa as itemName'
        )
        ->where('vid.speciesId','=', $speciesId)
        ->where('vid.itemStatus', '=', 1)
        ->orderBy('vid.shapesName', 'asc')
        ->orderBy('vid.gradeName', 'asc')
        ->orderBy('vid.sizeName', 'asc')
        ->orderBy('vid.freezingName')
        ->orderBy('vid.weightbase')
        ->get();

        return $query;  
    }
    public function generate(Request $request){

        $request->validate(
            [
                'species' => 'required|gt:0',
                'item' => 'required|gt:0',
                'transactionDate' => 'required|date|before_or_equal:today',
                'jumlahBarcode' => 'required|gt:0|lte:100',
                'printer' => 'required|gt:0',
                'company' => 'required|gt:0',
            ],
            [
                'jumlahBarcode.required'=> 'Jumlah barcode minimal 1'
            ]
        );
        $transactionDate = $request->transactionDate;
        $jumlah = $request->jumlahBarcode;
        $item = $request->item;
        $printer = $request->printer;

        $name = DB::table('view_item_details as vid')
        ->select(DB::raw('concat(speciesName, " ", gradeName, " ", sizeName, " ", shapesName) as name'))
        ->where('vid.itemStatus', '=', 1)
        ->where('vid.itemId','=', $request->item)
        ->first()->name;

        $date = \Carbon\Carbon::parse($transactionDate);
        $productionDateData = str_pad($date->year, 4, '0', STR_PAD_LEFT).
        str_pad($date->month, 2, '0', STR_PAD_LEFT).
        str_pad($date->day, 2, '0', STR_PAD_LEFT).
        str_pad($request->company, 4, '0', STR_PAD_LEFT).
        str_pad($item, 5, '0', STR_PAD_LEFT);

        $max = DB::table('codes')
        ->where('productionDate', $transactionDate)
        ->where('companyId', $request->company)
        ->where('itemId', $item)
        ->sum('amountPrinted');

        $companyShortname = Companies::where('id', $request->company)->first()->shortname;
        $name = $companyShortname." ".$name;

        $timeFormat = \Carbon\Carbon::now()->format('YmdHis');

        $filename = 'Barcode '.$item.' '.$timeFormat.'.pdf';
        $filepath = '../storage/app/barcodes/'.$filename;
        $startFrom = 1;
        if (DB::table('codes')->where('productionDate', $transactionDate)->where('itemId', $item)->exists()){
            $startFrom = $max + 1; 
        }

        $codes = [
            'productionDate'    => $transactionDate,
            'amountPrinted'     => $jumlah,
            'startFrom'         => $startFrom,
            'itemId'            => $item,
            'filename'          => $filename,
            'printer'           => $printer,
            'companyId'         => $request->company
        ];
        $codeId = DB::table('codes')->insertGetId($codes);            


        $arrData = array();
        $expireDate = \Carbon\Carbon::parse($transactionDate)->addYears(2);
        $supplier = str_pad($request->company, 6, '0', STR_PAD_LEFT);

        for ($a=$startFrom; $a<($startFrom+$jumlah); $a++){
            $barcode = $productionDateData.str_pad($a, 4, '0', STR_PAD_LEFT);

            $data = [
                "barcode" => $barcode, 
                "fullname" => url('/productChecking/'.$barcode).' - '.$name.' '.$barcode.' '.$supplier
            ];

            DB::table('code_usages')->insert([
                'codeId' => $codeId,
                'fullcode' => $barcode,
                'packagingDate' => $transactionDate,
                'expireDate' => $expireDate
            ]);     


            $arrData[$a] = $data;
        }

        if ($printer == 1){
            $customPaper = array(0,0,300.00,500.00);
            $pdf = PDF::loadview('barcode.barcodeFilePostek', compact('arrData','jumlah', 'startFrom', 'printer', 'name'))->setPaper($customPaper, 'landscape');
            $pdf->save($filepath);

        } else
        {
            $pdf = PDF::loadview('barcode.barcodeFileZebra', compact('arrData','jumlah', 'startFrom', 'printer', 'name'));
            $pdf->save($filepath);
        }

        return redirect('barcodeList')
        ->with('status','Barcode berhasil dibuat.');
    }

    public function productChecking($id){
        $query = DB::table('codes as c')
        ->select(
            'productionDate as productionDate',
            'packagingDate as packagingDate',
            'loadingDate as loadingDate',
            'expireDate as expireDate',
            'comp.name as companyName',
            'c.id as id',
            'cu.fullcode as fullcode',
            'i.name as item',
            's.name as size',
            'sh.name as shape',
            'f.name as freezing',
            'g.name as grade',
            'sp.name as species',
        )
        ->join('companies as comp', 'comp.id', '=', 'c.companyId')
        ->join('items as i', 'i.id', '=', 'c.itemId')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('shapes as sh', 'i.shapeId', '=', 'sh.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')

        ->join('code_usages as cu', 'c.id', '=', 'cu.codeId')
        ->where('cu.fullcode','=', $id);
        $product=0;
        $found=0;
        if($query->exists()){
            $product = $query->first();
            $found=1;
        }

        return view('barcode.productChecking', compact('product', 'found', 'id'));
    }

}
