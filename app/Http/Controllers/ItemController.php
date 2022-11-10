<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Species;
use App\Models\Store;
use App\Models\Shape;
use App\Models\Packing;
use App\Models\Freezing;
use App\Models\Size;
use App\Models\Grade;

use DB;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->item = new Item();
    }

    public function getItemForSelectOption($tid, $pid, $speciesId){
        $something=$this->item->getItemForSelectOption($tid, $pid, $speciesId);
        return $something;
    }

    public function getSizeForSpecies($speciesId){
        return $this->item->getSizeForSpecies($speciesId);
    }
    public function getGradeForSize($sizeId){
        return $this->item->getGradeForSize($sizeId);
    }
    public function getWeightbaseForSize($sizeId, $gradeId){
        return $this->item->getWeightbaseForSize($sizeId, $gradeId);
    }
    public function getShapesForWeightbase($sizeId, $gradeId, $weightbase){
        return $this->item->getShapesForWeightbase($sizeId, $gradeId, $weightbase);
    }
    public function getPackingsForShape($sizeId, $gradeId, $weightbase, $shapeId){
        return $this->item->getPackingsForShape($sizeId, $gradeId, $weightbase, $shapeId);
    }
    public function getFreezingsForPacking($sizeId, $gradeId, $weightbase, $shapeId, $packingId){
        return $this->item->getFreezingsForPacking($sizeId, $gradeId, $weightbase, $shapeId, $packingId);
    }

    public function getAllStockItem(Request $request){
        return $this->item->getAllItemData($request);
    }
    
    public function getItemHistory($itemId){
        return $this->item->getItemHistory($itemId);
    }
    public function getUnpackedItemHistory($itemId){
        return $this->item->getUnpackedItemHistory($itemId);
    }

    public function index(Request $request)
    {
        $speciesList=DB::table('species')
        ->where('isActive', 1)
        ->orderBy('name','asc')
        ->get();
        $grades=Grade::where('isActive', 1)->get();
        $shapes=Shape::where('isActive', 1)->orderBy('name')->get();
        $packings=Packing::where('isActive', 1)->get();
        $freezings=Freezing::where('isActive', 1)->get();
        $sizes=DB::table('sizes')
        ->where('isActive', 1)
        ->get();

        return view('item.itemStockList', compact('speciesList', 'grades', 'shapes', 'packings', 'freezings', 'sizes'));
    }

    public function indexStockSpecies(Request $request)
    {
        return view('item.speciesStockList');
    }
    public function getSpeciesStock(){
        return $this->item->getSpeciesStock();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($itemId)
    {
        return view('item.itemStockView', compact('itemId'));
    }
    public function showUnpacked($itemId)
    {
        return view('item.itemStockViewUnpacked', compact('itemId'));
    }
    public function showKurangi($itemId)
    {
        return view('item.itemStockSubtractView', compact('itemId'));
    }
}
