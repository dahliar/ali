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
        $query = DB::table('companies')->get();  
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
        //
        $request->validate([
            'name' => 'required|max:100|unique:companies',
            'address' => 'required|max:4000',
            'countryId' => 'required|gt:0'
        ]);


        $company = [
            'name'      => $request->name,
            'nation'    => $request->countryId,
            'address'   =>  $request->address,
            'isActive'  =>  1
        ];

        $companyId = DB::table('companies')->insertGetId($company);
        //insert dlu ke tabel company
        //ambil idnya
        //masukin ke $companyId=lastInsert
        //insert kontak

        //dd(count($request->contactName));
        if ((count($request->contactName)>0) or (count($request->phone)>0) or (count($request->email)>0)){
            $max = max(count($request->contactName), count($request->phone), count($request->email));

            for ($a=0; $a<$max; $a++){
                $contact[$a] = [
                    'name' =>  $request->contactName[$a],
                    'phone' =>  $request->phone[$a],
                    'email' =>  $request->email[$a],
                    'companyId' =>  $companyId
                ];
            }

            //dd($contact);

            DB::table('contact')->insert($contact);

            return redirect('companyList')
            ->with('status','Data company berhasil ditambahkan.');

        }
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

        return view('company.companyEdit', compact('company', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        //
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
