<?php

namespace App\Http\Controllers;
use DB;

use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\Company;


class GoodController extends Controller
{

    public function index(){
        return view('good.goodList');
    }

    public function create(){
        $categories = DB::table('good_categories')
        ->where('isActive', 1)
        ->orderBy('name')
        ->get();
        $units = DB::table('good_units')
        ->where('isActive', 1)
        ->orderBy('name')
        ->get();
        return view('good.goodCreate', compact('units', 'categories'));
    }
    public function edit(Good $good){
        $categories = DB::table('good_categories')
        ->select('name as unitName')
        ->where('isActive', 1)
        ->where('id', '=', $good->idCategories)
        ->first()->unitName;

        $unit = DB::table('good_units')
        ->select('name as unitName')
        ->where('isActive', 1)
        ->where('id', '=', $good->idUnit)
        ->first()->unitName;

        return view('good.goodEdit', compact('unit', 'categories', 'good'));
    }
    public function ubahTambah(Good $good){
        $companies = Company::all();
        $categories = DB::table('good_categories')
        ->select('name as unitName')
        ->where('isActive', 1)
        ->where('id', '=', $good->idCategories)
        ->first()->unitName;

        $unit = DB::table('good_units')
        ->select('name as unitName')
        ->where('isActive', 1)
        ->where('id', '=', $good->idUnit)
        ->first()->unitName;

        return view('good.goodUbahTambah', compact('companies', 'unit', 'categories', 'good'));
    }
    public function ubahKurang(Good $good){
        $categories = DB::table('good_categories')
        ->select('name as unitName')
        ->where('isActive', 1)
        ->where('id', '=', $good->idCategories)
        ->first()->unitName;

        $unit = DB::table('good_units')
        ->select('name as unitName')
        ->where('isActive', 1)
        ->where('id', '=', $good->idUnit)
        ->first()->unitName;

        return view('good.goodUbahKurang', compact('unit', 'categories', 'good'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name'      => 'required|unique:goods',
                'unit'      => 'required|gt:0',
                'category'  => 'required|gt:0',
                'amount'    => 'required|numeric|gte:0',
                'minimal'   => 'required|numeric|gte:0'
            ],[
                'name.unique' => 'Nama harus unik, ":input" sudah digunakan'
            ]
        );
        $file="";
        $filename="";
        if($request->hasFile('imageurl')){
            $file = $request->imageurl;
            $filename = $request->name.$request->unit.$request->category.".".$file->getClientOriginalExtension();

            $file->move(base_path("/public/images/goods/"), $filename);
        }

        $data = [
            'name' => $request->name,
            'idUnit' => $request->unit,
            'idCategories' =>  $request->category,
            'amount' =>  $request->amount,
            'minimalAmount' => $request->minimal,
            'imageurl' => "images/goods/".$filename
        ];

        $goodId=DB::table('goods')->insertGetId($data);

        $this->goodChangeLog(1, "Input Barang baru", $goodId, $request->amount);

        return redirect('goodList')
        ->with('status','Barang berhasil ditambahkan.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate(
            [
                'amount'    => 'required|numeric|gte:0',
                'minimal'   => 'required|numeric|gte:0',
                'isactive'  => 'required'
            ],[
                'name.unique' => 'Nama harus unik, ":input" sudah digunakan'
            ]
        );

        $file="";
        $filename="";
        $data = [
            'amount' =>  $request->amount,
            'minimalAmount' => $request->minimal,
            'isactive'  => $request->isactive
        ];

        if($request->hasFile('imageurl')){
            $file = $request->imageurl;
            $filename = $request->name.$request->unit.$request->category.".".$file->getClientOriginalExtension();

            $file->move(base_path("/public/images/goods/"), $filename);
            $data = [
                'amount' =>  $request->amount,
                'minimalAmount' => $request->minimal,
                'imageurl' => "images/goods/".$filename,
                'isactive'  => $request->isactive
            ];
        }

        $goodId=DB::table('goods')->where('id', '=', $request->idGood)->update($data);

        $this->goodChangeLog(1, "Update Barang", $goodId, $request->amount);

        return redirect('goodList')
        ->with('status','Barang berhasil diubah.');
    }

