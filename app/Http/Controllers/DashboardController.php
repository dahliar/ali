<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Species;


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





}
