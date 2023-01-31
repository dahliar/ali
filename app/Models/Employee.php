<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\EmployeeHistory;

use App\Http\Controllers\AdministrationController;




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
        $copy = User::get()->where('id', $id)->toArray();
        UserHistory::insert($copy);

        $affected = DB::table('users')
        ->where('id', $id)
        ->update(['accessLevel' => $accessLevel, 'email' => $email]);
        return $affected;
    }
    public function employeeUpdate($phone, $address, $employmentStatus, $isActive, $noRekening, $bankid, $id, $isactive, $pendidikan, $bidangPendidikan, $gender, $startdate){
        $copy = Employee::get()->where('id', $id)->toArray();
        EmployeeHistory::insert($copy);

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
            'startdate'         => $startdate
        ]);
        return $affected;
    }
    public function orgStructureStore($data){
        $id = DB::table('employeeorgstructuremapping')->insertGetId($data);
        return $id;
    }

    public function userMappingUpdate($eid, $nip, $nama, $newMappingData, $mappingid, $tanggalBerlaku){
        $affected = DB::table('employeeorgstructuremapping')
        ->where('id', '=', $mappingid)
        ->update(['isactive' => 0]);

        $newid = DB::table('employeeorgstructuremapping')->insertGetId($newMappingData);

        $text = DB::table('employeeorgstructuremapping as eos')
        ->select('os.name as jabatan', 'sp.name as level', 'wp.name as bagian', 'eos.isactive as stat')
        ->join('organization_structures as os', 'eos.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->whereIn('eos.id', [$mappingid, $newid])->get();

        $data = [
            'eid'                   => $eid,
            'nama'                  => $nama,
            'nip'                   => $nip,
            'oldJabatan'            => $text[0]->jabatan,
            'oldLevel'              => $text[0]->level,
            'oldBagian'             => $text[0]->bagian,
            'newJabatan'            => $text[1]->jabatan,
            'newLevel'              => $text[1]->level,
            'newBagian'             => $text[1]->bagian,
            'tanggalBerlaku'        => $tanggalBerlaku,
        ];

        $adm = new AdministrationController();
        $adm->cetakSuratMutasi($data);




        return $newid;
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
