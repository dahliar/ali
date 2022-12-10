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

    function presenceCalculator(Collection $collection, $isLembur){
        $a=0;
        $text = "";
        foreach ($collection as $row) 
        {
            if($row[11]==1){
                try{ 
                    $start = \Carbon\Carbon::parse($row[7].' '.$row[8].'.00');
                    $end = \Carbon\Carbon::parse($row[9].' '.$row[10].'.00');
                    if($end->gte($start)){
                        $this->simpanPresenceTunggal($row[0], $start, $end, $isLembur);
                    }
                }
                catch(\Exception $e){
                    $text.=$row[7].' '.$row[8]." - ".$row[1].", ";
                }
            }
        }

        return $text;
    }

    function storePresenceHarianEmployee($empid, $start, $end, $isLembur){
        $start = \Carbon\Carbon::parse($start);
        $end = \Carbon\Carbon::parse($end);
        $retValue="";

        if($end->gte($start)){
            $this->simpanPresenceTunggal($empid, $start, $end, $isLembur);
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

    function getMasukKerjaDanShift($start, $dateAcuan){
        $masuk=null;
        $shift=0;

        if ($start->lte(Carbon::parse($dateAcuan." 08:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 08:00:00");
            $shift=1;
        } else if ($start->lte(Carbon::parse($dateAcuan." 09:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 09:00:00");
            $shift=1;
        } else if ($start->lte(Carbon::parse($dateAcuan." 10:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 10:00:00");
            $shift=1;
        } else if ($start->lte(Carbon::parse($dateAcuan." 11:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 11:00:00");
            $shift=1;
        } else if ($start->lte(Carbon::parse($dateAcuan." 12:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 12:00:00");
            $shift=1;
        } else if ($start->lte(Carbon::parse($dateAcuan." 13:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 13:00:00");
            $shift=2;
        } else if ($start->lte(Carbon::parse($dateAcuan." 14:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 14:00:00");
            $shift=2;
        } else if ($start->lte(Carbon::parse($dateAcuan." 15:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 15:00:00");
            $shift=2;
        } else if ($start->lte(Carbon::parse($dateAcuan." 16:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 16:00:00");
            $shift=3;
        } else if ($start->lte(Carbon::parse($dateAcuan." 17:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 17:00:00");
            $shift=3;
        } else if ($start->lte(Carbon::parse($dateAcuan." 18:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 18:00:00");
            $shift=3;
        } else if ($start->lte(Carbon::parse($dateAcuan." 19:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 19:00:00");
            $shift=3;
        } else if ($start->lte(Carbon::parse($dateAcuan." 20:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 20:00:00");
            $shift=3;
        } else if ($start->lte(Carbon::parse($dateAcuan." 21:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 21:00:00");
            $shift=3;
        } else if ($start->lte(Carbon::parse($dateAcuan." 22:15:00"))){
            $masuk=Carbon::parse($dateAcuan." 22:00:00");
            $shift=3;
        } else {
            $masuk=$start;
            $shift=3;
        }

        $data=[
            'masuk'=>$masuk, 
            'shift'=>$shift
        ];
        return $data;

    }

    function hitungPresenceHarianRamadhan($start, $end){
        //hitung jam kerja
        $dateAcuan = $start->toDateString();
        $dataMasukDanShift=$this->getMasukKerjaDanShift($start, $dateAcuan);
        $masuk=$dataMasukDanShift['masuk'];
        $shift=$dataMasukDanShift['shift'];

        $isLembur=false;
        $keluar = $end;
        if ($end->gte(Carbon::parse($dateAcuan." 16:15:00"))){
            $keluar=Carbon::parse($dateAcuan." 16:00:00");
            $isLembur=true;
        }

        $jamKerja=0;
        if ($masuk->lte(Carbon::parse($dateAcuan." 16:15:00"))) {
            $menitKerja = $masuk->diffInMinutes($keluar); 
            $jamKerja = $masuk->diffInHours($keluar); 

            $selisihMenitSisa = $menitKerja - ($jamKerja * 60);
            if ($selisihMenitSisa > 30){
                $jamKerja+=1;
            }
            if ($start->lte(Carbon::parse($dateAcuan." 12:00:00")) and $end->gte(Carbon::parse($dateAcuan." 13:00:00"))) {
                $jamKerja-=1;
            }
        }


        //hitung jam lembur
        $jamLembur=0;
        if($isLembur)
        {
            if ($end->gte(Carbon::parse($dateAcuan." 19:00:00"))){
                $dataMasukDanShift=$this->getMasukKerjaDanShift($start, $dateAcuan);
                $masuk=$dataMasukDanShift['masuk'];

                if($start->lte(Carbon::parse($dateAcuan." 19:15:00"))){
                    $masuk = Carbon::parse($dateAcuan." 19:00:00");
                }
                $keluar=$end;

                $menitKerja = $masuk->diffInMinutes($keluar); 
                $jamLembur = $masuk->diffInHours($keluar); 

                $selisihMenitSisa = $menitKerja - ($jamLembur * 60);
                if ($selisihMenitSisa >= 30){
                    $jamLembur+=1;
                }
            }
        }

        $dataJam=[
            'jamKerja'=>$jamKerja, 
            'jamLembur'=>$jamLembur,
            'shift'=>$shift
        ];

        return $dataJam;

    }


    function hitungPresenceHarian($start, $end){
        //hitung jam kerja
        $dateAcuan = $start->toDateString();
        $dataMasukDanShift=$this->getMasukKerjaDanShift($start, $dateAcuan);
        $masuk=$dataMasukDanShift['masuk'];
        $shift=$dataMasukDanShift['shift'];
        $isLembur=false;
        $keluar = $end;
        if ($end->gte(Carbon::parse($dateAcuan." 16:15:00"))){
            $keluar=Carbon::parse($dateAcuan." 16:00:00");
            $isLembur=true;
        }

        $jamKerja=0;
        if ($masuk->lte(Carbon::parse($dateAcuan." 16:15:00"))){
            $menitKerja = $masuk->diffInMinutes($keluar); 
            $jamKerja = $masuk->diffInHours($keluar); 

            $selisihMenitSisa = $menitKerja - ($jamKerja * 60);
            if ($selisihMenitSisa > 30){
                $jamKerja+=1;
            }

            if ($start->lte(Carbon::parse($dateAcuan." 12:00:00")) and $end->gte(Carbon::parse($dateAcuan." 13:00:00"))) {
                $jamKerja-=1;
            }
        }


        //hitung jam lembur
        $jamLembur=0;
        if($isLembur)
        {
            $dataMasukDanShift=$this->getMasukKerjaDanShift($start, $dateAcuan);
            $masuk=$dataMasukDanShift['masuk'];
            if($start->lte(Carbon::parse($dateAcuan." 16:15:00"))){
                $masuk = Carbon::parse($dateAcuan." 16:00:00");
            }
            $keluar=$end;

            $menitKerja = $masuk->diffInMinutes($keluar); 
            $jamLembur = $masuk->diffInHours($keluar); 

            $selisihMenitSisa = $menitKerja - ($jamLembur * 60);
            if ($selisihMenitSisa >= 30){
                $jamLembur+=1;
            }
        }

        $dataJam=[
            'jamKerja'=>$jamKerja, 
            'jamLembur'=>$jamLembur,
            'shift'=>$shift
        ];

        return $dataJam;

    }


    function simpanPresenceTunggal($empid, $start, $end, $isLembur){
        $ramadhanStart = Carbon::parse("2022-04-03 00:00:00"); 
        $ramadhanEnd = Carbon::parse("2022-05-01 23:59:59"); 
        $dataJam=null;
        if (($start->gte($ramadhanStart)) and ($start->lte($ramadhanEnd))) {
            $dataJam = $this->hitungPresenceHarianRamadhan($start, $end);
        } else{
            $dataJam = $this->hitungPresenceHarian($start, $end);
        }
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
            'jamKerja'      => $dataJam['jamKerja'],
            'jamLembur'     => $dataJam['jamLembur'],
            'shift'         => $dataJam['shift'],
            'isLembur'      => $isLembur,
        ];

        if($presenceExist->jumlah > 0){
            DB::table('presences')
            ->where('id', '=', $presenceExist->presenceId)
            ->update($dataPresensi);
        } else{
            DB::table('presences')->insert($dataPresensi);
        }
        
        $honor = DB::table('employeeorgstructuremapping')
        ->select('uangharian as uh', 'uanglembur as ul')
        ->where('idemp', $empid)
        ->where('isactive', 1)
        ->first();


        $isPegawaiBulanan = DB::table('employees')
        ->select('employmentStatus as empStatus')
        ->where('id', '=', $empid)
        ->first()->empStatus;

        $isSunday=$start->dayOfWeekIso;

        //hitung uang harian proporsional terhadap jam
        $uh = ceil($honor->uh * ($dataJam['jamKerja']/7) / 100) * 100;
        if (($isPegawaiBulanan==1) and ($isSunday!=7)){
            $uh=0;
        }

        //hitung uang lembur berdasar isLembur
        $jamLembur=0;
        $uangLembur=0;
        if($isLembur==1){
            $jamLembur=$dataJam['jamLembur'];
            $uangLembur=$honor->ul * $dataJam['jamLembur'];
        }


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
            'jamKerja'      => $dataJam['jamKerja'],
            'jamLembur'     => $jamLembur,
            'uangharian'    => $uh,
            'uanglembur'    => $uangLembur
        ];

        if($dailySalariesExist->jumlah > 0){
            DB::table('dailysalaries')
            ->where('id', '=', $dailySalariesExist->dsid)
            ->update($datasalary);
        } else {
            DB::table('dailysalaries')->insert($datasalary);
        }
    }
}
