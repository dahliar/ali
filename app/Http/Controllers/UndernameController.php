<?php

namespace App\Http\Controllers;
use App\Models\Undername;
use App\Models\UndernameDetail;
use App\Models\Company;
use App\Models\Countries;

use Illuminate\Http\Request;
use PDF;


class UndernameController extends Controller
{
    public function cetak_pi(Undername $undername){
        $undername_details = UndernameDetail::where('undernameId',$undername->id)->get();

        $companyName = Company::select('name')->where('id',$undername->companyId)->first()->name;
        $registration = Countries::where('id',$undername->countryId)->first()->registration;
        $containerType = "";
        switch ($undername->containerType){
            case 1 : $containerType = "Dry"; break;
            case 2 : $containerType = "Reefer"; break;
        }
        $paymentValuta = "";
        switch($undername->paymentValuta){
            case(1) : $paymentValuta="Rp";   break;
            case(2) : $paymentValuta="USD";  break;
            case(3) : $paymentValuta="RMB";  break;
        }

        $pdf = PDF::loadview('undername.undernamePi', compact('registration','paymentValuta','containerType','companyName','undername', 'undername_details'));
        $filename = 'Proforma Invoice '.$undername->id.' '.$companyName.' '.today().'.pdf';
        return $pdf->download($filename);
    }
    public function cetak_ipl(Undername $undername){
        $undername_details = UndernameDetail::where('undernameId',$undername->id)->get();

        $companyName = Company::select('name')->where('id',$undername->companyId)->first()->name;
        $registration = Countries::where('id',$undername->countryId)->first()->registration;
        $containerType = "";
        switch ($undername->containerType){
            case 1 : $containerType = "Dry"; break;
            case 2 : $containerType = "Reefer"; break;
        }
        $paymentValuta = "";
        switch($undername->paymentValuta){
            case(1) : $paymentValuta="Rp";   break;
            case(2) : $paymentValuta="USD";  break;
            case(3) : $paymentValuta="RMB";  break;
        }

        $pdf = PDF::loadview('undername.undernameIpl', compact('registration','paymentValuta','containerType','companyName','undername', 'undername_details'));
        $filename = 'Invoice Packing List '.$undername->id.' '.$companyName.' '.today().'.pdf';
        return $pdf->download($filename);
    }
}
