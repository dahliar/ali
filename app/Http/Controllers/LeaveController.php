<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;
use Carbon\Carbon;
use App\Models\Leave;


class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('leave.leaveList');
    }
    public function ajukanCuti($empid)
    {
        $leaveTypes = DB::table('leave_types')
        ->where('isActive', 1)
        ->orderBy('name')
        ->get();
        $employee = self::getEmployeeData($empid);
        return view('leave.leaveForm', compact('employee', 'leaveTypes'));
    }
    public function update(Request $request)
    {
        $leave = Leave::find($request->id);
        $leave->isApproved = $request->status;
        $leave->save();

        return true;
    }
    public function store(Request $request)
    {
        $request->validate([
            'employeeId'    => [
                'required', 
                Rule::exists('employees', 'id')->where('id', $request->employeeId)
            ],
            'alasan'         => ['required'],
            'jumlahHari'         => ['required', 'gt:0'],
            'alamatCuti'         => ['required'],
            'startDate'    => ['required', 'date', 'after_or_equal:today'],
            'endDate'    => ['required', 'date', 'after_or_equal:startDate']
        ],
        [
            'alasan.*'  => 'Alasan wajib diisi',
            'alamatCuti.*'  => 'Alamat cuti wajib diisi',
            'startDate.*'  => 'Awal adalah hari ini atau setelahnya',
            'endDate.*'  => 'Akhir harus setelah tanggal awal',
        ]);

        $leave = new Leave;
        $leave->employeeId = $request->employeeId;
        $leave->startDate = $request->startDate;
        $leave->endDate = $request->endDate;
        $leave->jumlahHari = $request->jumlahHari;
        $leave->alasan = $request->alasan;
        $leave->isApproved = 0;
        $leave->alamat = $request->alamatCuti;
        $leave->save();

        return redirect('cuti')
        ->with('status','Cuti berhasil diajukan.');
    }
    public function view($empid)
    {
        $employee = self::getEmployeeData($empid);
        return view('leave.leaveHistory', compact('employee'));
    }
    public function dateCounterChecker(Request $request)
    {
        $dt1 = Carbon::create($request->startDate)->startOfDay();
        $dt2 = Carbon::create($request->endDate)->endOfDay();


        $holidays = DB::table('leave_holidays')
        ->whereBetween('dateActive',[$dt1, $dt2])
        ->count();
        $daysForExtraCoding = $dt1->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isSaturday();
        }, $dt2);
        return ($daysForExtraCoding-$holidays);
    }

    public function dateOverlapExist(Request $request)
    {
        $begin = $request->startDate;
        $end = $request->endDate;
        $overlap = Leave::where('employeeId', $request->employeeId)
        ->where(function ($query) use ($begin, $end) {
            $query->where(function ($q) use ($begin, $end) {
                $q->where('startDate', '>=', $begin)
                ->where('startDate', '<=', $end);
            })->orWhere(function ($q) use ($begin, $end) {
                $q->where('startDate', '<=', $begin)
                ->where('endDate', '>=', $end);
            })->orWhere(function ($q) use ($begin, $end) {
                $q->where('endDate', '>=', $begin)
                ->where('endDate', '<=', $end);
            })->orWhere(function ($q) use ($begin, $end) {
                $q->where('startDate', '>=', $begin)
                ->where('endDate', '<=', $end);
            });
        })
        ->whereIn('isApproved', [0,1])
        ->count();
        return $overlap;
    }

    private function getEmployeeData($employeeId){
        $employee = DB::table('employees as e')
        ->select(
            'e.id as employeeId', 
            'u.name as name', 
            'e.nik as nik', 
            'e.nip as nip', 
            'e.phone as phone',
            'e.startDate as startdate',
            DB::raw('concat(sp.name," ",CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END, " - ", os.name," - ", wp.name) as penempatan'),
            DB::raw('trim(e.address) as address'),
            DB::raw('concat(
                TIMESTAMPDIFF(YEAR, startdate, curdate()), 
                " Tahun + ",
                (TIMESTAMPDIFF(MONTH, startdate, curdate()) - (TIMESTAMPDIFF(YEAR, startdate, curdate()) * 12)), 
                " Bulan") as lamaKerja'),
            DB::raw('
                (CASE WHEN e.isActive="0" THEN "Non-Aktif" WHEN e.isActive="1" THEN "Aktif" END) AS statusKepegawaian
                ')
        )
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('access_levels as al', 'al.level', '=', 'u.accessLevel')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')

        ->where('e.id', '=', $employeeId)
        ->orderBy('u.name')
        ->first();
        return $employee;
    }

    public function getAllActiveEmployeesForLeave(){
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
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Ajukan cuti" onclick="ajukanCuti('."'".$row->id."'".')">
            <i class="fa fa-edit"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar Cuti" onclick="historyCutiPegawai('."'".$row->id."'".')">
            <i class="fa fa-list"></i>
            </button>            
            ';            
            return $html;
        })->addIndexColumn()->toJson();
    }
    public function viewEmployee($empid){
        $query = DB::table('leaves as l')
        ->select(
            'l.id as id',
            'l.employeeId as empid',
            'l.startDate as startDate',
            'l.endDate as endDate',
            'l.jumlahHari as jumlahHari',
            'l.alasan as alasan',
            'l.alamat as alamat',
            'l.isApproved as statusApprove'
        )
        ->where('employeeId', '=', $empid)
        ->orderBy('l.startDate');
        $query->get();

        return datatables()->of($query)
        ->editColumn('statusApprove', function ($row) {
            $html = '';
            if ($row->statusApprove==0){
                $html.='<i class="fas fa-question" style="font-size:20px"></i>';
            } else if ($row->statusApprove==1){
                $html.='<i class="far fa-check-square" style="font-size:20px"></i>';
            } else if ($row->statusApprove==2){
                $html.='<i class="far fa-times-circle" style="font-size:20px"></i>';
            }
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->statusApprove==0){
                $html .= '
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Approve" onclick="approval('."'".$row->id."',1".')">
                <i class="far fa-check-square"></i>
                </button>   
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Reject" onclick="approval('."'".$row->id."',2".')">
                <i class="far fa-times-circle"></i>
                </button>
                ';          
            }
            return $html;
        })
        ->rawColumns(['statusApprove', 'action'])
        ->addIndexColumn()->toJson();
    }
}
