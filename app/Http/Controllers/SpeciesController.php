<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Family;
use App\Models\Species;
use App\Models\Shape;
use App\Models\Packing;
use App\Models\Freezing;
use App\Models\Size;
use App\Models\Grade;
use DB;

class SpeciesController extends Controller
{

    public function __construct(){
        $this->species = new Species();
    }

    public function index(Request $request)
    {
        $families = Family::all();
        return view('species.speciesList', compact('families'));
    }
    public function getAllSpecies($familyId){
        return $this->species->getAllSpeciesData($familyId);
    }
    public function getAllSpeciesSize($speciesId){
        return $this->species->getAllSpeciesSizeData($speciesId);
    }
    public function getAllSpeciesItem($speciesId){
        return $this->species->getAllSpeciesItemData($speciesId);
    }

    public function itemList($speciesId)
    {
        return view('species.itemList', compact('speciesId'));
    }
    public function sizeList($speciesId)
    {
        return view('species.sizeList', compact('speciesId'));
    }

    public function getAllItem($speciesId){
        return $this->species->getAllSpeciesItem($speciesId);

    }

    public function createSpecies()
    {
        $families = Family::orderBy('name', 'asc')->get();
        return view('species.speciesCreate', compact('families'));
    }

    public function storeSpecies(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required', 
                    Rule::unique('Species')->where(function ($query) use ($request) {
                        return $query->where('name', $request->name);
                    })
                    
                ],
                'nameBahasa' => [
                    'required', 
                    Rule::unique('Species')->where(function ($query) use ($request) {
                        return $query->where('nameBahasa', $request->nameBahasa);
                    })
                ],
                'family' => ['required', 'gt:0']

            ],[
                'speciesEn.unique' => 'Nama (English) harus unik, ":input" sudah digunakan',
                'speciesId.unique' => 'Nama (Bahasa) harus unik, ":input" sudah digunakan',
            ]
        );

        $data = [
            'name' => $request->name,
            'nameBahasa' => $request->nameBahasa,
            'familyid' => $request->family,
            'isActive' =>  1
        ];
        DB::table('species')->insert($data);
        return redirect('speciesList/')
        ->with('status','Species berhasil ditambahkan.');

    }

    public function create($familyId)
    {
        //$oneItem = $this->item->getOneItem($speciesId);
        //return view('species.itemCreate', compact('oneItem'));
        return view('species.itemCreate');
    }

    public function editItem($itemId)
    {
    /*
        $species=Species::where('isActive', 1)->first();
        $grades=Grade::where('isActive', 1)->get();
        $packings=Packing::where('isActive', 1)->get();
        $freezings=Freezing::where('isActive', 1)->get();
        $sizes=DB::table('Sizes')
        ->where('isActive', 1)
        ->where('speciesId', $speciesId)
        ->get();
        return view('species.itemCreate', compact('species', 'sizes','packings','freezings','grades'));
        */

        $item = $this->species->getOneItem($itemId);
        return view('species.itemList', compact('item'));
    }


    public function editSpecies($speciesId)
    {
        $species = $this->species->getOneSpecies($itemId);
        return view('species.speciesEdit', compact('species'));
    }
    public function editSpeciesSize($sizeId)
    {
        $size = $this->species->getOneSize($sizeId);
        return view('species.sizeEdit', compact('size'));
    }
    public function editSpeciesItem($itemId)
    {
        $item = $this->species->getOneItem($itemId);
        $shapes =DB::table('shapes')
        ->where('isActive', 1)
        ->get();
        return view('species.itemEdit', compact('item', 'shapes'));
    }
    public function createItem($speciesId)
    {
        $species=DB::table('species')
        ->where('id', $speciesId)
        ->where('isActive', 1)
        ->first();
        $grades=Grade::where('isActive', 1)->get();
        $shapes=Shape::where('isActive', 1)->orderBy('name')->get();
        $packings=Packing::where('isActive', 1)->get();
        $freezings=Freezing::where('isActive', 1)->get();
        $sizes=DB::table('sizes')
        ->where('isActive', 1)
        ->where('speciesId', $speciesId)->orderBy('name')
        ->get();
        return view('species.itemCreate', compact('shapes', 'species', 'sizes','packings','freezings','grades'));
    }
    public function createSize($speciesId)
    {
        $species=Species::where('id', $speciesId)->first();
        return view('species.sizeCreate', compact('species'));
    }
    public function storeSize(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required', 

                    Rule::unique('sizes')->where(function ($query) use ($request) {
                        return $query->where('speciesId', $request->speciesId);
                    })
                    
                ]
            ],[
                'name.unique' => 'Nama harus unik, ":input" sudah digunakan',
            ]
        );

        $data = [
            'name' => $request->name,
            'speciesId' => $request->speciesId,
            'isActive' =>  1
        ];
        DB::table('sizes')->insert($data);
        return redirect('sizeList/'.$request->speciesId)
        ->with('status','Item berhasil ditambahkan.');

    }
    public function getIsItemAlreadyExist(Request $request){
        $query = DB::table('items')
        //->where("name", $request->name)
        ->where("shapeId", $request->shape)
        ->where("sizeId", $request->size)
        ->where("gradeId", $request->grade)
        ->where("packingId", $request->packing)
        ->where("freezingId", $request->freezing)
        ->where("weightbase", $request->weightbase)
        ->count();

        if ($query>0){
            return 1;
        }
        return 0;
    }


    public function storeItem(Request $request)
    {
        $validated = $request->validate(
            [
                'shape' => 'required|gt:0',
                'grade' => 'required|gt:0',
                'packing' => 'required|gt:0',
                'freezing' => 'required|gt:0',
                'baseprice' => 'required|numeric|gt:0',
                'weightbase' => 'required|numeric|gt:0',
                'amount' => 'required|numeric|gte:0',
            ]
        );

        $file="";
        $filename="";
        if($request->hasFile('imageurl')){
            $file = $request->imageurl;
            $filename = $request->shape.$request->size.$request->grade.$request->packing.$request->freezing.$request->weightbase.".".$file->getClientOriginalExtension();

            $file->move(base_path("/public/images/items/"), $filename);
        }

        $data = [
            'name' => $request->name,
            'sizeId' => $request->size,
            'shapeId' => $request->shape,
            'gradeId' =>  $request->grade,
            'packingId' => $request->packing,
            'freezingId' => $request->freezing,
            'amount' =>  $request->amount,
            'baseprice' => $request->baseprice,
            'weightbase' => $request->weightbase,
            'isActive' =>  1,
            'imageurl' => "images/items/".$filename
        ];
        $itemId=DB::table('items')->insertGetId($data);
        $this->transaction = new TransactionController();
        $this->transaction->stockChangeLog(1, "Input Item baru", $itemId, $request->amount);
        return redirect('itemList/'.$request->speciesId)
        ->with('status','Item berhasil ditambahkan.');
    }


    public function updateSize(Request $request)
    {
        DB::table('sizes')
        ->where('id', $request->sizeId)
        ->update(['isActive' => $request->isActive]);

        return redirect()->back()->with('status','Update berhasil dilakukan.');
    }

    public function updateItem(Request $request)
    {   
        $validated = $request->validate(
            [
                'shape'     => 'required|gt:0',
                'name'      => ['required', 'string', 'max:255', 'unique:items,name,'.$request->itemId],
            ],[
                'shape.*'   => 'Pilih bentuk olahan'
            ]
        );

        DB::table('items')
        ->where('id', $request->itemId)
        ->update([
            'isActive'      => $request->isActive, 
            'shapeId'       => $request->shape,
            'name'          => $request->name
        ]);        
        return redirect()->back()->with('status','Update berhasil dilakukan.');
    }


}
