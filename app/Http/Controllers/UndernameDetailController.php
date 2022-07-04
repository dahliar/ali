<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UndernameDetail;
use App\Models\Undername;
use Illuminate\Validation\Rule;
use DB;

class UndernameDetailController extends Controller
{
    public function __construct(){
        $this->DetailUndername = new UndernameDetail();
    }

    public function index($undernameId)
    {
        $undernameStatus=Undername::select('status')->where('id', $undernameId)->value('status');
        return view('undername.undernameDetailList', compact('undernameId', 'undernameStatus'));
    }
    public function create(Undername $undername)
    {
        $undernameId = $undername->id;
        $valutaType = $undername->valutaType;
        $marker="";
        switch ($undername->paymentValuta){
            case 1 : $marker = "Rp"; break;
            case 2 : $marker = "USD"; break;
            case 3 : $marker = "Rmb"; break;
        }
        return view('undername.undernameDetailAdd', compact('undernameId', 'marker'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'undernameId' => 
            [
                'required',
                Rule::exists('undernames', 'id')->where('id', $request->undernameId)
            ], 
            'item' => [
                'required',
                Rule::unique('undername_details', 'item')->where(function ($query) use ($request){
                    return $query->where('undernameId', $request->undernameId);
                })
            ],
            'amount' => 'required|numeric|gt:0',
            'harga' => 'required|numeric|gt:0',
        ],[
            'species.gt'=> 'Pilih satu Species',
            'item.required'=> 'Nama barang harus diisi',
            'item.unique'=> 'Nama barang sudah ada pada transaksi ini',
            'amount.gt' => 'Amount harus lebih dari 0', 
            'harga.gt' => 'Harga harus lebih dari 0', 
            'harga.integer' => 'Harga harus berupa angka',
        ]);

        $data = [
            'undernameId' => $request->undernameId,
            'item' => $request->item,
            'amount' =>  $request->amount,
            'price' =>  $request->harga
        ];

        DB::table('undername_details')->insert($data);
        return redirect('detailundernameList/'.$request->undernameId)
        ->with('status','Item berhasil ditambahkan.');
    }
    public function view($undernameId){
        $query = DB::table('undername_details as ud')
        ->select(
            'ud.id as id', 
            'ud.undernameId as undernameId', 
            'ud.item as item',
            'ud.amount as amount',
            'ud.price as price',
            DB::raw('(CASE   WHEN u.paymentValuta="1" THEN "Rp. " 
                WHEN u.paymentValuta="2" THEN "USD. " 
                WHEN u.paymentValuta="3" THEN "Rmb. " 
                END) as valuta'
            ), 
        )
        ->join('undernames as u', 'u.id', '=', 'ud.undernameId')
        ->where('u.id','=', $undernameId);
        $query->get();  


        return datatables()->of($query)
        ->addColumn('total', function ($row) {
            $html = $row->valuta.' '.number_format(($row->amount * $row->price), 2, ',', '.');
            return $html;
        })
        ->editColumn('price', function ($row) {
            $html = $row->valuta.' '.number_format($row->amount, 2, ',', '.');
            return $html;
        })
        ->editColumn('amount', function ($row) {
            $html = number_format($row->amount, 2, ',', '.').' Kg';
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html = '
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Detail" onclick="deleteItem('."'".$row->id."'".')">
            <i class="fa fa-trash" style="font-size:20px"></i>
            </button>
            ';
            return $html;
        })
        ->addIndexColumn()->toJson();
    }
    public function destroy(UndernameDetail $undernameDetail)
    {
        DB::table('undername_details')->delete($undernameDetail->id);
        return "sukses";
    }
}
