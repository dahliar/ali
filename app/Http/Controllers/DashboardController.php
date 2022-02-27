<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        //BELUM GENERATE
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
        $ungenerate=[$lembur->jumlah, $harian->jumlah, $borongan->jumlah, $honorarium->jumlah];




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
        ->first();

        $unpaid=[$lembur->jumlah, $harian->jumlah, $borongan->jumlah, $honorarium->jumlah];

        return view('home', compact('unpaid', 'ungenerate'));
    }
}
