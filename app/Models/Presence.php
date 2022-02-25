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
        $a=0;
        foreach ($collection as $row) 
        {
            $start = \Carbon\Carbon::parse($row[7].' '.$row[8].'.00');
            $end = \Carbon\Carbon::parse($row[7].' '.$row[9].'.00');
            if($end->gte($start)){
                if($row[10]==1){
                    $this->simpanPresenceTunggal($row[0], $start, $end);
                }
            }
        }
    }
    function storePresenceHarianEmployee($empid, $start, $end ){
        $start = \Carbon\Carbon::parse($start);
        $end = \Carbon\Carbon::parse($end);
        $retValue="";

        if($end->gte($start)){
            $this->simpanPresenceTunggal($empid, $start, $end);
            $retValue = [
                'message'       => "Data berhasil disimpan ",
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

    function simpanPresenceTunggal($empid, $start, $end){
        $menitKerja = $start->diffInMinutes($end); 
        $jamKerja = $start->diffInHours($end); 

        $batasPulangKerja = Carbon::parse($start->toDateString()." 16:00:00");

        //jika selisih menit kerja lebih dari 30 menit, ditambah jam kerja 1 jam
        $selisihMenitSisa = $menitKerja - ($jamKerja * 60);
        if ($selisihMenitSisa >= 30){
            $jamKerja+=1;
        }

        $shift = 1;
        $jamLembur=0;
        if ($start->gte(Carbon::parse($start->toDateString()." 16:00:00"))){
            $shift = 3; 
            $jamLembur=$jamKerja;
            $jamKerja=0;
        } else if ($start->gte(Carbon::parse($start->toDateString()." 12:30:00"))){
            $shift = 2; 
            $jamLembur = $jamKerja - ($start->diffInHours($batasPulangKerja)+1);
            if ($jamLembur < 0 ){
                $jamLembur=0;
            }
            $jamKerja = $jamKerja - $jamLembur;
            if ($jamKerja < 0 ){
                $jamKerja=0;
            }
        } else {
            $shift=1;
            $jamKerja-=1;   //mengurangi jam istirahat siang/sore baik untuk shift 1

            $jamLembur = $jamKerja - ($start->diffInHours($batasPulangKerja));
            if ($jamLembur < 0 ){
                $jamLembur=0;
            }
            $jamKerja = $jamKerja - $jamLembur;
            if ($jamKerja < 0 ){
                $jamKerja=0;
            }
        }

        $empiddate = $empid.$start->toDateString();
        $dataPresensi = [
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
            $dataPresensi,
            ['empiddate'],
            ['empiddate','employeeId','start','end','jamKerja','jamLembur','shift']
        );

        $honorarium = DB::table('employeeorgstructuremapping')
        ->select('uangharian as uh', 'uanglembur as ul')
        ->where('idemp', $empid)
        ->where('isactive', 1)
        ->first();


        //hitung uang harian proporsional terhadap jam
        $uh = 0;
        if ($shift == 1){
            $uh = $honorarium->uh * ($jamKerja/7);
        } else{
            $uh = $honorarium->uh * ($jamKerja/3);            
        }

        $datasalary = [
            'empiddate'     => $empiddate,
            'employeeId'    => $empid,
            'presenceDate'  => $start->toDateString(),
            'uangharian'    => $uh,
            'uanglembur'    => ($honorarium->ul * $jamLembur)
        ];

        DB::table('dailysalaries')
        ->upsert(
            $datasalary,
            ['empiddate'],
            ['empiddate','employeeId','presenceDate','uangharian','uanglembur']
        );

    }
}
