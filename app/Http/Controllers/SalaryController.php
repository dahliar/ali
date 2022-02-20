<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use Illuminate\Http\Request;

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
        return view('salary.salaryHarianList');
    }
    public function indexBorongan()
    {
        return view('salary.salaryBoronganList');
    }
    public function salaryHarianGenerate(Request $request)
    {
        //dd($request->end);
        //disini digunakan untuk melakukan proses generate gaji harian
        /*
        1. Hitung jumlah dailysalaries hingga tanggal END yang belum digenerate, check di isGenerated=0
        2. Jika tidak ada, return value data kosong
        3. Jika ada Generate
        4. Insert kedalam table s record baru, sId lastinsertid
        5. Ubah dailysalaries 
            isGenerated 0->1
            daily_salary_groupsid -> sId
        6. Return value ok, refresh page
        */

        $rowCount = DB::table('dailysalaries')
        ->where('isGenerated', 0)
        ->count();


        $retValue="";
        if ($rowCount>0){
            $data = [
                'enddatejenis'       => '2'.$request->end,
                'endDate'       => $request->end,
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
            ->where('endDate', $request->end)
            ->where('jenis', 2)
            ->first();

            $id = $query->id;

            $affected = DB::table('dailysalaries')
            ->where('isGenerated', 0)
            ->update([
                'isGenerated' => 1, 
                'salaryid' => $id
            ]);

            $retValue = [
                'message'       => $affected." record telah digenerate",
                'isError'       => "0"
            ];
        } else{
            $retValue = [
                'message'       => "Tidak terdapat record yang belum digenerate",
                'isError'       => "1"
            ];
        }

        return $retValue;


    }

    public function salaryBoronganGenerate(Request $request)
    {
        //dd($request->end);
        //disini digunakan untuk melakukan proses generate gaji borongan
        /*
        1. Cek tabel borongans, cari yang status=1.
        2. Jika tidak ada, return value data kosong
        3. Jika ada Generate
        4. Insert kedalam table salaries record baru, salariesId lastinsertid
        5. Ubah borongan 
            isGenerated 0->1
            salariesid -> salariesId
        6. Return value ok, refresh page
        */

        $rowCount = DB::table('borongans')
        ->where('status', 1)
        ->count();

        $retValue="";
        if ($rowCount>0){
            $data = [
                'enddatejenis'  => '3'.$request->end,
                'endDate'       => $request->end,
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
            ->where('endDate', $request->end)
            ->where('jenis', 3)
            ->first();

            $id = $query->id;

            $affected = DB::table('borongans')
            ->where('status', 1)
            ->update([
                'status' => 2, 
                'salariesId' => $id
            ]);

            $retValue = [
                'message'       => $affected." record telah digenerate",
                'isError'       => "0"
            ];
        } else{
            $retValue = [
                'message'       => "Tidak terdapat record yang belum digenerate",
                'isError'       => "1"
            ];
        }

        return $retValue;


    }


    public function markSalariesIsPaid(Salary $salary, $tanggalBayar)
    {
        $retValue="";        
        $affected = DB::table('salaries')
        ->where('id', $salary->id)
        ->update(['isPaid' => 1, 'userIdPaid' => auth()->user()->id, 'tanggalBayar' => $tanggalBayar]);


        //kalau borongan update borongans, kalau harian ngga perlu
        if ($salary->jenis == 3){
            $affected = DB::table('borongans')
            ->where('salariesId', $salary->id)
            ->update(['status' => 3]);
        }

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
            's.ispaid as statusPaid',
            DB::raw('(CASE WHEN s.isPaid="0" THEN "Belum Bayar" WHEN s.isPaid="1" THEN "Sudah Bayar" END) AS ispaid'),
            'ug.name as generatorName',
            'up.name as payerName',
            's.tanggalBayar as tanggalBayar'
        )
        ->leftjoin('users as ug', 's.userIdGenerator', '=', 'ug.id')
        ->leftjoin('users as up', 's.userIdPaid', '=', 'up.id')
        ->where('jenis', 2)
        ->orderBy('s.enddate', 'desc');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <a data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak gaji pegawai harian" href="checkCetakGajiPegawaiHarian/'.$row->id.'">
            <i class="fa fa-print"></i>
            </a>
            <a data-rowid="'.$row->id.'" class="btn btn-xs btn-dark" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak lembur pegawai bulanan" href="checkCetakLemburPegawaiBulanan/'.$row->id.'">
            <i class="fas fa-moon"></i>
            </a>';

            if($row->statusPaid==0){
                $html.='
                <button data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tandai sudah dibayar" onclick="setIsPaidModal('."'".$row->id."'".')">
                <i class="fa fa-save"></i>
                </button>
                ';
            }

            $html.='
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
            's.id as id', 
            's.endDate as enddate', 
            's.isPaid as paidStatus',
            DB::raw('(CASE WHEN s.isPaid="0" THEN "Belum" WHEN s.isPaid="1" THEN "Sudah" END) AS isPaid'),
            'ug.name as generatorName',
            'up.name as payerName',
            's.tanggalBayar as tanggalBayar'
        )
        ->leftjoin('users as ug', 's.userIdGenerator', '=', 'ug.id')
        ->leftjoin('users as up', 's.userIdPaid', '=', 'up.id')
        ->where('jenis', 3)
        ->orderBy('s.enddate');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <a data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak gaji pegawai borongan" 
            href="checkCetakGajiPegawaiBorongan/'.$row->id.'"><i class="fa fa-print"></i>
            </a>';

            if($row->paidStatus==0){
                $html.='
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tandai sudah dibayar" onclick="setStatusModal('."'".$row->id."'".')"><i class="fa fa-save"></i>
                </button>
                ';
            }
            return $html;
        })->addIndexColumn()->toJson();
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




    public function checkCetakLemburPegawaiBulanan(Salary $salary){
        return view('salary.checkLemburBulananList', compact('salary'));
    }
    public function checkCetakGajiPegawaiBorongan(Salary $salary){
        return view('salary.checkSalaryBoronganList', compact('salary'));
    }




    public function getSalariesHarianForCheck($salaryId){
        $query = DB::table('dailysalaries as ds')
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
        ->where('ds.salaryid', $salaryId)
        ->where('eosm.isactive', 1)
        ->where('e.employmentStatus', 2)
        ->groupBy('e.id')
        ->get();

        return datatables()->of($query)
        ->addIndexColumn()->toJson();
    }

    public function getLemburPegawaiBulanan($salaryId){
        $query = DB::table('dailysalaries as ds')
        ->select(
            'e.id as empid',
            'u.name as name',
            'os.name as osname',
            DB::raw('sum(ds.uanglembur) as ul')
        )
        ->join('employees as e', 'e.id', '=', 'ds.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('ds.salaryid', $salaryId)
        ->where('eosm.isactive', 1)
        ->where('e.employmentStatus', 1)
        ->groupBy('e.id')
        ->get();

        return datatables()->of($query)
        ->addIndexColumn()->toJson();
    }




    public function getBoronganSalariesForPrint($salaryId){
        $query = DB::table('salaries as s')
        ->select(
            'e.id as empid',
            'u.name as name',
            'os.name as osname',
            DB::raw('sum(db.netPayment) as netPayment')
        )
        ->join('borongans as b', 's.id', '=', 'b.salariesId')
        ->join('detail_borongans as db', 'b.id', '=', 'db.boronganId')
        ->join('employees as e', 'e.id', '=', 'db.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('s.id', $salaryId)
        ->where('eosm.isactive', 1)
        ->where('e.employmentStatus', 3)
        ->groupBy('e.id')
        ->get();
        return datatables()->of($query)
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
