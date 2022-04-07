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
    public function index()
    {
        //BELUM GENERATE
        /*
        $lembur = DB::table('dailysalaries as ds')
        ->select(
            DB::raw('count(distinct(ds.employeeId)) as jumlah')
        )
        ->join('employees as e', 'e.id', '=', 'ds.employeeId')
        ->where('e.employmentStatus', '=', '1')
        ->where('ds.isGenerated', '=', '0')
        ->where('ds.uangLembur', '>', 0)
        ->first();
        
        $harian = DB::table('dailysalaries as ds')
        ->select(
            DB::raw('count(distinct(ds.employeeId)) as jumlah')
        )
        ->join('employees as e', 'e.id', '=', 'ds.employeeId')
        ->where('e.employmentStatus', '=', '2')
        ->where('ds.isGenerated', '=', '0')
        ->where('ds.isPaid', '=', null)
        ->first();        

        $borongan = DB::table('detail_borongans as db')
        ->join('borongans as b', 'b.id', '=', 'db.boronganId')
        ->select(
            DB::raw('count(distinct(db.employeeId)) as jumlah')
        )
        ->join('employees as e', 'e.id', '=', 'db.employeeId')
        ->where('b.status', '=', '1')
        ->where('e.employmentStatus', '=', '3')
        ->where('db.isPaid', '=', null)
        ->first();

        $honorarium = DB::table('honorariums as h')
        ->select(
            DB::raw('count(distinct(h.employeeId)) as jumlah')
        )
        ->join('employees as e', 'e.id', '=', 'h.employeeId')
        ->where('h.isGenerated', '=', '0')
        ->where('h.isPaid', '=', null)
        ->first();
        */
        $ungenerate=[0, 0, 0, 0];
        /*
        //Sudah GENERATE tapi BELUM BAYAR
        $lembur = DB::table('dailysalaries as ds')
        ->select(
            DB::raw('count(distinct(ds.employeeId)) as jumlah')
        )
        ->join('employees as e', 'e.id', '=', 'ds.employeeId')
        ->where('e.employmentStatus', '=', '1')
        ->where('ds.uangLembur', '>', '0')
        ->where('ds.isGenerated', '=', '1')
        ->where('ds.isPaid', '=', null)
        ->first();

        $harian = DB::table('dailysalaries as ds')
        ->select(
            DB::raw('count(distinct(ds.employeeId)) as jumlah')
        )
        ->join('employees as e', 'e.id', '=', 'ds.employeeId')
        ->join('salaries as s', 's.id', '=', 'ds.salaryId')
        ->where('s.jenis', '=', 2)
        ->where('e.employmentStatus', '=', 2)
        ->where('ds.isGenerated', '=', 1)
        ->where('ds.isPaid', '=', null)
        ->first();        

        $borongan = DB::table('detail_borongans as db')
        ->join('borongans as b', 'b.id', '=', 'db.boronganId')
        ->select(
            DB::raw('count(distinct(db.employeeId)) as jumlah')
        )
        ->join('employees as e', 'e.id', '=', 'db.employeeId')
        ->where('e.employmentStatus', '=', '3')
        ->where('db.isPaid', '=', null)
        ->first();

        $honorarium = DB::table('honorariums as h')
        ->select(
            DB::raw('count(distinct(h.employeeId)) as jumlah')
        )
        ->join('employees as e', 'e.id', '=', 'h.employeeId')
        ->where('h.isGenerated', '=', '1')
        ->where('h.isPaid', '=', null)
        ->first();*/

        $unpaid=[0,0,0,0];
        return view('home', compact('unpaid', 'ungenerate'));
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
        $query = DB::table('items as i')
        ->select(
            DB::raw('concat(sp.name," ",g.name, " ", s.name, " ", f.name) as itemName'),
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
        ->whereBetween('pur.purchaseDate', [$start." 00:00:00", $end." 23:59:59"])
        ->groupBy('i.id')
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderByRaw('s.name+0', 'asc');

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
        $payroll = DB::table('detail_payrolls as dp')
        ->select(DB::raw('MONTH(p.payDate) as bulan'),
            DB::raw('sum(dp.bulanan) as bulanan'),
            DB::raw('sum(dp.harian) as harian'),
            DB::raw('sum(dp.borongan) as borongan'),
            DB::raw('sum(dp.honorarium) as honorarium')
        )
        ->join('payrolls as p', 'p.id', '=', 'dp.idPayroll')
        ->whereYear('p.payDate', $request->tahun)
        ->groupBy(DB::raw('MONTH(p.payDate)'))
        ->get();
        $tahun = $request->tahun;
        return view('dashboard.rekapitulasiGaji', compact('tahun', 'payroll'));        
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

        $payroll = DB::table('purchases as p')
        ->select(
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
        ->orderByRaw('p.purchasingNum+0', 'asc')
        ->orderBy('p.purchaseDate')
        ->get();

        $tahun = $request->tahun;
        $bulan = $request->bulan;

        return view('dashboard.rekapitulasiPembelianPerBulan', compact('tahun','bulan', 'payroll'));        
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

        $payroll = DB::table('purchases as p')
        ->select(
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
        ->orderByRaw('p.purchasingNum+0', 'asc')
        ->orderBy('p.purchaseDate')
        ->get();
        

        $monthYear = $bulan.' '.$request->tahun;

        $pdf = PDF::loadview('invoice.rekapPembelianPerBulan', compact('monthYear', 'payroll'))->setPaper('a4', 'landscape');
        $filename = 'Rekap Gaji '.$monthYear.' cetak tanggal '.today().'.pdf';
        return $pdf->download($filename);



    }


}
