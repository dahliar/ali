<?php

namespace App\Http\Controllers;

use App\Models\Honorarium;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\EmployeeHonorariumExport;
use App\Imports\EmployeeHonorariumImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;



use DB;
use Auth;


class HonorariumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('presence.presenceHonorariumList');
    }

    public function createImportHonorarium()
    {
        return view('presence.presenceHonorariumImport');
    }
    public function excelHonorariumFileGenerator($presenceDate)
    {
        return Excel::download(new EmployeeHonorariumExport($presenceDate), 'Honorarium tanggal '.$presenceDate.'.xlsx');
    }
    //Untuk datatable di halaman presensi satuan
    public function getPresenceHonorariumEmployees(){
        $presenceDate = Carbon::now()->toDateString();
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik',
            DB::raw('(CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian'), 
            DB::raw('(STR_TO_DATE(h.tanggalKerja,"%Y-%m-%d")) as presenceToday'),
            'os.name as orgStructure',
            'sp.name as jabatan',
            'wp.name as bagian'
        )
        ->leftJoin('honorariums as h', function($join) use ($presenceDate){
            $join->on('e.id', '=', 'h.employeeId')
            ->where(DB::raw("(STR_TO_DATE(h.tanggalKerja,'%Y-%m-%d'))"), '=', $presenceDate);
        })
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('mapping.isActive', '1');

        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '';
            if (is_null($row->presenceToday)){
                $html.='
                <button type="button" class="btn" onclick="presenceForTodayModal('."'".$row->id."'".')" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Honorarium '.$row->name.' Hari ini">
                <i class="fa fa-check" style="font-size:20px"></i>
                </button>
                ';
            }
            return $html;
        })->addIndexColumn()->toJson();
    }
    public function storePresenceHonorariumEmployee(Request $request)
    {
        $retValue="";

        $dataHonorarium = [
            'employeeId'        => $request->empid,
            'tanggalKerja'      => $request->tanggalKerja,
            'jumlah'            => $request->jumlah,
            'keterangan'        => $request->keterangan,
            'isGenerated'       => 0
        ];

        $affected = DB::table('honorariums')->insert($dataHonorarium);

        $retValue = [
            'message'       => "Data berhasil disimpan ",
            'isError'       => "0"
        ];
        return $retValue;
    }
    public function presenceHonorariumHistory()
    {
        return view('presence.presenceHonorariumHistory');
    }


    public function getPresenceHonorariumHistory($start, $end, $isGenerated){
        $query = DB::table('employees as e')
        ->select(
            'p.id as hid', 
            'u.name as name', 
            'os.name as orgStructure',
            'wp.name as bagian',
            'p.isGenerated as statusIsGenerated',
            'p.isGenerated as isGenerated',
            'p.tanggalKerja as tanggalKerja',
            'p.keterangan as keterangan',
            'p.jumlah as jumlah'
        )
        ->join('honorariums as p', 'e.id', '=', 'p.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->whereBetween('p.tanggalKerja', [$start." 00:00:00", $end." 23:59:59"])
        ->where('mapping.isActive', '1')
        ->orderBy('u.name');

        if ($isGenerated==0){
            $query = $query->where('isGenerated', '=', 0);
        } else if ($isGenerated==1){
            $query = $query->where('isGenerated', '=', 1);
        }
        $query->get();

        return datatables()->of($query)
        ->editColumn('isGenerated', function ($row) {
            $html = '';
            if ($row->isGenerated==0){
                $html.='<i class="far fa-check-square" style="font-size:20px"></i>';
            } else if ($row->isGenerated==1){
                $html.='<i class="far fa-times-circle" style="font-size:20px"></i>';
            }
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html = '';
            if ((Auth::user()->accessLevel <= 40) && ($row->statusIsGenerated==0)){
                $html.=' 
                <button type="button" class="btn btn-warning btn-sm" onclick="hapusHonorarium('."'".$row->hid."'".')" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Honorarium '.$row->name.'">
                <i class="fas fa-trash" style="font-size:20px"></i>
                </button>
                ';

            }
            return $html;
        })
        ->rawColumns(['isGenerated', 'action'])
        ->addIndexColumn()->toJson();
    }  
    function honorariumImport(Request $request){
        $request->validate(
            [
                'presenceFile' => 'required|mimes:xlsx',
            ]
        );
        $import = new EmployeeHonorariumImport();
        Excel::import($import, $request->presenceFile);
        $message = $import->getImportResult();
        return redirect('presenceHonorariumHistory')->with('status', $message);
    }  
    public function destroy($hid)
    {
        $query=DB::table('honorariums')->where('id', '=', $hid)->where('isGenerated', '=', 0);
        if ($query->exists()){
            $deleted = $query->delete();
            return $retValue = [
                'message'       => "Record telah dihapus",
                'isError'       => "0"
            ];
        } else {
            return $retValue = [
                'message'       => "Record gagal dihapus",
                'isError'       => "0"
            ];            
        }
    }
}
