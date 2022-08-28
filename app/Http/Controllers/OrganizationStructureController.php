<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrganizationStructure;

use App\Models\StructuralPosition;
use App\Models\WorkPosition;

use DB;

class OrganizationStructureController extends Controller
{
    public function __construct(){
        $this->orgStructure = new OrganizationStructure();
    }

    public function index()
    {
        return view('structure.organizationStructureList');
    }
    public function create()
    {
        $structpos = StructuralPosition::where('isActive', '>','0')->get();
        $workpos = WorkPosition::all();
        return view('structure.organizationStructureAdd', compact('structpos', 'workpos'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:255', 'unique:organization_structures'],
            'filterWorkPosition'          => ['required', 'gt:0'],
            'filterStructuralPosition'    => ['required', 'gt:0'],
            'workPosition'          => ['required', 'gt:0'],
            'structuralPosition'    => ['required', 'gt:0'],
            'reportTo'              => ['required', 'gt:0'],
            'maxemployee'           => ['required', 'integer', 'gt:0'],
            'gajiPokok'             => ['required', 'integer', 'gte:0'],
            'uangHarian'            => ['required', 'integer', 'gte:0'],
            'uangTransport'         => ['required', 'integer', 'gte:0'],
            'uangMakan'             => ['required', 'integer', 'gte:0'],
            'uangLembur'            => ['required', 'integer', 'gte:0']
        ],
        [
        ]);

        //insert ke table employees
        $orgStructure = [
            'name'                  => $request->name,
            'idworkpos'             => $request->workPosition,
            'idstructuralpos'       => $request->structuralPosition,
            'reportTo'              => $request->reportTo,
            'maxemployee'            => $request->maxemployee,
            'defGajiPokok'           => $request->gajiPokok,
            'defUangHarian'          => $request->uangHarian,
            'defUangTransport'       => $request->uangTransport,
            'defUangMakan'           => $request->uangMakan,
            'defUangLembur'          => $request->uangLembur,
        ];
        //$empid = $this->orgStructure->orgStructureStore($employee);
        DB::table('organization_structures')->insert($orgStructure);

        return redirect('organizationStructureList')
        ->with('status','Struktur organisasi baru berhasil ditambahkan.');
    }
    public function update(Request $request)
    {
        $request->validate([
            'filterWorkPosition'          => ['required', 'gt:0'],
            'filterStructuralPosition'    => ['required', 'gt:0'],
            'reportTo'              => ['required', 'gt:0'],
            'maxemployee'           => ['required', 'integer', 'gt:0'],
            'gajiPokok'             => ['required', 'integer', 'gte:0'],
            'uangHarian'            => ['required', 'integer', 'gte:0'],
            'uangTransport'         => ['required', 'integer', 'gte:0'],
            'uangMakan'             => ['required', 'integer', 'gte:0'],
            'uangLembur'            => ['required', 'integer', 'gte:0']
        ],
        [
        ]);

        //insert ke table employees
        $orgStructure = [
            'reportTo'               => $request->reportTo,
            'maxemployee'            => $request->maxemployee,
            'defGajiPokok'           => $request->gajiPokok,
            'defUangHarian'          => $request->uangHarian,
            'defUangTransport'       => $request->uangTransport,
            'defUangMakan'           => $request->uangMakan,
            'defUangLembur'          => $request->uangLembur,
        ];

        $action = OrganizationStructure::where('id', $request->idStructure)
        ->update($orgStructure);

        return redirect('organizationStructureList')
        ->with('status','Struktur Organisasi berhasil diubah.');
    }
    public function edit(OrganizationStructure $organization_structure)
    {
        //dd($organization_structure);
        $structpos = StructuralPosition::where('isActive', '>','0')->get();
        $workpos = WorkPosition::all();
        $reportTo = OrganizationStructure::where('id', $organization_structure->reportTo)->first();
        //dd($reportTo);
        return view('structure.organizationStructureEdit', compact('structpos', 'workpos', 'organization_structure', 'reportTo'));
    }
    public function list()
    {
        $query = DB::table('organization_structures as os')
        ->select(
            'os.id as id', 
            'os.name as name', 
            'sp.name as spname', 
            'wp.name as wpname', 
            'os2.name as reportToName', 
            'os.maxemployee as maxemployee', 
            'os.defGajiPokok as gajiPokok', 
            'os.defUangLembur as uangLembur', 
            'os.defUangTransport as uangTransport', 
            'os.defUangMakan as uangMakan', 
            'os.defUangHarian as uangHarian', 
        )
        ->orderBy('os.name')
        ->join('structural_positions as sp', 'sp.id', '=', 'os.idstructuralpos')
        ->join('work_positions as wp', 'wp.id', '=', 'os.idworkpos')
        ->join('organization_structures as os2', 'os.reportTo', '=', 'os2.id');
        $query->get();  


        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Struktur Organisasi" onclick="editStructure('."'".$row->id."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>            
            ';
            return $html;
        })->addIndexColumn()->toJson();
    }
}
