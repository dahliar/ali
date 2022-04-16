<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;


class Store extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function getOneStore($storeId){
        $query = DB::table('alicore_stock.stores as str')
        ->select('str.id', 
            'i.name as name', 
            's.name as size',
            'sp.name as species', 
            'g.name as grade',
            'p.name as packing',
            'f.name as freezing',
            'str.datePackage as datePackage',
            'str.dateProcess as dateProcess',
            'str.dateInsert as dateInsert',
            'm2users.name as username',
            'str.isApproved as isApproved',
            'str.amount',
            'str.price',
            'i.weightbase',
            'str.isPacked as isPacked'
        )
        ->join('users as m2users', 'm2users.id', '=', 'str.userId')
        ->join('items as i', 'i.id', '=', 'str.itemId')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.id','=', $storeId)
        ->get();    

        return $query;
    }
}
