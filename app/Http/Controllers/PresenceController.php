<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;


use App\Exports\EmployeePresenceExport;
use App\Imports\EmployeePresenceImport;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Auth;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->presence = new Presence();
    }

    public function index()
    {
        return view('presence.presenceList');
    }
    public function presenceHistory()
    {
        return view('presence.presenceHistoryList');
    }
    public function presenceHistoryEmployee(Employee $employee)
    {
        $employeeId = $employee->id;
        $employeeName = DB::table('users')
        ->select('name as name')
        ->where('id','=', $employee->userid)->first()->name;

        return view('presence.presenceHistoryEmployee', compact('employeeId', 'employeeName'));
    }

    
    public function getEmployeePresenceHistory($employeeId, $start, $end){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik',
            'e.nip as nip',
            'os.name as orgStructure',
            'wp.name as bagian',
            'p.start as start',
            'p.end as end',
            'p.jamKerja as jamKerja',
            'p.jamLembur as jamLembur',
            DB::raw('(CASE WHEN p.shift="1" THEN "Pagi" WHEN p.shift="2" THEN "Siang" END) AS shift')
        )
        ->join('presences as p', 'e.id', '=', 'p.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('e.employmentStatus', '!=', '3')
        ->where('e.id', $employeeId)
        ->whereBetween('p.start', [$start." 00:00:00", $end." 23:59:59"])
        ->where('mapping.isActive', '1')
        ->orderBy('p.start');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Presence History" onclick="presenceHistory('."'".$row->id."'".')">
            <i class="fa fa-save" style="font-size:20px"></i>
            </button>';
            return $html;
        })->addIndexColumn()->toJson();
    }    


    public function getPresenceHistory($start, $end){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik',
            'e.nip as nip',
            'os.name as orgStructure',
            'wp.name as bagian',
            'p.start as start',
            'p.end as end',
            'p.jamKerja as jamKerja',
            'p.jamLembur as jamLembur',
            DB::raw('(CASE WHEN p.shift="1" THEN "Pagi" WHEN p.shift="2" THEN "Siang" END) AS shift')
        )
        ->join('presences as p', 'e.id', '=', 'p.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('e.employmentStatus', '!=','3')
        ->whereBetween('p.start', [$start." 00:00:00", $end." 23:59:59"])
        ->where('mapping.isActive', '1');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Presence History" onclick="presenceHistory('."'".$row->id."'".')">
            <i class="fa fa-save" style="font-size:20px"></i>
            </button>';
            return $html;
        })->addIndexColumn()->toJson();
    }    

    public function getAllEmployeesForPresenceForm($presenceDate){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik',
            DB::raw('(CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian'), 
            DB::raw('(STR_TO_DATE(p.start,"%Y-%m-%d")) as presenceToday'),
            'os.name as orgStructure',
            'sp.name as jabatan',
            'wp.name as bagian'
        )
        ->leftJoin('presences as p', function($join) use ($presenceDate){
            $join->on('e.id', '=', 'p.employeeId')
            ->where(DB::raw("(STR_TO_DATE(p.start,'%Y-%m-%d'))"), '=', $presenceDate);

        })
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('e.employmentStatus', '!=', '3')
        ->where('mapping.isActive', '1');

        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Presence History" onclick="presenceHistory('."'".$row->id."'".')">
            <i class="fa fa-save" style="font-size:20px"></i>
            </button>';
            return $html;
        })->addIndexColumn()->toJson();
    }

    //Untuk datatable di halaman presensi satuan
    public function getAllEmployeesForPresence(){
        $presenceDate = Carbon::now()->toDateString();
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik',
            DB::raw('(CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian'), 
            DB::raw('(STR_TO_DATE(p.start,"%Y-%m-%d")) as presenceToday'),
            'os.name as orgStructure',
            'sp.name as jabatan',
            'wp.name as bagian'
        )
        ->leftJoin('presences as p', function($join) use ($presenceDate){
            $join->on('e.id', '=', 'p.employeeId')
            ->where(DB::raw("(STR_TO_DATE(p.start,'%Y-%m-%d'))"), '=', $presenceDate);
        })
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('e.employmentStatus', '!=','3')
        ->where('mapping.isActive', '1');

        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Presence History" onclick="presenceHistory('."'".$row->id."'".')">
            <i class="fa fa-history" style="font-size:20px"></i>
            </button>';
            if (is_null($row->presenceToday)){
                $html.='
                <button type="button" class="btn" onclick="presenceForTodayModal('."'".$row->id."'".')" title="Tambah Presensi Hari ini">
                <i class="fa fa-check" style="font-size:20px"></i>
                </button>
                ';
            }
            return $html;
        })->addIndexColumn()->toJson();
    }

    /*
                <button type="button" class="btn  btn-xs btn-light" data-bs-toggle="modal" data-toggle="tooltip" data-placement="top" data-container="body" data-bs-target="#exampleModal" onclick="presenceHistory('."'".$row->id."'".')" title="Tambah Presensi Hari ini" value="'.$row->id.'">
                <i class="fa fa-check" style="font-size:20px"></i>
                </button>

    */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function createForm()
    {
        return view('presence.presenceAddForm');
    }
    public function createImport()
    {
        return view('presence.presenceAddImport');
    }
    public function excelPresenceFileGenerator($presenceDate)
    {
        return Excel::download(new EmployeePresenceExport($presenceDate), 'Presensi Harian '.date('Y-m-d').'.xlsx');
    }


    
    public function storeOnePresence(Request $request)
    {
        $retValue = $this->presence->presenceTunggalHarian($request->empidModal, $request->start, $request->end);
        return $retValue;
    }
    public function presenceFileStore(Request $request)
    {
        Excel::import(new EmployeePresenceImport, $request->presenceFile);
        return redirect('presenceHistory')->with('success', 'All good!');
    }
}
