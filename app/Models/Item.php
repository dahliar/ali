<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use DB;
use Auth;


class Item extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function getAllItemData(Request $request){
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'sp.nameBahasa as speciesName', 
            'i.amount as jumlahPacked',
            'amountUnpacked as jumlahUnpacked',
            'p.shortname as packingShortname',
            DB::raw('(select sum(dt.amount) from detail_transactions as dt join transactions t on dt.transactionId=t.id where t.status=4 and dt.itemId=i.id) as jumlahOnLoading'),
            'i.name as iname',
            's.name as sname',
            'sh.name as shname',
            'f.name as fname',
            'g.name as gname',
            'baseprice',
            'weightbase'
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('shapes as sh', 'i.shapeId', '=', 'sh.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->groupBy('i.name')
        ->orderBy('sp.nameBahasa', 'asc')
        ->orderBy('sh.name', 'asc')
        ->orderBy('g.name', 'asc')
        ->orderBy('s.name', 'asc')
        ->orderBy('f.name', 'asc');


        if ($request->speciesId>0){
            $query->where('sp.id','=', $request->speciesId);
        }
        if ($request->sizeId>0){
            $query->where('i.sizeId','=', $request->sizeId);
        }
        if ($request->gradeId>0){
            $query->where('i.gradeId','=', $request->gradeId);
        }
        if ($request->weightbase>0){
            $query->where('i.weightbase','=', $request->weightbase);
        }
        if ($request->shapeId>0){
            $query->where('i.shapeId','=', $request->shapeId);
        }
        if ($request->packingId>0){
            $query->where('i.packingId','=', $request->packingId);
        }
        if ($request->freezingId>0){
            $query->where('i.freezingId','=', $request->freezingId);
        }
        $query->get();  

        return datatables()->of($query)
        ->addColumn('amount', function ($row) {
            $html = '
            <div class="row form-group">
            <span class="col-5">Packed</span>
            <span class="col-7 text-end">'.number_format($row->jumlahPacked, 2).' '.$row->packingShortname.'</span>
            </div>

            <div class="row form-group">
            <span class="col-5">Unpacked</span>
            <span class="col-7 text-end">'.number_format($row->jumlahUnpacked, 2).' Kg'.'</span>
            </div>';
            return $html;
        })
        ->addColumn('itemName', function ($row) {
            $name = $row->speciesName." ".
            $row->shname." Grade ".
            $row->gname. " ".
            $row->fname." Size ".
            $row->sname. " Packing ".
            $row->weightbase." Kg/".
            $row->packingShortname;
            return $name;
        })
        ->addColumn('loading', function ($row) {
            return number_format(($row->jumlahOnLoading*$row->weightbase), 2);
        })
        ->addColumn('totalGudang', function ($row) {
            return number_format(((($row->jumlahPacked) * $row->weightbase) + $row->jumlahUnpacked), 2);
        })
        ->addColumn('action1', function ($row) {
            $html="";
            if (Auth::user()->accessLevel <=40){
                $html = '
                <button onclick="tambahStockItem('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah stok barang"><i class="fa fa-plus"></i></button>
                <button onclick="historyStockItem('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="History tambah stock"><i class="far fa-list-alt"></i></button>
                <button onclick="UpdateStockUnpacked('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Update jumlah unpacked"><i class="fa fa-box-open"></i></button>';
            }
            return $html;
        })
        ->addColumn('action2', function ($row) {
            $html="";
            if (Auth::user()->accessLevel <=40){
                $html = '
                <button onclick="kurangiStockItem('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Kurangi stok barang"><i class="fa fa-minus"></i></button>
                <button onclick="historyStockKurang('."'".$row->id."'".')" data-rowid="'.$row->id.'" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="History kurangi stock"><i class="fas fa-clipboard-list"></i></button>';
            }
            return $html;
        })
        ->rawColumns(['action1', 'action2', 'amount'])->addIndexColumn()->toJson();
    }

