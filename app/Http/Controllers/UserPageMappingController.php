<?php

namespace App\Http\Controllers;

use App\Models\UserPageMapping;
use Illuminate\Http\Request;

use DB;
use Auth;


class UserPageMappingController extends Controller
{
    public function userMappingIndex()
    {
        return view('userMapping.userMappingList');
    }
    public function applicationIndex()
    {
        return view('userMapping.applicationList');
    }
    public function pageIndex($appid)
    {
        $application = DB::table('applications')->where('id', '=', $appid)->first();
        $pages = DB::table('pages as p')->where('p.applicationId', '=', $appid)->get();
        return view('userMapping.pageList', compact('application', 'pages'));
    }
    public function pageAdd($appid)
    {
        $application = DB::table('applications')->where('id', '=', $appid)->first();
        $access_levels = DB::table('access_levels')->orderBy('level')->get();
        return view('userMapping.pageAdd', compact('application', 'access_levels'));
    }

    public function pageStore(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100|unique:pages',
            'route' => 'required|max:1000|unique:pages',
            'level' => 'required|gte:0',

        ]);        
        $data = [
            'name' => $request->name,
            'minimumAccessLevel' => $request->level,
            'route' => $request->route,
            'icon' =>  $request->icon,
            'applicationId' =>  $request->appid,
            'isActive' =>  1
        ];

        $id = DB::table('pages')->insertGetId($data);
        return redirect('pageList'.'/'.$request->appid)
        ->with('status','Item berhasil ditambahkan dengan id '.$id);

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

    public function getApplicationList(){
        $query = DB::table('applications as a')
        ->select(
            'a.id as id', 
            'a.name as name', 
            DB::raw('count(p.id) as jumlahPage'),
            DB::raw('
                (CASE WHEN a.isActive="0" THEN "Non-Aktif" WHEN a.isActive="1" THEN "Aktif" END) AS isActive
                '),
        )
        ->join('pages as p', 'p.applicationId', '=', 'a.id')
        ->orderBy('a.name')
        ->groupBy('a.id');

        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Aplikasi" onclick="editAplikasi('.$row->id.')">
            <i class="fa fa-edit"></i>
            </button>
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar Pages" onclick="kelolaPages('.$row->id.')">
            <i class="fa fa-list"></i>
            </button>
            ';            
            return $html;
        })->addIndexColumn()->toJson();
    }

    public function getPageList($applicationId){
        $query = DB::table('pages as p')
        ->select(
            'p.id as id', 
            'p.name as name',
            'a.name as appName',
            'p.route as route',
            'p.minimumAccessLevel as level',
            'p.icon as icon',
            DB::raw('
                (CASE WHEN a.isActive="0" THEN "Non-Aktif" WHEN a.isActive="1" THEN "Aktif" END) AS isActive
                '),
        )
        ->join('applications as a', 'a.id', '=', 'p.applicationId')
        ->where('p.applicationId', '=', $applicationId)
        ->orderBy('a.name')
        ->orderBy('p.name');
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Aplikasi" onclick="editAplikasi('.$row->id.')">
            <i class="fa fa-edit"></i>
            </button>
            ';            
            return $html;
        })
        ->rawColumns(['action', 'icon'])
        ->addIndexColumn()->toJson();
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
            'p.id as nomorAplikasi',
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
        ->orderBy('a.name')
        ->orderBy('p.name');

        if(Auth::user()->accessLevel != 0){
            $pages->where('p.minimumAccessLevel', '>', 0);
        }
        $pages=$pages->get();
        return view('userMapping.userMapping', compact('user', 'pages'));
    }
    public function store(Request $request)
    {
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
