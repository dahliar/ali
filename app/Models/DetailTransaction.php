<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;


class DetailTransaction extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    
    public function deleteOneItemDetail($detailTransaction){
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
            'p.shortname as pshortname',
            's.name as sizeName', 
            'sp.name as speciesName', 
            't.status as status', 
            DB::raw('(CASE   WHEN t.valutaType="1" THEN "Rp. " 
                WHEN t.valutaType="2" THEN "USD. " 
                WHEN t.valutaType="3" THEN "Rmb. " 
                END) as valuta'
            ), 
            'dt.amount as amount',
            'i.weightbase as wb',
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
        ->orderBy('sp.name')
        ->orderBy('g.name', 'desc')
        ->orderByRaw('s.name+0 asc')
        ->orderBy('f.name');

        $query->get();  


        return datatables()->of($query)
        ->editColumn('itemName', function ($row) {
            return ($row->speciesName.' '.$row->gradeName.' '.$row->sizeName.' '.$row->freezingName.' '.$row->packingName.' ['.$row->itemName.']');
        })
        ->addColumn('weight', function ($row) {
            $html = number_format(($row->amount * $row->wb), 2, ',', '.').' Kg';
            return $html;
        })
        ->editColumn('amount', function ($row) {
            $html = number_format($row->amount, 2, ',', '.').' '.$row->pshortname;
            return $html;
        })
        ->editColumn('price', function ($row) {
            $html = $row->valuta.' '.number_format($row->price, 2, ',', '.').' /Kg';
            return $html;
        })
        ->editColumn('harga', function ($row) {
            $html = $row->valuta.' '.number_format(($row->price * $row->amount * $row->wb), 2, ',', '.');
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Detail" onclick="deleteItem('."'".$row->id."'".')">
            <i class="fa fa-trash" style="font-size:20px"></i>
            </button>
            ';

            if ($row->status == '1') return $html;
            if ($row->status != '2') return ;

            //return $html;
        })
        ->addIndexColumn()->toJson();
    }
}
