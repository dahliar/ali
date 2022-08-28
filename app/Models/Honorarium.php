<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use DB;

class Honorarium extends Model
{
    use HasFactory;
    protected $guarded = [];

    function honorariumStore(Collection $collection){
        $a=0;
        $text = "";
        foreach ($collection as $row) 
        {
            if($row[8]==1){
                try{ 
                    $dataHonorarium = [
                        'employeeId'        => $row[0],
                        'tanggalKerja'      => $row[7],
                        'jumlah'            => $row[9],
                        'keterangan'        => $row[10],
                        'isGenerated'       => 0
                    ];

                    $affected = DB::table('honorariums')->insert($dataHonorarium);
                }
                catch(\Exception $e){
                    $text.=$row[1].", ";
                }
            }
        }

        return $text;
    }
}
