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

        //ini masih belum ngitung yang lagi onProgress
        /*
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'i.name as itemName', 
            DB::raw('concat(s.name, " ",f.name, " ", g.name) as sizeblockgrade'),
            'sp.name as speciesName', 
            DB::raw('concat(i.amount, " ",p.shortname) as amountPacked'),
            DB::raw('concat(ifnull(am,0), " ",p.shortname) as onProgress'),
            DB::raw('concat(amountUnpacked, " Kg") as amountUnpacked'),
            DB::raw('concat(ifnull((((i.amount+sum(dt.amount)) * weightbase) + amountUnpacked),0)," Kg") as total'),
            DB::raw('concat(i.weightbase, " Kg/", p.shortname) as wb'),
            'baseprice',
            'weightbase'
        )
        ->groupBy('i.id')
        ->leftjoin('detail_transactions as dt', 'dt.itemId', '=', 'i.id')
        ->join(DB::raw("select ii.id, sum(dti.amount) as am from detail_transactions dti join transactions ti on dti.transactionId=ti.id join items ii on ii.id=dti.itemId where ti.status=1 group by ii.id").' as op', 'i.id', '=', 'iiid')
        ->join('transactions as t', 't.id', '=', 'dt.transactionId')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')

        ->where('i.isActive','=', 1);
        */
        /*
        $onprogressdata = DB::table('detail_transactions as dti')
        ->select('i.id as id', DB::raw('sum(dti.amount) as am'))
        ->join('transactions as ti', 'dti.transactionId', '=', 'ti.id')
        ->join('items as i', 'i.id', '=', 'dti.itemId')
        ->where('ti.status', '=', '1')
        ->groupby('i.id')->toSql();
        */
        //dd($onprogressdata);


        /*
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'i.name as itemName', 
            DB::raw('concat(s.name, " ",f.name, " ", g.name) as sizeblockgrade'),
            'sp.name as speciesName', 
            DB::raw('concat(i.amount, " ",p.shortname) as amountPacked'),
            DB::raw('concat(ifnull(sum(dt.amount),0), " ",p.shortname) as onProgress'),
            DB::raw('concat(amountUnpacked, " Kg") as amountUnpacked'),
            DB::raw('concat(ifnull((((i.amount+sum(dt.amount)) * weightbase) + amountUnpacked),0)," Kg") as total'),
            DB::raw('concat(i.weightbase, " Kg/", p.shortname) as wb'),
            'baseprice',
            'weightbase'
        )
        ->leftjoin('detail_transactions as dt', 'dt.itemId', '=', 'i.id')
        ->join('transactions as t', 't.id', '=', 'dt.transactionId')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->groupBy('i.id')
        ->where('t.status','=', 1)
        ->where('i.isActive','=', 1);

        if ($speciesId>0){
            $query->where('sp.id','=', $speciesId);
        }
        $query->get();    

        */

        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'i.name as itemName', 
            'sp.name as speciesName', 
            'i.amount as jumlahPacked',
            'amountUnpacked as jumlahUnpacked',
            DB::raw('ifnull(sum(dt.amount),0) as jumlahOnProgress'),
            DB::raw('concat(s.name, " ",f.name, " ", g.name) as sizeblockgrade'),
            DB::raw('concat(i.amount, " ",p.shortname) as amountPacked'),
            DB::raw('concat(amountUnpacked, " Kg") as amountUnpacked'),
            DB::raw('concat(ifnull(sum(dt.amount),0), " ",p.shortname) as onProgress'),
            DB::raw('concat(i.weightbase, " Kg/", p.shortname) as wb'),
            'baseprice',
            'weightbase'
        )
        ->leftjoin('detail_transactions as dt', 'dt.itemId', '=', 'i.id')
        ->leftjoin('transactions as t', 't.id', '=', 'dt.transactionId')

        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->groupBy('i.id');


        if ($speciesId>0){
            $query->where('sp.id','=', $speciesId);
        }
        $query->get();  




        return datatables()->of($query)
        ->addColumn('total', function ($row) {
            $jumlah = ((($row->jumlahPacked + $row->jumlahOnProgress) * $row->weightbase) + $row->jumlahUnpacked).' Kg';

            return $jumlah;
        })
        ->addColumn('action', function ($row) {
            $html = '<button  data-rowid="'.$row->id.'" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah stok barang">
            <i onclick="tambahStockItem('."'".$row->id."'".')" class="fa fa-plus"></i>
            </button>';
            if (Auth::user()->isAdmin() or Auth::user()->isProduction()){
                $html .= '
                <button onclick="UpdateStockUnpacked('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Update jumlah unpacked">
                <i class="fa fa-box-open"></i>
                </button>
                ';
            }
            $html .= '
            <button onclick="historyStockItem('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Stock History">
            <i class="far fa-list-alt"></i>
            </button>';
            $html .= '
            <button onclick="unpackedHistory('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Unpacked Stock History"><i class="fas fa-list-alt"></i>
            </button>';


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

    public function getItemForSelectOption($speciesId){
        $query = DB::table('items as i')
        ->select(
            'i.id as itemId', 
            'i.name as itemName', 
            'sp.name as speciesName', 
            's.name as sizeName',
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
        ->orderBy('i.name')
        ->get();    
        return $query;
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
}