    public function getSpeciesStock(){
        $query = DB::table('items as i')
        ->select(
            'i.id as id', 
            'sp.name as name', 
            'sp.nameBahasa as nameBahasa', 
            DB::raw('sum(i.amount*weightbase) as jumlahPacked'),
            DB::raw('sum(amountUnpacked) as jumlahUnpacked'),
            'p.shortname as packingShortname',
            DB::raw('sum((select sum(dt.amount*weightbase) from detail_transactions as dt join transactions t on dt.transactionId=t.id join items i2 on i2.id=dt.itemId join sizes s2 on s2.id=i2.sizeid join species sp2 on sp2.id=s2.speciesId where t.status=4 and dt.itemId=i.id group by sp2.id)) as jumlahOnLoading')            
        )
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->groupBy('sp.id')
        ->orderBy('s.name', 'asc');

        return datatables()->of($query)
        ->addColumn('total', function ($row) {
            $jumlah = $row->jumlahPacked + $row->jumlahUnpacked;
            return number_format($jumlah, 2);
        })
        ->editColumn('packed', function ($row) {
            return number_format($row->jumlahPacked, 2);
        })
        ->editColumn('unpacked', function ($row) {
            return number_format($row->jumlahUnpacked, 2);
        })
        ->editColumn('jumlahOnLoading', function ($row) {
            return number_format(($row->jumlahOnLoading), 2);
        })
        ->addIndexColumn()->toJson();
    }

