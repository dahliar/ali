<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    public function getOneInvoiceDetail($transactionId){
        $query = DB::table('detail_transactions as dt')
        ->select(
            DB::raw('concat(sp.name,    " ",s.name,         " ",i.name) as goods'),
            DB::raw('concat(dt.amount,  " ",p.shortname)                as quantity'),
            DB::raw('(dt.amount * i.weightbase) as netweight'),
            DB::raw('(dt.amount * (i.weightbase+0.5)) as grossweight'),
            'dt.amount as amount',
            'dt.price as price',

            //DB::raw('(CASE WHEN t.valutaType ="1" THEN "Rp"
                //WHEN t.valutaType ="2" then "USD"
                //WHEN t.valutaType ="3" then "RMB" END) AS valutaType'),

            DB::raw('(dt.price * dt.amount * i.weightbase) as totalPrice')
        )
        ->join('transactions as t', 't.id', '=', 'dt.transactionId')
        ->join('items as i', 'i.id', '=', 'dt.itemId')
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('t.id','=', $transactionId)
        ->get();  


        return $query;
    }


    public function getOnePurchaseDetail($purchaseId){
        $query = DB::table('detail_purchases as dp')
        ->select(
            DB::raw('concat(sp.name,    " ", g.name,    " ", s.name) as goods'),
            DB::raw('concat(sp.nameBahasa,    " ", g.name,    " ", s.name) as goodsBahasa'),
            DB::raw('concat(dp.amount,  " ",p.shortname)                as quantity'),
            'dp.amount as amount',
            'dp.price as price',
            DB::raw('(dp.price * dp.amount) as totalPrice')
        )
        ->join('purchases as pur', 'pur.id', '=', 'dp.purchasesId')
        ->join('items as i', 'i.id', '=', 'dp.itemId')
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('pur.id','=', $purchaseId)
        ->orderBy('sp.name')
        ->orderBy('g.name')
        ->orderByRaw('s.name+0 asc')
        ->get();  


        return $query;
    }
}
