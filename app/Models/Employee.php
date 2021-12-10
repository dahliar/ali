<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

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


    public function userUpdate($role, $email, $id){
        $affected = DB::table('users')
        ->where('id', $id)
        ->update(['role' => $role, 'email' => $email]);
    }
    public function employeeUpdate($address, $employmentStatus, $isActive, $noRekening, $bankid, $id, $isactive){
        $affected = DB::table('employees')
        ->where('id', $id)
        ->update([
            'address' => $address, 
            'employmentStatus' => $employmentStatus, 
            'isActive' => $isActive,
            'noRekening' => $noRekening,
            'isactive' => $isactive,
            'bankid' => $bankid]);
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
}
