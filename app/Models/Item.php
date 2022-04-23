<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;


class Item extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function getAllItemData($speciesId){
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'sp.nameBahasa as speciesName', 
            'i.amount as jumlahPacked',
            'amountUnpacked as jumlahUnpacked',
            'p.shortname as packingShortname',
            DB::raw('(select sum(dt.amount) from detail_transactions as dt join transactions t on dt.transactionId=t.id where t.status=4 and dt.itemId=i.id) as jumlahOnLoading'),
            'i.name as iname',
            's.name as sname',
            'f.name as fname',
            'g.name as gname',
            'baseprice',
            'weightbase'
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        //->where('dt.status','=', 1)
        ->where('i.isActive','=', 1)
        ->groupBy('i.name')
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderByRaw('s.name+0', 'asc');


        if ($speciesId>0){
            $query->where('sp.id','=', $speciesId);
        }
        $query->get();  

        return datatables()->of($query)
        ->addColumn('stockOnHand', function ($row) {
            $jumlah = number_format(((($row->jumlahPacked) * $row->weightbase) + $row->jumlahUnpacked), 2).' Kg';
            return $jumlah;
        })
        ->addColumn('itemName', function ($row) {
            $name = $row->speciesName." ".$row->gname. " ".$row->sname. " ".$row->fname." ".$row->weightbase." Kg/".$row->packingShortname." - ".$row->iname;
            return $name;
        })
        ->addColumn('amountPacked', function ($row) {
            return number_format($row->jumlahPacked, 2).' '.$row->packingShortname;
        })
        ->addColumn('amountUnpacked', function ($row) {
            return number_format($row->jumlahUnpacked, 2).' Kg';
        })
        ->addColumn('loading', function ($row) {
            return number_format(($row->jumlahOnLoading*$row->weightbase), 2).' Kg';
        })
        ->addColumn('action1', function ($row) {
            $html="";
            if (Auth::user()->accessLevel <=40){
                $html = '
                <button  data-rowid="'.$row->id.'" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah stok barang"><i onclick="tambahStockItem('."'".$row->id."'".')" class="fa fa-plus"></i></button>
                <button onclick="historyStockItem('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="History tambah stock"><i class="far fa-list-alt"></i></button>
                <button onclick="UpdateStockUnpacked('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Update jumlah unpacked"><i class="fa fa-box-open"></i></button>';
            }
            return $html;
        })
        ->addColumn('action2', function ($row) {
            $html="";
            if (Auth::user()->accessLevel <=40){
                $html = '
                <button  data-rowid="'.$row->id.'" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Kurangi stok barang"><i onclick="kurangiStockItem('."'".$row->id."'".')" class="fa fa-minus"></i></button>
                <button onclick="historyStockKurang('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="History kurangi stock"><i class="fas fa-clipboard-list"></i></button>';
            }
            return $html;
        })
        ->rawColumns(['action1', 'action2'])->addIndexColumn()->toJson();
    }

    public function getSpeciesStock(){
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'sp.name as name', 
            DB::raw('sum(i.amount*weightbase) as jumlahPacked'),
            DB::raw('sum(amountUnpacked) as jumlahUnpacked'),
            'p.shortname as packingShortname',
            DB::raw('sum((select sum(dt.amount*weightbase) from detail_transactions as dt join transactions t on dt.transactionId=t.id join items i2 on i2.id=dt.itemId join sizes s2 on s2.id=i2.sizeid join species sp2 on sp2.id=s2.speciesId where t.status=4 and dt.itemId=i.id group by sp2.id)) as jumlahOnLoading')            
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->groupBy('sp.id')
        ->orderByRaw('s.name+0', 'asc');

        return datatables()->of($query)
        ->addColumn('total', function ($row) {
            $jumlah = $row->jumlahPacked + $row->jumlahUnpacked;
            return number_format($jumlah, 2);
        })
        ->editColumn('packed', function ($row) {
            return number_format($row->jumlahPacked, 2);
        })
        ->editColumn('unpacked', function ($row) {
            return number_format($row->jumlahUnpacked, 2);
        })
        ->editColumn('jumlahOnLoading', function ($row) {
            return number_format(($row->jumlahOnLoading), 2);
        })
        ->addIndexColumn()->toJson();
    }

    public function getUnpackedItemHistory($itemId){
        $query = DB::table('unpacked_histories as u')
        ->select('u.id', 
            'i.name as item', 
            's.name as size',
            'g.name as grade',
            'p.name as packing',
            'f.name as freezing',
            'u.createdAt as tanggalPacking',
            'us.name as username',
            DB::raw('concat(u.amountPacked, " ", p.shortname) as amountPacked'),
            DB::raw('concat(u.amountUnpacked, " Kg") as amountUnpacked'),

            //'u.amountPacked as amountPacked',
            //'u.amountUnpacked as amountUnpacked',
            'i.weightbase'
        )
        ->join('users as us', 'us.id', '=', 'u.userId')
        ->join('items as i', 'i.id', '=', 'u.itemId')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->where('i.id','=', $itemId);
        $query->get();  

        return datatables()->of($query)
            /*
            ->addColumn('action', function ($row) {
            $html = '<button type="button"  data-toggle="tooltip" data-placement="top" data-container="body" title="Detail & edit penyimpanan" class="btn btn-primary" onclick="editStoreDetail('."'".$row->id."'".')" data-bs-target="#exampleModal"><i class="fa fa-edit" style="font-size:20px"></i></button>';
            return $html;
            
        })
        */
        ->addIndexColumn()->toJson();    
    }

    public function getItemForSelectOption($transactionId, $purchaseId, $speciesId){
        $query = DB::table('items as i')
        ->select(
            'i.id as itemId', 
            'i.name as itemName', 
            'sp.nameBahasa as speciesName', 
            'sp.name as speciesNameEng', 
            's.name as sizeName',
            'p.shortname as pshortname',
            'g.name as gradeName',
            'p.name as packingName',
            'f.name as freezingName',
            'i.amount as amount'
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('sp.id','=', $speciesId)
        ->where('sp.isActive','=', 1)
        ->where('s.isActive','=', 1)
        ->where('g.isActive','=', 1)
        ->where('f.isActive','=', 1)
        ->where('p.isActive','=', 1)
        ->where('i.isActive','=', 1)
        ->orderBy('g.name', 'desc')
        ->orderByRaw('s.name+0 asc')
        ->orderBy('f.name');

        if($transactionId>0){
            $list = DB::table("detail_transactions")
            ->select('itemId')
            ->where('transactionId', '=', $transactionId)
            ->get()
            ->pluck('itemId');
            $query->whereNotIn('i.id', $list);
        }
        if($purchaseId>0){
            $list = DB::table("detail_purchases")
            ->select('itemId')
            ->where('purchasesId', '=', $purchaseId)
            ->get()
            ->pluck('itemId');
            $query->whereNotIn('i.id', $list);
        }

        return $query->get();  
    }

    public function getOneItem($itemId){
        $query = DB::table('items as i')
        ->select(
            'i.id as itemId', 
            DB::raw('concat(
                sp.nameBahasa," ",
                g.name," ",
                s.name," ",
                p.name," ",
                f.name," ",
                weightbase," Kg/", p.shortname) as itemName'), 
            'amount',
            'amountUnpacked',
            'baseprice',
            'weightbase as wb',
            'p.shortname as packingShortname'
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.id','=', $itemId)
        ->get();    

        return $query->first();
    }
}
