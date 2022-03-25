<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\InvoiceController;
use App\Models\Stock;
use Illuminate\Http\Request;

use DB;


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
        if($request->jenis != -1){
            $query->where('t.isUndername', '=', $request->jenis);
        }
        if($request->statusTransaksi != -1){
            $query->where('t.status', '=', $request->statusTransaksi);
        }
        $query
        ->orderBy('t.creationDate', 'desc')
        ->orderBy('t.status', 'desc')
        ->get();  


        return datatables()->of($query)
        ->addColumn('number', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-2">PI</span>
            <span class="col-10 text-end">'.$row->pinum.'</span>
            </div>
            <div class="row form-group">
            <span class="col-2">INV</span>
            <span class="col-10 text-end">'.$row->invnum.'</span>
            </div>';
            return $html;
        })
        ->addColumn('tanggal', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-4">Transaksi</span>
            <span class="col-8 text-end">'.$row->td.'</span>
            </div>

            <div class="row form-group">
            <span class="col-4">Loading</span>
            <span class="col-8 text-end">'.$row->ld.'</span>
            </div>
            <div class="row form-group">
            <span class="col-4">Departure</span>
            <span class="col-8 text-end">'.$row->etd.'</span>
            </div>

            <div class="row form-group">
            <span class="col-4">Arrival</span>
            <span class="col-8 text-end">'.$row->eta.'</span>
            </div>';

            return $html;
        })
        ->addColumn('action', function ($row) {
            $html = '
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Item" onclick="tambahItem('."'".$row->id."'".')">
            <i class="fa fa-plus""></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Data" onclick="editTransaksi('."'".$row->id."'".')">
            <i class="fa fa-edit""></i>
            </button>
            <br>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="PI" onclick="cetakPI('."'".$row->id."'".')">PI
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="IPL" onclick="cetakIPL('."'".$row->id."'".')">IPL
            </button>
            ';
            return $html;
        })
        ->rawColumns(['action', 'tanggal', 'number'])
        ->toJson();
    }

    public function whenUndernameIsTrue($transactionId){
        /****
         * ketika isundername=2
         * 1. create transaction Number
         * 2. set status transaksi jadi finished status=>2
         * 
        */
        $this->inv = new InvoiceController();
        $tnum = $this->inv->createtransactionnum($transactionId);

        $affected = DB::table('transactions')
        ->where('id', $transactionId)
        ->update([
            'status' => 2,
            'transactionNum' => $tnum
        ]);


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
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Invoice" onclick="cetakIPL('."'".$row->id."'".')">Invoice
            </button>
            ';
            return $html;
        })
        ->rawColumns(['action', 'tanggal', 'number'])
        ->addIndexColumn()->toJson();
    }


}
