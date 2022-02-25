<?php

namespace App\Http\Controllers;

use App\Models\Honorarium;
use Illuminate\Http\Request;
use Carbon\Carbon;

use DB;


class HonorariumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('presence.presenceHonorariumList');
    }
    //Untuk datatable di halaman presensi satuan
    public function getPresenceHonorariumEmployees(){
        $presenceDate = Carbon::now()->toDateString();
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik',
            DB::raw('(CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="2" THEN "Harian" WHEN e.employmentStatus="3" THEN "Borongan" END) AS jenisPenggajian'), 
            DB::raw('(STR_TO_DATE(h.tanggalKerja,"%Y-%m-%d")) as presenceToday'),
            'os.name as orgStructure',
            'sp.name as jabatan',
            'wp.name as bagian'
        )
        ->leftJoin('honorariums as h', function($join) use ($presenceDate){
            $join->on('e.id', '=', 'h.employeeId')
            ->where(DB::raw("(STR_TO_DATE(h.tanggalKerja,'%Y-%m-%d'))"), '=', $presenceDate);
        })
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('mapping.isActive', '1');

        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '';
            if (is_null($row->presenceToday)){
                $html.='
                <button type="button" class="btn" onclick="presenceForTodayModal('."'".$row->id."'".', '."'".$row->name."'".')" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Honorarium '.$row->name.' Hari ini">
                <i class="fa fa-check" style="font-size:20px"></i>
                </button>
                ';
            }
            /*
            $html.='<button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="'.$row->name.' Presence History" onclick="employeePresenceHonorariumHistory('."'".$row->id."'".')">
            <i class="fa fa-history" style="font-size:20px"></i>
            </button>';
            */
            return $html;
        })->addIndexColumn()->toJson();
    }
    public function storePresenceHonorariumEmployee(Request $request)
    {
        $retValue="";
        
        $dataHonorarium = [
            'empiddate'         => $request->empid.$request->tanggalKerja,
            'tanggalKerja'      => $request->tanggalKerja,
            'employeeId'        => $request->empid,
            'tanggalKerja'      => $request->tanggalKerja,
            'jumlah'            => $request->jumlah,
            'keterangan'        => $request->keterangan
        ];

        DB::table('honorariums')
        ->upsert(
            $dataHonorarium,
            ['empiddate'],
            ['empiddate','employeeId','tanggalKerja','jumlah','keterangan']
        );
        $retValue = [
            'message'       => "Data berhasil disimpan ",
            'isError'       => "0"
        ];
        return $retValue;
    }
    public function presenceHonorariumHistory()
    {
        return view('presence.presenceHonorariumHistory');
    }


    public function getPresenceHonorariumHistory($start, $end){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik',
            'e.nip as nip',
            'os.name as orgStructure',
            'wp.name as bagian',
            'p.isPaid as statusIsPaid',
            DB::raw('(CASE WHEN p.isPaid="0" THEN "Belum" WHEN p.isPaid="1" THEN "Sudah" END) AS isPaid'),
            DB::raw('(CASE WHEN p.isGenerated="0" THEN "Belum" WHEN p.isGenerated="1" THEN "Sudah" END) AS isGenerated'),
            'p.tanggalKerja as tanggalKerja',
            'p.keterangan as keterangan',
            'p.jumlah as jumlah'
        )
        ->join('honorariums as p', 'e.id', '=', 'p.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->whereBetween('p.tanggalKerja', [$start." 00:00:00", $end." 23:59:59"])
        ->where('mapping.isActive', '1');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '';
            if ($row->statusIsPaid==0){
                $html.='
                <button type="button" class="btn btn-primary" onclick="tandaiSudahDibayar('."'".$row->id."'".', '."'".$row->name."'".')" data-toggle="tooltip" data-placement="top" data-container="body" title="Tandai sudah dibayar">
                <i class="fa fa-check"></i>
                </button>
                ';
            }
            return $html;
        })
        ->addIndexColumn()->toJson();
    }    
}
