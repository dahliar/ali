<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;


class Species extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function getAllSpeciesData($familyId){
        $query = DB::table('species as s')
        ->select(
            's.id as id', 
            's.name as name', 
            's.nameBahasa as nameBahasa', 
            'f.name as familyName',
            DB::raw('IF(count(i.id)>0, count(i.id), 0) as aktifCount'),
        )
        ->leftjoin('sizes as si', 'si.speciesId', '=', 's.id')
        ->join('families as f', 's.familyId', '=', 'f.id')
        ->leftjoin('items as i', 'si.id', '=', 'i.sizeId')
        ->where('s.isActive','=', 1)
        ->orderBy('s.nameBahasa')
        ->groupBy('s.id');

        if ($familyId>0){
            $query->where('s.familyId','=', $familyId);
        }
        $query->get();    

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button onclick="sizeItem('."'".$row->id."'".')"  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Size">
            <i class="fas fa-signal" style="font-size:20px"></i>
            </button>
            <button onclick="listItem('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Barang">
            <i class="fa fa-fish" style="font-size:20px"></i>
            </button>';
            return $html;
        })->addIndexColumn()->toJson();
    }


    public function getAllSpeciesSizeData($speciesId){
        $query = DB::table('sizes as si')
        ->select(
            'si.id as id', 
            'si.name as sizeName', 
            'sp.name as speciesName', 
            'sp.nameBahasa as speciesNameBahasa', 
            'f.name as familyName', 
            'si.isActive as isActive', 
        )
        ->join('species as sp', 'si.speciesId', '=', 'sp.id')
        ->join('families as f', 'sp.familyid', '=', 'f.id')
        ->where('sp.id','=', $speciesId)
        ->get();    

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button onclick="editSpeciesSize('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Size">
            <i class="fa fa-edit" style="font-size:20px"></i>
            </button>';
            return $html;
        })->addIndexColumn()->toJson();
    }

    public function getOneItem($itemId){
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'i.name as itemName', 
            'sp.name as speciesName', 
            'sp.nameBahasa as speciesNameBahasa', 
            'sh.name as shapeName', 
            'sh.id as shid', 
            's.name as sizeName',
            'g.name as gradeName',
            'p.name as packingName',
            'f.name as freezingName',
            'amount',
            'baseprice',
            'weightbase',
            'i.isActive as isActive'
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('shapes as sh', 'i.shapeId', '=', 'sh.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.id','=', $itemId)
        ->get();    

        return $query->first();
    }
    //this
    public function getAllSpeciesItemData($speciesId){
        $query = DB::table('items as i')
        ->select(
            'i.id as id',
            'sp.nameBahasa as species',
            DB::raw('concat(
                sp.nameBahasa," ",
                sh.name," Grade ",
                g.name," ",
                fr.name," Size ",
                si.name," Packing ",
                weightbase," Kg/", p.shortname
            ) as itemName'),
            'p.shortname as shortname',
            'i.weightbase as wb',
            'i.amount as amount',
            'i.imageurl as url', 
            DB::raw('(CASE WHEN i.isActive=0 THEN "Tidak" WHEN i.isActive="1" THEN "Ya" END) AS isActive'),
        )
        ->join('sizes as si', 'i.sizeId', '=', 'si.id')
        ->join('species as sp', 'si.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('shapes as sh', 'i.shapeId', '=', 'sh.id')
        ->join('freezings as fr', 'i.freezingId', '=', 'fr.id')
        ->orderBy('sp.name', 'asc')
        ->orderBy('sh.name', 'asc')
        ->orderBy('g.name', 'asc')
        ->orderBy('si.name', 'asc');


        if ($speciesId>0){
            $query->where('sp.id','=', $speciesId);
        }
        $query->get();

        return datatables()->of($query)
        ->editColumn('packing', function ($row) {
            $html = number_format($row->amount, 2, ',', '.').' '.$row->shortname;
            return $html;
        })
        ->editColumn('amount', function ($row) {
            $html = number_format($row->amount, 2, ',', '.').' '.$row->wb;
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html="";
            $html = '<button onclick="editSpeciesItem('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit Item"><i class="fa fa-edit" style="font-size:20px"></i></button>';

            $file = URL::to('/').'/'.$row->url;
            $path = public_path() .'/'. $row->url;

            if (file_exists($path) and ($row->url!=null)){
                $html.='<a href="'.URL::to('/').'/'.$row->url.'" target="_blank"><i class="fa fa-image"></i></a>';
            }
            return $html;
        })
        ->addIndexColumn()->toJson();
    }

    public function getOneSpecies($speciesId){

    }
    public function getOneSize($sizeId){
        $query = DB::table('sizes as s')
        ->select(
            's.id as id', 
            's.name as name', 
            'sp.name as speciesName', 
            'sp.id as speciesId', 
            's.isActive as isActive'
        )
        
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('s.id','=', $sizeId)
        ->get();    

        return $query->first();

        //jika $query->all() maka untuk multi rows, perlu diolah lagi pada saat hendak ditampilkan dalam view

    }
}
