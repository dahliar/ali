<?php

namespace App\Http\Controllers;

use App\Models\UserPageMapping;
use Illuminate\Http\Request;

use DB;


class UserPageMappingController extends Controller
{
    public function index()
    {
        return view('userMapping.userMappingList');
    }

    public function getEmployeesMappingList(){
        $query = DB::table('employees as e')
        ->select(
            'u.id as id', 
            'u.name as name', 
            'e.nip as nip', 
            'u.username as username', 
            'os.name as jabatan',
            'wp.name as bagian',
            DB::raw('
                (CASE WHEN e.isActive="0" THEN "Non-Aktif" WHEN e.isActive="1" THEN "Aktif" END) AS statusKepegawaian
                '),
        )
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('eosm.isactive', 1)
        ->where('e.isActive', '=', 1)
        ->where('sp.levelAccess', '<=', 3)
        ->orderBy('u.name');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Petakan User" onclick="userMapping('."'".$row->id."'".')">
            <i class="fa fa-edit"></i>
            </button>
            ';            
            return $html;
        })->addIndexColumn()->toJson();
    }
    public function mapping(Request $request)
    {
        $user = DB::table('users as u')
        ->select(
            'u.name as name',
            'u.id as uid',
            'u.username as uname',
            'os.name as jabatan',
            'wp.name as bagian',

        )
        ->join('employees as e', 'e.userid', '=', 'u.id')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->join('structural_positions as sp', 'os.idstructuralpos', '=', 'sp.id')
        ->join('work_positions as wp', 'os.idworkpos', '=', 'wp.id')
        ->where('u.id', '=', $request->userId)
        ->where('eosm.isactive', 1)
        ->where('e.isActive', '=', 1)
        ->first();
        $uid=$request->userId;
        $pages=DB::table('pages as p')
        ->select(
            'p.name as pageName',
            'a.name as applicationName',
            'a.id as applicationId',
            'p.id as pageId',
            'upm.pageId as upmPageId'
        )
        ->leftJoin('user_page_mappings as upm', function($join) use ($uid){
            $join->on('upm.pageId', '=', 'p.id')
            ->where('upm.userId', '=', $uid);
        })
        ->join('applications as a', 'a.id', '=', 'p.applicationId')
        ->where('a.isActive','=', 1)
        ->where('p.isActive','=', 1)
        ->get();
        return view('userMapping.userMapping', compact('user', 'pages'));
    }
    public function store(Request $request)
    {
        //dd($request);
        $dataDelete = array();
        if ($request->has('mapping')){
            foreach($request->mapping as $mapping)
            {   
                $upm = UserPageMapping::firstOrCreate(
                    [   
                        'userId' => $request->uid, 
                        'pageId'=>$mapping
                    ],
                    [
                        'userId' => $request->uid, 
                        'pageId'=>$mapping
                    ]
                );
            }
        }
        if ($request->has('mappingHidden')){
            if ($request->has('mapping')){
                foreach($request->mappingHidden as $mapping)
                {
                    if ( (!in_array($mapping, $request->mapping)) and ($mapping!=null))
                    {
                        $deleted = UserPageMapping::where('userId', $request->uid)->where('pageId', $mapping)->delete();
                    };
                }
            } else
            {
                foreach($request->mappingHidden as $mapping)
                {
                    if (($mapping!=null))
                    {
                        $deleted = UserPageMapping::where('userId', $request->uid)->where('pageId', $mapping)->delete();
                    };
                }

            }
        }
        return redirect('userMappingList')
        ->with('status','Pemetaan berhasil dilakukan.');            
    }
}