    public function storeKurang(Request $request)
    {
        $validated = $request->validate(
            [
                'amountKurang'    => 'required|numeric|gt:0',
                'usageDate'    => 'required|date|before_or_equal:today'
            ],[
            ]
        );
        
        try {
            DB::beginTransaction();
            $data = [
                'idGood'        => $request->idGood,
                'amount'        => $request->amountKurang,
                'userId'        => auth()->user()->id,
                'usageDate'     => $request->usageDate,
                'keterangan'    => $request->keterangan
            ];

            $goodId=DB::table('good_usages')->insert($data);

            $affected = DB::table('goods')
            ->where('id', $request->idGood)
            ->update([
                'amount' => DB::raw('amount - '.$request->amountKurang),
            ]);
            $this->goodChangeLog(1, "Penggunaan barang : ".$request->keterangan, $request->idGood, $request->amountKurang);

            DB::commit();
            return redirect('goodList')
            ->with('status','Pengurangan jumlah barang sukses.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect('goodList')
            ->with('status','Data gagal diperbaharui');
        }
        
    }
    public function storeTambah(Request $request)
    {
        $validated = $request->validate(
            [
                'amountTambah'    => 'required|numeric|gt:0',
                'hargaTotal'      => 'required|numeric|gt:0',
                'company'         => 'required|gt:0',
                'purchaseDate'    => 'required|date|before_or_equal:today'
            ],[
            ]
        );

        try {
            DB::beginTransaction();
            $data = [
                'idGood'        => $request->idGood,
                'amount'        => $request->amountTambah,
                'userId'        => auth()->user()->id,
                'hargaTotal'    => $request->hargaTotal,
                'purchaseDate'  => $request->purchaseDate,
                'companyId'     => $request->company,
                'invoiceNumber' => $request->invoiceNumber
            ];

            $goodId=DB::table('good_procurements')->insert($data);

            $affected = DB::table('goods')
            ->where('id', $request->idGood)
            ->update([
                'amount' => DB::raw('amount + '.$request->amountTambah),
            ]);
            $this->goodChangeLog(1, "Tambah jumlah barang", $goodId, $request->amountTambah);

            DB::commit();
            return redirect('goodList')
            ->with('status','Penambahan barang sukses.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('goodList')
            ->with('status','Data gagal diperbaharui');
        }
    }

    public function goodChangeLog($jenis, $info, $goodId, $amount){
        $data = [
            'userId'    => auth()->user()->name,
            'jenis'     => $jenis,
            'informasiTransaksi' => $info,
            'idGood'    =>  $goodId,
            'amount'    =>  $amount                
        ];
        DB::table('good_histories')->insert($data);
    }

    public function getGoods($isChecked){
        $query = DB::table('goods as g')
        ->select(
            'g.id as id', 
            'g.name as name',
            'g.amount as amount',
            'g.minimalAmount as minimal',
            'gu.name as satuan',
            'gc.name as kategori',
            'g.isactive as stat',
            DB::raw('(CASE WHEN g.isactive="1" THEN "Ya" WHEN g.isactive="0" THEN "Tidak" END) AS isactive')
        )
        ->join('good_units as gu', 'gu.id', '=', 'g.idUnit')
        ->join('good_categories as gc', 'gc.id', '=', 'g.idCategories');
        if ($isChecked == 0){ 
            $query->where("g.amount","=",0);
        } else if ($isChecked == 1){
            $query->where("g.amount",">",0);
        } 
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            if ($row->stat==0){
                $html = '
                <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Barang Masuk" disabled>
                <i class="fa fa-plus" style="font-size:20px"></i>
                </button>
                <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Barang Keluar" disabled>
                <i class="fa fa-minus" style="font-size:20px"></i>
                </button>
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit & stock opname Barang" onclick="editBarang('."'".$row->id."'".')">
                <i class="fa fa-edit" style="font-size:20px"></i>
                </button>';
            }
            else{
                $html = '
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Barang Masuk" onclick="ubahTambah('."'".$row->id."'".')">
                <i class="fa fa-plus" style="font-size:20px"></i>
                </button>
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Barang Keluar" onclick="ubahKurang('."'".$row->id."'".')">
                <i class="fa fa-minus" style="font-size:20px"></i>
                </button>
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit & stock opname Barang" onclick="editBarang('."'".$row->id."'".')">
                <i class="fa fa-edit" style="font-size:20px"></i>
                </button>
                ';
            }
            return $html;
        })
        ->editColumn('amount', function ($row) {
            if ($row->amount <= $row->minimal){
                $html = '<div class="col-12 text-end" style="color:red">'.number_format($row->amount, 2, ',', '.').'</div>';
            } else
            {
                $html = '<div class="col-12 text-end">'.number_format($row->amount, 2, ',', '.').'</div>';
            }
            return $html;
        }) 
        ->rawColumns(['action', 'amount'])
        ->addIndexColumn()->toJson();
    }


//Good Categories
    public function goodCategoriesIndex(){
        return view('good.goodCategories');
    }
    public function goodCategoriesAdd(){
        return view('good.goodCategoriesAdd');
    }

