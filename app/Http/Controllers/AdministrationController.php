<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;



class AdministrationController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('administration.administrasiList');
    }
    public function paperList($employeeId)
    {
        $employee = self::getEmployeeData($employeeId);
        return view('administration.administrasiDaftarSuratPegawai', compact('employee'));
    }
    public function formPilih($employeeId)
    {
        $employee = self::getEmployeeData($employeeId);

        $papers = DB::table('paperwork_types')
        ->where('isActive', 1)
        ->orderBy('name')
        ->get();
        return view('administration.administrasiFormPilihSurat', compact('papers', 'employee'));
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
            'os.name as orgstructure',
            'wp.name as workPosition',
            'sp.name as structuralPosition',
            DB::raw('trim(e.address) as address'),
            DB::raw('concat(
                TIMESTAMPDIFF(YEAR, startdate, curdate()), 
                " Tahun + ",
                (TIMESTAMPDIFF(MONTH, startdate, curdate()) - (TIMESTAMPDIFF(YEAR, startdate, curdate()) * 12)), 
                " Bulan") as lamaKerja'),
            DB::raw('
                (CASE WHEN e.isActive="0" THEN "Non-Aktif" WHEN e.isActive="1" THEN "Aktif" END) AS statusKepegawaian
                '),
            DB::raw('
                (CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian
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

    public function store(Request $request)
    {

        $request->validate([
            'employeeId'    => [
                'required', 
                Rule::exists('employees', 'id')->where('id', $request->employeeId)
            ],
            'paper'         => ['gt:0']

        ],
        [
            'paper.gt'  => 'Pilih salah satu surat yang hendak dibuat'
        ]);
        
        $papers = DB::table('paperwork_types')
        ->select('masaBerlaku')
        ->where('isActive', 1)
        ->where('id', $request->paper)
        ->first();

        $now    = Carbon::now();
        $until  = Carbon::now()->addMonths($papers->masaBerlaku);

        $paper = [
            'employeeId'        => $request->employeeId,
            'paperworkTypeId'   => $request->paper,
            'startdate'         => $now,
            'enddate'           => $until
        ];
        $id = DB::table('paperworks')->insertGetId($paper);


        $paperwork = self::cetakSuratKeterangan($request->employeeId, $id);

        return redirect('administrasi')
        ->with('status','Surat berhasil dibuat');

    }

    public function getAllEmployeePaper($employeeId){
        $query = DB::table('paperworks as p')
        ->select(
            'p.id as id', 
            'pt.name as name',
            'p.startdate as startdate',
            'p.enddate as enddate',
            'p.filepath as filename',
            DB::raw('concat(
                (TIMESTAMPDIFF(DAY, curdate(), enddate)), 
                " hari") as hariMasaBerlaku'),
        )
        ->join('paperwork_types as pt', 'pt.id', '=', 'p.paperworkTypeId')
        ->where('p.employeeId', '=', $employeeId)
        ->orderBy('p.startdate')
        ->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Tampilkan file" onclick="getFileDownload('."'".$row->filename."'".')">
            <i class="fa fa-file"></i>
            </button>            
            ';            
            return $html;
        })->addIndexColumn()->toJson();
    }


    public function cetakSuratKeterangan($employeeId, $paperworkId)
    {
        $employee = DB::table('employees as e')
        ->select(
            'e.id as employeeId', 
            'u.name as name', 
            'e.nik as nik', 
            'e.nip as nip', 
            'e.phone as phone',
            'e.startDate as startdate',
            'os.name as orgstructure',
            'wp.name as workPosition',
            'sp.name as structuralPosition',
            DB::raw('trim(e.address) as address'),
            DB::raw('concat(
                TIMESTAMPDIFF(YEAR, startdate, curdate()), 
                " Tahun dan ",
                (TIMESTAMPDIFF(MONTH, startdate, curdate()) - (TIMESTAMPDIFF(YEAR, startdate, curdate()) * 12)), 
                " Bulan") as lamaKerja'),
            DB::raw('
                (CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian
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



        $pdf = PDF::loadView('administration.suratKeterangan', compact('employee'));
        $filename = 'Surat Keterangan Bekerja '.$employee->name.' '.Carbon::now()->format('Ymd His').'.pdf';

        //$filepath = '../storage/app/paperworks/'.$filename;
        $filepath = storage_path('/app/paperworks/'.$filename);
        $pdf->save($filepath);

        $affected = DB::table('paperworks')
        ->where('id', $paperworkId)
        ->update(['filepath' => $filename]);

        return true;
    }
    public function getAdministrationFileDownload($filename){
        $filepath = storage_path('/app/paperworks/'. $filename);
        $headers = ['Content-Type: application/pdf'];
        return \Response::download($filepath, $filename, $headers);
    }

}
