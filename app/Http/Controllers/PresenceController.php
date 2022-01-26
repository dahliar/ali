<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Employee;
use Illuminate\Http\Request;
use DB;
use Auth;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        return view('presence.presenceList');
    }

    public function getAllEmployeesForPresence($presenceDate){
        $query = DB::table('employees as e')
        ->select(
            'e.id as id', 
            'u.name as name', 
            'e.nik as nik',
            DB::raw('(CASE WHEN e.employmentStatus="1" THEN "Bulanan" WHEN e.employmentStatus="1" THEN "Harian" END) AS jenisPenggajian'), 
            DB::raw('(STR_TO_DATE(p.start,"%Y-%m-%d")) as presenceToday'),
            'os.name as orgStructure',
            'sp.name as jabatan',
            'wp.name as bagian'
        )
        ->leftJoin('presences as p', function($join) use ($presenceDate){
            $join->on('e.id', '=', 'p.employeeId')
            ->where(DB::raw("(STR_TO_DATE(p.start,'%Y-%m-%d'))"), '=', $presenceDate);

        })
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as mapping', 'mapping.idemp', '=', 'e.id')
        ->join('organization_structures as os', 'mapping.idorgstructure', '=', 'os.id')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('e.employmentStatus', '1')
        ->where('mapping.isActive', '1');

        $query->get();
        //dd($presenceDate);
        //dd($query->toSql());
        //dd($query->get());

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Presence History" onclick="presenceHistory('."'".$row->id."'".')">
            <i class="fa fa-history" style="font-size:20px"></i>
            </button>';
            if (is_null($row->presenceToday)){
                $html.='
                <button type="button" class="btn  btn-xs btn-light" data-bs-toggle="modal" data-toggle="tooltip" data-placement="top" data-container="body" data-bs-target="#exampleModal" title="Tambah Presensi Hari ini" value="'.$row->id.'">
                <i class="fa fa-check" style="font-size:20px"></i>
                </button>
                ';
            }
            return $html;
        })->addIndexColumn()->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function createBatch()
    {
        return view('presence.presenceAddBatch');
    }
    public function createImport()
    {
        return view('presence.presenceAddImport');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function show(Presence $presence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function edit(Presence $presence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Presence $presence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Presence  $presence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Presence $presence)
    {
        //
    }
}
