<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Purchase extends Model
{
    use HasFactory;

    public function storeOnePurchase($data){
        //insert into table transactions, untuk setiap penambahan
        $id = DB::table('purchases')->insertGetId($data);
        return $id;
    }

}
