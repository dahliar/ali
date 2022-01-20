<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


use App\Models\Employee;
use App\Models\StructuralPosition;
use App\Models\WorkPosition;
use DB;
use Auth;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

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
            'name'                  => ['required', 'string', 'max:255'],
            'username'              => ['required', 'string', 'max:255', 'unique:users'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
            'role'                  => ['required', 'gt:0'],
            'email'                 => ['email'],
            'nik'                   => ['required', 'string', 'max:20', 'unique:employees'],
            'birthdate'             => ['required', 'date', 'before:today'],
            'startdate'             => ['required', 'date', 'after:birthdate', 'before:today'],
            'address'               => ['required', 'string'],
            'structural'            => ['required', 'gt:0'],
            'workPosition'          => ['required', 'gt:0'],
            'OrgStructureOption'    => ['required', 'gt:0'],
            'employmentStatus'      => ['required', 'gt:0'],
            'pendidikan'            => ['required', 'gt:0'],
            'bidangPendidikan'      => ['required', 'string'],
            'gajiPokok'             => ['required', 'integer', 'gte:0'],
            'gajiHarian'            => ['required', 'integer', 'gte:0'],
            'uangTransport'         => ['required', 'integer', 'gte:0'],
            'uangMakan'             => ['required', 'integer', 'gte:0'],
            'uangLembur'            => ['required', 'integer', 'gte:0']
        ],
        [
            'startdate.after'  => 'Tanggal mulai harus sebelum tanggal lahir',
            'startdate.before' => 'Tanggal mulai harus sebelum hari ini',
            'birthdate.before' => 'Tanggal lahir harus sebelum hari ini'
        ]);


        //bagian proses insert kedalam table Users
        
        $user = User::create([
            'name' => $request->name,
            'role' => $request->role,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);
        //event(new Registered($user));
        $userid = DB::getPdo()->lastInsertId();

        //insert ke table employees
        $employee = [
            'userid'                => $userid,
            'nik'                   => $request->nik,
            'birthdate'             => $request->birthdate,
            'startdate'             => $request->startdate,
            'address'               => $request->address,
            'employmentStatus'      => $request->employmentStatus,
            'isActive'              => 1,
            'noRekening'            => $request->noRekening,
            'bankid'                => $request->bankid,
            'jenjangPendidikan'     => $request->pendidikan,
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
            'uangtransport'     => $request->uangTransport,
            'uangmakan'         => $request->uangMakan,
            'uanglembur'        => $request->uangLembur
        ];
        $mappingId = $this->employee->orgStructureStore($mapping);

        return redirect('employeeList')
        ->with('status','Item berhasil ditambahkan.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        return view('employee.employeeView');
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


    public function editMapping(Employee $employee)
    {
        $structpos = StructuralPosition::all();
        $workpos = WorkPosition::all();
        $choosenUser = User::where('id', $employee->userid)->first();

        $orgstructure = DB::table('employees as e')
        ->select('mapping.id as id', 'mapping.idorgstructure as idorgstructure','wp.id as workPosition','sp.id as structuralPosition', 'mapping.gajipokok as gp', 'mapping.uangtransport as ut', 'mapping.uangmakan as um', 'mapping.uangharian as uh', 'mapping.uanglembur as ul')
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
        //
        $request->validate([
            'email'                 => ['email'],
            'role'                  => ['required', 'gt:0'],
            'address'               => ['required', 'string'],
            'employmentStatus'      => ['required', 'gt:0'],
            'bankid'                => ['required', 'gt:0'],
            'noRekening'            => ['required', 'gt:0'],
            'pendidikan'            => ['required', 'gt:0'],
            'bidangPendidikan'      => ['required', 'string'],
            'isactive'              => ['required', 'gt:0']
        ]);


        $this->employee->userUpdate($request->role, $request->email, $request->userid);
        $this->employee->employeeUpdate($request->address, $request->employmentStatus, $request->isActive, $request->noRekening, $request->bankid, $request->employeeId, $request->isactive, $request->pendidikan, $request->bidangPendidikan);

        return redirect('employeeList')
        ->with('status','Data Karyawan berhasil diubah.');
    }


    public function updateMapping(Request $request)
    {
        //
        $request->validate([
            'structural'            => ['required', 'gt:0'],
            'workPosition'          => ['required', 'gt:0'],
            'OrgStructureOption'    => ['required', 'gt:0'],
            'gajiPokok'             => ['required', 'gte:0'],
            'uangHarian'            => ['required', 'gte:0'],
            'uangTransport'         => ['required', 'gte:0'],
            'uangMakan'             => ['required', 'gte:0'],
            'uangLembur'            => ['required', 'gte:0']
        ]);


        //bagian proses insert kedalam table Users
        //REMEMBER, Tabel mapping itu insert baru, dan update data tstruktur lama,s et struktur lama isactive=0
        //REMEMBER, Tabel mapping itu insert baru, dan update data tstruktur lama,s et struktur lama isactive=0
        //REMEMBER, Tabel mapping itu insert baru, dan update data tstruktur lama,s et struktur lama isactive=0
        //REMEMBER, Tabel mapping itu insert baru, dan update data tstruktur lama,s et struktur lama isactive=0

        $dataOrgStructure = [
            'idemp'                 => $request->empid,
            'idorgstructure'        => $request->OrgStructureOption,
            'gajipokok'             => $request->gajiPokok,
            'uangharian'            => $request->uangHarian,
            'uangtransport'         => $request->uangTransport,
            'uangmakan'             => $request->uangMakan,
            'isactive'              => 1,
            'uanglembur'            => $request->uangLembur
        ];
        $this->employee->userMappingUpdate($dataOrgStructure, $request->mappingid);

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


    /*
    select `e`.`id` as `id`, `u`.`name` as `name`, `e`.`nik` as `nik`, `u`.`username` as `username`, `e`.`employmentStatus` as `jenisPenggajian`, `e`.`startDate` as `startDate`, `e`.`startDate` as `lamaKerja`, (CASE WHEN e.isActive='0' THEN 'Non-Aktif' WHEN e.isActive='1' THEN 'Aktif' END) AS statusKepegawaian from `employees` as `e` inner join `users` as `u` on `u`.`id` = `e`.`userid`
    */
    public function getAllEmployees(){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik', 
            'u.username as username', 
            'e.startDate as startDate',
            DB::raw('concat(
                TIMESTAMPDIFF(YEAR, startDate, curdate()), 
                " tahun + ",
                (TIMESTAMPDIFF(MONTH, startDate, curdate()) - (TIMESTAMPDIFF(YEAR, startDate, curdate()) * 12)), 
                " bulan") as lamaKerja'),
            DB::raw('
                (CASE WHEN e.isActive="0" THEN "Non-Aktif" WHEN e.isActive="1" THEN "Aktif" END) AS statusKepegawaian
                '),
            DB::raw('
                (CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="1" THEN "Harian" END) AS jenisPenggajian
                ')
        )
        ->join('users as u', 'u.id', '=', 'e.userid');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Pegawai" onclick="editEmployee('."'".$row->id."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Penempatan" onclick="editPemetaan('."'".$row->id."'".')">
            <i class="fa fa-address-card" style="font-size:20px"></i>
            </button>
            ';            

            return $html;
        })->addIndexColumn()->toJson();
    }
}