    public function getUnpackedItemHistory($itemId){
        $query = DB::table('unpacked_histories as u')
        ->select('u.id', 
            'i.name as item', 
            's.name as size',
            'g.name as grade',
            'p.name as packing',
            'f.name as freezing',
            'u.createdAt as tanggalPacking',
            'us.name as username',
            DB::raw('concat(u.amountPacked, " ", p.shortname) as amountPacked'),
            DB::raw('concat(u.amountUnpacked, " Kg") as amountUnpacked'),

            //'u.amountPacked as amountPacked',
            //'u.amountUnpacked as amountUnpacked',
            'i.weightbase'
        )
        ->join('users as us', 'us.id', '=', 'u.userId')
        ->join('items as i', 'i.id', '=', 'u.itemId')
        ->join('sizes as s', 'i.sizeId', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->where('i.isActive','=', 1)
        ->where('i.id','=', $itemId);
        $query->get();  

        return datatables()->of($query)
            /*
            ->addColumn('action', function ($row) {
            $html = '<button type="button"  data-toggle="tooltip" data-placement="top" data-container="body" title="Detail & edit penyimpanan" class="btn btn-primary" onclick="editStoreDetail('."'".$row->id."'".')" data-bs-target="#exampleModal"><i class="fa fa-edit" style="font-size:20px"></i></button>';
            return $html;
            
        })
        */
        ->addIndexColumn()->toJson();    
    }

    public function getItemForSelectOption($transactionId, $purchaseId, $speciesId){
        $query = DB::table('view_item_details as vid')
        ->where('vid.speciesId','=', $speciesId)
        ->orderBy('vid.shapesName', 'asc')
        ->orderBy('vid.gradeName', 'asc')
        ->orderBy('vid.sizeName', 'asc')
        ->orderBy('vid.freezingName');

        if($transactionId>0){
            $list = DB::table("detail_transactions")
            ->select('itemId')
            ->where('transactionId', '=', $transactionId)
            ->get()
            ->pluck('itemId');
            $query->select(
                'vid.itemId as itemId', 
                'vid.name as itemName'
            )
            ->whereNotIn('vid.itemId', $list);
            return $query->get();
        }
        if($purchaseId>0){
            $list = DB::table("detail_purchases")
            ->select('itemId')
            ->where('purchasesId', '=', $purchaseId)
            ->pluck('itemId');
            $query->select(
                'vid.itemId as itemId', 
                'vid.nameBahasa as itemName'
            )
            ->whereNotIn('vid.itemId', $list);
            return $query->get();
        }

        $query->select(
            'vid.itemId as itemId', 
            'vid.nameBahasa as itemName'
        );
        return $query->get();  
    }

    public function getOneItem($itemId){
        $query = DB::table('items as i')
        ->select(
            'i.id as itemId', 
            DB::raw('concat(
                sp.nameBahasa," ",
                sh.name," ",
                g.name," ",
                s.name," ",
                p.name," ",
                f.name," ",
                weightbase," Kg/", p.shortname) as itemName'), 
            'amount',
            'amountUnpacked',
            'baseprice',
            'weightbase as wb',
            'p.shortname as packingShortname'
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

    public function getSizeForSpecies($speciesId){
        $query = DB::table('sizes as s')
        ->select(
            's.id as sizeId', 
            's.name as name'
        )
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('s.speciesId','=', $speciesId)
        ->where('s.isActive','=', 1)
        ->orderBy('s.name', 'asc');  

        return $query->get();
    }
    public function getGradeForSize($sizeId){
        $query = DB::table('items as i')
        ->select(
            'g.id as gradeId', 
            'g.name as name'
        )
        ->join('grades as g', 'i.gradeId', '=', 'g.id')
        ->where('i.sizeId','=', $sizeId)
        ->where('i.isActive','=', 1)
        ->where('g.isActive','=', 1)
        ->distinct()
        ->orderBy('g.name', 'asc');  

        return $query->get();
    }
    public function getWeightbaseForSize($sizeId, $gradeId){
        $query = DB::table('items as i')
        ->select(
            'i.weightbase as weightbase'
        )
        ->where('i.sizeId','=', $sizeId)
        ->where('i.gradeId','=', $gradeId)
        ->where('i.isActive','=', 1)
        ->distinct()
        ->orderBy('i.weightbase', 'asc');  

        return $query->get();
    }
    public function getShapesForWeightbase($sizeId, $gradeId, $weightbase){
        $query = DB::table('items as i')
        ->select(
            's.id as id',
            's.name as name'
        )
        ->join('shapes as s', 's.id', '=', 'i.shapeId')
        ->where('i.sizeId','=', $sizeId)
        ->where('i.gradeId','=', $gradeId)
        ->where('i.weightbase','=', $weightbase)
        ->where('i.isActive','=', 1)
        ->where('s.isActive','=', 1)
        ->distinct()
        ->orderBy('s.name', 'asc'); 

        return $query->get();
    }
    public function getPackingsForShape($sizeId, $gradeId, $weightbase, $shapeId){
        $query = DB::table('items as i')
        ->select(
            'p.id as id',
            'p.name as name'
        )
        ->join('packings as p', 'p.id', '=', 'i.packingId')
        ->where('i.sizeId','=', $sizeId)
        ->where('i.gradeId','=', $gradeId)
        ->where('i.weightbase','=', $weightbase)
        ->where('i.shapeId','=', $shapeId)
        ->where('i.isActive','=', 1)
        ->where('p.isActive','=', 1)
        ->distinct()
        ->orderBy('p.name', 'asc'); 
        return $query->get();
    }
    public function getFreezingsForPacking($sizeId, $gradeId, $weightbase, $shapeId, $packingId){
        $query = DB::table('items as i')
        ->select(
            'f.id as id',
            'f.name as name'
        )
        ->join('freezings as f', 'f.id', '=', 'i.freezingId')
        ->where('i.sizeId','=', $sizeId)
        ->where('i.gradeId','=', $gradeId)
        ->where('i.weightbase','=', $weightbase)
        ->where('i.shapeId','=', $shapeId)
        ->where('i.packingId','=', $packingId)
        ->where('i.isActive','=', 1)
        ->where('f.isActive','=', 1)
        ->distinct()
        ->orderBy('f.name', 'asc'); 
        return $query->get();
    }
}
