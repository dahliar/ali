<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\InvoiceController;
use App\Models\Stock;
use Illuminate\Http\Request;

use DB;
use Illuminate\Support\Facades\Storage;


class Transaction extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function getAllExportTransactionData(Request $request){
        $start=$request->start;
        $end=$request->end;
        $query = DB::table('transactions as t')
        ->select(
            't.id as id', 
            't.transactionnum as invnum', 
            't.pinum as pinum', 
            'c.name as name', 
            'n.name as nation', 
            't.loadingDate as ld',
            't.transactionDate as td',
            't.departureDate as etd',
            't.arrivalDate as eta',
            't.pebFile as peb',
            't.status as stat',
            DB::raw('(CASE WHEN t.isundername ="1" THEN "Internal"
                WHEN t.isundername ="2" then "Undername" END) AS undername'),
            DB::raw('(CASE WHEN t.status ="0" THEN "New Submission"
                WHEN t.status ="1" then "Offering"
                WHEN t.status ="2" then "Finished"
                WHEN t.status ="3" then "Canceled"
                WHEN t.status ="4" then "Sailing"
                END) AS status')
        )
        ->join('companies as c', 'c.id', '=', 't.companyid')
        ->join('countries as n', 'n.id', '=', 'c.nation')
        ->where('t.jenis', '=', 1)
        ->where(function($query2) use ($start, $end){
            $query2->whereBetween('loadingDate', [$start, $end])
            ->orWhereBetween('transactionDate', [$start, $end])
            ->orWhereBetween('departureDate', [$start, $end])
            ->orWhereBetween('arrivalDate', [$start, $end]);
        });
        if($request->negara != -1){
            $query->where('n.id', '=', $request->negara);
        }
        if($request->statusTransaksi != -1){
            $query->where('t.status', '=', $request->statusTransaksi);
        }
        $query
        ->orderBy('t.creationDate', 'desc')
        ->orderBy('t.status', 'desc')
        ->get();  

