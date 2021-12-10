<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stock;
use DB;


class Transaction extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function getAllItemData(){
        $query = DB::table('transactions as t')
        ->select(
            't.id as id', 
            't.transactionnum as nosurat', 
            'c.name as name', 
            'c.nation as nation', 
            't.departureDate as etd',
            't.arrivalDate as eta',
            't.transactiondate as tanggaltransaksi',
            't.status as status',
            DB::raw('(CASE WHEN t.status ="0" THEN "New Submission"
                WHEN t.status ="1" then "On Progress"
                WHEN t.status ="2" then "Finished"
                ELSE "Cancelled" END) AS status')
        )
        ->join('companies as c', 'c.id', '=', 't.companyid');
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

    public function storeOneTransaction($data){
        //insert into table transactions, untuk setiap penambahan
        $id = DB::table('transactions')->insertGetId($data);
        return $id;
    }
    public function storeNotes($data){
        //insert into table transaction_notes, untuk setiap penambahan transaction
        DB::table('transaction_notes')->insert($data);
    }

    public function updateOneTransaction($data, $transactionId){
        //update into table transactions berdasar $transactionId
        $action = Transaction::where('id', $transactionId)
        ->update($data);

        /*
        update ke canceled, dan kembalikan seluruh stock yang sudah dimasukkan         
        */

        if ($data['status'] == 3){
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
            DB::table('detail_transactions')
            ->where('transactionId', $itemDetail->tranId)
            ->update(['status' => 0]);

        }
    }
}
