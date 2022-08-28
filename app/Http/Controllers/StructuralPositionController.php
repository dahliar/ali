<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrganizationStructure;
use App\Models\StructuralPosition;
use App\Models\WorkPosition;

use DB;

class StructuralPositionController extends Controller
{
    public function __construct(){
    }

    public function index()
    {
        return view('structure.structuralPositionList');
    }
    public function create()
    {
        return view('structure.structuralPositionAdd');
    }   
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255', 'unique:structural_positions']
        ]);

        $structural_position = [
            'name'      => $request->name,
            'isActive'  => 1
        ];
        DB::table('structural_positions')->insert($structural_position);

        return redirect('structuralPositionList')
        ->with('status','Jabatan baru berhasil ditambahkan.');
    }
    public function update(Request $request)
    {
        $request->validate([
            'id'          => ['required', 'gt:0', 'exists:structural_positions'],
            'isActive'    => ['required', 'gte:0']
        ]);

        $structpos = [
            'isActive'          => $request->isActive
        ];

        $action = StructuralPosition::where('id', $request->id)
        ->update($structpos);

        return redirect('structuralPositionList')
        ->with('status','Jabatan berhasil diubah.');
    }
    public function getAllStructuralPosition(){
        $query = DB::table('structural_positions as sp')
        ->select(
            'sp.id as id', 
            'sp.name as name', 
            DB::raw('(CASE WHEN sp.isActive ="0" THEN "Non Aktif"
                WHEN sp.isActive ="1" then "Aktif"
                END) AS isActive') 
        )
        ->orderBy('sp.name');
        $query->get();
        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Jabatan" onclick="editJabatan('."'".$row->id."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>            
            ';
            return $html;
        })->addIndexColumn()->toJson();
    }
    public function edit(StructuralPosition $structural_position)
    {
        return view('structure.structuralPositionEdit', compact('structural_position'));
    }
}
