<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;


use App\Models\Employee;
use App\Models\StructuralPosition;
use App\Models\WorkPosition;
use App\Models\EmployeeHistory;

use App\Http\Controllers\AdministrationController;
use DB;
use Auth;
use Carbon\Carbon;
use Milon\Barcode\DNS1D;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(){
        $this->employee = new Employee();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('employee.employeeList');
    }
    public function indexBarcodeList()
    {
        return view('employee.employeeBarcodeList');
    }
    public function index2()
    {
        return view('employee.employeeList2');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $banks = DB::table('banks')
        ->where('isActive', 1)
        ->orderBy('name')
        ->get();
        $structpos = StructuralPosition::all();
        $workpos = WorkPosition::all();
        return view('employee.employeeAdd', compact('structpos', 'workpos', 'banks'));
    }

    public function orgStructureList(Request $request)
    {
        $orgstructure = DB::table("organization_structures")
        ->where("idstructuralpos", $request->structPosId)
        ->where("idworkpos", $request->workPosId)
        ->pluck("name", "id");
        return response()->json($orgstructure);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ktp'                   => ['mimes:jpg,jpeg,png,pdf','max:2048'],
            'kk'                    => ['mimes:jpg,jpeg,png,pdf','max:2048'],
            'name'                  => ['required', 'string', 'max:255'],
            'username'              => ['required', 'string', 'max:255', 'unique:users'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
            'accessLevel'           => ['required', 'gte:0'],
            'phone'                 => ['required'],
            'email'                 => ['email'],
            'nik'                   => ['required', 'string', 'max:20', 'unique:employees'],
            'birthdate'             => ['required', 'date', 'before:today'],
            'gender'                => ['required', 'integer', 'gt:0'],
            'startdate'             => ['required', 'date', 'after:birthdate', 'before:today'],
            'address'               => ['required', 'string'],
            'structural'            => ['required', 'gt:0'],
            'workPosition'          => ['required', 'gt:0'],
            'OrgStructureOption'    => ['required', 'gt:0'],
            'employmentStatus'      => ['required', 'gt:0'],
            'pendidikan'            => ['required', 'gte:0'],
            'bidangPendidikan'      => ['required', 'string'],
            'gajiPokok'             => ['required', 'integer', 'gte:0'],
            'gajiHarian'            => ['required', 'integer', 'gte:0'],
            'uangLembur'            => ['required', 'integer', 'gte:0'],
            'noRekening'            => ['required'],
            'bankid'                => ['required', 'gt:0']
        ],
        [
            'ktp.max'                  => 'Ukuran file maksimal adalah 2 MB',
            'kk.max'                   => 'Ukuran file maksimal adalah 2 MB',
            'startdate.after'  => 'Tanggal mulai harus sebelum tanggal lahir',
            'startdate.before' => 'Tanggal mulai harus sebelum hari ini',
            'birthdate.before' => 'Tanggal lahir harus sebelum hari ini'
        ]);


        //bagian proses insert kedalam table Users
        $tanggal=Carbon::now()->toDateString();

        $file="";
        $filenameKTP="";
        if($request->hasFile('ktp')){
            $file = $request->ktp;
            $filenameKTP = "KTP ".$tanggal." ".$request->name.".".$file->getClientOriginalExtension();
            $file->move(base_path("storage/app/docs/"), $filenameKTP);
        }
        $filenameKK="";
        if($request->hasFile('kk')){
            $file = $request->kk;
            $filenameKK = "KK ".$tanggal." ".$request->name.".".$file->getClientOriginalExtension();
            $file->move(base_path("storage/app/docs/"), $filenameKK);
        }




        $user = User::create([
            'name' => $request->name,
            'accessLevel' => $request->accessLevel,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);
        //event(new Registered($user));
        $userid = DB::getPdo()->lastInsertId();

        //insert ke table employees
        $nip=$this->employee->generateNIP($request->birthdate, $request->startdate);


        $employee = [
            'userid'                => $userid,
            'nik'                   => $request->nik,
            'nip'                   => $nip,
            'phone'                 => $request->phone,
            'birthdate'             => $request->birthdate,
            'gender'                => $request->gender,
            'startdate'             => $request->startdate,
            'address'               => $request->address,
            'employmentStatus'      => $request->employmentStatus,
            'isActive'              => 1,
            'noRekening'            => $request->noRekening,
            'bankid'                => $request->bankid,
            'jenjangPendidikan'     => $request->pendidikan,
            'kk'                    => $filenameKK,
            'ktp'                   => $filenameKTP,
            'bidangPendidikan'      => $request->bidangPendidikan
        ];
        $empid = $this->employee->employeeStore($employee);

        //insert ke table employee Mapping
        $mapping = [
            'idemp'             => $empid,
            'idorgstructure'    => $request->OrgStructureOption,
            'isactive'          => 1,
            'gajipokok'         => $request->gajiPokok,
            'uangharian'        => $request->gajiHarian,
            'uanglembur'        => $request->uangLembur
        ];
        $mappingId = $this->employee->orgStructureStore($mapping);
        

        $empStatusString ="";
        switch ($request->employmentStatus){
            case('1') : $empStatusString ="Tetap";break;
            case('2') : $empStatusString ="Kontrak Harian";break;
            case('3') : $empStatusString ="Kontrak Borongan";break;
        }

        //$adm = new AdministrationController();

        //$adm->cetakSuratKeputusanPegawaiBaru($empid, $mappingId, $request->employmentStatus, $request->name, $nip, $request->startdate, $empStatusString);

        return redirect('employeeList')
        ->with('status','Employee berhasil ditambahkan.');

    }

    public function storePassword(Request $request)
    {
        $request->validate([
            'userid'                => [
                'required', 
                Rule::exists('users', 'id')->where('id', $request->userid)
            ],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()]
        ]);

        $affected = DB::table('users')
        ->where('id', $request->userid)
        ->update(['password' => Hash::make($request->password)]);

        return redirect('employeeList')
        ->with('status','Password berhasil diubah');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //return view('employee.employeeView');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        $banks = DB::table('banks')
        ->where('isActive', 1)
        ->orderBy('name')
        ->get();

        $structpos = StructuralPosition::all();
        $workpos = WorkPosition::all();
        $choosenUser = User::where('id', $employee->userid)->first();



        $orgstructure = DB::table('employees as e')
        ->select('mapping.idorgstructure as idorgstructure','wp.id as workPosition','sp.id as structuralPosition')
        ->where('e.id', $employee->id)
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->first();


        return view('employee.employeeEdit', compact('orgstructure', 'employee', 'choosenUser','structpos', 'workpos', 'banks'));
    }

    public function editPassword(Employee $employee)
    {
        $choosenUser = User::where('id', $employee->userid)->first();
        return view('employee.editPassword', compact('employee', 'choosenUser'));
    }

    public function editMapping(Employee $employee)
    {
        $structpos = StructuralPosition::all();
        $workpos = WorkPosition::all();
        $choosenUser = User::where('id', $employee->userid)->first();

        $orgstructure = DB::table('employees as e')
        ->select('mapping.id as id', 'mapping.idorgstructure as idorgstructure','wp.id as workPosition','sp.id as structuralPosition', 'mapping.gajipokok as gp', 'mapping.uangharian as uh', 'mapping.uanglembur as ul')
        ->where('e.id', $employee->id)
        ->where('mapping.isactive', 1)
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->first();

        return view('employee.employeeMappingEdit', compact('orgstructure', 'employee', 'choosenUser','structpos', 'workpos'));
    }


    public function employeePersonalDataEdit(Employee $employee)
    {
        $banks = DB::table('banks')
        ->where('isActive', 1)
        ->orderBy('name')
        ->get();

        $structpos = StructuralPosition::all();
        $workpos = WorkPosition::all();
        $choosenUser = User::where('id', $employee->userid)->first();

        $orgstructure = DB::table('employees as e')
        ->select('mapping.idorgstructure as idorgstructure','wp.id as workPosition','sp.id as structuralPosition')
        ->where('e.id', $employee->id)
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->first();


        return view('employee.employeePersonalDataEdit', compact('orgstructure', 'employee', 'choosenUser','structpos', 'workpos', 'banks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'ktp'                   => ['mimes:jpg,jpeg,png,pdf','max:2048'],
            'kk'                    => ['mimes:jpg,jpeg,png,pdf','max:2048'],
            'email'                 => ['email'],
            'phone'                 => ['required'],
            'fullname'              => ['required', 'string'],
            'accessLevel'           => ['required', 'gte:curUserAccessLevel'],
            'address'               => ['required', 'string'],
            'gender'                => ['required', 'integer', 'gt:0'],
            'employmentStatus'      => ['required', 'gt:0'],
            'bankid'                => ['required', 'gt:0'],
            'startdate'             => ['required', 'date', 'before_or_equal:today'],
            'noRekening'            => ['required', 'gt:0'],
            'pendidikan'            => ['required', 'gte:0'],
            'bidangPendidikan'      => ['required', 'string'],
            'isactive'              => ['required']
        ],
        [
            'ktp.max'                  => 'Ukuran file maksimal adalah 2 MB',
            'kk.max'                   => 'Ukuran file maksimal adalah 2 MB',
            'startdate.required'            => 'Tanggal mulai harus diisi',
            'startdate.before_or_equal'     => 'Tanggal mulai harus sebelum atau sama dengan hari ini',            
        ]);
        DB::beginTransaction();
        try {
            $userUpdate = $this->employee->userUpdate($request->fullname, $request->accessLevel, $request->email, $request->userid);

            $file="";
            $filenameKTP="";
            if($request->hasFile('ktp')){
                $file = $request->ktp;
                $filenameKTP = "KTP ".$request->employeeId." ".$request->fullname.".".$file->getClientOriginalExtension();
                $file->move(base_path("storage/app/docs/"), $filenameKTP);
            }
            $filenameKK="";
            if($request->hasFile('kk')){
                $file = $request->kk;
                $filenameKK = "KK ".$request->employeeId." ".$request->fullname.".".$file->getClientOriginalExtension();
                $file->move(base_path("storage/app/docs/"), $filenameKK);
            }
            /*
            $employeeUpdate = $this->employee->employeeUpdate(
                $request->phone, 
                $request->address,
                $request->employmentStatus, 
                $request->isActive, 
                $request->noRekening, 
                $request->bankid, 
                $request->employeeId, 
                $request->isactive, 
                $request->pendidikan, 
                $request->bidangPendidikan, 
                $request->gender,
                $request->startdate
            );
            */


            $copy = Employee::get()->where('id', $request->employeeId)->toArray();
            EmployeeHistory::insert($copy);

            $affected = DB::table('employees')
            ->where('id', $request->employeeId)
            ->update([
                'phone'             => $request->phone, 
                'address'           => $request->address, 
                'employmentStatus'  => $request->employmentStatus, 
                'isActive'          => $request->isActive,
                'noRekening'        => $request->noRekening,
                'gender'            => $request->gender,
                'isactive'          => $request->isactive,
                'jenjangPendidikan' => $request->pendidikan,
                'bidangPendidikan'  => $request->bidangPendidikan,
                'bankid'            => $request->bankid,
                'startdate'         => $request->startdate,
                'ktp'               => $filenameKTP,
                'kk'                => $filenameKK,
                'endDate'           => Carbon::now()->toDateString()
            ]);


            $setActive = $this->setEmployeeActiveness($request->employeeId, $request->isactive);
            DB::commit();
            return redirect('employeeList')
            ->with('status','Data Karyawan berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('employeeList')
            ->with('status',$e.' Data gagal diperbaharui');
        }

    }

    public function setEmployeeActiveness($employeeId, $isActive){
        $ideosm = DB::table('employeeorgstructuremapping')
        ->select('id')
        ->where('idemp', $employeeId)
        ->orderBy('id', 'desc')
        ->first()->id;

        $affected = DB::table('employeeorgstructuremapping')
        ->where('id', $ideosm)
        ->where('idemp', $employeeId)
        ->update(['isActive' => $isActive]);
        return $affected;
    }


    public function updateMapping(Request $request)
    {
        $request->validate([
            'structural'            => ['required', 'gt:0'],
            'workPosition'          => ['required', 'gt:0'],
            'OrgStructureOption'    => ['required', 'gt:0'],
            'gajiPokok'             => ['required', 'gte:0'],
            'uangHarian'            => ['required', 'gte:0'],
            'tanggalBerlaku'        => ['required', 'before_or_equal:today'],
            'uangLembur'            => ['required', 'gte:0']
        ]);

        $dataOrgStructure = [
            'idemp'                 => $request->empid,
            'idorgstructure'        => $request->OrgStructureOption,
            'gajipokok'             => $request->gajiPokok,
            'uangharian'            => $request->uangHarian,
            'isactive'              => 1,
            'uanglembur'            => $request->uangLembur,
            'updatedBy'             => Session()->get('employeeId')
        ];
        $this->employee->userMappingUpdate($request->empid, $request->nip, $request->name, $dataOrgStructure, $request->mappingid, $request->tanggalBerlaku, $request->oldOrgStructureOption);

        return redirect('employeeList')
        ->with('status','Data Karyawan berhasil diubah.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        //
    }


    public function getAllEmployees($isChecked, $empType){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik', 
            'e.phone as phone',
            DB::raw('
                (CASE WHEN e.gender="1" THEN "L" WHEN e.gender="2" THEN "P" END) AS gender
                '),
            'e.nip as nip', 
            'u.username as username', 
            'e.isActive as isActive', 
            'e.startDate as startDate',
            'al.name as accessLevel',
            'startDate as tanggalMulai',
            'endDate as tanggalAkhir',

/*
                        DB::raw('concat(
                TIMESTAMPDIFF(YEAR, startdate, curdate()), 
                " Tahun + ",
                (TIMESTAMPDIFF(MONTH, startdate, curdate()) - (TIMESTAMPDIFF(YEAR, startdate, curdate()) * 12)), 
                " Bulan") as lamaKerja'),
*/


            DB::raw('
                concat(
                TIMESTAMPDIFF(YEAR, startDate, IFNULL(curdate(), endDate)), 
                "Y + ",
                (TIMESTAMPDIFF(MONTH,startDate,IFNULL(curdate(), endDate))-(
                TIMESTAMPDIFF(YEAR,startDate,IFNULL(curdate(), endDate)) * 12)), 
                "M")
                as lamaKerja'),
            DB::raw('
                (CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian
                ')
        )
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('access_levels as al', 'al.level', '=', 'u.accessLevel');
        
        if ($isChecked == 0){ 
            $query->where("e.isActive","=",0);
        } else if ($isChecked == 1){
            $query->where("e.isActive","=",1);
        } 
        if ($empType == 0){ 
        } else if ($empType == 1){
            $query->where("e.employmentStatus","=",1);
        } else if ($empType == 2){
            $query->where("e.employmentStatus","=",2);
        } else if ($empType == 3){
            $query->where("e.employmentStatus","=",3);
        } 
        $query->orderBy('u.name');

        return datatables()->of($query)
        ->addColumn('statusKepegawaian', function ($row) {
            $html = '';
            if ($row->isActive==1){
                $html.='<i class="far fa-check-square" style="font-size:20px" data-toggle="tooltip" data-placement="top" data-container="body" title="Aktif"></i>';
            } else if ($row->isActive==0){
                $html.='<i class="far fa-times-circle" style="font-size:20px" data-toggle="tooltip" data-placement="top" data-container="body" title="Non-Aktif"></i>';
            }
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html = '';
            if($row->isActive == 1){
                $html = '            
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Pegawai" onclick="editEmployee('."'".$row->id."'".')">
                <i class="fa fa-edit"></i>
                </button>
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Surat-Surat Pegawai'."'".$row->id."'".'" onclick="employeePaperList('."'".$row->id."'".')">
                <i class="fas fa-envelope"></i>
                </button>            

                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Penempatan" onclick="editPemetaan('."'".$row->id."'".')">
                <i class="fa fa-address-card"></i>
                </button>            
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="History Penempatan" onclick="historyPemetaan('."'".$row->id."'".')">
                <i class="fa fa-list"></i>
                </button>            
                ';      
                if (Auth::user()->accessLevel <= 30){
                    $html .= '
                    <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="EID '.$row->id.'" onclick="editPassword('."'".$row->id."'".')">
                    <i class="fa fa-key"></i>
                    </button>            
                    ';                            
                }
            } else{
                $html = '            
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Pegawai" onclick="editEmployee('."'".$row->id."'".')">
                <i class="fa fa-edit"></i>
                </button>
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Surat-Surat Pegawai'."'".$row->id."'".'" onclick="employeePaperList('."'".$row->id."'".')">
                <i class="fas fa-envelope"></i>
                </button>            
                ';                      
            }      
            return $html;
        })
        ->rawColumns(['statusKepegawaian', 'action'])
        ->addIndexColumn()->toJson();

        //return datatables()->of($query)->toJson();

    }

    public function getEmployeesBarcode(){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik', 
            'e.phone as phone',
            DB::raw('
                (CASE WHEN e.gender="1" THEN "L" WHEN e.gender="2" THEN "P" END) AS gender
                '),
            'e.nip as nip', 
            'u.username as username', 
            'e.isActive as isActive', 
            'e.startDate as startDate',
            'al.name as accessLevel',
            DB::raw('
                (CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian
                ')
        )
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('access_levels as al', 'al.level', '=', 'u.accessLevel')
        ->where('isActive', '=', 1)
        ->orderBy('u.name');
        $query->get();


        return datatables()->of($query)
        ->addColumn('nipBarcode', function ($row) {
            $html = '';
            if ($row->isActive==1){
                $html.='<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($row->nip, 'C128',1,33,array(1,1,1), true) . '" alt="barcode"   />';
            }
            return $html;
        })



        ->rawColumns(['action', 'nipBarcode'])
        ->addIndexColumn()->toJson();
    }

    public function getAllActiveEmployees(){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.phone as phone',
            DB::raw('
                (CASE WHEN e.gender="1" THEN "L" WHEN e.gender="2" THEN "P" END) AS gender
                '),
            'e.nip as nip', 
            DB::raw('
                (CASE WHEN e.isActive="0" THEN "Non-Aktif" WHEN e.isActive="1" THEN "Aktif" END) AS statusKepegawaian
                '),
            DB::raw('
                (CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian
                ')
        )
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('access_levels as al', 'al.level', '=', 'u.accessLevel')
        ->where('isActive', '=', '1')
        ->orderBy('u.name');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Buat Surat" onclick="buatSurat('."'".$row->id."'".')">
            <i class="fa fa-edit"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Surat yang pernah dibuat" onclick="employeePaperList('."'".$row->id."'".')">
            <i class="fa fa-list"></i>
            </button>            
            ';            
            return $html;
        })->addIndexColumn()->toJson();
    }

    public function historyMapping(Employee $employee)
    {
        return view('employee.employeeMappingHistory');
    }

}
