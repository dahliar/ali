<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use Illuminate\Http\Request;
use App\Models\Borongan; 


use Carbon\Carbon;
use DB;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('salary.generate');
    }

    public function indexHarian()
    {
        return view('salary.salaryHarianList');
    }
    public function indexBorongan()
    {
        return view('salary.salaryBoronganList');
    }
    public function indexLemburBulanan()
    {
        return view('salary.lemburBulananList');
    }
    public function indexHonorarium()
    {
        return view('salary.honorariumList');
    }
    public function store(Request $request)
    {
        $request->validate(
            [
                'start' => 'required|date|before_or_equal:end',
                'end' => 'required|date|before_or_equal:today'
            ],
            [
                'start.before_or_equal'=>'Tanggal awal harus sebelum tanggal akhir atau hari ini',
                'end.before_or_equal'=>'Tanggal akhir harus sebelum hari ini'
            ]
        );

        $harian = $this->salaryHarianGenerate($request->start, $request->end);
        $borongan = $this->salaryBoronganGenerate($request->start, $request->end);
        $bulanan = $this->lemburBulananGenerate($request->start, $request->end);
        $honorarium = $this->honorariumGenerate($request->start, $request->end);
        $val = array($harian, $borongan, $bulanan, $honorarium);
        return redirect()->route('generateGaji')->with('val', $val);        
    }

    public function honorariumGenerate($start, $end)
    {
        $rowCount = DB::table('honorariums as h')
        ->where('h.isGenerated', 0)
        ->count();


        $retValue="";
        if ($rowCount>0){
            $data = [
                'enddatejenis'      => '4'.$end,
                'endDate'           => $end,
                'userIdGenerator'   => auth()->user()->id,
                'jenis' => 4
            ];

            DB::table('salaries')
            ->upsert(
                $data,
                ['endatejenis'],
                ['enddatejenis','endDate','userIdGenerator','jenis']
            );


            $query = DB::table('salaries')
            ->select('id as id')
            ->where('endDate', $end)
            ->where('jenis', 4)
            ->first();

            $id = $query->id;

            $affected = DB::table('honorariums as h')
            ->where('h.isGenerated', 0)
            ->join('employees as e', 'e.id', '=',  'h.employeeId')
            ->update([
                'h.isGenerated' => 1, 
                'h.salaryid' => $id
            ]);
            $retValue = $affected." record honorarium telah digenerate";
        } else{
            $retValue = "Tidak terdapat record honorarium yang belum digenerate";

        }
        return $retValue;
    }
    public function lemburBulananGenerate($start, $end)
    {
        $rowCount = DB::table('dailysalaries as ds')
        ->where('ds.isGenerated', 0)
        ->where('e.employmentStatus', 1)
        ->where('ds.uangLembur', '>', '0')
        ->join('employees as e', 'e.id', '=',  'ds.employeeId')
        ->count();


        $retValue="";
        if ($rowCount>0){
            $data = [
                'enddatejenis'      => '1'.$end,
                'endDate'           => $end,
                'userIdGenerator'   => auth()->user()->id,
                'jenis' => 1
            ];

            DB::table('salaries')
            ->upsert(
                $data,
                ['endatejenis'],
                ['enddatejenis','endDate','userIdGenerator','jenis']
            );


            $query = DB::table('salaries')
            ->select('id as id')
            ->where('endDate', $end)
            ->where('jenis', 1)
            ->first();

            $id = $query->id;

            $affected = DB::table('dailysalaries as ds')
            ->where('ds.isGenerated', 0)
            ->where('e.employmentStatus', 1)
            ->where('ds.uangLembur', '>', '0')
            ->join('employees as e', 'e.id', '=',  'ds.employeeId')
            ->update([
                'ds.isGenerated' => 1, 
                'ds.salaryid' => $id
            ]);
            $retValue = $affected." record lembur pegawai bulanan telah digenerate";
        } else{
            $retValue = "Tidak terdapat record lembur pegawai bulanan yang belum digenerate";

        }
        return $retValue;
    }

    public function salaryHarianGenerate($start, $end)
    {
        $rowCount = DB::table('dailysalaries as ds')
        ->where('ds.isGenerated', 0)
        ->where('e.employmentStatus', 2)
        ->join('employees as e', 'e.id', '=',  'ds.employeeId')
        ->count();



        $retValue="";
        if ($rowCount>0){
            $data = [
                'enddatejenis'       => '2'.$end,
                'endDate'       => $end,
                'userIdGenerator' => auth()->user()->id,
                'jenis' => 2
            ];

            DB::table('salaries')
            ->upsert(
                $data,
                ['endatejenis'],
                ['enddatejenis','endDate','userIdGenerator','jenis']
            );


            $query = DB::table('salaries')
            ->select('id as id')
            ->where('endDate', $end)
            ->where('jenis', 2)
            ->first();

            $id = $query->id;

            $affected = DB::table('dailysalaries as ds')
            ->where('ds.isGenerated', 0)
            ->where('e.employmentStatus', 2)
            ->join('employees as e', 'e.id', '=',  'ds.employeeId')
            ->update([
                'ds.isGenerated' => 1, 
                'ds.salaryid' => $id
            ]);

            $retValue = $affected." record presensi pegawai harian telah digenerate";
        } else{
            $retValue = "Tidak terdapat record presensi pegawai harian yang belum digenerate";
        }

        return $retValue;
    }

    public function salaryBoronganGenerate($start, $end)
    {
        $rowCount = DB::table('borongans')
        ->where('status', 1)
        ->count();

        $retValue="";
        if ($rowCount>0){
            $data = [
                'enddatejenis'  => '3'.$end,
                'endDate'       => $end,
                'userIdGenerator' => auth()->user()->id,
                'jenis' => 3
            ];

            DB::table('salaries')
            ->upsert(
                $data,
                ['endatejenis'],
                ['enddatejenis','endDate','userIdGenerator','jenis']
            );


            $query = DB::table('salaries')
            ->select('id as id')
            ->where('endDate', $end)
            ->where('jenis', 3)
            ->first();

            $id = $query->id;

            $affected = DB::table('borongans')
            ->where('status', 1)
            ->update([
                'status' => 2, 
                'salariesId' => $id
            ]);

            $retValue = $affected." record kerja borongan telah digenerate";
        } else{
            $retValue = "Tidak terdapat record kerja borongan yang belum digenerate";

        }
        return $retValue;
    }

    public function getBoronganSalariesForPrint(Borongan $borongan){
        $query = DB::table('salaries as s')
        ->select(
            'db.id as dbid',
            'b.id as boronganId',
            's.id as salaryId',
            'e.id as empid',
            'e.nip as nip',
            'u.name as name',
            'os.name as osname',
            'db.isPaid as statusIsPaid',
            'e.noRekening as noRekening',
            'ba.shortname as bankName',
            DB::raw('sum(db.netPayment) as netPayment'),
            DB::raw('(CASE WHEN db.isPaid is null THEN "Belum" WHEN db.isPaid="1" THEN "Sudah" END) AS isPaid')
        )
        ->join('borongans as b', 's.id', '=', 'b.salariesId')
        ->join('detail_borongans as db', 'b.id', '=', 'db.boronganId')
        ->join('employees as e', 'e.id', '=', 'db.employeeId')
        ->join('banks as ba', 'ba.id', '=', 'e.bankid')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('s.id', $borongan->salariesId)
        ->where('eosm.isactive', 1)
        ->where('e.employmentStatus', 3)
        ->groupBy('e.id')
        ->get();
        return datatables()
        ->of($query)        
        ->addColumn('action', function ($row) {
            $html = '';
            if($row->statusIsPaid != 1){
                $html.='<button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Tandai sudah dibayar" onclick="setIsPaidModal('."'".$row->salaryId."','".$row->empid."'".')">
                <i class="fa fa-save" style="font-size:20px"></i>
                </button>
                ';
            }
            return $html;
        })
        ->addIndexColumn()
        ->toJson();
    }
    public function markBoronganIsPaid(Request $request)
    {
        $affected = DB::table('detail_borongans as db')
        ->join('borongans as b', 'b.id', '=', 'db.boronganId')
        ->join('salaries as s', 's.id', '=', 'b.salariesId')
        ->where('s.id', $request->salaryId)
        ->where('db.employeeId', $request->empid)
        ->update([
            'db.isPaid' => 1, 
            'db.paidDate'=>$request->tanggalBayar,
            'db.userPaid'=>auth()->user()->id
        ]);


        $affected = DB::table('borongans as b')
        ->join('detail_borongans as db', 'db.boronganId', '=', 'b.id')
        ->where('db.employeeId', $request->empid)
        ->where('b.salariesId', $request->salaryId)
        ->update(['status' => 3]);

        $retValue = [
            'message'       => "Record telah ditandai",
            'isError'       => "0"
        ];
        return $retValue;
    }

    public function getLemburPegawaiBulanan($salaryId){
        $query = DB::table('dailysalaries as ds')
        ->select(
            'ds.salaryId as salaryId',
            'e.id as empid',
            'e.nip as nip',
            'u.name as name',
            'os.name as osname',
            DB::raw('sum(ds.uanglembur) as ul'),
            'e.noRekening as noRekening',
            'b.shortname as bank',
            DB::raw('(CASE WHEN ds.isPaid is null THEN "Belum" WHEN ds.isPaid="1" THEN "Sudah" END) AS isPaid'),
            'ds.isPaid as isPaidStatus'
        )
        ->join('employees as e', 'e.id', '=', 'ds.employeeId')
        ->join('banks as b', 'b.id', '=', 'e.bankid')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('ds.salaryid', $salaryId)
        ->where('eosm.isactive', 1)
        ->where('e.employmentStatus', 1)
        ->groupBy('e.id')
        ->get();

        return datatables()
        ->of($query)
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->isPaidStatus == null){
                $html .= '
                <button class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Set sudah dibayar" 
                onclick="setSalaryIsPaid('.$row->salaryId.','.$row->empid.')"><i class="fa fa-check"></i>
                </button>';
            }
            return $html;
        })
        ->addIndexColumn()->toJson();
    }
    public function markLemburIsPaid(Request $request)
    {
        //dd($request);
        $affected = DB::table('dailysalaries as ds')
        ->where('ds.salaryId', $request->salaryId)
        ->where('ds.employeeId', $request->empid)
        ->update([
            'ds.isPaid' => 1, 
            'ds.payDate'=>$request->tanggalBayar,
            'ds.userPaid'=>auth()->user()->id
        ]);

        $retValue = [
            'message'       => "Record telah ditandai",
            'isError'       => "0"
        ];
        return $retValue;
    }
    
    public function getSalariesHarian(){
        $query = DB::table('salaries as s')
        ->select(
            's.id as id', 
            's.endDate as enddate',
            DB::raw('
                concat(
                count(distinct(concat(ds.isPaid,ds.employeeId))), 
                " dari ", 
                count(distinct(ds.employeeId))
                )
                AS terbayar'),            
            'ug.name as generatorName',
        )
        ->join('dailysalaries as ds', 'ds.salaryid', '=', 's.id')
        ->leftjoin('users as ug', 's.userIdGenerator', '=', 'ug.id')
        ->where('jenis', 2)
        ->orderBy('s.enddate', 'desc')
        ->groupBy('ds.salaryid');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <a data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak gaji pegawai harian" href="checkCetakGajiPegawaiHarian/'.$row->id.'">
            <i class="fa fa-print"></i>
            </a>';

            $html.='
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-container="body" title="INI MASIH BELUM Hapus Generate Gaji">
            <i class="fa fa-trash">BELUM</i>
            </button>
            ';

            return $html;
        })->addIndexColumn()->toJson();
    }  


    public function getSalariesHonorarium(){
        $query = DB::table('salaries as s')
        ->select(
            's.id as id', 
            's.endDate as enddate',
            DB::raw('
                concat(
                count(distinct(concat(h.isPaid, h.employeeId))), 
                " dari ", 
                count(distinct(h.employeeId))
                )
                AS countIsPaid'),
            'ug.name as generatorName',
        )
        ->leftjoin('users as ug', 's.userIdGenerator', '=', 'ug.id')
        ->join('honorariums as h', 's.id', '=', 'h.salaryId')
        ->where('jenis', 4)
        ->orderBy('s.enddate', 'desc')
        ->groupBy('h.salaryId');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <a data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak honorarium pegawai" href="checkCetakHonorariumPegawai/'.$row->id.'">
            <i class="fa fa-list"></i>
            </a>';

            $html.='
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-container="body" title="INI MASIH BELUM Hapus Generate Gaji" onclick="hapusRecordHonorarium('."'".$row->id."'".')">
            <i class="fa fa-trash">BELUM</i>
            </button>
            ';
            return $html;
        })->addIndexColumn()->toJson();
    }  

    public function getLemburBulanan(){
        $query = DB::table('salaries as s')
        ->select(
            's.id as id', 
            's.endDate as enddate',
            DB::raw('
                concat(
                count(distinct(concat(ds.isPaid,ds.employeeId))), 
                " dari ", 
                count(distinct(ds.employeeId))
                )
                AS countIsPaid'),            
            'ug.name as name',
        )
        ->join('dailysalaries as ds', 'ds.salaryid', '=', 's.id')
        ->leftjoin('users as ug', 's.userIdGenerator', '=', 'ug.id')
        ->where('jenis', 1)
        ->where('ds.uangLembur', '>', '0')
        ->orderBy('s.enddate', 'desc')
        ->groupBy('ds.salaryid');
        $query->get();


        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <a data-rowid="'.$row->id.'" class="btn btn-xs btn-dark" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak lembur pegawai bulanan" href="checkCetakLemburPegawaiBulanan/'.$row->id.'">
            <i class="fas fa-moon"></i>
            </a>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-container="body" title="INI MASIH BELUM Hapus Generate Gaji">
            <i class="fa fa-trash">BELUM</i>
            </button>
            ';
            return $html;
        })->addIndexColumn()->toJson();
    }  



    public function getSalariesBorongan(){
        $query = DB::table('salaries as s')
        ->select(
            'b.id as id', 
            's.endDate as tanggalKerja', 
            's.created_at as tanggalGenerate', 
            DB::raw('
                concat(
                count(distinct(concat(db.isPaid,db.employeeId))), 
                " dari ", 
                count(distinct(db.employeeId))
                )
                AS terbayar'), 
            'u.name as generatorName'
        )
        ->join('borongans as b', 'b.salariesId', '=', 's.id')
        ->join('detail_borongans as db', 'db.boronganId', '=', 'b.id')
        ->join('employees as e', 'e.id', '=', 'db.employeeId')
        ->join('users as u', 's.userIdGenerator', '=', 'u.id')
        ->where('jenis', 3)
        ->groupBy('s.id')
        ->orderBy('s.enddate');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <a data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak gaji pegawai borongan" 
            href="checkCetakGajiPegawaiBorongan/'.$row->id.'"><i class="fa fa-print"></i>
            </a>';
            return $html;
        })
        ->addIndexColumn()->toJson();
    }  

    public function checkCetakGajiPegawaiHarian(Salary $salary){
        return view('salary.checkSalaryHarianList', compact('salary'));
    }
    public function printSalaryHarianList(Salary $salary){

        $dailysalaries = DB::table('dailysalaries as ds')
        ->select(
            'e.id as empid',
            'e.nip as nip',
            'u.name as name',
            'os.name as osname',
            DB::raw('sum(ds.uangharian) as uh'),
            DB::raw('sum(ds.uanglembur) as ul'),
            DB::raw('(sum(ds.uangharian) + sum(ds.uanglembur)) AS total'),
        )
        ->join('employees as e', 'e.id', '=', 'ds.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('ds.salaryid', $salary->id)
        ->where('eosm.isactive', 1)
        ->where('e.employmentStatus', 2)
        ->groupBy('e.id')
        ->get();

        $generatorName = DB::table('users as u')
        ->select('u.name as name')
        ->where('u.id', $salary->userIdGenerator)
        ->first();
        $payerName = DB::table('users as u')
        ->select('u.name as name')
        ->where('u.id', auth()->user()->id)
        ->first();
        return view('invoice.slipGajiHarian', compact('salary', 'dailysalaries', 'payerName', 'generatorName'));
    }


    public function printSalaryHonorariumList(Salary $salary){

        $honorariums = DB::table('honorariums as h')
        ->select(
            'e.id as empid',
            'e.nip as nip',
            'u.name as name',
            'os.name as osname',
            DB::raw('sum(h.jumlah) as jumlah')
        )
        ->join('employees as e', 'e.id', '=', 'h.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('h.salaryid', $salary->id)
        ->where('eosm.isactive', 1)
        ->groupBy('e.id')
        ->get();

        $generatorName = DB::table('users as u')
        ->select('u.name as name')
        ->where('u.id', $salary->userIdGenerator)
        ->first();
        $payerName = DB::table('users as u')
        ->select('u.name as name')
        ->where('u.id', auth()->user()->id)
        ->first();
        return view('invoice.slipHonorarium', compact('salary', 'honorariums', 'payerName', 'generatorName'));
    }


    public function printSalaryBoronganList(Borongan $borongan){
        $detail_borongans = DB::table('salaries as s')
        ->select(
            'db.id as dbid',
            'b.id as boronganId',
            's.id as salaryId',
            'e.nip as nip',
            'u.name as name',
            'os.name as osname',
            'db.isPaid as statusIsPaid',
            'e.noRekening as noRekening',
            'ba.shortname as bankName',
            DB::raw('sum(db.netPayment) as netPayment'),
            DB::raw('(CASE WHEN db.isPaid is null THEN "Belum" WHEN db.isPaid="1" THEN "Sudah" END) AS isPaid')
        )
        ->join('borongans as b', 's.id', '=', 'b.salariesId')
        ->join('detail_borongans as db', 'b.id', '=', 'db.boronganId')
        ->join('employees as e', 'e.id', '=', 'db.employeeId')
        ->join('banks as ba', 'ba.id', '=', 'e.bankid')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('s.id', $borongan->salariesId)
        ->where('eosm.isactive', 1)
        ->where('e.employmentStatus', 3)
        ->groupBy('e.id')
        ->get();

        $generatorName = DB::table('users as u')
        ->select('u.name as name')
        ->join('salaries as s', 's.userIdGenerator', '=', 'u.id')
        ->join('borongans as b', 's.id', '=', 'b.salariesId')
        ->where('b.id', $borongan->id)
        ->first();

        $payerName = DB::table('users as u')
        ->select('u.name as name')
        ->where('u.id', auth()->user()->id)
        ->first();

        $salary = DB::table('salaries as s')
        ->where('s.id', '=', $borongan->salariesId)
        ->first();
        
        return view('invoice.slipGajiBorongan', compact('salary','borongan', 'detail_borongans', 'payerName', 'generatorName'));
    }


    public function checkCetakLemburPegawaiBulanan(Salary $salary){
        return view('salary.checkLemburBulananList', compact('salary'));
    }
    public function checkCetakGajiPegawaiBorongan(Borongan $borongan){
        return view('salary.checkSalaryBoronganList', compact('borongan'));
    }
    public function checkCetakHonorariumPegawai(Salary $salary){
        return view('salary.checkHonorariumList', compact('salary'));
    }
    public function harianMarkedPaid($dsid){
        DB::table('dailysalaries')
        ->where('id', $dsid)
        ->update([
            'isPaid' => 1,
            'payDate' => Carbon::now()->toDateTimeString(),
            'userPaid' => auth()->user()->id
        ]);
        return true;
    }
    public function honorariumMarkedPaid($hid){
        DB::table('honorariums as h')
        ->where('h.id', $hid)
        ->update([
            'h.isPaid' => 1,
            'h.paidDate' => Carbon::now()->toDateTimeString(),
            'h.userPaid' => auth()->user()->id
        ]);
        return true;
    }

    public function getSalariesHonorariumForCheck($salaryId){
        $query = DB::table('honorariums as h')
        ->select(
            'e.id as empid',
            'e.nip as nip',
            'u.name as name',
            'h.salaryId as salaryId',
            'e.noRekening as noRekening',
            'h.isPaid as isPaidStatus',
            'b.name as bankName',
            'h.id as hid',
            DB::raw('(CASE WHEN h.isPaid is null THEN "Belum" WHEN h.isPaid="1" THEN "Sudah" END) AS isPaid'),
            'os.name as osname',
            DB::raw('sum(h.jumlah) as jumlah'),
        )
        ->join('employees as e', 'e.id', '=', 'h.employeeId')
        ->join('banks as b', 'b.id', '=', 'e.bankid')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('h.salaryid', $salaryId)
        ->where('eosm.isactive', 1)
        ->groupBy('e.id')
        ->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->isPaidStatus == null){
                $html .= '
                <button data-rowid="'.$row->empid.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Set sudah dibayar" 
                onclick="setHonorariumIsPaid('.$row->hid.')"><i class="fa fa-check"></i>
                </button>';
            }
            return $html;
        })
        ->addIndexColumn()->toJson();
    }


    public function getSalariesHarianForCheck($salaryId){
        $query = DB::table('dailysalaries as ds')
        ->select(
            'e.id as empid',
            'ds.id as dsid',
            'e.nip as nip',
            'u.name as name',
            'e.noRekening as noRekening',
            'b.name as bankName',
            'b.name as bankName',
            'os.name as osname',
            'ds.isPaid as isPaidStatus',
            DB::raw('(CASE WHEN ds.isPaid is null THEN "Belum" WHEN ds.isPaid="1" THEN "Sudah" END) AS isPaid'),
            DB::raw('sum(ds.uangharian) as uh'),
            DB::raw('sum(ds.uanglembur) as ul'),
            DB::raw('(sum(ds.uangharian) + sum(ds.uanglembur)) AS total'),
        )
        ->join('employees as e', 'e.id', '=', 'ds.employeeId')
        ->join('banks as b', 'b.id', '=', 'e.bankid')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('ds.salaryid', $salaryId)
        ->where('eosm.isactive', 1)
        ->where('e.employmentStatus', 2)
        ->groupBy('e.id')
        ->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->isPaidStatus == null){
                $html .= '
                <button data-rowid="'.$row->empid.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Set sudah dibayar" 
                onclick="setSalaryIsPaid('.$row->dsid.')"><i class="fa fa-check"></i>
                </button>';
            }
            return $html;
        })
        ->addIndexColumn()->toJson();
    }

    public function getDailySalariesDetail(){
        $query = DB::table('salaries as s')
        ->select(
            's.id as id', 
            's.endDate as enddate', 
            DB::raw('(CASE WHEN s.isPaid="0" THEN "Belum" WHEN s.isPaid="1" THEN "Sudah" END) AS ispaid'),
            'ug.name as generatorName',
            'up.name as payerName',
            's.tanggalBayar as tanggalBayar'
        )
        ->leftjoin('users as ug', 's.userIdGenerator', '=', 'ug.id')
        ->leftjoin('users as up', 's.userIdPaid', '=', 'up.id')
        ->where('jenis', 2)
        ->orderBy('s.enddate');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak gaji harian" onclick="CetakDaftarGaji('."'".$row->id."'".')">
            <i class="fa fa-print" style="font-size:20px"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Tandai sudah dibayar" onclick="setIsPaidModal('."'".$row->id."'".')">
            <i class="fa fa-save" style="font-size:20px"></i>
            </button>
            ';
            return $html;
        })->addIndexColumn()->toJson();
    }  

}
