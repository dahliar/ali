<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Species;
use PDF;



class DashboardController extends Controller
{
    public function getServerDate(){
        return Carbon::now()->toDateString();
    }
    public function index(Request $request)
    {   
        if ($request->has('tahun')){
            $request->validate([
                'tahun' => 'required|numeric|gt:0',
            ],[
                'tahun.*'=> 'Pilih tahun dulu'
            ]);
            $tahun = $request->tahun;

            $employees = DB::table('employees as e')
            ->select(
                DB::raw('
                    (CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS empStatus
                    '),
                DB::raw('count(e.id) as status')
            )
            ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
            ->where('mapping.isActive', '1')
            ->groupBy('employmentStatus')
            ->get();

            $employeesGender = DB::table('employees as e')
            ->select(
                DB::raw('
                    (CASE WHEN e.gender="1" THEN "Laki-laki" WHEN e.gender="2" THEN "Perempuan" END) AS gender
                    '),
                DB::raw('count(e.id) as jumlahGender')
            )
            ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
            ->where('mapping.isActive', '1')
            ->groupBy('e.gender')
            ->get();

            $transactions = DB::table('transactions as t')
            ->select(
                DB::raw('
                    (CASE WHEN t.jenis="1" THEN "Ekspor" WHEN t.jenis="2" THEN "Lokal" END) AS jenis
                    '),
                DB::raw('count(t.id) as jumlahJenis')
            )
            ->whereIn('t.status', [2,4])
            ->groupBy('t.jenis')
            ->whereYear('t.transactionDate', $tahun)
            ->get();

            $stocks = DB::table('items as i')
            ->select(
                'sp.nameBahasa as name',
                DB::raw('sum(i.amount) as jumlahSpecies'),
                DB::raw('"blue" as kedua')
            )
            ->join('sizes as s', 's.id', '=', 'i.sizeId')
            ->join('species as sp', 'sp.id', '=', 's.speciesId')
            ->groupBy('sp.id')
            ->orderBy('sp.name')
            ->get();

            $transactionRupiah = DB::table('transactions as t')
            ->select(
                'c.name as name',
                DB::raw('sum(t.payment) as amount')
            )
            ->join('companies as c', 'c.id', '=', 't.companyId')
            ->where('t.valutaType', '=', '1')
            ->whereYear('t.transactionDate', $tahun)
            ->groupBy('c.id')
            ->orderBy('c.name')
            ->get();

            $transactionUSD = DB::table('transactions as t')
            ->select(
                'c.name as name',
                DB::raw('sum(t.payment) as amount')
            )
            ->join('companies as c', 'c.id', '=', 't.companyId')
            ->where('t.valutaType', '=', '2')
            ->groupBy('c.id')
            ->orderBy(DB::raw('sum(t.payment)'), 'desc')
            ->whereYear('t.transactionDate', $tahun)
            ->get();



            $transactionUSDLine = DB::table('transactions as t')
            ->select(
                DB::raw('MONTH(t.transactionDate) as bulan'),
                DB::raw('sum(t.payment) as amount')
            )
            ->where('t.valutaType', '=', '2')
            ->orderBy(DB::raw('sum(t.payment)'), 'desc')
            ->whereYear('t.transactionDate', $tahun)
            ->groupBy(DB::raw('MONTH(t.transactionDate)'))
            ->get();
            $transactionRupiahLine = DB::table('transactions as t')
            ->select(
                DB::raw('MONTH(t.transactionDate) as bulan'),
                DB::raw('sum(t.payment) as amount')
            )
            ->where('t.valutaType', '=', '1')
            ->orderBy(DB::raw('sum(t.payment)'), 'desc')
            ->whereYear('t.transactionDate', $tahun)
            ->groupBy(DB::raw('MONTH(t.transactionDate)'))
            ->get();




            $goods = DB::table('goods as g')
            ->select(
                'g.name as name',
                'g.amount as amount',
                'g.minimalAmount as minimal'
            )
            ->whereRaw('g.amount <= g.minimalAmount')
            ->orderBy('g.name')
            ->get();
        //dd($goods);

            $purchases = DB::table('purchases as p')
            ->select(
                'c.name as name',
                DB::raw('sum(p.paymentAmount) as amount')
            )
            ->join('companies as c', 'c.id', '=', 'p.companyId')
            ->groupBy('c.id')
            ->orderBy(DB::raw('sum(p.paymentAmount)'), 'desc')
            ->whereYear('p.purchaseDate', $tahun)
            ->get();


            $purchaseRupiahLine = DB::table('purchases as p')
            ->select(
                DB::raw('MONTH(p.purchaseDate) as bulan'),
                DB::raw('sum(p.paymentAmount) as amount')
            )
            ->where('p.valutaType', '=', '1')
            ->orderBy(DB::raw('sum(p.paymentAmount)'), 'desc')
            ->whereYear('p.purchaseDate', $tahun)
            ->groupBy(DB::raw('MONTH(p.purchaseDate)'))
            ->get();

            $birthday = DB::table('employees as e')
            ->select(
                'u.name as name',
                'e.birthdate as birthdate',
                DB::raw('concat(
                    TIMESTAMPDIFF(YEAR, e.birthdate, curdate()), 
                    " Y") as usia')
            )
            ->join('users as u', 'u.id', '=', 'e.userid')
            ->whereMonth('e.birthdate', date('m'))
            ->orderBy(DB::raw('DAY(e.birthdate)'))
            ->get();

            $date = Carbon::parse(now())->locale('id');
            $date->settings(['formatFunction' => 'translatedFormat']);
            $month=$date->format('F');

            return view('home', compact('month','birthday','purchaseRupiahLine','transactionRupiahLine','transactionUSDLine','purchases','transactionRupiah','transactionUSD','employees','transactions','stocks','employeesGender','goods', 'tahun'));          
        }
        else{
            return view('home');
        }
    }

    public function indexHome2()
    {
        return view('home2');
    }


    public function indexHarga(Request $request)
    {
        $speciesList = Species::orderBy('name')->get();
        return view('dashboard.priceList', compact('speciesList'));
    }
    public function getPriceList($species, $start, $end){
        //DB::enableQueryLog();
        $startStr=$start." 00:00:00";
        $endStr=$end." 23:59:59";
        //dd($species);

        $query = DB::table('items as i')
        ->select(
            DB::raw('concat(sp.name," ",g.name," ",s.name," ",f.name) as itemName'),
            DB::raw('ifnull(min(dp.price),0) as minPrice'),
            DB::raw('ifnull(avg(dp.price),0) as avgPrice'),
            DB::raw('ifnull(max(dp.price),0) as maxPrice')
        )
        ->leftjoin('detail_purchases as dp', 'i.id', '=', 'dp.itemId')
        ->join('purchases as pur', 'dp.purchasesId', '=', 'pur.id')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->whereBetween('pur.purchaseDate', [$startStr, $endStr])
        ->groupBy('i.id')
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderBy('s.name', 'asc');

        if ($species>0){
            $query->where('sp.id','=', $species);
        }

        

        return datatables()->of($query)

        ->editColumn('minPrice', function ($row) {
            return number_format($row->minPrice, 2);
        })
        ->editColumn('maxPrice', function ($row) {
            return number_format($row->maxPrice, 2);
        })
        ->editColumn('avgPrice', function ($row) {
            return number_format($row->avgPrice, 2);
        })
        ->addIndexColumn()
        ->toJson();
    }    

    public function indexHpp(Request $request)
    {
        $species = Species::orderBy('name')->get();
        return view('dashboard.hppList', compact('species'));
    }
    public function getHpp(Request $request){
        $species = Species::orderBy('name')->get();
        $harian = DB::table('dailysalaries')
        ->select(
            DB::raw('sum(uangHarian + uangLembur) as total'),
            DB::raw('count(id) as jumlahOrang'),
        )
        ->whereBetween('presenceDate', [$request->start, $request->end])
        ->first();

        $dataHarian=[
            'total' => $harian->total,
            'orang' => $harian->jumlahOrang
        ];

        $borongan = DB::table('borongans as b')
        ->select(
            DB::raw('sum(db.netPayment) as total'),
            DB::raw('count(db.employeeId) as jumlahOrang'),
        )
        ->join('detail_borongans as db', 'db.boronganId', '=', 'b.id')
        ->whereBetween('b.tanggalKerja', [$request->start, $request->end])
        ->first();

        $dataBorongan=[
            'total' => $borongan->total,
            'orang' => $borongan->jumlahOrang
        ];

        $honorarium = DB::table('honorariums as h')
        ->select(
            DB::raw('sum(jumlah) as total'),            
            DB::raw('count(h.employeeId) as jumlahOrang'),
        )
        ->whereBetween('tanggalKerja', [$request->start, $request->end])
        ->first();

        $dataHonorarium=[
            'total' => $honorarium->total,
            'orang' => $honorarium->jumlahOrang
        ];

        $purchases= DB::table('purchases as pur')
        ->join('companies as c', 'c.id', '=', 'pur.companyId')
        ->join('detail_purchases as dp', 'dp.purchasesId', '=', 'pur.id')
        ->join('items as i', 'i.id', '=', 'dp.itemId')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->select(
            'c.name as perusahaan',
            DB::raw('concat(sp.name, " ", g.name, " ", s.name) as name'), 
            'pur.purchaseDate as tanggal',
            'dp.amount as amount',
            'dp.price as price'
        )
        ->whereBetween('pur.purchaseDate', [$request->start, $request->end]);
        if ($request->species > 0){
            $purchases = $purchases->where('sp.id', '=', $request->species);
        }
        if ($request->item > 0){
            $purchases = $purchases->where('i.id', '=', $request->item);
        }

        $purchases = $purchases->get();
        $start=$request->start;
        $end=$request->end;
        $speciesChoosen=$request->species;
        $itemChoosen=$request->item;
        $showDetail=$request->showDetail;

        return view('dashboard.hppList', compact('showDetail','species','start','end','dataHarian', 'dataBorongan', 'dataHonorarium', 'purchases', 'speciesChoosen', 'itemChoosen'));    
    }


    public function rekapitulasiGaji(){
        return view('dashboard.rekapitulasiGaji');
    }
    public function getRekapitulasiGaji(Request $request){
        $dt = Carbon::create($request->tahun, 5, 2, 12, 0, 0);
        $tahun = $request->tahun;

        $payrollBulanan = DB::table('detail_payrolls as dp')
        ->select(
            DB::raw('MONTH(p.payDate) as bulan'),
            DB::raw('(sum(dp.bulanan) + sum(dp.harian) + sum(dp.borongan) +sum(dp.honorarium)) as bulanan'),
            DB::raw('0 as harian'),
            DB::raw('0 as borongan')
        )
        ->join('payrolls as p', 'p.id', '=', 'dp.idPayroll')
        ->join('employees as e', 'e.id', '=', 'dp.employeeId')
        ->where('e.employmentStatus', '=', '1')
        ->whereYear('p.payDate', $request->tahun)
        ->groupBy(DB::raw('MONTH(p.payDate)'))
        ->groupBy('e.employmentStatus')
        ->get();
        $payrollHarian = DB::table('detail_payrolls as dp')
        ->select(DB::raw('MONTH(p.payDate) as bulan'),
            DB::raw('0 as bulanan'),
            DB::raw('(sum(dp.bulanan) + sum(dp.harian) + sum(dp.borongan) +sum(dp.honorarium)) as harian'),
            DB::raw('0 as borongan')
        )
        ->join('payrolls as p', 'p.id', '=', 'dp.idPayroll')
        ->join('employees as e', 'e.id', '=', 'dp.employeeId')
        ->where('e.employmentStatus', '=', '2')
        ->whereYear('p.payDate', $request->tahun)
        ->groupBy(DB::raw('MONTH(p.payDate)'))
        ->groupBy('e.employmentStatus')
        ->get();

        $payrollBorongan = DB::table('detail_payrolls as dp')
        ->select(DB::raw('MONTH(p.payDate) as bulan'),
            DB::raw('0 as bulanan'),
            DB::raw('0 as harian'),
            DB::raw('(sum(dp.bulanan) + sum(dp.harian) + sum(dp.borongan) +sum(dp.honorarium)) as borongan')
        )
        ->join('payrolls as p', 'p.id', '=', 'dp.idPayroll')
        ->join('employees as e', 'e.id', '=', 'dp.employeeId')
        ->where('e.employmentStatus', '=', '3')
        ->whereYear('p.payDate', $request->tahun)
        ->groupBy(DB::raw('MONTH(p.payDate)'))
        ->groupBy('e.employmentStatus')
        ->get();

        $total=$payrollHarian->merge($payrollBulanan)->merge($payrollBorongan);
        $payroll = [ 
            ["Januari ".$tahun,0,0,0],
            ["Februari ".$tahun,0,0,0],
            ["Maret ".$tahun,0,0,0],
            ["April ".$tahun,0,0,0],
            ["Mei ".$tahun,0,0,0],
            ["Juni ".$tahun,0,0,0],
            ["Juli ".$tahun,0,0,0],
            ["Agustus ".$tahun,0,0,0],
            ["September ".$tahun,0,0,0],
            ["Oktober ".$tahun,0,0,0],
            ["November ".$tahun,0,0,0],
            ["Desember ".$tahun,0,0,0],
        ];
        foreach($total as $p){
            $payroll[$p->bulan-1][1] +=$p->bulanan; 
            $payroll[$p->bulan-1][2] +=$p->harian; 
            $payroll[$p->bulan-1][3] +=$p->borongan; 
        }

        $payrollChart = DB::table('detail_payrolls as dp')
        ->select(
            DB::raw('MONTH(p.payDate) as bulan'),
            'e.employmentStatus as status',
            DB::raw('(sum(dp.bulanan) + sum(dp.harian) + sum(dp.borongan) +sum(dp.honorarium)) as total')
        )
        ->join('payrolls as p', 'p.id', '=', 'dp.idPayroll')
        ->join('employees as e', 'e.id', '=', 'dp.employeeId')
        ->whereYear('p.payDate', "2022")
        ->groupBy(DB::raw('MONTH(p.payDate)'))
        ->whereYear('p.payDate', $request->tahun)
        ->groupBy('e.employmentStatus')
        ->get();

        return view('dashboard.rekapitulasiGaji', compact('payrollChart','tahun', 'payroll'));        
    }
    public function rekapitulasiGajiPerBulan(){
        return view('dashboard.rekapitulasiGajiPerBulan');
    }
    public function getRekapitulasiGajiPerBulan(Request $request){
        $request->validate(
            [
                'tahun'    => 'required|gt:0',
                'bulan'    => 'required|gt:0'
            ],[
                'tahun.*'  => 'Pilih tahun',
                'bulan.*'  => 'Pilih bulan'
            ]
        );

        $payroll = DB::table('detail_payrolls as dp')
        ->select(
            DB::raw('dp.employeeId as empid'),
            DB::raw('u.name as name'),
            DB::raw('sum(dp.bulanan) as bulanan'),
            DB::raw('sum(dp.harian) as harian'),
            DB::raw('sum(dp.borongan) as borongan'),
            DB::raw('sum(dp.honorarium) as honorarium')
        )
        ->join('payrolls as p', 'p.id', '=', 'dp.idPayroll')
        ->join('employees as e', 'e.id', '=', 'dp.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->whereMonth('p.payDate', $request->bulan)
        ->whereYear('p.payDate', $request->tahun)
        ->groupBy('dp.employeeId')
        ->orderBy('u.name')
        ->get();

        $tahun = $request->tahun;
        $bulan = $request->bulan;

        return view('dashboard.rekapitulasiGajiPerBulan', compact('tahun','bulan', 'payroll'));        
    }

    public function cetakRekapGajiBulanan(Request $request){
        $bulan="";
        switch($request->bulan){
            case 1 : $bulan="Januari";break;
            case 2 : $bulan="Februari";break;
            case 3 : $bulan="Maret";break;
            case 4 : $bulan="April";break;
            case 5 : $bulan="Mei";break;
            case 6 : $bulan="Juni";break;
            case 7 : $bulan="Juli";break;
            case 8 : $bulan="Agustus";break;
            case 9 : $bulan="September";break;
            case 10 : $bulan="Oktober";break;
            case 11 : $bulan="November";break;
            case 12 : $bulan="Desember";break;
        }

        $payroll = DB::table('detail_payrolls as dp')
        ->select(
            DB::raw('dp.employeeId as empid'),
            DB::raw('u.name as name'),
            DB::raw('sum(dp.bulanan) as bulanan'),
            DB::raw('sum(dp.harian) as harian'),
            DB::raw('sum(dp.borongan) as borongan'),
            DB::raw('sum(dp.honorarium) as honorarium')
        )
        ->join('payrolls as p', 'p.id', '=', 'dp.idPayroll')
        ->join('employees as e', 'e.id', '=', 'dp.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->whereMonth('p.payDate', $request->bulan)
        ->whereYear('p.payDate', $request->tahun)
        ->orderBy('u.name')
        ->groupBy('dp.employeeId')
        ->get();


        $monthYear = $bulan.' '.$request->tahun;

        $pdf = PDF::loadview('invoice.rekapGajiBulanan', compact('monthYear', 'payroll'))->setPaper('a4', 'landscape');
        $filename = 'Rekap Gaji '.$monthYear.' cetak tanggal '.today().'.pdf';
        return $pdf->download($filename);



    }

    public function rekapitulasiPembelianPerBulan(){
        return view('dashboard.rekapitulasiPembelianPerBulan');
    }
    public function getRekapitulasiPembelianPerBulan(Request $request){
        $request->validate(
            [
                'tahun'    => 'required|gt:0',
                'bulan'    => 'required|gt:0'
            ],[
                'tahun.*'  => 'Pilih tahun',
                'bulan.*'  => 'Pilih bulan'
            ]
        );

        $payroll = DB::table('purchases as p');
        if($request->opsi == '1'){
            $payroll = $payroll->select(
                DB::raw('c.name as name'),
                DB::raw('p.purchasingNum as nomor'),
                DB::raw('c.npwp as npwp'),
                DB::raw('p.purchaseDate as tanggal'),
                DB::raw('p.paymentAmount as jumlah'),
                DB::raw('p.taxPercentage as persen'),
                DB::raw('p.tax as pajak'),
                DB::raw('(CASE   WHEN p.taxIncluded=0 THEN "Tidak" 
                    WHEN p.taxIncluded="1" THEN "Ya" 
                    END) as taxIncluded'
                )
            )
            ->join('companies as c', 'p.companyId', '=', 'c.id')
            ->whereMonth('p.purchaseDate', $request->bulan)
            ->whereYear('p.purchaseDate', $request->tahun)
            ->orderBy('p.purchasingNum', 'asc')
            ->orderBy('p.purchaseDate');
        } else if($request->opsi == '2'){
            $payroll = $payroll->select(
                DB::raw('c.name as name'),
                DB::raw('c.npwp as npwp'),
                DB::raw('sum(p.paymentAmount) as jumlah'),
                DB::raw('p.taxPercentage as persen'),
                DB::raw('sum(p.tax) as pajak'),
                DB::raw('(CASE   WHEN p.taxIncluded=0 THEN "Tidak" 
                    WHEN p.taxIncluded="1" THEN "Ya" 
                    END) as taxIncluded'
                )
            )
            ->join('companies as c', 'p.companyId', '=', 'c.id')
            ->whereMonth('p.purchaseDate', $request->bulan)
            ->whereYear('p.purchaseDate', $request->tahun)
            ->groupBy("c.id")
            ->orderBy('c.name');
        }
        $payroll = $payroll->get();

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $opsi = $request->opsi;

        return view('dashboard.rekapitulasiPembelianPerBulan', compact('opsi','tahun','bulan', 'payroll'));        
    }

    public function cetakRekapPembelianPerBulan(Request $request){
        $bulan="";
        switch($request->bulan){
            case 1 : $bulan="Januari";break;
            case 2 : $bulan="Februari";break;
            case 3 : $bulan="Maret";break;
            case 4 : $bulan="April";break;
            case 5 : $bulan="Mei";break;
            case 6 : $bulan="Juni";break;
            case 7 : $bulan="Juli";break;
            case 8 : $bulan="Agustus";break;
            case 9 : $bulan="September";break;
            case 10 : $bulan="Oktober";break;
            case 11 : $bulan="November";break;
            case 12 : $bulan="Desember";break;
        }

        $payroll = DB::table('purchases as p');

        if($request->opsi == '1'){
            $payroll = $payroll->select(
                DB::raw('c.name as name'),
                DB::raw('p.purchasingNum as nomor'),
                DB::raw('c.npwp as npwp'),
                DB::raw('p.purchaseDate as tanggal'),
                DB::raw('p.paymentAmount as jumlah'),
                DB::raw('p.taxPercentage as persen'),
                DB::raw('p.tax as pajak'),
                DB::raw('(CASE   WHEN p.taxIncluded=0 THEN "Tidak" 
                    WHEN p.taxIncluded="1" THEN "Ya" 
                    END) as taxIncluded'
                )
            )
            ->join('companies as c', 'p.companyId', '=', 'c.id')
            ->whereMonth('p.purchaseDate', $request->bulan)
            ->whereYear('p.purchaseDate', $request->tahun)
            ->orderBy('p.purchasingNum', 'asc')
            ->orderBy('p.purchaseDate');
        } else if($request->opsi == '2'){
            $payroll = $payroll->select(
                DB::raw('c.name as name'),
                DB::raw('c.npwp as npwp'),
                DB::raw('sum(p.paymentAmount) as jumlah'),
                DB::raw('p.taxPercentage as persen'),
                DB::raw('sum(p.tax) as pajak'),
                DB::raw('(CASE   WHEN p.taxIncluded=0 THEN "Tidak" 
                    WHEN p.taxIncluded="1" THEN "Ya" 
                    END) as taxIncluded'
                )
            )
            ->join('companies as c', 'p.companyId', '=', 'c.id')
            ->whereMonth('p.purchaseDate', $request->bulan)
            ->whereYear('p.purchaseDate', $request->tahun)
            ->groupBy("c.id")
            ->orderBy('c.name');
        }
        $payroll = $payroll->get();


        $monthYear = $bulan.' '.$request->tahun;
        $opsi = $request->opsi;

        $pdf = PDF::loadview('invoice.rekapPembelianPerBulan', compact('opsi','monthYear', 'payroll'))->setPaper('a4', 'landscape');
        $filename = 'Rekap pembelian '.$monthYear.' cetak tanggal '.today().'.pdf';
        return $pdf->download($filename);
    }

    public function checkPayrollByDateRange(){
        return view ('salary.checkPayrollByDateRange');

    }

    /*
    SELECT 'harian',        null,           ds.employeeId,  ds.uangHarian,  ds.uangLembur,  null as borongan,   null as honorarium, ds.presenceDate     from dailysalaries as ds where ds.presenceDate='2022-04-03'
    union
    select 'borongan',      b.name,         db.employeeId,  null,           null,           db.netPayment,      null,               b.tanggalKerja      from borongans b join detail_borongans db on b.id=db.boronganId where b.tanggalKerja='2022-04-03'
    UNION
    select 'honorarium',    h.keterangan,   h.employeeId,   null,           null,           null,               h.jumlah,           h.tanggalKerja      from honorariums h where h.tanggalKerja='2022-04-03'
    */
    public function getPayrollByDateRange(Request $request){
        //dd($request);
        $start=$request->start;
        $end=$request->end;
        $opsi=$request->opsi;

        $harian = DB::table('dailysalaries as ds')
        ->select('e.id as empid', 'u.name as name', 'ds.uangHarian as uh', 'ds.uangLembur as ul', DB::raw('0 as borongan'), DB::raw('0 as honorarium'), 'ds.presenceDate as tanggal')
        ->join('employees as e', 'e.id', '=', 'ds.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->orderBy('u.name')
        ->orderBy('ds.presenceDate')
        ->whereBetween('ds.presenceDate', [$start, $end]);

        $borongan = DB::table('borongans as b')
        ->join('detail_borongans as db', 'db.boronganId', 'b.id')
        ->join('employees as e', 'e.id', '=', 'db.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->orderBy('u.name')
        ->orderBy('b.tanggalKerja')
        ->select('e.id as empid', 'u.name as name', DB::raw('0 as uh'), DB::raw('0 as ul'), 'db.netPayment', DB::raw('0 as honorarium'), 'b.tanggalKerja as tanggal'
    )
        ->whereBetween('b.tanggalKerja', [$start, $end]);

        $honorarium = DB::table('honorariums as h')
        ->join('employees as e', 'e.id', '=', 'h.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->orderBy('u.name')
        ->orderBy('h.tanggalKerja')
        ->select('e.id as empid', 'u.name as name', DB::raw('0 as uh'), DB::raw('0 as ul'), DB::raw('0 as borongan'), 'h.jumlah as honorarium', 'h.tanggalKerja as tanggal' 
    )
        ->whereBetween('h.tanggalKerja', [$start, $end])
        ->union($harian)
        ->union($borongan);

        if ($opsi==1){
            $third = DB::table($honorarium)
            ->select(
                'name',
                'empid', 
                DB::raw('sum(uh) as uh'), 
                DB::raw('sum(ul) as ul'), 
                DB::raw('sum(borongan) as borongan'), 
                DB::raw('sum(honorarium) as honorarium'),
                DB::raw('(sum(uh)+sum(ul)+sum(borongan)+sum(honorarium)) as total'),
                'tanggal'
            )
            ->groupBy('empid')
            ->groupBy('tanggal')
            ->having(DB::raw('(sum(uh)+sum(ul)+sum(borongan)+sum(honorarium))'), '>', 0)
            ->orderBy('name')
            ->orderBy('tanggal')
            ->get();
        } else{
            $third = DB::table($honorarium)
            ->select(
                'name',
                'empid', 
                DB::raw('sum(uh) as uh'), 
                DB::raw('sum(ul) as ul'), 
                DB::raw('sum(borongan) as borongan'), 
                DB::raw('sum(honorarium) as honorarium'),
                DB::raw('(sum(uh)+sum(ul)+sum(borongan)+sum(honorarium)) as total')
            )
            ->groupBy('empid')
            ->having(DB::raw('(sum(uh)+sum(ul)+sum(borongan)+sum(honorarium))'), '>', 0)
            ->orderBy('name')
            ->get();
        }

        return view('salary.checkPayrollByDateRange', compact('opsi','start','end','third'));  
    }


    public function rekapitulasiPresensi(){
        return view('dashboard.rekapitulasiPresensi');
    }
    public function getRekapitulasiPresensi($start, $end, $opsi){
        $start=Carbon::parse($start);
        $end=Carbon::parse($end);
        $query = DB::table('presences as p')
        ->select(
            'e.id as empid', 
            'u.name as name', 
            'os.name as jabatan',
            DB::raw('(CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenis'),
            'wp.name as bagian',
            DB::raw('sum(p.jamKerja) as jamKerja'), 
            DB::raw('sum(p.jamlembur) as jamLembur'),
            DB::raw('sum(p.jamKerja + p.jamlembur) as totalJam'),
            DB::raw('count(p.id) as hari')
        )
        ->whereBetween('p.start', [$start->startOfDay(), $end->endOfDay()])
        ->join('employees as e', 'e.id', '=', 'p.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('mapping.isactive','=','1')
        ->orderBy('totalJam', 'desc')
        ->groupBy('p.employeeId');

        switch ($opsi){
            case "1" :
            $query=$query->where('e.employmentStatus', '=', '1');
            break;
            case "2" :
            $query=$query->where('e.employmentStatus', '=', '2');
            break;
            case "3" :
            $query=$query->where('e.employmentStatus', '=', '3');
            break;
        }

        $query = $query->get();
        return datatables()->of($query) 
        ->addColumn('action', function ($row) {
            $html='<button  data-rowid="'.$row->empid.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Ubah Presensi" onclick="employeePresenceHarianHistory('."'".$row->empid."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>';
            return $html;
        })       
        ->addIndexColumn()->toJson();      
    }

    public function historyDetailPenjualan()
    {
        $speciesList = Species::orderBy('name')->get();

        return view('dashboard.historyTransaksiPenjualanBarang', compact('speciesList'));
    }

    public function getDetailTransactionListHistory($species, $start, $end){
        $query = DB::table('detail_transactions as dt')
        ->select(
            'dt.id as id', 
            'dt.transactionId as transactionId', 
            'i.name as itemName', 
            'f.name as freezingName', 
            'g.name as gradeName', 
            'p.name as packingName', 
            'p.shortname as pshortname',
            's.name as sizeName', 
            'sp.name as speciesName', 
            't.status as status', 
            't.loadingDate as loadingDate',
            'c.name as company',
            DB::raw('(CASE   WHEN t.valutaType="1" THEN "Rp. " 
                WHEN t.valutaType="2" THEN "USD. " 
                WHEN t.valutaType="3" THEN "Rmb. " 
                END) as valuta'
            ), 
            'dt.amount as amount',
            'i.weightbase as wb',
            'dt.price as price',
        )
        ->join('transactions as t', 't.id', '=', 'dt.transactionId')
        ->join('companies as c', 'c.id','=', 't.companyId')
        ->join('items as i', 'i.id', '=', 'dt.itemId')
        ->join('freezings as f', 'i.freezingid', '=', 'f.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('packings as p', 'i.packingid', '=', 'p.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->whereBetween('t.transactionDate', [$start." 00:00:00", $end." 23:59:59"])
        ->whereIn('t.status', [2,4])
        ->orderBy('t.transactionDate', 'desc')
        ->orderBy('sp.name')
        ->orderBy('g.name', 'desc')
        ->orderBy('s.name', 'asc')
        ->orderBy('f.name');
        if ($species>0){
            $query->where('sp.id','=', $species);
        }


        return datatables()->of($query)
        ->editColumn('itemName', function ($row) {
            $name = $row->speciesName." ".$row->gradeName. " ".$row->sizeName. " ".$row->freezingName." ".$row->wb." Kg/".$row->pshortname." - ".$row->itemName;
            return $name;
        })
        ->addColumn('weight', function ($row) {
            $html = number_format(($row->amount * $row->wb), 2, ',', '.').' Kg';
            return $html;
        })
        ->editColumn('amount', function ($row) {
            $html = number_format($row->amount, 2, ',', '.').' '.$row->pshortname;
            return $html;
        })
        ->editColumn('price', function ($row) {
            $html = $row->valuta.' '.number_format($row->price, 2, ',', '.').' /Kg';
            return $html;
        })
        ->editColumn('harga', function ($row) {
            $html = $row->valuta.' '.number_format(($row->price * $row->amount * $row->wb), 2, ',', '.');
            return $html;
        })
        ->addIndexColumn()->toJson();    
    }

}
