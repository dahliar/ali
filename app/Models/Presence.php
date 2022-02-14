<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use DB;


class Presence extends Model
{
    use HasFactory;
    protected $guarded = [];


    function presenceCalculator(Collection $collection){
        //dd($collection);
        $a=0;
        foreach ($collection as $row) 
        {
            $start = \Carbon\Carbon::parse($row[7].' '.$row[8].'.00');
            $end = \Carbon\Carbon::parse($row[7].' '.$row[9].'.00');

            $formatted_dt1=Carbon::parse($start);
            $formatted_dt2=Carbon::parse($end);

            $menitKerja = $formatted_dt1->diffInMinutes($formatted_dt2); 
            $jamKerja = $formatted_dt1->diffInHours($formatted_dt2); 

            //jika selisih menit kerja lebih dari 30 menit, ditambah jam kerja 1 jam
            $selisihMenitSisa = $menitKerja - ($jamKerja * 60);
            if ($selisihMenitSisa >= 30){
                $jamKerja+=1;
            }

            //mengurangi jam istirahat
            $jamKerja-=1;

            $shift = 1;
            $jamLembur=0;

            if ($start->gte(Carbon::parse($start->toDateString()." 13:00:00"))){
            //shift 2 mulai dari jam 1300
                $jamLembur = $jamKerja - 4;
                $jamKerja = $jamKerja - 4;
                $shift = 2; 
            } else {
                $jamLembur = $jamKerja - 8;
            }
            
            $empiddate = $row[0].$row[7];

            if ($jamLembur <= 0 ){
                $jamLembur=0;
            }
            if ($jamKerja <= 0 ){
                $jamKerja=0;
            }

            $data[$a] = [
                'empiddate'     => $empiddate,
                'employeeId'    => $row[0],
                'start'         => $start,
                'end'           => $end,
                'jamKerja'      => $jamKerja,
                'jamLembur'     => $jamLembur,
                'shift'         => $shift
            ];
            $a++;
        }

        DB::table('presences')
        ->upsert(
            $data,
            ['empiddate'],
            ['empiddate','employeeId','start','end','jamKerja','jamLembur','shift']
        );
    }
    function presenceTunggalHarian($empid, $start, $end ){
        $start = \Carbon\Carbon::parse($start);
        $end = \Carbon\Carbon::parse($end);
        $retValue="";

        if($end->gte($start)){
            $menitKerja = $start->diffInMinutes($end); 
            $jamKerja = $start->diffInHours($end); 


        //jika selisih menit kerja lebih dari 30 menit, ditambah jam kerja 1 jam
            $selisihMenitSisa = $menitKerja - ($jamKerja * 60);
            if ($selisihMenitSisa >= 30){
                $jamKerja+=1;
            }

        //mengurangi jam istirahat
            $jamKerja-=1;

            $shift = 1;
            $jamLembur=0;

            if ($start->gte(Carbon::parse($start->toDateString()." 13:00:00"))){
            //shift 2 mulai dari jam 1300
                $jamLembur = $jamKerja - 4;
                $jamKerja = $jamKerja - 4;
                $shift = 2; 
            } else {
                $jamLembur = $jamKerja - 8;
            }

            if ($jamLembur <= 0 ){
                $jamLembur=0;
            }
            if ($jamKerja <= 0 ){
                $jamKerja=0;
            }

            $empiddate = $empid.$start->toDateString();
            $data = [
                'empiddate'     => $empiddate,
                'employeeId'    => $empid,
                'start'         => $start,
                'end'           => $end,
                'jamKerja'      => $jamKerja,
                'jamLembur'     => $jamLembur,
                'shift'         => $shift
            ];

            DB::table('presences')
            ->upsert(
                $data,
                ['empiddate'],
                ['empiddate','employeeId','start','end','jamKerja','jamLembur','shift']
            );
            $retValue = [
                'message'       => "Data berhasil disimpan",
                'isError'       => "0"
            ];

        }else{
            $retValue = [
                'message'       => "Tanggal dan jam pulang harus lebih dari tanggal dan jam masuk",
                'isError'       => "1"
            ];
        }
        return $retValue;        
    }
}
