<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Species;
use App\Models\Company;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

use DB;

class BarcodeController extends Controller
{
    public function __construct(){
        $this->dns1d = new DNS1D();
        $this->dns2d = new DNS2D();
    }
    public function create()
    {
        $species = Species::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        return view('barcode.barcodeAdd', compact('species', 'companies'));
    }
    public function itemList($speciesId){
        $query = DB::table('view_item_details as vid')
        ->select(
            'vid.itemId as itemId', 
            'vid.nameBahasa as itemName'
        )
        ->where('vid.speciesId','=', $speciesId)
        ->orderBy('vid.gradeName', 'asc')
        ->orderBy('vid.sizeName', 'asc')
        ->orderBy('vid.freezingName');

        return $query->get();  
    }
    public function generate(Request $request){

        $request->validate(
            [
                //'transactionNum' => 'required|unique:transactions',
                'species' => 'required|gt:0',
                'item' => 'required|gt:0',
                'transactionDate' => 'required|date|after_or_equal:today',
                'company' => 'required|gt:0',
                'jumlahBarcode' => 'required|gt:0',

            ],
            [
                'jumlahBarcode.required'=> 'Jumlah barcode minimal 1'
            ]
        );
        $date = \Carbon\Carbon::parse($request->transactionDate);
        for ($a=1; $a<=$request->jumlahBarcode; $a++){
            $barcodeId = 
            str_pad($date->year, 4, '0', STR_PAD_LEFT).
            str_pad($date->month, 2, '0', STR_PAD_LEFT).
            str_pad($date->day, 2, '0', STR_PAD_LEFT).
            str_pad($request->item, 4, '0', STR_PAD_LEFT).
            str_pad($request->company, 4, '0', STR_PAD_LEFT).
            str_pad($a, 5, '0', STR_PAD_LEFT);
            echo '<br>';

            echo $barcodeId." ". $this->dns2d->getBarcodeHTML($barcodeId, 'QRCODE');
        }

        dd($request->jumlahBarcode);

        

        $pdf = PDF::loadview('barcode.cetakBarcode', compact('registration','paymentValuta','containerType','companyName','undername', 'undername_details'));
        $filename = 'Proforma Invoice '.$undername->id.' '.$companyName.' '.today().'.pdf';
        return $pdf->download($filename);
    }

}