    public function getGoodCategories(){
        $query = DB::table('good_categories as gc')
        ->select(
            'gc.id as id', 
            'gc.name as name',
            'gc.idMaterial as idMaterial',
            'm.name as materialName',
        )
        ->join('materials as m', 'm.id', '=', 'gc.idMaterial')
        ->orderBy('name')
        ->get();  

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = "";
        /*     '
        
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Kategori" onclick="editBarang('."'".$row->id."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>';
            */
            return $html;
        })
        ->rawColumns(['action'])
        ->addIndexColumn()->toJson();
    }

    public function goodCategoryStore(Request $request)
    {
        $validated = $request->validate(
            [
                'name'      => 'required|unique:good_categories',
            ],[
                'name.unique' => 'Nama harus unik, ":input" sudah digunakan'
            ]
        );
        $data = [
            'name' => $request->name,
            'idMaterial' => 1,
            'isActive' =>  1
        ];

        $goodId=DB::table('good_categories')->insertGetId($data);
        return redirect('goodCategories')
        ->with('status','Kategori baru berhasil ditambahkan.');
    }


//Good Units
    public function goodUnitsIndex(){
        return view('good.goodUnits');
    }
    public function goodUnitsAdd(){
        return view('good.goodUnitsAdd');
    }

    public function getGoodUnits(){
        $query = DB::table('good_units as gu')
        ->select(
            'gu.id as id', 
            'gu.name as name',
            'gu.shortname as shortname'
        )
        ->orderBy('name')
        ->get();  

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = "";
        /*     '
        
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Kategori" onclick="editBarang('."'".$row->id."'".')">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>';
            */
            return $html;
        })
        ->rawColumns(['action'])
        ->addIndexColumn()->toJson();
    }

    public function goodUnitsStore(Request $request)
    {
        $validated = $request->validate(
            [
                'name'      => 'required|unique:good_units',
                'shortname'      => 'required|unique:good_units',
            ],[
                'name.unique' => 'Nama harus unik, ":input" sudah digunakan',
                'shortname.unique' => 'Nama pendek harus unik, ":input" sudah digunakan'
            ]
        );
        $data = [
            'name' => $request->name,
            'shortname' => $request->shortname,
            'isActive' =>  1
        ];

        $goodId=DB::table('good_units')->insertGetId($data);
        return redirect('goodUnits')
        ->with('status','Satuan baru berhasil ditambahkan.');
    }

}
