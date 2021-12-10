<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;


class DetailTransaction extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function storeOneItemDetail($data){
        //insert into table transactions, untuk setiap penambahan
        DB::table('detail_transactions')->insert($data);


        //update table item, untuk mengurangi jumlah stock
        $affected = DB::table('items')
        ->where('id', $data['itemId'])
        ->decrement('amount', $data['amount']);
    }




    
    public function deleteOneItemDetail($detailTransaction){


        //update table item, untuk mengurangi jumlah stock
        $affected = DB::table('items')
        ->where('id', $detailTransaction->itemId)
        ->increment('amount', $detailTransaction->amount);

        //delete record di tabel detailTransaction dengan id $detailTransaction->id
        DB::table('detail_transactions')->delete($detailTransaction->id);
        return true;
    }
    public function getAllDetail($transactionId){
        $query = DB::table('detail_transactions as dt')
        ->select(
            'dt.id as id', 
            'dt.transactionId as transactionId', 
            'i.name as itemName', 
            'f.name as freezingName', 
            'g.name as gradeName', 
            'p.name as packingName', 
            's.name as sizeName', 
            't.status as status', 
            DB::raw(
                'concat(dt.amount, " ",p.shortname) 
                as amount'),
            DB::raw('
                concat((dt.amount * i.weightbase)," Kg") as weight'),
            'dt.price as price',
        )
        ->join('transactions as t', 't.id', '=', 'dt.transactionId')
        ->join('items as i', 'i.id', '=', 'dt.itemId')
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('t.id','=', $transactionId)
        ->orderBy('sp.name');
        $query->get();  


        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Detail" onclick="deleteItem('."'".$row->id."'".')">
            <i class="fa fa-trash" style="font-size:20px"></i>
            </button>
            ';

            if ($row->status == '1') return $html;
            if ($row->status != '2') return ;

            //return $html;
        })->addIndexColumn()->toJson();
    }
}
