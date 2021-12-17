<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrganizationStructure;
use App\Models\StructuralPosition;
use App\Models\WorkPosition;
use DB;

class WorkPositionController extends Controller
{
    public function __construct(){
    }

    public function index()
    {
        return view('structure.workPositionList');
    }
    public function create()
    {
        return view('structure.workPositionAdd');
    }   
    public function store(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255', 'unique:work_positions']
        ]);

        $work_position = [
            'name'      => $request->name,
            'isActive'  => 1
        ];
        DB::table('work_positions')->insert($work_position);

        return redirect('workPositionList')
        ->with('status','Bagian baru berhasil ditambahkan.');
    }
    public function update(Request $request)
    {
        $request->validate([
            'id'          => ['required', 'gt:0', 'exists:work_positions'],
            'isActive'    => ['required', 'gte:0']
        ]);

        $workpos = [
            'isActive'          => $request->isActive
        ];

        $action = WorkPosition::where('id', $request->id)
        ->update($workpos);

        return redirect('workPositionList')
        ->with('status','Jabatan berhasil diubah.');
    }
    public function getAllWorkPosition(){
        $query = DB::table('work_positions as wp')
        ->select(
            'wp.id as id', 
            'wp.name as name', 
            DB::raw('(CASE WHEN wp.isActive ="0" THEN "Non Aktif"
                WHEN wp.isActive ="1" then "Aktif"
                END) AS isActive') 
        )
        ->orderBy('wp.name');
        $query->get();
        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Bagian" onclick="editBagian('."'".$row->id."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>            
            ';
            return $html;
        })->addIndexColumn()->toJson();
    }
    public function edit(WorkPosition $work_position)
    {
        return view('structure.workPositionEdit', compact('work_position'));
    }
}
