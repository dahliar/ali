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
        $batasMasuk         = $start;
        $batasPulangKerja   = Carbon::parse($start->toDateString()." 16:00:00");

        /*
        if ($start->lte(Carbon::parse($start->toDateString()." 08:00:00"))) {
            $batasMasuk = Carbon::parse($start->toDateString()." 08:00:00");
            $menitKerja = $batasMasuk->diffInMinutes($end); 
            $jamKerja = $batasMasuk->diffInHours($end); 
        }
        //jika selisih menit kerja lebih dari 30 menit, ditambah jam kerja 1 jam
        $selisihMenitSisa = $menitKerja - ($jamKerja * 60);
        if ($selisihMenitSisa >= 30){
            $jamKerja+=1;
        }
        */

        $shift = 1;
        $jamLembur=0;
        if ($start->gte(Carbon::parse($start->toDateString()." 16:00:00"))){
            $shift = 3; 
            $jamKerja=0;

            $menitKerja = $start->diffInMinutes($end); 
            $jamLembur = $start->diffInHours($end); 

            $selisihMenitSisa = $menitKerja - ($jamLembur * 60);
            if ($selisihMenitSisa >= 30){
                $jamLembur+=1;
            }
        } else if ($start->gte(Carbon::parse($start->toDateString()." 12:00:00"))){
            $shift = 2; 
            $batasMasuk = Carbon::parse($start->toDateString()." 13:00:00");

            if ($start > $batasMasuk){
                $batasMasuk = $start;
            }
            $menitKerja = $batasMasuk->diffInMinutes($end); 
            $jamKerja = $batasMasuk->diffInHours($end); 

            $selisihMenitSisa = $menitKerja - ($jamKerja * 60);
            if ($selisihMenitSisa >= 30){
                $jamKerja+=1;
            }
            if ($end->gte(Carbon::parse($start->toDateString()." 16:00:00"))){
                //lembur
                $jamLembur = $jamKerja-3;
                if ($jamLembur < 0 ){
                    $jamLembur=0;
                }
                $jamKerja = $jamKerja-$jamLembur;
            } else {
                //ngga lembur
                $jamLembur=0;
            }
        } else {
            $shift=1;
            $batasMasuk = Carbon::parse($start->toDateString()." 08:00:00");
            if ($start > $batasMasuk){
                $batasMasuk = $start;
            }
            $menitKerja = $batasMasuk->diffInMinutes($end); 
            $jamKerja = $batasMasuk->diffInHours($end); 

            $selisihMenitSisa = $menitKerja - ($jamKerja * 60);
            if ($selisihMenitSisa >= 30){
                $jamKerja+=1;
            }
            if ($end->gte(Carbon::parse($start->toDateString()." 13:30:00"))){
                $jamKerja-=1;   //mengurangi jam istirahat siang/sore baik untuk shift 1
            }
            if ($end->gte(Carbon::parse($start->toDateString()." 16:30:00"))){
                //lembur
                $jamLembur = $jamKerja-($batasMasuk->diffInHours($batasPulangKerja)-1);
                if ($jamLembur < 0 ){
                    $jamLembur=0;
                }
                $jamKerja = $jamKerja-$jamLembur;
            } else {
                //ngga lembur
                $jamLembur=0;
            }
        }
        /*
        dump("Jam Start : ". $start);
        dump("Jam Masuk : ". $batasMasuk);
        dump("Jam Kerja : ". $jamKerja);
        dump("Jam Lembur : ". $jamLembur);
        dd("Jam Keluar : ". $end);
        */

        $presenceExist=DB::table('presences')
        ->select(
            DB::raw('count(id) as jumlah'),
            'id as presenceId'
        )
        ->whereDate('start', '=', $start->toDateString())
        ->where('employeeId', '=', $empid)
        ->first();

        $dataPresensi = [
            'employeeId'    => $empid,
            'start'         => $start,
            'end'           => $end,
            'jamKerja'      => $jamKerja,
            'jamLembur'     => $jamLembur,
            'shift'         => $shift
        ];
        if($presenceExist->jumlah > 0){
            DB::table('presences')->update($dataPresensi)->where('id', '=', $presenceExist->presenceId);
        } else{
            DB::table('presences')->insert($dataPresensi);
        }

        $honorarium = DB::table('employeeorgstructuremapping')
        ->select('uangharian as uh', 'uanglembur as ul')
        ->where('idemp', $empid)
        ->where('isactive', 1)
        ->first();

        //hitung uang harian proporsional terhadap jam
        $uh = ceil($honorarium->uh * ($jamKerja/7) / 100) * 100;
        $dailySalariesExist=DB::table('dailysalaries')
        ->select(
            DB::raw('count(id) as jumlah'),
            'id as dsid'
        )
        ->whereDate('presenceDate', '=', $start->toDateString())
        ->where('employeeId', '=', $empid)
        ->first();

        $datasalary = [
            'employeeId'    => $empid,
            'presenceDate'  => $start->toDateString(),
            'uangharian'    => $uh,
            'uanglembur'    => ($honorarium->ul * $jamLembur)
        ];
        if($dailySalariesExist->jumlah > 0){
            DB::table('dailysalaries')->update($datasalary)->where('id', '=', $dailySalariesExist->dsid);
        } else {
            DB::table('dailysalaries')->insert($datasalary);
        }
    }
}
