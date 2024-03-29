<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use Illuminate\Http\Request;
use App\Models\Borongan; 


use Carbon\Carbon;
use Auth;
use DB;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexGenerate()
    {
        return view('salary.generate');
    }
    public function indexGenerateGajiBulanan()
    {
        return view('salary.generateGajiBulanan');
    }
    public function indexPayroll()
    {
        $end = Carbon::now();
        if ($end->dayOfWeekIso != 6){
            $end = Carbon::now()->next(6);
        }
        $end=$end->toDateString();
        $start=Carbon::now()->subDays(30)->toDateString();
        return view('salary.payrollList', compact('start','end'));
    }
    public function indexPayrollBulanan()
    {
        $end = Carbon::now();
        if ($end->dayOfWeekIso != 6){
            $end = Carbon::now()->endOfMonth();
        }
        $end=$end->toDateString();
        $start=Carbon::now()->subDays(120)->toDateString();
        return view('salary.payrollListBulanan', compact('start','end'));
    }


    public function index($salaryId)
    {
        return view('salary.salariesList', compact('salaryId'));
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
    public function viewSlipGaji(Request $request)
    {
        return view('salary.slipGajiPegawai');
    }


    public function storeBasisStartDate(Request $request)
    {
        $request->validate(
            [
                'start' => 'required|date|before_or_equal:today'
            ],
            [
                'start.before_or_equal'=>'Tanggal awal harus sebelum tanggal akhir atau hari ini'
            ]
        );
        $start=Carbon::parse($request->start);
        $nextSaturday = Carbon::now();
        if ($nextSaturday->dayOfWeekIso != 6){
            $nextSaturday = Carbon::now()->next(6);
        }

        $data = [
            'payDate' => $nextSaturday->toDateString(), 
            'jenis' => 2, 
            'creator' =>auth()->user()->id
        ];

        $payrollExist=DB::table('payrolls as p')
        ->select(
            DB::raw('max(p.id) as pid'),
        )
        ->where('payDate', '=', $nextSaturday->toDateString())
        ->where('p.jenis', '=', 2)
        ->first();

        $pid="";
        if($payrollExist->pid == null){
            $pid = DB::table('payrolls')->insertGetId($data);
        } else{
            $pid = $payrollExist->pid;
        }


        $end=$nextSaturday->subDays(1);

        $harian = $this->salaryHarianGenerate($start, $end, $pid, 2);
        $borongan = $this->salaryBoronganGenerate($start, $end, $pid);
        $honorarium = $this->honorariumGenerate($start, $end, $pid, 2);
        $val = array($harian, $borongan, $honorarium);
        return redirect()->route('generateGaji')->with('val', $val);        
    }

    public function store(Request $request)
    {
        /*
        Kode    : 16 April 2023.
        Fungsi  :   melakukan generate gaji berbasis range tanggal awal-akhir yang akan digenerate
        Input   :   tanggal awal, tanggal akhir, tanggal  
        */
       // dd($request);
        $request->validate(
            [
                'startDate' => 'required|date|before:today',
                'endDate' => 'required|date|before_or_equal:today|after:startDate',
                'payDate' => 'required|date|after:endDate'
            ]
        );
        $startDate=Carbon::parse($request->startDate);
        $endDate=Carbon::parse($request->endDate);
        $payDate=Carbon::parse($request->payDate);

        $data = [
            'payDate' => $payDate, 
            'jenis' => 2, 
            'creator' =>auth()->user()->id
        ];

        $payrollExist=DB::table('payrolls as p')
        ->select(
            DB::raw('max(p.id) as pid'),
        )
        ->where('payDate', '=', $payDate)
        ->where('p.jenis', '=', 2)
        ->first();

        $pid="";
        if($payrollExist->pid == null){
            $pid = DB::table('payrolls')->insertGetId($data);
        } else{
            $pid = $payrollExist->pid;
        }

        $harian = $this->salaryHarianGenerate($startDate, $endDate, $pid, 2);
        $borongan = $this->salaryBoronganGenerate($startDate, $endDate, $pid);
        $honorarium = $this->honorariumGenerate($startDate, $endDate, $pid, 2);
        $val = array($harian, $borongan, $honorarium);
        return redirect()->route('generateGaji')->with('val', $val);        
    }



    public function hapusPenggajian(Request $request)
    {
        /*
            1. get salary list dengan payrollId $request->payrollId
            2. Untuk masing-masing result[dailysalaries, honorarium dan borongan], 
                ubah status is generated menjadi 0 kembali
                null atau nol kan kolom salaryId
            3. hapus data di tabel salaryId untuk masing result[dailysalaries, honorarium dan borongan], 
            4. hapus detail_payrolls dengan idPayroll $request->payrollId
            4. hapus data di tabel payrollId $request->payrollId 
        */


            $salaries=DB::table('salaries as s')
            ->select('id', 'jenis')
            ->where('s.idPayroll', '=', $request->payrollId)
            ->get();

            foreach($salaries as $salary){
                switch($salary->jenis){
                    case(1): //bulanan
                    $affected = DB::table('monthly_salaries as ms')
                    ->where('ms.salaryId', $salary->id)
                    ->delete();
                    break;
                    case(2): //harian

                    $affected = DB::table('dailysalaries as ds')
                    ->where('ds.salaryId', '=', $salary->id)
                    ->update([
                        'isGenerated' => 0, 
                        'salaryId' => null
                    ]);
                    break;
                    case(3): //borongan
                    $affected = DB::table('borongans as b')
                    ->where('b.salariesId', '=', $salary->id)
                    ->update([
                        'status' => 1, 
                        'salariesId' => null
                    ]);
                    break;
                    case(4): //honorarium

                    $affected = DB::table('honorariums as h')
                    ->where('h.salaryId', '=', $salary->id)
                    ->update([
                        'isGenerated' => 0, 
                        'salaryId' => null
                    ]);
                    break;
                }

                $sal = Salary::find($salary->id); 
                $sal->delete();
            }

            $affected = DB::table('detail_payrolls')
            ->where('idPayroll', '=', $request->payrollId)
            ->delete();


            $affected = DB::table('payrolls')
            ->where('id', '=', $request->payrollId)
            ->delete();
            return true;        
        }

        public function generateGajiBulananStore(Request $request)
        {
            $start  = Carbon::parse("21-".$request->monthBefore."-".$request->yearBefore);
            $end    = Carbon::parse("20-".$request->monthPresent."-".$request->yearPresent);

            $data = [
                'payDate' => $end->toDateString(), 
                'jenis' => 1, 
                'creator' =>auth()->user()->id
            ];

            $payrollExist=DB::table('payrolls as p')
            ->select(
                DB::raw('max(p.id) as pid'),
            )
            ->where('payDate', '=', $end->toDateString())
            ->where('p.jenis', '=', 1)
            ->first();

            $pid="";
            if($payrollExist->pid == null){
                $pid = DB::table('payrolls')->insertGetId($data);
            } else{
                $pid = $payrollExist->pid;
            }

        //perhitungan [lembur dan salary harian, dan honorarium] untuk pegawai bulanan sama dengan pegawai harian
            $bulananHarian = $this->salaryHarianGenerate($start, $end, $pid, 1);
            $bulananHonorarium = $this->honorariumGenerate($start, $end, $pid, 1);
            $bulananGaji = $this->gajiBulananGenerate($start, $end, $pid);
            $val = array($bulananGaji, $bulananHarian, $bulananHonorarium);

            return $val;
        }

        public function gajiBulananGenerate($start, $end, $payrollId)
        {
            $employeeMonthlyList = DB::table('employees as e')
            ->select(
                'e.id as id',
                'eosm.gajiPokok as gp',
            )
            ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
            ->where('e.employmentStatus', '=', '1')
            ->where('e.isActive', '=', '1')
            ->where('eosm.isactive', '=', '1')
            ->get();


            $salariesPaidExist=DB::table('salaries as s')
            ->select(
                DB::raw('max(s.id) as sid'),
            )
            ->join('payrolls as p', 'p.id', '=', 's.idPayroll')
            ->where('endDate', '=', $end->toDateString())
            ->where('p.jenis', 1)
            ->where('s.jenis', '=', 1)
            ->where('isPaid', '=', null)
            ->first();

            $data = [
                'startDate'         => $start->toDateString(),
                'endDate'           => $end->toDateString(),
                'userIdGenerator'   => auth()->user()->id,
                'jenis'             => 1,
                'isPaid'            => null,
                'idPayroll'         => $payrollId
            ];

            $salaryId="";
            if($salariesPaidExist->sid == null){
                $salaryId = DB::table('salaries')->insertGetId($data);
            } else{
                $salaryId = $salariesPaidExist->sid;
            }

            foreach($employeeMonthlyList as $detail){
                DB::table('monthly_salaries')
                ->upsert(
                    [
                        'employeeId'        => $detail->id,
                        'tanggalGenerate'   => $end->toDateString(),
                        'empiddate'         => $detail->id.'-'.$end->toDateString(),
                        'salaryId'          => $salaryId,
                        'jumlah'            => $detail->gp
                    ], 
                    ['empiddate'], 
                    ['salaryId', 'jumlah']
                );
            }
            $moveGeneratedData = DB::table('monthly_salaries as ms')
            ->select(
                'ms.employeeId as empid',
                'jumlah as jumlah'
            )
            ->where('ms.salaryId', '=', $salaryId)
            ->get();
            foreach($moveGeneratedData as $row){
                DB::table('detail_payrolls')
                ->upsert([
                    ['idPayroll'        => $payrollId, 
                    'employeeId'        => $row->empid, 
                    'idPayrollEmpid'    => ($payrollId.'-'.$row->empid), 
                    'bulanan'           => $row->jumlah]],
                    ['idPayrollEmpid'], 
                    ['bulanan']
                );
            }
            return count($moveGeneratedData)." record gaji bulanan";
        }



        public function honorariumGenerate($start, $end, $payrollId, $jenis)
        {
            $rowCount = DB::table('honorariums as h')
            ->join('employees as e', 'e.id', '=',  'h.employeeId')
            ->whereBetween('h.tanggalKerja', [$start->toDateString(), $end->toDateString()])
            ->where('h.isGenerated', 0);

            if ($jenis==1){
            //bulanan
                $rowCount=$rowCount->whereIn('e.employmentStatus', [1]);
            } else {
            //harian
                $rowCount=$rowCount->whereIn('e.employmentStatus', [2,3]);
            }

            $rowCount=$rowCount->count();

            $retValue="";
            if ($rowCount>0){
                $data = [
                    'startDate'         => $start->toDateString(),
                    'endDate'           => $end->toDateString(),
                    'userIdGenerator'   => auth()->user()->id,
                    'jenis'             => 4,
                    'isPaid'            => null,
                    'idPayroll'         => $payrollId
                ];

                $salariesPaidExist=DB::table('salaries as s')
                ->select(
                    DB::raw('max(s.id) as sid'),
                )
                ->join('payrolls as p', 'p.id', '=', 's.idPayroll')
                ->where('endDate', '=', $end->toDateString())
                ->where('s.jenis', '=', 4)
                ->where('isPaid', '=', null);

                if ($jenis==1){
            //bulanan
                    $salariesPaidExist=$salariesPaidExist->where('p.jenis', 1);
                } else{
            //harian
                    $salariesPaidExist=$salariesPaidExist->where('p.jenis', 2);
                }
                $salariesPaidExist=$salariesPaidExist->first();

                $salaryId="";
                if($salariesPaidExist->sid == null){
                    $salaryId = DB::table('salaries')->insertGetId($data);
                } else{
                    $salaryId = $salariesPaidExist->sid;
                }

                $affected = DB::table('honorariums as h')
                ->whereBetween('h.tanggalKerja', [$start, $end])
                ->where('h.isGenerated', 0)
                ->join('employees as e', 'e.id', '=',  'h.employeeId');

                if ($jenis==1){
            //bulanan
                    $affected=$affected->whereIn('e.employmentStatus', [1]);
                } else{
            //harian
                    $affected=$affected->whereIn('e.employmentStatus', [2,3]);
                }

                $affected=$affected->update([
                    'h.isGenerated' => 1, 
                    'h.salaryid' => $salaryId
                ]);

                $moveGeneratedData = DB::table('honorariums as h')
                ->select(
                    'h.employeeId as empid',
                    DB::raw('sum(jumlah) as jumlah')
                )
                ->where('h.salaryId', '=', $salaryId)
                ->groupBy('h.employeeId')
                ->get();

                foreach($moveGeneratedData as $row){
                    DB::table('detail_payrolls')
                    ->upsert([
                        ['idPayroll'        => $payrollId, 
                        'employeeId'        => $row->empid, 
                        'idPayrollEmpid'    => ($payrollId.'-'.$row->empid), 
                        'honorarium'        => $row->jumlah]],
                        ['idPayrollEmpid'], 
                        ['honorarium']
                    );
                }
                $retValue = $affected." record honorarium telah digenerate";
            } else{
                $retValue = "Tidak terdapat record honorarium yang belum digenerate";
            }
            return $retValue;
        }

        public function salaryHarianGenerate($start, $end, $payrollId, $jenis)
        {
            $rowCount = DB::table('dailysalaries as ds')
            ->join('employees as e', 'e.id', '=',  'ds.employeeId')
            ->where('ds.isGenerated', 0)
            ->whereBetween('ds.presenceDate', [$start->startOfDay(), $end->endOfDay()]);

            if ($jenis==1){
            //bulanan
                $rowCount=$rowCount->where('e.employmentStatus', 1);
            } else{
            //harian
                $rowCount=$rowCount->where('e.employmentStatus', 2);
            }

            $rowCount=$rowCount->count();

            $retValue="";
            if ($rowCount>0){
                $data = [
                    'startDate'         => $start->toDateString(),
                    'endDate'           => $end->toDateString(),
                    'userIdGenerator'   => auth()->user()->id,
                    'jenis'             => 2,
                    'isPaid'            => null,
                    'idPayroll'         => $payrollId
                ];

                $salariesPaidExist=DB::table('salaries as s')
                ->select(
                    DB::raw('max(s.id) as sid'),
                )
                ->join('payrolls as p', 'p.id', '=', 's.idPayroll')
                ->where('endDate', '=', $end->toDateString())
                ->where('s.jenis', '=', 2)
                ->where('isPaid', '=', null);

                if ($jenis==1){
            //bulanan
                    $salariesPaidExist=$salariesPaidExist->where('p.jenis', 1);
                } else{
            //harian
                    $salariesPaidExist=$salariesPaidExist->where('p.jenis', 2);
                }
                $salariesPaidExist=$salariesPaidExist->first();

                $salaryId="";
                if($salariesPaidExist->sid == null){
                    $salaryId = DB::table('salaries')->insertGetId($data);
                } else{
                    $salaryId = $salariesPaidExist->sid;
                }

                $affected = DB::table('dailysalaries as ds')
                ->where('ds.isGenerated', 0)
                ->whereBetween('ds.presenceDate', [$start->toDateString(), $end->toDateString()])
                ->join('employees as e', 'e.id', '=',  'ds.employeeId');

                if ($jenis==1){
            //bulanan
                    $affected=$affected->where('e.employmentStatus', 1);
                } else{
            //harian
                    $affected=$affected->where('e.employmentStatus', 2);
                }

                $affected=$affected->update([
                    'ds.isGenerated' => 1, 
                    'ds.salaryid' => $salaryId
                ]);

                $moveGeneratedData = DB::table('dailysalaries as ds')
                ->select(
                    'ds.employeeId as empid',
                    DB::raw('sum(uangHarian) as uh'),
                    DB::raw('sum(uangLembur) as ul'),
                    DB::raw('sum(jamKerja) as jk'),
                    DB::raw('sum(jamLembur) as jl'),
                    DB::raw('count(id) as hari'),
                )
                ->where('ds.salaryId', '=', $salaryId)
                ->groupBy('ds.employeeId')
                ->get();

                foreach($moveGeneratedData as $row){
                    $query = DB::table('detail_payrolls')
                    ->upsert([
                        [
                            'idPayroll'    => $payrollId, 
                            'employeeId'    => $row->empid, 
                            'idPayrollEmpid'=> ($payrollId.'-'.$row->empid),
                            'jamKerja'      => $row->jk,
                            'jamLembur'     => $row->jl,
                            'hariKerja'     => $row->hari,
                            'harian'        => ($row->uh + $row->ul)
                        ]
                    ],
                    ['idPayrollEmpid'], 
                    ['harian', 'jamKerja', 'jamLembur', 'hariKerja']
                );                
                }
                $retValue = $affected." record presensi pegawai telah digenerate";
            } else{
                $retValue = "Tidak terdapat record presensi pegawai yang belum digenerate";
            }

            return $retValue;
        }

        public function salaryBoronganGenerate($start, $end, $payrollId)
        {
            $rowCount = DB::table('borongans as b')
            ->where('b.status', 1)
            ->whereIn('e.employmentStatus', [2,3])
            ->whereBetween('b.tanggalKerja', [$start, $end])
            ->join('detail_borongans as db', 'b.id', '=',  'db.boronganId')
            ->join('employees as e', 'e.id', '=',  'db.employeeId')
            ->count();

            $retValue="";
            if ($rowCount>0){
                $data = [
                    'startDate'         => $start,
                    'endDate'           => $end,
                    'userIdGenerator'   => auth()->user()->id,
                    'jenis'             => 3,
                    'isPaid'            => null,
                    'idPayroll'         => $payrollId
                ];


                $salariesPaidExist=DB::table('salaries as s')
                ->select(
                    DB::raw('max(s.id) as sid'),
                )
                ->join('payrolls as p', 'p.id', '=', 's.idPayroll')
                ->where('endDate', '=', $end->toDateString())
                ->where('s.jenis', '=', 3)
                ->where('isPaid', '=', null)
                ->where('p.jenis', 2)
                ->first();

                $salaryId="";
                if($salariesPaidExist->sid == null){
                    $salaryId = DB::table('salaries')->insertGetId($data);
                } else{
                    $salaryId = $salariesPaidExist->sid;
                }

                $affected = DB::table('borongans as b')
                ->where('status', 1)
                ->whereBetween('b.tanggalKerja', [$start->toDateString(), $end->toDateString()])
                ->update([
                    'status' => 2, 
                    'salariesId' => $salaryId
                ]);

                $moveGeneratedData = DB::table('detail_borongans as db')
                ->select(
                    'db.employeeId as empid',
                    DB::raw('sum(b.netWeight) as berat'),
                    DB::raw('sum(netPayment) as jumlah')
                )
                ->join('borongans as b', 'db.boronganId', '=', 'b.id')
                ->where('b.salariesId', '=', $salaryId)
                ->groupBy('db.employeeId')
                ->get();

                foreach($moveGeneratedData as $row){
                    DB::table('detail_payrolls')
                    ->upsert([
                        [
                            'idPayroll'         => $payrollId, 
                            'employeeId'        => $row->empid, 
                            'idPayrollEmpid'    => ($payrollId.'-'.$row->empid), 
                            'berat'             => $row->berat,
                            'borongan'          => $row->jumlah
                        ]
                    ],
                    ['idPayrollEmpid'], 
                    ['borongan', 'berat']
                );
                }
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
                's.id as sid',
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
            ->whereIn('e.employmentStatus', [2,3])
            ->groupBy('e.id')
            ->get();
            return datatables()->of($query)        
            ->addColumn('action', function ($row) {
                $html = '';
                if($row->statusIsPaid != 1){
                    $html.='<button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Tandai sudah dibayar" onclick="setIsPaidModal('."'".$row->sid."','".$row->empid."'".')">
                    <i class="fa fa-check" style="font-size:20px"></i>
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
            ->where('b.salariesId', $request->sid)
            ->where('db.employeeId', $request->empid)
            ->update([
                'db.isPaid' => 1, 
                'db.paidDate'=>Carbon::now()->toDateTimeString(),
                'db.userPaid'=>auth()->user()->id
            ]);

            $jumlahPaid = DB::table('detail_borongans as db')
            ->select(
                DB::raw('sum(db.isPaid) as terbayar'),
                'b.worker as worker'
            )
            ->join('borongans as b', 'b.id', '=', 'db.boronganId')
            ->where('b.salariesId', '=', $request->sid)
            ->first();

        //dd($jumlahPaid->terbayar." - - - ".$jumlahPaid->worker);
            if($jumlahPaid->terbayar == $jumlahPaid->worker){
                DB::table('salaries as s')
                ->where('s.id', $request->sid)
                ->update([
                    's.isPaid' => 1
                ]);
                DB::table('borongans as b')
                ->where('b.salariesId', $request->sid)
                ->update([
                    'b.status' => 3
                ]);
            }

            $retValue = [
                'message'       => "Record telah ditandai",
                'isError'       => "0"
            ];
            return $retValue;
        }

        public function getLemburPegawaiBulanan($salaryId){
            $query = DB::table('dailysalaries as ds')
            ->select(
                'ds.salaryId as sid',
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
                    onclick="setSalaryIsPaid('.$row->sid.','.$row->empid.')"><i class="fa fa-check"></i>
                    </button>';
                }
                return $html;
            })
            ->addIndexColumn()->toJson();
        }
        public function markLemburIsPaid(Request $request)
        {
            DB::table('dailysalaries')
            ->where('salaryId', $request->sid)
            ->where('employeeId', $request->empid)
            ->update([
                'isPaid' => 1,
                'payDate' => Carbon::now()->toDateTimeString(),
                'userPaid' => auth()->user()->id
            ]);

            $jumlahPaid = DB::table('dailysalaries as ds')
            ->select(
                DB::raw('count(ds.isPaid) as terbayar')
            )
            ->where('ds.isPaid', '=', null)
            ->where('ds.salaryid', '=', $request->sid)
            ->first();

            if($jumlahPaid->terbayar == 0){
                DB::table('salaries as s')
                ->where('s.id', $request->sid)
                ->update([
                    's.isPaid' => 1
                ]);
            }
            return true;
        }

        public function getSalariesHarian(){
            $query = DB::table('salaries as s')
            ->select(
                's.id as id', 
                's.endDate as enddate',
                DB::raw('
                    count(distinct(concat(ds.isPaid,ds.employeeId)))
                    AS isPaidStatus'), 
                DB::raw('
                    concat(
                    count(distinct(concat(ds.isPaid,ds.employeeId))), 
                    " dari ", 
                    count(distinct(ds.employeeId)), " pegawai"
                    )
                    AS terbayar'),            
                'ug.name as generatorName'
            )
            ->join('dailysalaries as ds', 'ds.salaryid', '=', 's.id')
            ->join('employees as e', 'e.id', '=', 'ds.employeeId')
            ->leftjoin('users as ug', 's.userIdGenerator', '=', 'ug.id')
            ->where('jenis', 2)
            ->where('e.employmentStatus', '=', 2)
            ->where('ds.isGenerated', '=', 1)
            ->orderBy('s.enddate', 'desc')
            ->groupBy('ds.salaryid');
            $query->get();

            return datatables()->of($query)
            ->addColumn('action', function ($row) {
                $html = '
                <a data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak gaji pegawai harian" href="checkCetakGajiPegawaiHarian/'.$row->id.'">
                <i class="fa fa-print"></i>
                </a>';
                if ($row->isPaidStatus == 0 ){
                    $html.='
                    <button data-rowid="'.$row->id.'" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Generate Gaji" onclick="hapusGenerateGajiHarian('."'".$row->id."'".')">
                    <i class="fa fa-trash"></i>
                    </button>
                    ';
                }

                return $html;
            })->addIndexColumn()->toJson();
        }  


        public function getSalariesHonorarium(){
            $query = DB::table('salaries as s')
            ->select(
                's.id as id', 
                's.endDate as enddate',
                DB::raw('
                    count(distinct(concat(h.isPaid, h.employeeId)))
                    AS isPaidStatus'), 
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
                if ($row->isPaidStatus == 0 ){
                    $html.='
                    <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus generate honorarium" onclick="hapusGenerateHonorarium('."'".$row->id."'".')">
                    <i class="fa fa-trash"></i>
                    </button>
                    ';
                }
                return $html;
            })->addIndexColumn()->toJson();
        }  

        public function getLemburBulanan(){
            $query = DB::table('salaries as s')
            ->select(
                's.id as id', 
                's.endDate as enddate',
                DB::raw('
                    count(distinct(concat(ds.isPaid, ds.employeeId)))
                    AS isPaidStatus'), 
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
                </a>';
                if ($row->isPaidStatus == 0 ){
                    $html.='
                    <button data-rowid="'.$row->id.'" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Generate Gaji" onclick="hapusGenerateLemburBulanan('."'".$row->id."'".')">
                    <i class="fa fa-trash"></i>
                    </button>
                    ';
                }
                return $html;
            })->addIndexColumn()->toJson();
        }  



        public function getSalariesBorongan(){
            $query = DB::table('salaries as s')
            ->select(
                'b.id as id', 
                's.id as sid',
                's.endDate as tanggalKerja', 
                's.created_at as tanggalGenerate', 
                DB::raw('sum(db.isPaid) AS isPaidStatus'), 
                DB::raw('count(distinct(db.employeeId)) AS jumlahEmployee'), 
                'u.name as generatorName'
            )
            ->join('borongans as b', 'b.salariesId', '=', 's.id')
            ->join('detail_borongans as db', 'db.boronganId', '=', 'b.id')
            ->join('employees as e', 'e.id', '=', 'db.employeeId')
            ->join('users as u', 's.userIdGenerator', '=', 'u.id')
            ->where('s.jenis', 3)
            ->groupBy('s.id')
            ->orderBy('s.enddate');
            $query->get();

            return datatables()->of($query)
            ->addColumn('terbayar', function ($row) {
                $html = $row->isPaidStatus." dari ".$row->jumlahEmployee;
                return $html;
            })
            ->addColumn('action', function ($row) {
                $html = '
                <a data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak gaji pegawai borongan" 
                href="checkCetakGajiPegawaiBorongan/'.$row->id.'"><i class="fa fa-print"></i>
                </a>';
                if($row->isPaidStatus == 0){
                    $html.='
                    <button data-rowid="'.$row->id.'" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Generate Gaji Borongan" onclick="hapusGenerateBorongan('."'".$row->sid."'".')">
                    <i class="fa fa-trash"></i>
                    </button>
                    ';
                }
                return $html;
            })
            ->addIndexColumn()->toJson();
        }  

        public function checkCetakGajiPegawaiHarian(Salary $salary){
            $generatorName = DB::table('users as u')
            ->select('name')
            ->where('u.id', '=', $salary->userIdGenerator)
            ->first()->name;
            return view('salary.checkSalaryHarianList', compact('salary', 'generatorName'));
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
                'h.keterangan as keterangan',
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
            ->whereIn('e.employmentStatus', [2,3])
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
            $salary = DB::table('salaries as s')->where('s.id', '=', $borongan->salariesId)->first();

            $user = DB::table('users as u')
            ->select('u.name as name')
            ->join('salaries as s', 's.userIdGenerator', '=', 'u.id')
            ->where('s.id', '=', $borongan->salariesId)
            ->first();
            $generatorName=$user->name;

            return view('salary.checkSalaryBoronganList', compact('generatorName','borongan', 'salary'));
        }
        public function checkCetakHonorariumPegawai(Salary $salary){
            $generatorName = DB::table('users as u')->select('name')->where('u.id', '=', $salary->userIdGenerator)->first()->name;
            return view('salary.checkHonorariumList', compact('generatorName','salary'));
        }
        public function harianMarkedPaid(Request $request){
            DB::table('dailysalaries')
            ->where('salaryId', $request->sid)
            ->where('employeeId', $request->empid)
            ->update([
                'isPaid' => 1,
                'payDate' => Carbon::now()->toDateTimeString(),
                'userPaid' => auth()->user()->id
            ]);

            $jumlahPaid = DB::table('dailysalaries as ds')
            ->select(
                DB::raw('count(ds.isPaid) as terbayar')
            )
            ->where('ds.isPaid', '=', null)
            ->where('ds.salaryid', '=', $request->sid)
            ->first();

            if($jumlahPaid->terbayar == 0){
                DB::table('salaries as s')
                ->where('s.id', $request->sid)
                ->update([
                    's.isPaid' => 1
                ]);
            }
            return true;
        }

        public function honorariumMarkedPaid(Request $request){
            DB::table('honorariums as h')
            ->where('h.id', $request->hid)
            ->update([
                'h.isPaid' => 1,
                'h.paidDate' => Carbon::now()->toDateTimeString(),
                'h.userPaid' => auth()->user()->id
            ]);

            $jumlahPaid = DB::table('honorariums as h')
            ->select(
                DB::raw('count(h.isPaid) as terbayar')
            )
            ->where('h.isPaid', '=', null)
            ->where('h.salaryid', '=', $request->sid)
            ->first();

            if($jumlahPaid->terbayar == 0){
                DB::table('salaries as s')
                ->where('s.id', $request->sid)
                ->update([
                    's.isPaid' => 1
                ]);
            }

            return true;
        }

        public function getSalariesHonorariumForCheck($salaryId){
            $query = DB::table('honorariums as h')
            ->select(
                'e.id as empid',
                'e.nip as nip',
                'u.name as name',
                'h.salaryId as sid',
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
                    onclick="setHonorariumIsPaid('.$row->hid.','.$row->sid.')"><i class="fa fa-check"></i>
                    </button>';
                }
                return $html;
            })
            ->addIndexColumn()->toJson();
        }


        public function getSalariesHarianForCheck(Salary $salary){
        //DB::enableQueryLog(); // Enable query log
            $query = DB::table('salaries as s')
            ->select(
                'u.name as name',
                'e.nip as nip',
                'os.name as osname',
                'e.noRekening as noRekening',
                'b.name as bankName',
                'e.id as empid',
                'ds.salaryId as sid',
                DB::raw('count(ds.id) as jumlahId'),
                DB::raw('sum(ds.uangharian) as uh'),
                DB::raw('sum(ds.uanglembur) as ul'),
                DB::raw('sum(ds.isPaid) as jumlahIsPaid'),
            )
            ->join('dailysalaries as ds', 'ds.salaryid', '=', 's.id')
            ->join('employees as e', 'e.id', '=', 'ds.employeeId')
            ->join('banks as b', 'b.id', '=', 'e.bankid')
            ->join('users as u', 'u.id', '=', 'e.userid')
            ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
            ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')

            ->where('ds.salaryid', $salary->id)
            ->where('e.employmentStatus', 2)
            ->where('eosm.isactive', 1)
            ->whereBetween('ds.presenceDate', [$salary->startDate, $salary->endDate])
            ->groupBy('ds.salaryId')
            ->groupBy('ds.employeeId')
            ->having('ds.salaryId', '=',$salary->id)
            ->get();
        //dd(DB::getQueryLog());
            return datatables()->of($query)
            ->addColumn('action', function ($row) {
                $html='';
                if ($row->jumlahIsPaid < $row->jumlahId){
                    $html .= '
                    <button data-rowid="'.$row->empid.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Set sudah dibayar" 
                    onclick="setSalaryIsPaid('.$row->sid.','.$row->empid.')"><i class="fa fa-check"></i>
                    </button>';
                }
                return $html;
            })
            ->addColumn('isPaid', function ($row) {
                $html='SUDAH';
                if ($row->jumlahIsPaid < $row->jumlahId){
                    $html = 'BELUM';
                }
                return $html;
            })
            ->addColumn('total', function ($row) {
                return $row->uh + $row->ul;
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


        public function hapusGenerateGajiHarian(Request $request){
        //dd($request);
            $affected = DB::table('dailysalaries')
            ->where('salaryid', '=', $request->sid)
            ->update([
                'salaryid' => null,
                'isGenerated' => 0
            ]);

            $jumlahPaid = DB::table('salaries')
            ->where('id', '=', $request->sid)
            ->delete();
            $data[] = $affected." baris catatan penggajian dihapus.";
            return $data;
        }
        public function hapusGenerateLemburBulanan(Request $request){
            $affected = DB::table('dailysalaries')
            ->where('salaryid', '=', $request->sid)
            ->update([
                'salaryid' => null,
                'isGenerated' => 0
            ]);

            $jumlahPaid = DB::table('salaries')
            ->where('id', '=', $request->sid)
            ->delete();
            $data[] = $affected." baris catatan penggajian dihapus.";
            return $data;
        }
        public function hapusGenerateHonorarium(Request $request){
            $affected = DB::table('honorariums')
            ->where('salaryId', '=', $request->sid)
            ->update([
                'salaryId' => null,
                'isGenerated' => 0
            ]);

            $jumlahPaid = DB::table('salaries')
            ->where('id', '=', $request->sid)
            ->delete();
            $data[] = $affected." baris catatan penggajian dihapus.";
            return $data;
        }
        public function hapusGenerateBorongan(Request $request){
            $affected = DB::table('borongans')
            ->where('salariesId', '=', $request->sid)
            ->update([
                'salariesId' => null,
                'status' => 1
            ]);

            $jumlahPaid = DB::table('salaries')
            ->where('id', '=', $request->sid)
            ->delete();
            $data[] = $affected." baris catatan penggajian dihapus.";
            return $data;
        }

        public function getSalariesList($salaryId){
            $query = DB::table('salaries as s')
            ->select(
                's.id as id',
                DB::raw('(CASE 
                    WHEN s.jenis="1" THEN "Bulanan" 
                    WHEN s.jenis="2" THEN "Harian" 
                    WHEN s.jenis="3" THEN "Borongan" 
                    WHEN s.jenis="4" THEN "Honorarium"                 
                    END) as jenisSalary'),
                's.startDate as startdate',
                DB::raw('(CASE 
                    WHEN s.isPaid is null THEN "BELUM" 
                    WHEN s.isPaid is not null THEN "SUDAH" 
                    END) as isPaid'),
                's.enddate as enddate',
                'u.name as generator',
                's.id as total',
                's.idPayroll as idp',
            )
            ->join('users as u', 'u.id', '=', 's.userIdGenerator')
            ->where('s.idPayroll', '=', $salaryId)
            ->get();

            return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '1';
                return $html;
            })
            ->toJson();
        }  
        public function getPayrollList($start, $end){
            $start = Carbon::parse($start);
            $end = Carbon::parse($end);

            $query = DB::table('payrolls as p')
            ->select(
                'p.id as id',
                'p.payDate as payDate',
                'u.name as generator',
                DB::raw('count(distinct(dp.id)) as totalPegawai'),
                DB::raw('sum(dp.harian) as harian'),
                DB::raw('sum(dp.borongan) as borongan'),
                DB::raw('sum(dp.honorarium) as honorarium')
            )
            ->join('detail_payrolls as dp', 'dp.idPayroll', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'p.creator')
            ->where('p.jenis', '=', '2')
            ->whereBetween('p.payDate', [$start->startOfDay(), $end->endOfDay()])
            ->groupBy('p.id')
            ->get();

            return datatables()->of($query)
            ->addIndexColumn()


            ->editColumn('totalPegawai', function ($row) {
                return $row->totalPegawai.' Pegawai';
            })
            ->editColumn('totalBayar', function ($row) {
                return 'Rp. '.number_format(($row->harian+$row->borongan+$row->honorarium), 2, ',', '.');
            })
            ->editColumn('harian', function ($row) {
                return 'Rp. '.number_format($row->harian, 2, ',', '.');
            })
            ->editColumn('borongan', function ($row) {
                return 'Rp. '.number_format($row->borongan, 2, ',', '.');
            })
            ->editColumn('honorarium', function ($row) {
                return 'Rp. '.number_format($row->honorarium, 2, ',', '.');
            })
            ->addColumn('action', function ($row) {


                $html = '
                <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak daftar gaji pegawai" onclick="printPayrollList('.$row->id.')">
                <i class="fa fa-print" style="font-size:20px"></i>
                </button>
                ';
                if (Auth::user()->accessLevel <= 10){
                    $html .= '
                    <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus catatan payroll ini" onclick="deletePayrollList('.$row->id.')">
                    <i class="fas fa-trash" style="font-size:20px"></i>
                    </button>
                    ';                
                }


                return $html;
            })
            ->toJson();
        }  
        public function getPayrollListBulanan($start, $end){
            $start = Carbon::parse($start);
            $end = Carbon::parse($end);

            $query = DB::table('payrolls as p')
            ->select(
                'p.id as id',
                'p.payDate as payDate',
                'u.name as generator',
                DB::raw('count(distinct(dp.id)) as totalPegawai'),
                DB::raw('sum(dp.harian) as harian'),
                DB::raw('sum(dp.bulanan) as bulanan'),
                DB::raw('sum(dp.honorarium) as honorarium')
            )
            ->join('detail_payrolls as dp', 'dp.idPayroll', '=', 'p.id')
            ->join('users as u', 'u.id', '=', 'p.creator')
            ->where('p.jenis', '=', '1')
            ->whereBetween('p.payDate', [$start->startOfDay(), $end->startOfDay()])
            ->groupBy('p.id')
            ->get();

            return datatables()->of($query)
            ->addIndexColumn()


            ->editColumn('totalPegawai', function ($row) {
                return $row->totalPegawai.' Pegawai';
            })
            ->editColumn('totalBayar', function ($row) {
                return 'Rp. '.number_format(($row->harian+$row->bulanan+$row->honorarium), 2, ',', '.');
            })
            ->editColumn('harian', function ($row) {
                return 'Rp. '.number_format($row->harian, 2, ',', '.');
            })
            ->editColumn('bulanan', function ($row) {
                return 'Rp. '.number_format($row->bulanan, 2, ',', '.');
            })
            ->editColumn('honorarium', function ($row) {
                return 'Rp. '.number_format($row->honorarium, 2, ',', '.');
            })
            ->addColumn('action', function ($row) {
                $html = '
                <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak daftar gaji pegawai" onclick="printPayrollList('.$row->id.')">
                <i class="fa fa-print" style="font-size:20px"></i>
                </button>
                ';
                if (Auth::user()->accessLevel <= 10){
                    $html .= '
                    <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus catatan payroll ini" onclick="deletePayrollList('.$row->id.')">
                    <i class="fas fa-trash" style="font-size:20px"></i>
                    </button>
                    ';                
                }
                return $html;
            })
            ->toJson();
        }  
        public function printPayrollList($payrollId){
            return view('salary.checkPayrollList', compact('payrollId'));
        }
        public function printPayrollListBulanan($payrollId){
            return view('salary.checkPayrollListBulanan', compact('payrollId'));
        }
        public function getEmployeeDetailSalaries($jenis, $payrollId){
            $query = DB::table('detail_payrolls as dp')
            ->select(
                'dp.id as dpid',
                'u.name as name',
                'e.nip as nip',
                'e.noRekening as noRekening',
                'b.shortname as bankName',
                'dp.hariKerja as hk',
                'dp.jamKerja as jk',
                'dp.jamLembur as jl',
                'dp.berat as berat',
                'dp.bulanan as bulanan',
                'dp.harian as harian',
                'dp.borongan as borongan',
                'dp.honorarium as honorarium'
            )
            ->join('employees as e', 'e.id', '=', 'dp.employeeId')
            ->join('banks as b', 'b.id', '=', 'e.bankid')
            ->join('users as u', 'u.id', '=', 'e.userid')
            ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
            ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        //->where('eosm.isactive', 1)
        //->where('e.isactive', '=', 1)        
            ->where('dp.idPayroll', '=', $payrollId);

        //bulanan
            if ($jenis==1){
                $query->whereIn('e.employmentStatus', ['1']);
            } else{
                $query->whereIn('e.employmentStatus', ['2','3']);
            }

            $query->groupBy('e.id')
            ->get();

            return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html ='
                <button  data-rowid="'.$row->dpid.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak slip gaji" onclick="cetakSlipGajiPayroll('."'".$row->dpid."'".')">
                <i class="fa fa-print"></i>
                </button> 
                ';
                return $html;
            })
            ->editColumn('name', function ($row) {
                $html = '
                <div style="text-align:left" class="row form-group">
                <span class="col-12">'.$row->name.'</span>
                </div>
                <div style="text-align:left" class="row form-group">
                <span class="col-12">'.$row->nip.'</span>
                </div>
                ';
                return $html;

                $row->hk." hari, ".$row->jk." jam, ".$row->jl." lembur";
                return $html;
            })
            ->addColumn('detilHarian', function ($row) {
                $html = '
                <div style="text-align:left" class="row form-group">
                <span class="col-5">Hari Kerja</span>
                <span class="col-1">:</span>
                <span style="text-align:right" class="col-5">'.$row->hk.' hari</span>
                </div>
                <div style="text-align:left" class="row form-group">
                <span class="col-5">Jam Kerja</span>
                <span class="col-1">:</span>
                <span style="text-align:right" class="col-5">'.$row->jk.' jam</span>
                </div>
                <div style="text-align:left" class="row form-group">
                <span class="col-5">Jam Lembur</span>
                <span class="col-1">:</span>
                <span style="text-align:right" class="col-5">'.$row->jl.' jam</span>
                </div>
                <div style="text-align:left" class="row form-group">
                <span class="col-5">Borongan</span>
                <span class="col-1">:</span>
                <span style="text-align:right" class="col-5">'.number_format($row->berat, 2, ',', '.')." Kg".'</span>
                </div>
                ';
                return $html;

                $row->hk." hari, ".$row->jk." jam, ".$row->jl." lembur";
                return $html;
            })
            ->addColumn('detilBorongan', function ($row) {
                $html = number_format($row->berat, 2, ',', '.')." Kg";
                return $html;
            })
            ->editColumn('harian', function ($row) {
                $html = number_format($row->harian, 2, ',', '.');
                return $html;
            })
            ->editColumn('bulanan', function ($row) {
                $html = number_format($row->bulanan, 2, ',', '.');
                return $html;
            })
            ->editColumn('borongan', function ($row) {
                $html = number_format($row->borongan, 2, ',', '.');
                return $html;
            })
            ->editColumn('honorarium', function ($row) {
                $html = number_format($row->honorarium, 2, ',', '.');
                return $html;
            })        
            ->addColumn('bank', function ($row) {
                $html = $row->bankName.'-'.$row->noRekening;
                return $html;
            })
            ->addColumn('total', function ($row) {
                $total = $row->bulanan+$row->borongan+$row->honorarium+$row->harian;
                return $total;
            })
            ->rawColumns(['action', 'detilHarian', 'name'])
            ->toJson();
        }
    }
