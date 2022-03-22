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
            'sp.name as speciesName', 
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
        ->addColumn('wb', function ($row) {
            return number_format($row->weightbase, 1). " Kg/". $row->packingShortname;
        })
        ->addColumn('stockOnHand', function ($row) {
            $jumlah = number_format(((($row->jumlahPacked) * $row->weightbase) + $row->jumlahUnpacked), 2).' Kg';
            return $jumlah;
        })
        ->addColumn('itemName', function ($row) {
            $name = $row->speciesName." ".$row->gname. " ".$row->sname. " ".$row->fname." ".$row->iname;
            return $name;
        })
        ->addColumn('amountPacked', function ($row) {
            return number_format($row->jumlahPacked, 2).' '.$row->packingShortname;
        })
        ->addColumn('amountUnpacked', function ($row) {
            return number_format($row->jumlahUnpacked, 2).' Kg';
        })
        ->addColumn('loading', function ($row) {
            return number_format($row->jumlahOnLoading, 2).' '.$row->packingShortname;
        })
        ->addColumn('action', function ($row) {
            $html="";
            if (Auth::user()->isAdmin() or Auth::user()->isProduction()){
                $html .= '<button  data-rowid="'.$row->id.'" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah stok barang">
                <i onclick="tambahStockItem('."'".$row->id."'".')" class="fa fa-plus"></i>
                </button>';
                $html .= '
                <button onclick="UpdateStockUnpacked('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Update jumlah unpacked">
                <i class="fa fa-box-open"></i>
                </button>
                ';
                $html .= '
                <button onclick="historyStockItem('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Stock History">
                <i class="far fa-list-alt"></i>
                </button>';
                $html .= '
                <button onclick="unpackedHistory('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Unpacked Stock History"><i class="fas fa-list-alt"></i>
                </button>';
            }

            return $html;
        })->addIndexColumn()->toJson();
    }

    public function getSpeciesStock(){
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'sp.name as name', 
            DB::raw('sum(i.amount * i.weightbase) as jumlahPacked'),
            'amountUnpacked as jumlahUnpacked',
            DB::raw('(select (sum(dt.amount) * i.weightbase) from detail_transactions as dt join transactions t on dt.transactionId=t.id where t.status=4 and dt.itemId=i.id) as jumlahOnLoading'),
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('i.isActive','=', 1)
        ->where('s.isActive','=', 1)
        ->groupBy('sp.id')
        ->orderBy('sp.name')
        ->get();  

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
            return number_format($row->jumlahOnLoading, 2);
        })
        ->addColumn('action', function ($row) {
            $html="";

            return $html;
        })->addIndexColumn()->toJson();
    }


    public function getItemHistory($itemId){
        $query = DB::table('stores as str')
        ->select('str.id', 
            'i.name as item', 
            's.name as size',
            'g.name as grade',
            'p.name as packing',
            'f.name as freezing',
            'str.datePackage as datePackage',
            'str.dateProcess as dateProcess',
            'str.dateInsert as dateInsert',
            'us.name as username',
            'str.amountPacked as amountPacked',
            'str.amountUnpacked as amountUnpacked',
            'i.weightbase'
        )
        ->join('users as us', 'us.id', '=', 'str.userId')
        ->join('items as i', 'i.id', '=', 'str.itemId')
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

    public function getItemForSelectOption($speciesId, $transactionId){
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
        ->orderBy('g.name')
        ->orderByRaw('s.name+0 asc');


        $list = DB::table("detail_transactions")
        ->select('itemId')
        ->where('transactionId', '=', $transactionId)
        ->get()
        ->pluck('itemId');

        if($transactionId>0){
            $query->whereNotIn('i.id', $list);
        }

        return $query->get();  
    }

    public function getOneItem($itemId){
        $query = DB::table('items as i')
        ->select(
            'i.id as itemId', 
            'i.name as itemName', 
            'sp.name as speciesName', 
            's.name as sizeName',
            'g.name as gradeName',
            'p.name as packingName',
            'p.shortname as packingShortname',
            'f.name as freezingName',
            'amount',
            'amountUnpacked',
            'baseprice',
            'weightbase'
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

    public function getPriceListByPurchasing($speciesId, $start, $end){
        $query = DB::table('items as i')
        ->select(
            'i.id as id',
            DB::raw('concat(sp.name," ",g.name, " ", s.name, " ", f.name) as itemName'),
            DB::raw('ifnull(min(dp.price),0) as minPrice'),
            DB::raw('ifnull(avg(dp.price),0) as avgPrice'),
            DB::raw('ifnull(max(dp.price),0) as maxPrice')
        )
        ->leftjoin('detail_purchases as dp', 'i.id', '=', 'dp.itemId')
        ->join('purchases as pur', 'dp.purchasesId', '=', 'pur.id')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->whereBetween('pur.purchaseDate', [$start." 00:00:00", $end." 23:59:59"])
        ->groupBy('i.id')
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderByRaw('s.name+0', 'asc');

        if ($speciesId>0){
            $query->where('sp.id','=', $speciesId);
        }
        $query->get();  

        return datatables()->of($query)
        ->editColumn('minPrice', function ($row) {
            return number_format($row->minPrice, 2);
        })
        ->editColumn('maxPrice', function ($row) {
            return number_format($row->maxPrice, 2);
        })
        ->editColumn('avgPrice', function ($row) {
            return number_format($row->avgPrice, 2);
        })
        ->addIndexColumn()->toJson();
    }
}
