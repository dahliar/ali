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
        $text = "";
        foreach ($collection as $row) 
        {
            if($row[10]==1){
                try{ 
                    $start = \Carbon\Carbon::parse($row[7].' '.$row[8].'.00');
                    $end = \Carbon\Carbon::parse($row[7].' '.$row[9].'.00');
                    if($end->gte($start)){
                        $this->simpanPresenceTunggal($row[0], $start, $end);
                    }
                }
                catch(\Exception $e){
                    $text.=$row[1].", ";
                }
            }
        }
        return $text;
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
        $menitKerja =0;
        $jamKerja=0;
        $menitLembur =0;
        $jamLembur=0;

        $shift = 1;
        $jamLembur=0;
        if ($start->gte($batasPulangKerja)) {
            $shift = 3; 
            $batasMasuk = Carbon::parse($start->toDateString()." 16:00:00");
            if($start->lte(Carbon::parse($start->toDateString()." 16:15:00"))){
                $menitKerja = $batasMasuk->diffInMinutes($end); 
                $jamLembur = $batasMasuk->diffInHours($end); 
            } else{
                $menitKerja = $start->diffInMinutes($end); 
                $jamLembur = $start->diffInHours($end); 
            }

            $selisihMenitSisa = $menitKerja - ($jamLembur * 60);
            if ($selisihMenitSisa > 30){
                $jamLembur+=1;
            }
        } else if ($start->gte(Carbon::parse($start->toDateString()." 12:00:00"))){
            $shift = 2; 
            $batasMasuk = Carbon::parse($start->toDateString()." 13:00:00");
            if($start->lte(Carbon::parse($start->toDateString()." 13:15:00"))){
                $mulai = $batasMasuk;
            } else{
                $mulai = $start;
            }

            $batasToleransiPulangKerja=Carbon::parse($start->toDateString()." 16:15:00");

            if($end->lte($batasToleransiPulangKerja)) {
                //kerja
                $menitKerja = $mulai->diffInMinutes($end);
                $jamKerja = $mulai->diffInHours($end);
            } else {
                //kerja dan lembur
                $menitKerja = $mulai->diffInMinutes($batasPulangKerja); 
                $jamKerja = $mulai->diffInHours($batasPulangKerja);

                $menitLembur = $batasPulangKerja->diffInMinutes($end); 
                $jamLembur = $batasPulangKerja->diffInHours($end);
            }

            //jam kerja
            $selisihMenit = $menitKerja - ($jamKerja * 60);
            if ($selisihMenit >= 30){
                $jamKerja+=1;
            }
            //jam lembur
            $selisihMenit = $menitLembur - ($jamLembur * 60);
            if ($selisihMenit >= 30){
                $jamLembur+=1;
            }
        } else {
            $shift=1;
            $batasMasuk = Carbon::parse($start->toDateString()." 08:00:00");

            if($start->lte($batasMasuk)) {
                $mulai = $batasMasuk;
            } else{
                $mulai = $start;
            }

            $batasToleransiPulangKerja=Carbon::parse($start->toDateString()." 16:15:00");

            if($end->lte($batasToleransiPulangKerja)) {
                //kerja
                $menitKerja = $mulai->diffInMinutes($end); 
                $jamKerja = $mulai->diffInHours($end);
            } else {
                //kerja dan lembur
                $menitKerja = $mulai->diffInMinutes($batasPulangKerja); 
                $jamKerja = $mulai->diffInHours($batasPulangKerja);

                $menitLembur = $batasPulangKerja->diffInMinutes($end); 
                $jamLembur = $batasPulangKerja->diffInHours($end);
            }

            //jam kerja
            $selisihMenit = $menitKerja - ($jamKerja * 60);
            if ($selisihMenit >= 30){
                $jamKerja+=1;
            }
            //jam lembur
            $selisihMenit = $menitLembur - ($jamLembur * 60);
            if ($selisihMenit >= 30){
                $jamLembur+=1;
            }

            //berapa batas dikuranginya
            //berapa batas dikuranginya
            //berapa batas dikuranginya
            //berapa batas dikuranginya
            if ($end->gte(Carbon::parse($start->toDateString()." 14:00:00"))){
                    $jamKerja-=1;   //mengurangi jam istirahat
                }
            }
            //berapa batas dikuranginya
            //berapa batas dikuranginya
            //berapa batas dikuranginya
            //berapa batas dikuranginya

            dump($start.' '.$end.' '.$jamKerja.' '.$jamLembur);
        /*
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
            DB::table('presences')
            ->where('id', '=', $presenceExist->presenceId)
            ->update($dataPresensi);
        } else{
            DB::table('presences')->insert($dataPresensi);
        }


        /*
        $honor = DB::table('employeeorgstructuremapping')
        ->select('uangharian as uh', 'uanglembur as ul')
        ->where('idemp', $empid)
        ->where('isactive', 1)
        ->first();

        //hitung uang harian proporsional terhadap jam
        $uh = ceil($honor->uh * ($jamKerja/7) / 100) * 100;
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
            'jamKerja'      => $jamKerja,
            'jamLembur'     => $jamLembur,
            'uanglembur'    => ($honorarium->ul * $jamLembur)
        ];
        if($dailySalariesExist->jumlah > 0){
            DB::table('dailysalaries')
            ->where('id', '=', $dailySalariesExist->dsid)
            ->update($datasalary);
        } else {
            DB::table('dailysalaries')->insert($datasalary);
        }
        */
    }
}
