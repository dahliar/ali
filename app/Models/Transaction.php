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

    public function getAllItemData(Request $request){
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
            't.transactiondate as tanggaltransaksi',
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
        ->where(function($query2) use ($start, $end){
            $query2->whereBetween('loadingDate', [$start, $end])
            ->orWhereBetween('transactionDate', [$start, $end])
            ->orWhereBetween('departureDate', [$start, $end])
            ->orWhereBetween('arrivalDate', [$start, $end]);
        })
        ->orderBy('t.id');

        if($request->negara != -1){
            $query->where('n.id', '=', $request->negara);
        }
        if($request->jenis != -1){
            $query->where('t.isUndername', '=', $request->jenis);
        }
        if($request->statusTransaksi != -1){
            $query->where('t.status', '=', $request->statusTransaksi);
        }
        $query->get();  


        return datatables()->of($query)
        ->addColumn('number', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-2">PI</span>
            <span class="col-1">:</span>
            <span class="col-8">'.$row->pinum.'</span>
            </div>
            <div class="row form-group">
            <span class="col-2">INV</span>
            <span class="col-1">:</span>
            <span class="col-8">'.$row->invnum.'</span>
            </div>';
            return $html;
        })
        ->addColumn('tanggal', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-4">Transaksi</span>
            <span class="col-1">:</span>
            <span class="col-6">'.$row->td.'</span>
            </div>

            <div class="row form-group">
            <span class="col-4">Loading</span>
            <span class="col-1">:</span>
            <span class="col-6">'.$row->ld.'</span>
            </div>

            <div class="row form-group">
            <span class="col-4">Departure</span>
            <span class="col-1">:</span>
            <span class="col-6">'.$row->etd.'</span>
            </div>

            <div class="row form-group">
            <span class="col-4">Arrival</span>
            <span class="col-1">:</span>
            <span class="col-6">'.$row->eta.'</span>
            </div>';

            return $html;
        })
        ->addColumn('action', function ($row) {
            $html = '
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Item" onclick="tambahItem('."'".$row->id."'".')">
            <i class="fa fa-plus" style="font-size:20px"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Data" onclick="editTransaksi('."'".$row->id."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>
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

    public function getAllTransactionData($jenis, $negara, $statusTransaksi, $start, $end){

       $query = DB::table('transactions as t')
       ->select(
        't.id as id', 
        't.transactionnum as nosurat', 
        'c.name as name', 
        'n.name as nation', 
        't.departureDate as etd',
        't.arrivalDate as eta',
        't.transactiondate as tanggaltransaksi',
        't.status as status',
        DB::raw('(CASE WHEN t.isundername ="1" THEN "Internal"
            WHEN t.isundername ="2" then "Undername" END) AS undername'),
        DB::raw('(CASE 
            WHEN t.status ="1" then "Offering"
            WHEN t.status ="2" then "Finished"
            WHEN t.status ="3" THEN "Canceled"
            WHEN t.status ="4" THEN "Sailing"
            END) AS status')
    )
       ->whereBetween('transactionDate', [$end, $start])
       ->join('companies as c', 'c.id', '=', 't.companyid')
       ->join('countries as n', 'n.id', '=', 'c.nation');

       if ($jenis!=-1){
        $query->where('t.isundername', $jenis);
    }
    if ($negara!=-1){
        $query->where('n.id', $negara);
    }
    if ($statusTransaksi!=-1){
        $query->where('t.status', $statusTransaksi);
    }
    $query->get();  


    return datatables()->of($query)
    ->addColumn('action', function ($row) {

        $html = '
        <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Item" onclick="tambahItem('."'".$row->id."'".')">
        <i class="fa fa-plus" style="font-size:20px"></i>
        </button>
        <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Transaction Data" onclick="editTransaksi('."'".$row->id."'".')">
        <i class="fa fa-edit" style="font-size:20px"></i>
        </button>
        <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="PI" onclick="cetakPI('."'".$row->id."'".')">PI
        </button>
        <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="IPL" onclick="cetakIPL('."'".$row->id."'".')">IPL
        </button>
        ';
        return $html;
    })->addIndexColumn()->toJson();
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
    public function storeOneTransaction($data){
        //insert into table transactions, untuk setiap penambahan
        $id = DB::table('transactions')->insertGetId($data);
        return $id;
    }
    public function storeNotes($data){
        //insert into table transaction_notes, untuk setiap penambahan transaction
        DB::table('transaction_notes')->insert($data);
    }

    public function updateOneTransaction($data, $transactionId, $curStatus){
        //update into table transactions berdasar $transactionId
        $action = Transaction::where('id', $transactionId)
        ->update($data);

        //Update data barang jika dilakukan pembatalan
        //dari finished atau loading ke cancel
        if ((($curStatus==2) or ($data['status'] == 4)) and ($data['status'] == 3)) {
            $this->transactionCanceled($transactionId);
        }

        //dari penawaran ke finished atau loading
        if (($curStatus==1) and (($curStatus == 2) or ($data['status'] == 4))) {
            $this->transactionLoadedOrFinished($transactionId);
        }
    }

    public function transactionLoadedOrFinished($transactionId){
        $result = DB::table('detail_transactions as dt')
        ->select(
            'dt.itemId as itemId', 
            'dt.amount as amount'
        )
        ->where('transactionId', $transactionId)
        ->get();

        foreach ($result as $itemDetail){
            DB::table('items')
            ->where('id', $itemDetail->itemId)
            ->decrement('amount', $itemDetail->amount);
        }
    }

    public function transactionCanceled($transactionId){
            /*
            1. get all data from detailTransaction where transactionId=$transactionId
            2. Update Item foreach item result from poin1, increment the amount sebesar amount dari poin1
            3. update detailTransaction->status menjadi 0 semua (Tidak aktif)
            */
            
            $result1 = DB::table('detail_transactions as dt')
            ->select(
                'dt.id as id', 
                'dt.itemId as itemId', 
                'dt.transactionId as tranId', 
                'dt.amount as amount', 
            )
            ->where('transactionId', $transactionId)
            ->get();

            foreach ($result1 as $itemDetail){
                DB::table('items')
                ->where('id', $itemDetail->itemId)
                ->increment('amount', $itemDetail->amount);
            }

        }


    }
