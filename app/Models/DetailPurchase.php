<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class DetailPurchase extends Model
{
    use HasFactory;

    
    public function purchaseItemAddStore($data){
        //insert into table transactions, untuk setiap penambahan
        DB::table('detail_purchases')->insert($data);
    }
}
