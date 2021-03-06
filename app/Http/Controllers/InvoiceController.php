<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\DetailTransaction;
use App\Models\Purchase;
use App\Models\DetailPurchase;
use App\Models\Rekening;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Countries;
use App\Models\TransactionNote;
use PDF;
use DB;
use Auth;

class InvoiceController extends Controller
{
    public function __construct(){
        $this->invoice = new Invoice();
    }
    public function getPurchaseNumber($purchaseId){
        $bagian="PURCHASE-ALI";
        $month = date('m');
        $year = date('Y');
        $isActive=1;

        $result = DB::table('document_numbers as dn')
        ->where('year', $year)
        ->where('bagian', $bagian)
        ->where('purchaseId','!=', null)
        ->max('nomor');

        if ($result>0){
            $nomor=$result+1;
        }
        else{
            $nomor=1;
        }

        $data = [
            'nomor'=>$nomor,
            'purchaseId'=>$purchaseId,
            'bagian'=>$bagian,
            'month'=>$month,
            'year'=>$year,
            'isActive'=>$isActive
        ];
        $tnum = $nomor.'/'.$bagian.'/'.$month.'/'.$year;
        DB::table('document_numbers')->insert($data);

        return $tnum;
    }


    public function createtransactionnum($transactionId){
        $bagian="INV-ALI";
        $month = date('m');
        $year = date('Y');
        $isActive=1;

        $result = DB::table('document_numbers as dn')
        ->where('year', $year)
        ->where('bagian', $bagian)
        ->where('transactionId','!=', null)
        ->max('nomor');

        if ($result>0){
            $nomor=$result+1;
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
            'isActive'=>$isActive
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

        $result = DB::table('document_numbers as dn')
        ->where('year', $year)
        ->where('bagian', $bagian)
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
            'isActive'=>$isActive
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
        $payerName = Auth::user()->name;

        $pdf = PDF::loadview('invoice.ipl', compact('valutaType','containerType','companyName', 'transaction', 'detailTransactions', 'rekening', 'payerName'));
        $filename = 'IPL '.$transaction->id.' '.$companyName->name.' '.today().'.pdf';
        return $pdf->download($filename);
    }
    public function cetak_local_ipl(Transaction $transaction)
    {
        $detailTransactions = $this->invoice->getOneInvoiceDetail($transaction->id);
        $companyName = Company::select('name')->where('id',$transaction->companyId)->first();

        $rekening = Rekening::where('id',$transaction->rekeningid)->first();
        $valutaType = "";
        switch($transaction->valutaType){
            case(1) : $valutaType="Rp";   break;
            case(2) : $valutaType="USD";  break;
            case(3) : $valutaType="RMB";  break;
        }
        $payerName = Auth::user()->name;

        $pdf = PDF::loadview('invoice.localIpl', compact('valutaType','companyName', 'transaction', 'detailTransactions', 'rekening', 'payerName'));
        $filename = 'IPL '.$transaction->id.' '.$companyName->name.' '.today().'.pdf';
        return $pdf->download($filename);
    }


    public function cetakNotaPembelian(Purchase $purchase)
    {
        //dd($purchase);
        $purchaseDetails = $this->invoice->getOnePurchaseDetail($purchase->id);
        $company = Company::where('id',$purchase->companyId)->first();
        $valutaType = "";
        switch($purchase->valutaType){
            case(1) : $valutaType="Rp";   break;
            case(2) : $valutaType="USD";  break;
            case(3) : $valutaType="RMB";  break;
        }
        $pdf = PDF::loadview('invoice.notaPembelian', compact('valutaType', 'company','purchase', 'purchaseDetails'));
        $filename = 'NotaPembelian '.$purchase->id.' '.$company->name.' '.today().'.pdf';
        return $pdf->download($filename);
    }

    public function cetakDaftarGajiHarian(Transaction $transaction)
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

        $pdf = PDF::loadview('invoice.ipl', compact('valutaType','containerType','companyName','transaction', 'detailTransactions', 'rekening'));
        $filename = 'IPL '.$transaction->id.' '.$companyName->name.' '.today().'.pdf';
        return $pdf->download($filename);
    }

