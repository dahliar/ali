<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;


class Store extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';


    public function storeOneItem($data){
        //insert into table stores, untuk setiap penambahan
        DB::table('stores')->insert($data);

        //update table item, untuk tambahkan jumlah stock
        $affected = DB::table('items')
        ->where('id', $data['itemId'])
        ->update([
            'amount' => DB::raw('amount + '.$data['amountPacked']),
            'amountUnpacked' => DB::raw('amountUnpacked + '.$data['amountUnpacked']),
        ]);

        return $affected;

    }
    public function unpackedUpdate($data){
        //insert kedalam table history perubahan unpacked
        DB::table('unpacked_histories')->insert($data);

        //update table item, untuk update jumlah amount dan amountUnpacked
        $affected = DB::table('items')
        ->where('id', $data['itemId'])
        ->update([
            'amount' => DB::raw('amount + '.$data['amountPacked']),
            'amountUnpacked' => DB::raw('amountUnpacked - '.$data['amountUnpacked']),
        ]);

        return $affected;

    }






//    public function updateOneStore($itemId, $storeId, $amountPacked, $amountUnpacked, $pastAmount, $newAmount, $tanggalPacking){
    public function updateOneStore($itemId, $storeId, $pastAmount, $newAmount, $tanggalPacking){

    /*
        1. Perubahan amount dalam tabel Item dengan id $itemId
                $perubahanAmountItem = $newAmount - $pastAmount.
        2. Update amount dalam tabel items dengan $perubahanAmountItem
        3. Update jumlah amount, packedamount, unpackedamount di tabel stores dengan data baru
    */
        $perubahanAmountItem = $newAmount - $pastAmount;

        //update table item, untuk tambahkan jumlah stock
        $affected = DB::table('items')
        ->where('id', $itemId)
        ->increment('amount', $perubahanAmountItem);


        $affected = DB::table('stores')
        ->where('id', $storeId)
        ->update([
            //'amountPacked' => $amountPacked,
            //'amountUnpacked' => $amountUnpacked,
            'amount' => $newAmount
        ]);


        return $affected;

    }

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
