<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\DetailTransaction;
use App\Models\Rekening;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Countries;
use App\Models\TransactionNote;
use PDF;
use DB;

class InvoiceController extends Controller
{
    public function __construct(){
        $this->invoice = new Invoice();
    }

    public function createtransactionnum($transactionId){
        $bagian="INV-ALI";
        $month = date('m');
        $year = date('Y');
        $isActive=1;
        $createdAt=date('Y-m-d');

        $result = DB::table('document_numbers as dn')
        ->where('month', $month)
        ->where('year', $year)
        ->where('bagian', $bagian)
        //->where('documentType', $documentType)
        ->orderBy('id', 'desc')
        ->first();

        if ($result){
            $nomor=($result->nomor)+1;
        }
        else{
            $nomor=1;
        }

        $data = [
            'nomor'=>$nomor,
            'transactionId'=>$transactionId,
            'bagian'=>$bagian,
            //'documentType'=>$documentType,
            'month'=>$month,
            'year'=>$year,
            'isActive'=>$isActive,
            'createdAt'=>$createdAt,
        ];
        $tnum = $nomor.'/'.$bagian.'/'.$month.'/'.$year;
        DB::table('document_numbers')->insert($data);
        return $tnum;
    }


    public function createpinum($transactionId){
        $bagian="PI-ALI";
        $month = date('m');
        $year = date('Y');
        $isActive=1;
        $createdAt=date('Y-m-d');

        $result = DB::table('document_numbers as dn')
        ->where('month', $month)
        ->where('year', $year)
        ->where('bagian', $bagian)
        //->where('documentType', $documentType)
        ->orderBy('id', 'desc')
        ->first();

        //var_dump($nomor);
        if ($result){
            $nomor=($result->nomor)+1;
        }
        else{
            $nomor=1;
        }

        $data = [
            'nomor'=>$nomor,
            'transactionId'=>$transactionId,
            'bagian'=>$bagian,
            'month'=>$month,
            //'documentType'=>$documentType,
            'year'=>$year,
            'isActive'=>$isActive,
            'createdAt'=>$createdAt,
        ];
        $pinum = $nomor.'/'.$bagian.'/'.$month.'/'.$year;
        DB::table('document_numbers')->insert($data);
        DB::table('transactions')
        ->where('id', $transactionId)
        ->update(['pinum' => $pinum]);

        return $pinum;
    }



    public function cetak_pi(Transaction $transaction)
    {
        $detailTransactions = $this->invoice->getOneInvoiceDetail($transaction->id);
        $companyName = Company::select('name')->where('id',$transaction->companyId)->first();

        $rekening = Rekening::where('id',$transaction->rekeningid)->first();
        $notes = TransactionNote::where('transactionId',$transaction->id)->get();
        $registration = Countries::where('id',$transaction->countryId)->first()->registration;

        $containerType = "";
        switch ($transaction->containerType){
            case 1 : $containerType = "Dry"; break;
            case 2 : $containerType = "Reefer"; break;
        }
        $valutaType = "";
        switch($transaction->valutaType){
            case(1) : $valutaType="Rp";   break;
            case(2) : $valutaType="USD";  break;
            case(3) : $valutaType="RMB";  break;
        }



        //return  view('invoice.pi', compact('notes', 'valutaType', 'containerType', 'companyName', 'transaction', 'detailTransactions', 'rekening'));


        $pdf = PDF::loadview('invoice.pi', compact('registration','notes','valutaType','containerType','companyName','transaction', 'detailTransactions', 'rekening'));
        $filename = 'Proforma Invoice '.$transaction->id.' '.$companyName->name.' '.today().'.pdf';
        return $pdf->download($filename);

    }
    public function cetak_ipl(Transaction $transaction)
    {
        $detailTransactions = $this->invoice->getOneInvoiceDetail($transaction->id);
        $companyName = Company::select('name')->where('id',$transaction->companyId)->first();

        $rekening = Rekening::where('id',$transaction->rekeningid)->first();

        $containerType = "";
        switch ($transaction->containerType){
            case 1 : $containerType = "Dry"; break;
            case 2 : $containerType = "Reefer"; break;
        }
        $valutaType = "";
        switch($transaction->valutaType){
            case(1) : $valutaType="Rp";   break;
            case(2) : $valutaType="USD";  break;
            case(3) : $valutaType="RMB";  break;
        }



        //return  view('invoice.ipl', 
        //    compact('containerType','companyName','transaction', 'detailTransactions', 'rekening'));
        $pdf = PDF::loadview('invoice.ipl', compact('valutaType','containerType','companyName','transaction', 'detailTransactions', 'rekening'));
        $filename = 'IPL '.$transaction->id.' '.$companyName->name.' '.today().'.pdf';
        return $pdf->download($filename);
    }
}
