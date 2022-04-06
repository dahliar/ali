<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Species;
use App\Models\Store;
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



    public function getAllStockItem($speciesId){
        return $this->item->getAllItemData($speciesId);
    }
    
    public function getItemHistory($itemId){
        return $this->item->getItemHistory($itemId);
    }
    public function getUnpackedItemHistory($itemId){
        return $this->item->getUnpackedItemHistory($itemId);
    }

    public function index(Request $request)
    {
        $speciesList = Species::orderBy('name')->get();
        return view('item.itemStockList', compact('speciesList'));
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
        //
        return view('item.itemStockView', compact('itemId'));
    }
    public function showUnpacked($itemId)
    {
        //
        return view('item.itemStockViewUnpacked', compact('itemId'));
    }
}
