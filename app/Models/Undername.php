<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use DB;
class Undername extends Model
{
    use HasFactory;


    public function getAllUndernameTransactionData(Request $request){
        $start=$request->start;
        $end=$request->end;
        $query = DB::table('undernames as u')
        ->select(
            'u.id as id', 
            'u.transactionnum as invnum', 
            'u.pinum as pinum', 
            'c.name as name', 
            'n.name as nation', 
            'u.loadingDate as ld',
            'u.transactionDate as td',
            'u.departureDate as etd',
            'u.arrivalDate as eta',
            'u.pebFile as peb',
            'u.status as stat',
            DB::raw('(CASE WHEN u.status ="0" THEN "New Submission"
                WHEN u.status ="1" then "Offering"
                WHEN u.status ="2" then "Finished"
                WHEN u.status ="3" then "Canceled"
                WHEN u.status ="4" then "Sailing"
                END) AS status')
        )
        ->join('companies as c', 'c.id', '=', 'u.companyid')
        ->join('countries as n', 'n.id', '=', 'c.nation')
        ->where('u.jenis', '=', 1)
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
            $query->where('u.status', '=', $request->statusTransaksi);
        }
        $query
        ->orderBy('u.creationDate', 'desc')
        ->orderBy('u.status', 'desc')
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
            <button  data-rowid="'.$row->id.'"  style="width:100px" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Data" onclick="editTransaksi('."'".$row->id."'".')"><i class="fa fa-edit""></i>Edit</button><br>';


            $html =$html.'
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar dokumen" onclick="documentList('."'".$row->id."'".')"><i class="fa fa-file-pdf""></i> Documents</button>
            ';
            /*
            $html =$html.'
            <button  data-rowid="'.$row->id.'"  style="width:100px" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="PI" onclick="cetakPI('."'".$row->id."'".')"><i class="fa fa-file-alt""></i>PI</button>
            <button  data-rowid="'.$row->id.'"  style="width:100px" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="IPL" onclick="cetakIPL('."'".$row->id."'".')"><i class="fa fa-file-invoice-dollar""></i>IPL</button>
            ';
            */
            if (($row->stat == 2) and ($row->peb)) {
                $html = $html.'
                <button class="btn btn-xs btn-light"  style="width:100px" data-toggle="tooltip" data-placement="top" data-container="body" title="Download" onclick="getFileDownload('."'".$row->peb."'".')"><i class="fas fa-file-alt"></i> PEB
                </button>';
            }
            return $html;
        })
        ->rawColumns(['name', 'action', 'tanggal', 'number'])
        ->toJson();
    }
}
