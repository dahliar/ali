<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;


class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
    public function employeeStore($data){
        $id = DB::table('employees')->insertGetId($data);
        return $id;
    }


    public function userUpdate($accessLevel, $email, $id){
        $affected = DB::table('users')
        ->where('id', $id)
        ->update(['accessLevel' => $accessLevel, 'email' => $email]);
        return $affected;
    }
    public function employeeUpdate($phone, $address, $employmentStatus, $isActive, $noRekening, $bankid, $id, $isactive, $pendidikan, $bidangPendidikan, $gender){
        $affected = DB::table('employees')
        ->where('id', $id)
        ->update([
            'phone'             => $phone, 
            'address'           => $address, 
            'employmentStatus'  => $employmentStatus, 
            'isActive'          => $isActive,
            'noRekening'        => $noRekening,
            'gender'            => $gender,
            'isactive'          => $isactive,
            'jenjangPendidikan' => $pendidikan,
            'bidangPendidikan'  => $bidangPendidikan,
            'bankid'            => $bankid,
            
        ]);
        return $affected;
    }
    public function orgStructureStore($data){
        $id = DB::table('employeeorgstructuremapping')->insertGetId($data);
        return $id;
    }

    public function userMappingUpdate($newMappingData, $mappingid){
        $affected = DB::table('employeeorgstructuremapping')
        ->where('id', $mappingid)
        ->update(['isactive' => 0]);

        $id = DB::table('employeeorgstructuremapping')->insertGetId($newMappingData);
        return $id;
    }

    public function generateNIP($birthdate, $startdate){
        $birthdate = new Carbon($birthdate);
        $startdate = new Carbon($startdate);

        $birthyear=$birthdate->year;
        $startyear=$startdate->year;
        $count = DB::table('employees')
        ->whereYear('birthdate', $birthyear)
        ->whereYear('startdate', $startyear)
        ->count();
        $nip=$startyear.$birthyear.sprintf("%04d", $count+1);
        return $nip;
    }
}