    public function slipGajiPerPayroll($dpid)
    {   
        $detail_payroll = DB::table('detail_payrolls as dp')->where('dp.id', '=', $dpid)->first();
        $employee =  DB::table('employees as e')
        ->select(
            'e.nip as nip',
            'e.nik as nik',
            'u.name as name',
            'os.name as osname',
            'e.noRekening as noRekening',
            'ba.shortname as bankName'
        )
        ->join('banks as ba', 'ba.id', '=', 'e.bankid')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('eosm.isactive', 1)
        ->where('e.id', '=', $detail_payroll->employeeId)->first();
        $payroll=DB::table('payrolls as p')->where('p.id', '=', $detail_payroll->idPayroll)->first();
        $salaries=DB::table('salaries as s')->where('s.idPayroll', '=', $payroll->id)->get();
        $presence="";
        $bulanan="";
        $borongan="";
        $harian="";
        $honorarium="";
        $startDate=$salaries[0]->startDate;
        $endDate=$salaries[0]->endDate;

        foreach($salaries as $sal){
            switch($sal->jenis){
                case('2') :
                $harian = DB::table('dailysalaries as ds')
                ->select(
                    'p.start as start', 
                    'p.end as end', 
                    'p.employeeId as empid', 
                    'p.jamKerja as jk', 
                    'p.jamLembur as jl', 
                    'ds.uangHarian as uh', 
                    'ds.uangLembur as ul')
                ->join('presences as p', 
                    DB::raw('concat(date(p.start),p.employeeId)'), 
                    '=', 
                    DB::raw('concat(date(ds.presenceDate),ds.employeeId)'))
                ->where('ds.salaryId', '=', $sal->id)
                ->where('ds.employeeId', '=', $detail_payroll->employeeId)
                ->get();
                break;
                
                case('3') : 
                $borongan = DB::table('borongans as b')
                ->select(
                    'b.name', 'b.tanggalkerja', 'b.hargaSatuan', 'b.netweight','b.worker',
                    'db.netPayment'
                )
                ->join('detail_borongans as db', 'db.boronganId', '=', 'b.id')
                ->where('b.salariesId', '=', $sal->id)
                ->where('db.employeeId', '=', $detail_payroll->employeeId)
                ->get();

                break;
                case('4') : 
                $honorarium = DB::table('honorariums as h')
                ->select(
                    'h.tanggalKerja', 'h.jumlah', 'h.keterangan'
                )
                ->where('h.salaryId', '=', $sal->id)
                ->where('h.employeeId', '=', $detail_payroll->employeeId)
                ->get();

                break;
            }

        }
        $pdf = PDF::loadview('invoice.slipGajiPegawai', compact('endDate','startDate','employee', 'payroll', 'detail_payroll', 'presence', 'bulanan','harian', 'borongan', 'honorarium'));
        $filename = 'Slip Gaji '.$employee->nip.' '.$employee->name.' '.now().'.pdf';
        return $pdf->download($filename);

    }
    public function slipGajiPerPayrollBulanan($dpid)
    {   
        $detail_payroll = DB::table('detail_payrolls as dp')->where('dp.id', '=', $dpid)->first();
        $employee =  DB::table('employees as e')
        ->select(
            'e.nip as nip',
            'e.nik as nik',
            'u.name as name',
            'os.name as osname',
            'e.noRekening as noRekening',
            'ba.shortname as bankName'
        )
        ->join('banks as ba', 'ba.id', '=', 'e.bankid')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eosm', 'e.id', '=', 'eosm.idemp')
        ->join('organization_structures as os', 'os.id', '=', 'eosm.idorgstructure')
        ->where('eosm.isactive', 1)
        ->where('e.id', '=', $detail_payroll->employeeId)->first();
        $payroll=DB::table('payrolls as p')->where('p.id', '=', $detail_payroll->idPayroll)->first();
        $salaries=DB::table('salaries as s')->where('s.idPayroll', '=', $payroll->id)->get();
        $presence="";
        $bulanan="";
        $borongan="";
        $harian="";
        $honorarium="";
        $startDate=$salaries[0]->startDate;
        $endDate=$salaries[0]->endDate;


        foreach($salaries as $sal){
            switch($sal->jenis){
                case('1') :
                $bulanan = DB::table('monthly_salaries as ms')
                ->select(
                    'ms.tanggalGenerate as tanggal', 
                    'ms.employeeId as empid', 
                    'ms.jumlah as jumlah'
                )
                ->where('ms.salaryId', '=', $sal->id)
                ->where('ms.salaryId', '=', $sal->id)
                ->where('ms.employeeId', '=', $detail_payroll->employeeId)
                ->first();
                break;
                case('2') :
                $harian = DB::table('dailysalaries as ds')
                ->select(
                    'p.start as start', 
                    'p.end as end', 
                    'p.employeeId as empid', 
                    'p.jamKerja as jk', 
                    'p.jamLembur as jl', 
                    'ds.uangHarian as uh', 
                    'ds.uangLembur as ul')
                ->join('presences as p', 
                    DB::raw('concat(date(p.start),p.employeeId)'), 
                    '=', 
                    DB::raw('concat(date(ds.presenceDate),ds.employeeId)'))
                ->join('salaries as s', 's.id', '=', 'ds.salaryId')
                ->where('s.jenis', '=', '2')
                ->where('ds.salaryId', '=', $sal->id)
                ->where('ds.employeeId', '=', $detail_payroll->employeeId)
                ->get();
                break;
                case('4') : 
                $honorarium = DB::table('honorariums as h')
                ->select(
                    'h.tanggalKerja', 'h.jumlah', 'h.keterangan'
                )
                ->where('h.salaryId', '=', $sal->id)
                ->where('h.employeeId', '=', $detail_payroll->employeeId)
                ->get();

                break;
            }

        }
        $pdf = PDF::loadview('invoice.slipGajiPegawai', compact('endDate','startDate','employee', 'payroll', 'detail_payroll', 'presence', 'bulanan','harian', 'borongan', 'honorarium'));
        $filename = 'Slip Gaji '.$employee->nip.' '.$employee->name.' '.now().'.pdf';
        return $pdf->download($filename);

    }


}
