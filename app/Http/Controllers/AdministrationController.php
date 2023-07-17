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

        $paperworkTypes = DB::table('paperwork_types')
        ->where('isActive', 1)
        ->where('showOnOption', 1)
        ->orderBy('name')
        ->get();
        return view('administration.administrasiFormPilihSurat', compact('paperworkTypes', 'employee'));
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
            'paperworkTypeId'         => ['required']
        ],
        [
            'paperworkTypeId'  => 'Pilih salah satu surat yang hendak dibuat'
        ]);

        $paperwork = DB::table('paperwork_types')
        ->where('isActive', 1)
        ->where('id', $request->paperworkTypeId)
        ->first();

        switch($paperwork->kode){
            case "SKB":   //Surat Keterangan bekerja
            $paperwork = self::suratKeteranganKerja($request->employeeId, $paperwork->id, $paperwork->kode, $paperwork->masaBerlaku);
            return redirect('administrasi')->with('status','Surat berhasil dibuat');
            break;

            case "SPP":   //surat peringatan
            return redirect()->route('administrasiFormSuratPeringatan', [
                'employeeId'        => $request->employeeId,
                'name'              => $request->name,
                'nip'               => $request->nip,
                'jabatan'           => $request->jabatan,
                'orgStructure'      => $request->orgstructure,
                'workPosition'      => $request->workPosition,
                'paperworkTypeId'   => $paperwork->id,
                'masaBerlaku'       => $paperwork->masaBerlaku
            ]);
            break;

            case "PHK":   //Surat Pemutusan Hubungan Kerja
            return redirect()->route('administrasiFormSuratPHK', [
                'employeeId'        => $request->employeeId,
                'name'              => $request->name,
                'nip'               => $request->nip,
                'jabatan'           => $request->jabatan,
                'orgStructure'      => $request->orgstructure,
                'workPosition'      => $request->workPosition,
                'paperworkTypeId'   => $paperwork->id,
                'masaBerlaku'       => $paperwork->masaBerlaku
            ]);
            break;
            case "SDP":   //Surat Dinas Perusahaan
            return redirect()->route('administrasiFormSuratPerjalanan', [
                'employeeId'        => $request->employeeId,
                'name'              => $request->name,
                'nip'               => $request->nip,
                'nik'               => $request->nik,
                'jabatan'           => $request->jabatan,
                'orgStructure'      => $request->orgstructure,
                'workPosition'      => $request->workPosition,
                'paperworkTypeId'   => $paperwork->id,
                'masaBerlaku'       => $paperwork->masaBerlaku
            ]);
            break;
        }
    }

    public function formSuratPeringatan(Request $request){
        $employeeId = $request->employeeId;
        $name = $request->name;
        $nip    = $request->nip;
        $jabatan= $request->jabatan;
        $orgStructure = $request->orgStructure;
        $workPosition = $request->workPosition;
        $paperworkTypeId = $request->paperworkTypeId;
        $masaBerlaku = $request->masaBerlaku;
        return view('administration.administrasiSuratPeringatan', compact('employeeId', 'name', 'nip', 'jabatan', 'orgStructure', 'workPosition', 'paperworkTypeId', 'masaBerlaku'));
    }

    public function formSuratPHK(Request $request){
        $employeeId = $request->employeeId;
        $name = $request->name;
        $nip    = $request->nip;
        $jabatan= $request->jabatan;
        $orgStructure = $request->orgStructure;
        $workPosition = $request->workPosition;
        $paperworkTypeId = $request->paperworkTypeId;
        $masaBerlaku = $request->masaBerlaku;
        return view('administration.administrasiFormSuratPHK', compact('employeeId', 'name', 'nip', 'jabatan', 'orgStructure', 'workPosition', 'paperworkTypeId', 'masaBerlaku'));
    }
    public function formSuratPerjalananDinas(Request $request){
        $employeeId = $request->employeeId;
        $name = $request->name;
        $nip    = $request->nip;
        $nik    = $request->nik;
        $jabatan= $request->jabatan;
        $orgStructure = $request->orgStructure;
        $workPosition = $request->workPosition;
        $paperworkTypeId = $request->paperworkTypeId;
        $masaBerlaku = $request->masaBerlaku;
        return view('administration.administrasiFormSuratPerjalanan', compact('employeeId', 'name', 'nip', 'nik', 'jabatan', 'orgStructure', 'workPosition', 'paperworkTypeId', 'masaBerlaku'));
    }

    public function suratKeteranganKerja($employeeId, $paperworkTypeId, $paperCode, $masaBerlaku){
        $now    = Carbon::now();
        $until  = Carbon::now()->addMonths($masaBerlaku);

        $paper = [
            'employeeId'        => $employeeId,
            'paperworkTypeId'   => $paperworkTypeId,
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
        $filename = 'Surat Keterangan Bekerja '.$employee->nip.' '.Carbon::now()->format('Ymd His').'.pdf';

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

        $startdate = Carbon::createFromFormat('Y-m-d', $request->publishDate);  
        $until = Carbon::createFromFormat('Y-m-d', $request->publishDate)->addMonths($request->masaBerlaku);
        $past = Carbon::createFromFormat('Y-m-d', $request->publishDate)->addMonths(-12);


        $warningNumber = DB::table('paperwork_warning as pw')
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
            'paperworkTypeId'   => $request->paperworkTypeId,
            'name'              => "Surat Peringatan ".$num,
            'startdate'         => $startdate." 00:00:00",
            'enddate'           => $until." 23:59:59"
        ];
        $paperworkId = DB::table('paperworks')->insertGetId($paper);

        $paperworkNum = self::generatePaperNumber($paperworkId, "SPP");


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
        $filename = 'Surat Peringatan ke '.$num.' - '.$nip.' '.Carbon::now()->format('Ymd His').'.pdf';

        $filepath = storage_path('/app/paperworks/'.$filename);
        $pdf->save($filepath);

        $affected = DB::table('paperworks')
        ->where('id', $paperworkId)
        ->update(['filepath' => $filename]);

        return view('administration.administrasiList');
    }

    public function suratPHK(Request $request)
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

        $startdate = Carbon::createFromFormat('Y-m-d', $request->publishDate);  
        $until = Carbon::createFromFormat('Y-m-d', $request->publishDate)->addMonths($request->masaBerlaku);
        $past = Carbon::createFromFormat('Y-m-d', $request->publishDate)->addMonths(-12);
        $paper = [
            'employeeId'        => $request->employeeId,
            'paperworkTypeId'   => $request->paperworkTypeId,
            'name'              => "Surat Pemberhentian Hubungan Kerja",
            'startdate'         => $startdate." 00:00:00",
            'enddate'           => $until." 23:59:59"
        ];
        $paperworkId = DB::table('paperworks')->insertGetId($paper);
        $paperworkNum = self::generatePaperNumber($paperworkId, "PHK");


        $warning = [
            'paperworkId'       => $paperworkId,
            'publishDate'       => $request->publishDate,
            'warningNumber'     => 1,
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
        $skorsingTanggal = $request->skorsingTanggal;


        $pdf = PDF::loadView('administration.suratPHK', compact('employeeId', 'name', 'nip', 'jabatan', 'orgStructure', 'workPosition', 'startdate', 'until','reason', 'skorsingTanggal', 'paperworkNum'));
        $filename = 'Surat Pemutusan Hubungan Kerja - '.$nip.' '.Carbon::now()->format('Ymd His').'.pdf';

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
        *   Surat Keterangan  Bekerja           No. 123/ALS-ADM/SKB/Bulan/Tahun
        *   Surat Peringatan                    No. 123/ALS-ADM/SPP/Bulan/Tahun
        *   Surat Pemutusan Hubungan Kerja      No. 123/ALS-ADM/PHK/Bulan/Tahun
        *   Surat Mutasi Pegawai                No. 123/ALS-ADM/SMP/Bulan/Tahun
        *   Surat Pengangkatan Pegawai Baru     No. 123/ALS-ADM/SPB/Bulan/Tahun
        *   Surat Dinas Perusahaan              No. 123/ALS-ADM/SDP/Bulan/Tahun
        */ 
        $bagian="ALS-ADM";
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


    public function cetakSuratMutasi($data)
    {
        $now    = Carbon::now();
        $until  = Carbon::now()->addMonths(120);

        $paperwork = DB::table('paperwork_types')
        ->where('isActive', 1)
        ->where('kode', "SMP")
        ->first();

        $paper = [
            'employeeId'        => $data['eid'],
            'paperworkTypeId'   => $paperwork->id,
            'name'              => "Surat Keputusan Mutasi Pegawai",
            'startdate'         => $now,
            'enddate'           => $until
        ];
        
        $paperworkId = DB::table('paperworks')->insertGetId($paper);
        $paperworkNum = self::generatePaperNumber($paperworkId, "SMP");
        $paperwork = self::cetakSuratKeterangan($data['eid'], $paperworkId, $paperworkNum);

        $pdf = PDF::loadView('administration.suratMutasi', compact('data', 'paperworkNum'));
        $filename = 'Surat Keputusan Mutasi Pegawai '.$data['nip'].' '.Carbon::now()->format('Ymd His').'.pdf';

        $filepath = storage_path('/app/paperworks/'.$filename);
        $pdf->save($filepath);

        $affected = DB::table('paperworks')
        ->where('id', $paperworkId)
        ->update(['filepath' => $filename]);

        return true;
    }

    public function suratPerjalanan(Request $request)
    {        
        $request->validate([
            'employeeId'    => [
                'required', 
                Rule::exists('employees', 'id')->where('id', $request->employeeId)
            ],
            'kepada'        => ['required'],
            'kegiatan'      => ['required'],
            'startdate'     => ['required', 'date', 'after_or_equal:today'],
            'enddate'       => ['required', 'date', 'after_or_equal:startdate']
        ],
        [
            'reason.*'      => 'Alasan wajib diisi',
            'startdate.*'   => 'Tanggal terbit harus setelah hari ini',
            'enddate.*'     => 'Tanggal terbit harus setelah tanggal mulai',
        ]);

        $start  = Carbon::createFromFormat('Y-m-d', $request->startdate);  
        $end    = Carbon::createFromFormat('Y-m-d', $request->enddate);

        $paper = [
            'employeeId'        => $request->employeeId,
            'paperworkTypeId'   => $request->paperworkTypeId,
            'name'              => "Surat Perjalanan Dinas ke ". $request->kepada,
            'startdate'         => $start." 00:00:00",
            'enddate'           => $end." 23:59:59"
        ];
        $paperworkId = DB::table('paperworks')->insertGetId($paper);
        $paperworkNum = self::generatePaperNumber($paperworkId, "SPD");

        $employeeId = $request->employeeId;
        $name = $request->name;
        $nip    = $request->nip;
        $nik    = $request->nik;
        $jabatan= $request->jabatan;
        $orgStructure = $request->orgStructure;
        $workPosition = $request->workPosition;
        $kepada = $request->kepada;
        $kegiatan = $request->kegiatan;

        $pdf = PDF::loadView('administration.suratPerjalananDinas', compact('employeeId', 'name', 'nip', 'nik', 'jabatan', 'orgStructure', 'workPosition', 'start', 'end','kepada', 'kegiatan', 'paperworkNum'));
        $filename = 'Surat Perjalanan Dinas ke '.$request->kepada.' '.Carbon::now()->format('Ymd His').'.pdf';

        $filepath = storage_path('/app/paperworks/'.$filename);
        $pdf->save($filepath);

        $affected = DB::table('paperworks')
        ->where('id', $paperworkId)
        ->update(['filepath' => $filename]);

        return view('administration.administrasiList');
    }

    public function cetakSuratKeputusanPegawaiBaru($empId, $mappingId, $employmentStatus, $nama, $nip, $startdate, $empStatus)
    {
        $now    = Carbon::now();
        $until  = Carbon::now()->addMonths(120);

        $paperwork = DB::table('paperwork_types')
        ->where('isActive', 1)
        ->where('kode', "SPB")
        ->first();

        $paper = [
            'employeeId'        => $empId,
            'paperworkTypeId'   => $paperwork->id,
            'name'              => "Surat Pengangkatan Pegawai",
            'startdate'         => $now,
            'enddate'           => $until
        ];
        $paperworkId = DB::table('paperworks')->insertGetId($paper);
        $paperworkNum = self::generatePaperNumber($paperworkId, "SPB");
        $paperwork = self::cetakSuratKeterangan($empId, $paperworkId, $paperworkNum);

        $text = DB::table('employeeorgstructuremapping as eos')
        ->select('os.name as jabatan', 'sp.name as level', 'wp.name as bagian', 'eos.isactive as stat')
        ->join('organization_structures as os', 'eos.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('eos.id', '=',$mappingId)->first();

        $data = [
            'eid'                   => $empId,
            'nama'                  => $nama,
            'nip'                   => $nip,
            'jabatan'            => $text->jabatan,
            'level'              => $text->level,
            'bagian'             => $text->bagian,
            'startdate'        => $startdate,
            'empStatus'        => $empStatus
        ];

        $pdf = PDF::loadView('administration.suratPegawaiBaru', compact('data', 'paperworkNum'));
        $filename = 'Surat Keputusan Pegawai '.$data['nip'].' '.Carbon::now()->format('Ymd His').'.pdf';

        $filepath = storage_path('/app/paperworks/'.$filename);
        $pdf->save($filepath);

        $affected = DB::table('paperworks')
        ->where('id', $paperworkId)
        ->update(['filepath' => $filename]);

        return true;
    }

}
