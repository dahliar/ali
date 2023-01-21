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
    public function indexAllSurat()
    {
        return view('administration.administrasiAllSurat');
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

        ->where('mapping.isactive', '=', 1)
        ->where('e.id', '=', $employeeId)
        ->orderBy('u.name')
        ->get();
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
        
        switch($request->paper){
            case "1":
            $paperwork = self::suratKeteranganKerja($request->employeeId, $request->paper);
            return redirect('administrasi')
            ->with('status','Surat berhasil dibuat');
            break;

            case "2":
            return redirect()->route('administrasiFormSuratPeringatan', [
                'employeeId'    => $request->employeeId,
                'name'          => $request->name,
                'nip'           => $request->nip,
                'jabatan'       => $request->jabatan,
                'orgStructure'  => $request->orgstructure,
                'workPosition'  => $request->workPosition,
                'paper'         => $request->paper
            ]);
            break;
        }
    }

    public function formSuratPeringatan(Request $request){
        //dd($request);
        $employeeId = $request->employeeId;
        $name = $request->name;
        $nip    = $request->nip;
        $jabatan= $request->jabatan;
        $orgStructure = $request->orgStructure;
        $workPosition = $request->workPosition;
        $paper = $request->paper;
        return view('administration.administrasiSuratPeringatan', compact('employeeId', 'name', 'nip', 'jabatan', 'orgStructure', 'workPosition', 'paper'));

    }

    public function suratKeteranganKerja($employeeId, $paper){
        $papers = DB::table('paperwork_types')
        ->select('masaBerlaku')
        ->where('isActive', 1)
        ->where('id', $paper)
        ->first();

        $now    = Carbon::now();
        $until  = Carbon::now()->addMonths($papers->masaBerlaku);

        $paper = [
            'employeeId'        => $employeeId,
            'paperworkTypeId'   => $paper,
            'name'              => "Surat Keterangan Kerja",
            'startdate'         => $now,
            'enddate'           => $until
        ];
        $paperworkId = DB::table('paperworks')->insertGetId($paper);
        $paperworkNum = self::generatePaperNumber($paperworkId, "SKK");
        $paperwork = self::cetakSuratKeterangan($employeeId, $paperworkId, $paperworkNum);
        return view('administration.administrasiList');
    }
    public function getAllPapers(){
        $query = DB::table('paperworks as p')
        ->select(
            'p.id as id', 
            'p.name as name',
            'u.name as empName',
            'e.nik as empNik',
            'p.startdate as startdate',
            'p.enddate as enddate',
            'p.filepath as filename',
            DB::raw('concat(
                (TIMESTAMPDIFF(DAY, curdate(), enddate)), 
                " hari") as hariMasaBerlaku'),
        )
        ->join('paperwork_types as pt', 'pt.id', '=', 'p.paperworkTypeId')
        ->join('employees as e', 'p.employeeId', '=', 'e.id')
        ->join('users as u', 'e.userid', '=', 'u.id')
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
    public function getAllEmployeePaper($employeeId){
        $query = DB::table('paperworks as p')
        ->select(
            'p.id as id', 
            'p.name as name',
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


    public function cetakSuratKeterangan($employeeId, $paperworkId, $paperworkNum)
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



        $pdf = PDF::loadView('administration.suratKeterangan', compact('employee', 'paperworkNum'));
        $filename = 'Surat Keterangan Bekerja '.$employee->name.' '.Carbon::now()->format('Ymd His').'.pdf';

        $filepath = storage_path('/app/paperworks/'.$filename);
        $pdf->save($filepath);

        $affected = DB::table('paperworks')
        ->where('id', $paperworkId)
        ->update(['filepath' => $filename]);

        return true;
    }

    public function suratPeringatan(Request $request)
    {        
        $request->validate([
            'employeeId'    => [
                'required', 
                Rule::exists('employees', 'id')->where('id', $request->employeeId)
            ],
            'reason'         => ['required'],
            'publishDate'    => ['required', 'date', 'before_or_equal:today']
        ],
        [
            'reason.*'  => 'Alasan wajib diisi',
            'publishDate.*'  => 'Tanggal terbit harus sebelum hari ini',
        ]);


        $papers = DB::table('paperwork_types')
        ->select('masaBerlaku')
        ->where('isActive', 1)
        ->where('id', $request->paper)
        ->first();

        $startdate = Carbon::createFromFormat('Y-m-d', $request->publishDate);  
        $until = Carbon::createFromFormat('Y-m-d', $request->publishDate)->addMonths($papers->masaBerlaku);
        $past = Carbon::createFromFormat('Y-m-d', $request->publishDate)->addMonths(-12);


        $warningNumber = DB::table('paperwork_warning as pw')
        //->select(DB::raw('max(pw.warningNumber) as warningNumber'))
        ->join('paperworks as p', 'pw.paperworkId', '=', 'p.id')
        ->where('p.employeeId', '=', $request->employeeId)
        ->whereBetween('pw.publishDate', [$past." 00:00:00", $startdate." 23:59:59"])
        ->max('pw.warningNumber');

        if ($warningNumber>0){
            $num = $warningNumber+1;
        } else {
            $num=1;
        }
        
        $paper = [
            'employeeId'        => $request->employeeId,
            'paperworkTypeId'   => $request->paper,
            'name'              => "Surat Peringatan ".$num,
            'startdate'         => $startdate." 00:00:00",
            'enddate'           => $until." 23:59:59"
        ];
        $paperworkId = DB::table('paperworks')->insertGetId($paper);

        $paperworkNum = self::generatePaperNumber($paperworkId, "SKP");


        $warning = [
            'paperworkId'       => $paperworkId,
            'publishDate'       => $request->publishDate,
            'warningNumber'     => $num,
            'reason'            => $request->reason,
            'skorsingTanggal'   => $request->skorsingTanggal,
            'skorsingDenda'     => $request->skorsingDenda
        ];
        $pw = DB::table('paperwork_warning')->insertGetId($warning);

        $employeeId = $request->employeeId;
        $name = $request->name;
        $nip    = $request->nip;
        $jabatan= $request->jabatan;
        $orgStructure = $request->orgStructure;
        $workPosition = $request->workPosition;
        $paper = $request->paper;
        $reason = $request->reason;
        $skorsingDenda = $request->skorsingDenda;
        $skorsingTanggal = $request->skorsingTanggal;

        $pdf = PDF::loadView('administration.suratPeringatan', compact('employeeId', 'name', 'nip', 'jabatan', 'orgStructure', 'workPosition', 'num', 'startdate', 'until','reason', 'skorsingDenda', 'skorsingTanggal', 'paperworkNum'));
        $filename = 'Surat Peringatan ke '.$num.' - '.$name.' '.Carbon::now()->format('Ymd His').'.pdf';

        $filepath = storage_path('/app/paperworks/'.$filename);
        $pdf->save($filepath);

        $affected = DB::table('paperworks')
        ->where('id', $paperworkId)
        ->update(['filepath' => $filename]);

        return view('administration.administrasiList');
    }
    public function getAdministrationFileDownload($filename){
        $filepath = storage_path('/app/paperworks/'. $filename);
        $headers = ['Content-Type: application/pdf'];
        return \Response::download($filepath, $filename, $headers);
    }


    public function generatePaperNumber($paperworkId, $paperworkType){
        /*  format nomor surat
        *   Surat Keterangan    No. 123/ALI-ADM/SKT/Bulan/Tahun
        *   Surat Peringatan    No. 123/ALI-ADM/SKP/Bulan/Tahun
        *   Surat Keputusan     No. 123/ALI-ADM/SKK/Bulan/Tahun
        */ 
        $bagian="ALI-ADM";
        $month = date('m');
        $year = date('Y');
        $isActive=1;

        $result = DB::table('paperwork_numbers as pn')
        ->where('year', $year)
        ->max('paperworkNumber');

        if ($result>0){
            $paperworkNumber=$result+1;
        }
        else{
            $paperworkNumber=1;
        }

        $data = [
            'paperworkNumber'=>$paperworkNumber,
            'paperworkId'=>$paperworkId,
            'month'=>$month,
            'year'=>$year
        ];
        $paperworkNumberId = DB::table('paperwork_numbers')->insertGetId($data);
        $pnum = $paperworkNumber.'/'.$bagian.'/'.$paperworkType.'/'.$month.'/'.$year;

        $affected = DB::table('paperworks')
        ->where('id', $paperworkId)
        ->update([
            'paperworkNumber'       => $paperworkNumber,
            'paperworkNumberFull'   => $pnum,
        ]);

        //DB::table('document_numbers')->insert($data);
        return $pnum;



    }

}