        return datatables()->of($query)
        ->editColumn('name', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-12 text-left">'.$row->name." - ".$row->nation.'</span>
            </div>
            ';
            return $html;
        })
        ->addColumn('number', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-4">PI</span>
            <span class="col-8 text-end">'.$row->pinum.'</span>
            </div>
            <div class="row form-group">
            <span class="col-4">INV</span>
            <span class="col-8 text-end">'.$row->invnum.'</span>
            </div>
            <div class="row form-group">
            <span class="col-4">Status</span>
            <span class="col-8 text-end">'.$row->status.'</span>
            </div>
            ';
            return $html;
        })
        ->addColumn('tanggal', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-5">Transaksi</span>
            <span class="col-7 text-end">'.$row->td.'</span>
            </div>

            <div class="row form-group">
            <span class="col-5">Loading</span>
            <span class="col-7 text-end">'.$row->ld.'</span>
            </div>
            <div class="row form-group">
            <span class="col-5">Departure</span>
            <span class="col-7 text-end">'.$row->etd.'</span>
            </div>

            <div class="row form-group">
            <span class="col-5">Arrival</span>
            <span class="col-7 text-end">'.$row->eta.'</span>
            </div>';

            return $html;
        })
        ->addColumn('action', function ($row) {
            $html ='<button data-rowid="'.$row->id.'" style="width:100px" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Item" onclick="tambahItem('."'".$row->id."'".')"><i class="fa fa-plus""></i> Items</button> 
            <button  data-rowid="'.$row->id.'"  style="width:100px" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Data" onclick="editTransaksi('."'".$row->id."'".')"><i class="fa fa-edit""></i>Edit</button>';
            $html =$html.'
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar dokumen" onclick="documentList('."'".$row->id."'".')"><i class="fa fa-file-pdf""></i> Documents</button>
            ';


            if (($row->stat == 2) and ($row->peb)) {
                $html = $html.'
                <button class="btn btn-xs btn-light"  style="width:100px" data-toggle="tooltip" data-placement="top" data-container="body" title="Download" onclick="getFileDownload('."'".$row->peb."'".')"><i class="fas fa-file-alt"></i> PEB
                </button>';
            }
            return $html;
        })
        ->rawColumns(['name','action', 'tanggal', 'number'])
        ->toJson();
    }


    public function getAllExportTransactionDataToRevoke(Request $request){
        $start=$request->start;
        $end=$request->end;
        $query = DB::table('transactions as t')
        ->select(
            't.id as id', 
            't.transactionnum as invnum', 
            't.pinum as pinum', 
            'c.name as name', 
            'n.name as nation', 
            't.loadingDate as ld',
            't.transactionDate as td',
            't.departureDate as etd',
            't.arrivalDate as eta',
            't.pebFile as peb',
            't.status as stat',
            DB::raw('(CASE WHEN t.isundername ="1" THEN "Internal"
                WHEN t.isundername ="2" then "Undername" END) AS undername'),
            DB::raw('(CASE WHEN t.status ="0" THEN "New Submission"
                WHEN t.status ="1" then "Offering"
                WHEN t.status ="2" then "Finished"
                WHEN t.status ="3" then "Canceled"
                WHEN t.status ="4" then "Sailing"
                END) AS status')
        )
        ->join('companies as c', 'c.id', '=', 't.companyid')
        ->join('countries as n', 'n.id', '=', 'c.nation')
        ->where('t.jenis', '=', 1)
        ->where(function($query2) use ($start, $end){
            $query2->whereBetween('loadingDate', [$start, $end])
            ->orWhereBetween('transactionDate', [$start, $end])
            ->orWhereBetween('departureDate', [$start, $end])
            ->orWhereBetween('arrivalDate', [$start, $end]);
        });
        if($request->negara != -1){
            $query->where('n.id', '=', $request->negara);
        }
        if($request->statusTransaksi != -1){
            $query->where('t.status', '=', $request->statusTransaksi);
        }
        $query
        ->orderBy('t.creationDate', 'desc')
        ->orderBy('t.status', 'desc')
        ->get();  

        return datatables()->of($query)
        ->editColumn('name', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-12 text-left">'.$row->name." - ".$row->nation.'</span>
            </div>
            ';
            return $html;
        })
        ->addColumn('number', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-4">PI</span>
            <span class="col-8 text-end">'.$row->pinum.'</span>
            </div>
            <div class="row form-group">
            <span class="col-4">INV</span>
            <span class="col-8 text-end">'.$row->invnum.'</span>
            </div>
            <div class="row form-group">
            <span class="col-4">Status</span>
            <span class="col-8 text-end">'.$row->status.'</span>
            </div>
            ';
            return $html;
        })
        ->addColumn('tanggal', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-5">Transaksi</span>
            <span class="col-7 text-end">'.$row->td.'</span>
            </div>

            <div class="row form-group">
            <span class="col-5">Loading</span>
            <span class="col-7 text-end">'.$row->ld.'</span>
            </div>
            <div class="row form-group">
            <span class="col-5">Departure</span>
            <span class="col-7 text-end">'.$row->etd.'</span>
            </div>

            <div class="row form-group">
            <span class="col-5">Arrival</span>
            <span class="col-7 text-end">'.$row->eta.'</span>
            </div>';

            return $html;
        })
        ->addColumn('action', function ($row) {
            $html ='<button data-rowid="'.$row->id.'" style="width:200px" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Item" onclick="BackToOffering('."'".$row->id."'".')"><i class="fa fa-redo""></i> Reset to Offering</button>';
            return $html;
        })
        ->rawColumns(['name','action', 'tanggal', 'number'])
        ->toJson();
    }

    public function getAllTransactionDocuments(Request $request){
        $query = DB::table('documents as d')
        ->select(
            't.id as id', 
            't.transactionnum as invnum', 
            't.pinum as pinum', 
            'u.name as name', 
            'd.created_at as tanggal',
            'd.filepath as filepath',
            DB::raw('(CASE 
                WHEN dn.bagian ="PI-ALI" THEN "PI"
                WHEN dn.bagian ="PI-ALS" THEN "PI"
                WHEN dn.bagian ="INV-ALI" then "Invoice" 
                WHEN dn.bagian ="INV-ALS" then "Invoice" 
                WHEN dn.bagian ="LINV-ALS" then "Invoice" 
                END) AS jenis')
        )
        ->join('document_numbers as dn', 'dn.id', '=', 'd.document_numbers_id')
        ->join('transactions as t', 't.id', '=', 'dn.transactionId')
        ->join('users as u', 'u.id', '=', 'd.userId')
        ->where('t.id', '=', $request->transactionId)
        ->orderBy('d.created_at', 'desc')
        ->get();  


        return datatables()->of($query)
        ->addColumn('documentNo', function ($row) {
            $html="";
            if ($row->jenis == "PI")
                $html = $row->pinum;
            if ($row->jenis == "Invoice")
                $html = $row->invnum;
            return $html;
        })
        //return Storage::download('file.jpg');

        ->addColumn('action', function ($row) {
            $html = '    
            <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Download" onclick="getFileDownload('."'".$row->filepath."'".')">Download
            </button>
            ';

            return $html;
        })
        ->rawColumns(['action', 'tanggal'])
        ->addIndexColumn()->toJson();
    }

    public function getAllLocalTransactionData(Request $request){
        $start=$request->start;
        $end=$request->end;
        $query = DB::table('transactions as t')
        ->select(
            't.id as id', 
            't.transactionnum as invnum', 
            't.pinum as pinum', 
            'c.name as name', 
            't.loadingDate as ld',
            't.transactionDate as td',
            't.departureDate as etd',
            't.arrivalDate as eta',
            DB::raw('(CASE
                WHEN t.status ="1" then "Transaksi baru"
                WHEN t.status ="2" then "Selesai"
                WHEN t.status ="3" then "Batal"
                WHEN t.status ="4" then "Dalam perjalanan"
                END) AS status')
        )
        ->join('companies as c', 'c.id', '=', 't.companyid')
        ->join('countries as n', 'n.id', '=', 'c.nation')
        ->where('t.jenis', '=', 2)
        ->where(function($query2) use ($start, $end){
            $query2->whereBetween('loadingDate', [$start, $end])
            ->orWhereBetween('transactionDate', [$start, $end])
            ->orWhereBetween('departureDate', [$start, $end])
            ->orWhereBetween('arrivalDate', [$start, $end]);
        })
        ->orderBy('t.transactionnum');

        if($request->statusTransaksi != -1){
            $query->where('t.status', '=', $request->statusTransaksi);
        }
        $query->get();  


        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Item" onclick="tambahItem('."'".$row->id."'".')">
            <i class="fa fa-plus""></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Data" onclick="editTransaksi('."'".$row->id."'".')">
            <i class="fa fa-edit""></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar dokumen" onclick="documentList('."'".$row->id."'".')"><i class="fa fa-file-pdf""></i> Documents
            </button><br>';
            return $html;
        })
        ->rawColumns(['action', 'tanggal', 'number'])
        ->addIndexColumn()->toJson();
    }


}
