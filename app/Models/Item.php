<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;


class Item extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function getAllItemData($speciesId){
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'i.name as itemName', 
            'sp.name as speciesName', 
            's.name as sizeName',
            'g.name as gradeName',
            'p.name as packingName',
            'f.name as freezingName',
            'amount',
            

            //DB::raw('concat(g.name, " - ", s.name) as gradesize'),
            //DB::raw('concat(f.name, " - ", p.name) as freezepacking'),
            DB::raw('concat(amount, " ",p.shortname) as amountweightbase'),
            DB::raw('concat(i.weightbase, " Kg/", p.shortname) as wb'),
            DB::raw('(amount * weightbase) as totalWeight'),
            'baseprice',
            'weightbase'
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')

        ->where('i.isActive','=', 1);

        if ($speciesId>0){
            $query->where('sp.id','=', $speciesId);
        }
        $query->get();    

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Stock Barang">
            <i onclick="tambahStockItem('."'".$row->id."'".')" class="fa fa-plus" style="font-size:20px"></i>
            </button>
            <button onclick="historyStockItem('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" title="History Tambah Stock">
            <i class="fa fa-history" style="font-size:20px"></i>
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
            'str.amount as amount',
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
        ->addColumn('action', function ($row) {
            $html = '<button type="button"  data-toggle="tooltip" data-placement="top" data-container="body" title="Detail & edit penyimpanan" class="btn btn-primary" onclick="editStoreDetail('."'".$row->id."'".')" data-bs-target="#exampleModal"><i class="fa fa-edit" style="font-size:20px"></i></button>';
            return $html;
        })->addIndexColumn()->toJson();    
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
            'f.name as freezingName',
            'amount',
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
