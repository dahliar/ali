<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Exports\EmployeePresenceExport;
use App\Imports\EmployeePresenceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

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
        return view('presence.presenceHarianList');
    }
    public function indexScanMasuk()
    {
        return view('presence.presenceHarianScanMasuk');
    }
    public function indexScanKeluar()
    {
        return view('presence.presenceHarianScanKeluar');
    }
    public function createImport()
    {
        return view('presence.presenceHarianImport');
    }

    public function presenceHarianHistory()
    {
        return view('presence.presenceHarianHistory');
    }
    public function presenceHarianEdit(Presence $presence)
    {
        $employee = DB::table('users as u')
        ->select(
            'u.name as nama', 
            'e.nip as nip',
            'os.name as orgStructure',
            'wp.name as bagian',
            DB::raw('(CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenis') 

        )
        ->join('employees as e', 'e.userid', '=', 'u.id')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('e.id','=', $presence->employeeId)
        ->where('mapping.isActive', '1')
        ->first();


        $dailysalaries = DB::table('dailysalaries')
        ->where('employeeId', $presence->employeeId)
        ->where('presenceDate', Carbon::parse($presence->start)->toDateString())
        ->first();

        return view('presence.presenceHarianEdit', compact('dailysalaries','presence', 'employee'));
    }
    public function presenceHarianUpdate(Request $request)
    {
        $request->validate(
            [
                'isGenerated'       => 'required|lt:1',
                'progressStatus'    => 'required|gt:0',
                'start'             => 'required|date|before_or_equal:tomorrow',
                'end'               => 'required|date|after_or_equal:start'
            ],[
                'isGenerated.lt'    => 'Data sudah digenerate, tidak bisa diubah',
                'progressStatus.*'  => 'Pilih salah satu jenis perubahan',
                'start.*'           => 'Jam masuk wajib diisi dan tidak boleh lebih dari hari ini',
                'end.*'             => 'Jam keluar wajib diisi, tidak boleh lebih dari hari ini, dan tidak boleh kurang dari jam masuk',
            ]
        );

        $deleted = DB::table('presences')
        ->where('id', '=', $request->presenceId)
        ->delete();
        $deleted = DB::table('dailysalaries')
        ->where('id', '=', $request->dailysalariesid)
        ->delete();
        $message="Data berhasil dihapus";
        if($request->progressStatus == 1){
            $this->presence->storePresenceHarianEmployee($request->empid, $request->start, $request->end, $request->lembur,1);
            $message="Data berhasil diubah";
        }

        return redirect('employeePresenceHarianHistory/'.$request->empid)->with('status', $message);
    }


    public function employeePresenceHarianHistory(Employee $employee)
    {
        $employeeId = $employee->id;
        $employeeName = DB::table('users')
        ->select('name as name')
        ->where('id','=', $employee->userid)->first()->name;

        return view('presence.employeePresenceHarianHistory', compact('employeeId', 'employeeName'));
    }


    public function getEmployeePresenceHarianHistory($employeeId, $start, $end){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'p.id as pid',
            'ds.uangHarian as uangHarian',
            'ds.uangLembur as uangLembur',
            'u.name as name', 
            'p.start as start',
            'p.end as end',
            'p.jamKerja as jamKerja',
            'p.jamLembur as jamLembur',
            'ds.isGenerated as isGenerated',
        )
        ->join('dailysalaries as ds', 'e.id', '=', 'ds.employeeId')
        ->join('presences as p', 'ds.employeeId', '=', 'p.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->where('e.employmentStatus', '!=', '3')
        ->where('e.id', $employeeId)
        ->whereBetween('p.start', [$start." 00:00:00", $end." 23:59:59"])
        ->whereBetween('ds.presenceDate', [$start, $end])
        ->orderBy('p.start');
        $query->get();

        return datatables()->of($query)
        ->addColumn('tanggal', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-3">Start </span>
            <span class="col-9 text-end">'.$row->start.'</span>
            </div>

            <div class="row form-group">
            <span class="col-3">End</span>
            <span class="col-9 text-end">'.$row->end.'</span>
            </div>';
            return $html;
        })
        ->addColumn('salary', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-5">Uang Harian</span>
            <span class="col-7 text-end">'.number_format($row->uangHarian, 2).'</span>
            </div>

            <div class="row form-group">
            <span class="col-5">Uang Lembur</span>
            <span class="col-7 text-end">'.number_format($row->uangLembur, 2).'</span>
            </div>';
            return $html;
        })
        ->addColumn('speciesName', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-3">Kerja</span>
            <span class="col-9 text-end">'.number_format($row->jamKerja, 2).'</span>
            </div>

            <div class="row form-group">
            <span class="col-3">Lembur</span>
            <span class="col-9 text-end">'.number_format($row->jamLembur, 2).'</span>
            </div>';
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->isGenerated == 0){
                if (Auth::user()->accessLevel <= 40){
                    $html .= '<button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Ubah Presensi" onclick="editPresence('."'".$row->pid."'".')">
                    <i class="fa fa-edit" style="font-size:20px"></i>
                    </button>';
                }
                return $html;
            }
        })
        ->addColumn('isGenerated', function ($row) {
            $html='<i class="fas fa-check-circle"></i>';
            if ($row->isGenerated == 0){
                $html = '<i class="fas fa-times-circle"></i>';
            }
            return $html;
        })
        ->rawColumns(['salary', 'action', 'jam', 'tanggal', 'posisi', 'isGenerated'])
        ->addIndexColumn()->toJson();
    }    


    public function getPresenceHarianHistory($start, $end){
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
            DB::raw('(CASE WHEN p.shift="1" THEN "Pagi" WHEN p.shift="2" THEN "Siang" WHEN p.shift="3" THEN "Malam" END) AS shift')
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
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Arsip Presensi" onclick="presenceHistory('."'".$row->id."'".')">
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
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Arsip Presensi" onclick="presenceHistory('."'".$row->id."'".')">
            <i class="fa fa-save" style="font-size:20px"></i>
            </button>';
            return $html;
        })->addIndexColumn()->toJson();
    }

    //Untuk datatable di halaman presensi satuan
    public function getPresenceHarianEmployees(){
        $presenceDate = Carbon::now()->toDateString();
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            DB::raw('(CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian'), 
            DB::raw('(STR_TO_DATE(p.start,"%Y-%m-%d")) as presenceToday'),
            'os.name as orgStructure',
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
        ->where('e.isActive', '=','1')
        ->where('mapping.isActive', '1')
        ->orderBy('u.name');

        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '';
            /*
            if (is_null($row->presenceToday)){
                $html.='
                <button type="button" class="btn" onclick="presenceForTodayModal('."'".$row->id."'".')" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Presensi '.$row->name.' Hari ini">
                <i class="fa fa-check" style="font-size:20px"></i>
                </button>
                ';
            }
            */
            $html.='<button type="button" class="btn" onclick="presenceForTodayModal('."'".$row->id."'".')" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Presensi '.$row->name.' Hari ini">
            <i class="fa fa-check" style="font-size:20px"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Arsip Presensi '.$row->name.'" onclick="employeePresenceHarianHistory('."'".$row->id."'".')">
            <i class="fa fa-history" style="font-size:20px"></i>
            </button>';
            return $html;
        })->addIndexColumn()->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function createForm()
    {
        return view('presence.presenceAddForm');
    }
    public function excelPresenceHarianFileGenerator($presenceDate)
    {
        return Excel::download(new EmployeePresenceExport($presenceDate), 'Presensi Harian '.$presenceDate.'.xlsx');
    }
    
    public function storePresenceHarianEmployee(Request $request)
    {
        $retValue = $this->presence->storePresenceHarianEmployee($request->empidModal, $request->start, $request->end, $request->lembur, $request->shift);
        return $retValue;
    }
    public function presenceHarianImportStore(Request $request)
    {
        $request->validate(
            [
                'presenceFile' => 'required|mimes:xlsx',
            ]
        );

        $lembur=0;
        if ($request->has('isLembur')) {
            $lembur=1;
        }
        $import = new EmployeePresenceImport($lembur);
        Excel::import($import, $request->presenceFile);

        $message = $import->getImportResult();
        return redirect('presenceHarianHistory')->with('status', $message);
    }

    public function submitPresensiMasukKartuPegawai(Request $request){
        $start = Carbon::now();
        $nip = $request->barcode;
        $shift=1;

        $dateAcuan = $start->toDateString();
        if ($start->lte(Carbon::parse($dateAcuan." 16:00:00"))) {
            $shift=1;
        }
        else{
            $shift=2;
        }

        
        $cekPresenceExist = DB::table('presences as p')
        ->select('start', 'name')
        ->where('e.nip', '=', $request->barcode)
        ->whereDate('p.start', '=', $start->toDateString())
        ->join('employees as e', 'e.id', '=', 'p.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->first();

        if (is_null($cekPresenceExist)){
            $query = DB::table('employees as e')
            ->select('e.id as empid','e.isActive as isActive','u.name as name','e.nip as nip','sp.name as spname','wp.name as wpname','os.name as osname')
            ->where('mapping.isactive', 1)
            ->where('e.nip', '=', $request->barcode)
            ->join('users as u', 'u.id', '=', 'e.userid')
            ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
            ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
            ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
            ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
            ->first();
            $retValue="";
            if ($query){
                if ($query->isActive==1){
                    $presence = new Presence();
                    $presence->employeeId   = $query->empid;
                    $presence->start        = $start;
                    $presence->shift        = $shift;
                    $presence->save();
                    $retValue = [
                        'message'       => $query->name." : ".$query->osname."<br>".$start,
                        'isError'       => "1"
                    ];
                } else {
                    $retValue = [
                        'message'       => "Status karyawan ".$query->name." tidak aktif",
                        'isError'       => "0"
                    ];
                }

            } else {
                $retValue = [
                    'message'       => "Ada kesalahan, barcode tidak ditemukan atau status karyawan tidak aktif",
                    'isError'       => "0"
                ];
            }
        } else{
            $retValue = [
                'message'       => "Presensi ".$cekPresenceExist->name." sudah dilakukan pada ".$cekPresenceExist->start,
                'isError'       => "0"
            ];            
        }
        return $retValue;        
    }
    public function submitPresensiKeluarKartuPegawai(Request $request){
        $end = Carbon::now();
        $nip = $request->barcode;

        $cekBarcode = DB::table('employees as e')
        ->select('name', 'e.isActive as isActive')
        ->where('e.nip', '=', $request->barcode)
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->first();

        if ($cekBarcode){
            if($cekBarcode->isActive == 1){
                $cekPresenceExist = DB::table('presences as p')
                ->select('p.id as presenceId','p.start as start','p.end as end','e.isActive as isActive','u.name as name','e.nip as nip', 'e.id as empid', 'p.shift as shift', 'p.status as status')
                ->where('e.nip', '=', $request->barcode)
                ->whereDate('p.start', '=', $end->toDateString())
                ->join('employees as e', 'e.id', '=', 'p.employeeId')
                ->join('users as u', 'u.id', '=', 'e.userid')
                ->orderBy('p.start', 'desc')
                ->first();

                if (!is_null($cekPresenceExist)){
                    $retValue="";
                    if ($cekPresenceExist->isActive==1){
                        if ($cekPresenceExist->status==1){
                            $this->presence->simpanPresenceScan($cekPresenceExist->empid, Carbon::parse($cekPresenceExist->start), $end, $cekPresenceExist->presenceId, $cekPresenceExist->shift);
                            $retValue = [
                                'message'       => $cekPresenceExist->name."<br>"."Presensi Keluar ".$end,
                                'isError'       => "1"
                            ];
                        }
                        else{
                            $retValue = [
                                'message'       => $cekPresenceExist->name." telah presensi Keluar pada ".$cekPresenceExist->end,
                                'isError'       => "0"
                            ];
                        }
                    } else {
                        $retValue = [
                            'message'       => "Status karyawan ".$cekPresenceExist->name." tidak aktif",
                            'isError'       => "0"
                        ];
                    }

                } 
                else{
                    $retValue = [
                        'message'       => "Presensi masuk ".$cekPresenceExist->name." tanggal ".$start->toDateString()." tidak ditemukan",
                        'isError'       => "0"
                    ];            
                }
            } else{
                $retValue = [
                    'message'       => $cekBarcode->name."<br> Status karyawan tidak aktif",
                    'isError'       => "0"
                ];     
            }
        }else{
            $retValue = [
                'message'       => "Barcode tidak ditemukan",
                'isError'       => "0"
            ];         
        }
        return $retValue;     
    }

}
