<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('company.companyList');
    }

    public function getAllCompany(){
        $query = DB::table('companies as com')
        ->select(
            'com.id as id', 
            'com.name as name',
            'com.address as address',
            'cn.name as nation'
        )
        ->join('countries as cn', 'cn.id', '=', 'com.nation')
        ->get();  
        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Company" onclick="editCompany('."'".$row->id."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>
            '
            ;
            return $html;
        })->addIndexColumn()->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = DB::table('countries')
        ->orderBy('name')
        ->get();
        return view('company.companyAdd', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100|unique:companies',
            'address' => 'required|max:4000',
            'taxIncluded' => 'required|gte:0',
            'countryId' => 'required|gt:0'
        ]);

        $company = [
            'name'      => $request->name,
            'nation'    => $request->countryId,
            'address'   =>  $request->address,
            'taxIncluded' =>  $request->taxIncluded,
            'npwp'      => $request->npwpnum
        ];

        $companyId = DB::table('companies')->insertGetId($company);
        //insert dlu ke tabel company
        //ambil idnya
        //masukin ke $companyId=lastInsert
        //insert kontak

        if ($request->has('contactName') or $request->has('phone') or $request->has('email'))
        {
            $max = max(count($request->contactName), count($request->phone), count($request->email));

            for ($a=0; $a<$max; $a++){

                $contact[$a] = [
                    'name' =>  $request->contactName[$a],
                    'phone' =>  $request->phone[$a],
                    'email' =>  $request->email[$a],
                    'companyId' =>  $companyId
                ];
            }

            DB::table('contacts')->insert($contact);
        }
        return redirect('companyList')
        ->with('status','Data company berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        $countries = DB::table('countries')
        ->orderBy('name')
        ->get();
        $contacts = DB::table('contacts')
        ->where('companyId','=',$company->id)
        ->get();

        //dd($contact);

        return view('company.companyEdit', compact('company', 'countries', 'contacts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'address' => 'required|max:4000',
            'taxIncluded' => 'required|gte:0',
            'countryId' => 'required|gt:0'
        ]);

        $company = [
            'nation'    => $request->countryId,
            'address'   =>  $request->address,
            'taxIncluded' =>  $request->taxIncluded,
            'npwp'      => $request->npwpnum
        ];

        Company::where('id', $request->companyId)
        ->update($company);
        //insert dlu ke tabel company
        //ambil idnya
        //masukin ke $companyId=lastInsert
        //insert kontak

        if ($request->has('contactName') or $request->has('phone') or $request->has('email'))
        {
            $max = max(count($request->contactName), count($request->phone), count($request->email));

            for ($a=0; $a<$max; $a++){
                $tableid = $request->tableid[$a];
                if ($request->tableid[$a]==-1){
                    $tableid=null;
                }                
                $email = "";
                if (!empty($request->email[$a])){
                    $email=$request->email[$a];
                }
                $contactName = "";
                if (!empty($request->contactName[$a])){
                    $contactName=$request->contactName[$a];
                }
                $phone = "";
                if (!empty($request->phone[$a])){
                    $phone=$request->phone[$a];
                }


                $contact[$a] = [
                    'id'        =>  $tableid,
                    'name'      =>  $contactName,
                    'companyId' =>  $request->companyId,
                    'phone'     =>  $phone,
                    'email'     =>  $email
                ];
            }

            //dd($contact);

            DB::table('contacts')
            ->upsert($contact, 
                ['id'], 
                ['name', 'phone', 'email']
            );
        }
        return redirect('companyList')
        ->with('status','Data company berhasil diperbaharui .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        //
    }
}
