<?php

namespace App\Http\Controllers;
use App\Models\Undername;
use App\Models\UndernameDetail;
use App\Models\Company;
use App\Models\Countries;
use App\Models\Bank;
use App\Models\Liner;
use App\Models\Forwarder;
use App\Models\Currency;

use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\InvoiceController;

use Illuminate\Http\Request;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class UndernameController extends Controller
{
    public function __construct(){
        $this->undername = new Undername();
    }
    public function index()
    {
        $nations = Countries::where('isActive',1)->get();
        return view('undername.undernameList', compact('nations'));
    }

    public function getAllUndernameTransaction(Request $request){
        return $this->undername->getAllUndernameTransactionData($request);
    }

    public function undernameDocument(Undername $undername)
    {
        return view('undername.undernameDocuments', compact('undername'));
    }

    public function createUndername()
    {
        $companies = Company::all();
        $banks = Bank::all();
        $liners = Liner::orderBy('name', 'ASC')->get();
        $forwarders = Forwarder::where('isActive', 1)->orderBy('name', 'ASC')->get();
        $countryRegister = Countries::where('isActive',1)->get();
        $currencies = Currency::orderBy('name')->get();

        return view('undername.undernameAdd', compact('countryRegister', 'companies', 'banks', 'forwarders', 'liners', 'currencies'));
    }

    public function undernameEdit(Undername $undername)
    {
        $companies = Company::all();
        $banks = Bank::all();
        $liners = Liner::all();
        $forwarders = Forwarder::where('isActive', 1)->orderBy('name', 'ASC')->get();
        $countryRegister = Countries::where('isActive',1)->get();
        $currencies = Currency::orderBy('name')->get();


        return view('undername.undernameEdit', compact('undername', 'countryRegister', 'companies', 'banks', 'forwarders', 'liners', 'currencies'));
    }

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
        $filename = 'Proforma Invoice Undername '.$undername->id.' '.$companyName.' '.today().'.pdf';
        $filepath = '../storage/app/docs/'.$filename;
        $pdf->save($filepath);

        //insert kedalam tabel documents
        $document_numbers_id = DB::table('document_numbers as dn')
        ->select('id')
        ->wherein('bagian', ['PIU-ALI','PIU-ALS'])
        ->where('undernameId','=', $undername->id)
        ->first()->id;

        $data = [
            'document_numbers_id'   => $document_numbers_id,
            'filepath'              => $filename,
            'userId'                => auth()->user()->id
        ];
        $id = DB::table('documents')->insertGetId($data);
        return true;

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
        $filepath = '../storage/app/docs/'.$filename;
        $pdf->save($filepath);

        //insert kedalam tabel documents
        $document_numbers_id = DB::table('document_numbers as dn')
        ->select('id')
        ->wherein('bagian', ['INVU-ALI','INVU-ALS'])
        ->where('undernameId','=', $undername->id)
        ->first()->id;

        $data = [
            'document_numbers_id'   => $document_numbers_id,
            'filepath'              => $filename,
            'userId'                => auth()->user()->id
        ];
        $id = DB::table('documents')->insertGetId($data);
        return true;
    }

    public function undernameStore(Request $request)
    {   
        //21 validasi inputan wajib
        //2 default value dari inputan : swiftcode, valuta
        //3 inputan default: userId, creationDate, status

        $request->validate(
            [
                'shipper' => 'required',
                //'pinum' => 'required|unique:undernames',
                //'transactionNum' => 'required|unique:undernames',
                //'pebNum' => 'required', 
                //'pebDate' => 'required|date|after_or_equal:today',                
                'shipperAddress' => 'required',
                'company' => 'required|gt:0',
                'companydetail' => 'required',                
                'countryId' => 'required|gt:0',
                'packer' => 'required',
                'loadingPort' => 'required',
                'orderType' => 'required',                
                'destinationPort' => 'required',                
                'containerType' => 'required|gt:0',                
                'containerParty' => 'required',
                'forwarder' => 'required|gt:0',

                'containerNumber' => 'required',
                'containerSeal' => 'required',
                'containerVessel' => 'required',
                'liner' => 'required|gt:0',
                'bl' => 'required',

                'transactionDate' => 'required|date|before_or_equal:today',
                'loadingDate' => 'required|date',
                'departureDate' => 'required|date|after_or_equal:loadingDate',
                'arrivalDate' => 'required|date|after:departureDate',

                'paymentTo' => 'required|gt:0',
                'bank' => 'required|gt:0',
                'paymentBankAddress' => 'required',
                'account' => 'required',
                'paymentAccountName' => 'required',
                'swiftcode' => 'required',
                'valutaType' => 'required|gt:0',
                'paymentAmount' => 'required|gte:0',
                'advanceAmount' => 'required|gte:0',
                'paymentStatus' => 'required|gt:0',
            ],
            [
                'rekening.gt'=> 'Pilih salah satu rekening',
                'liner.*'=> 'Pilih salah satu Liner',
                'bl.*'=> 'Nomor BL harus diisi',
                'company.gt'=> 'Pilih salah satu perusahaan',
                'containerType.gt'=> 'Pilih salah satu jenis pengiriman',
                'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
                'pinum.unique'=> 'Nomor pinum harus unik',
                'transactionNum.unique'=> 'Nomor transaksi harus unik',
                'countryId.gt'=>'Pilih salah satu negara',
                'forwarder.gt'=>'Pilih forwarder',
                'undernamePayment.*'=>'Pilih status jenis undername',
                'paymentStatus.*'=>'Pilih status pembayaran',
                'pinum.*'=> 'Nomor PI harus diisi',
                'transactionNum.*'=> 'Nomor transaksi harus diisi',
                'companydetail.*' => 'Detail perusahaan harus diisi',  
                'packer.*' => 'Nama perusahaan packer harus diisi',
                'loadingPort.*' => 'Port loading harus diisi',
                'destinationPort.*' => 'Port tujuan harus diisi',
                'containerNumber.*' => 'Nomor kontainer harus diisi',
                'containerSeal.*' => 'Nomor seal harus diisi',
                'containerVessel.*' => 'Nama vessel harus diisi',
                'liner.*' => 'Liner harus dipilih',
                'bl.*' => 'Nomor Bill of Lading harus diisi',
            ]
        );

        $data = [
            'userId' => auth()->user()->id,
            'jenis' => 1,
            'shipper' =>  $request->shipper,
            'shipperAddress' =>  $request->shipperAddress,
            'companyId' =>  $request->company,
            'companydetail' =>  $request->companydetail,
            //'transactionNum' => $request->transactionNum,
            //'pinum' => $request->pinum,
            'status' =>  1,
            'packer' => $request->packer,
            'loadingport' =>  $request->loadingPort,
            'destinationport' =>  $request->destinationPort,
            'orderType' => $request->orderType,
            'countryId' => $request->countryId,
            'packer' =>  $request->packer,
            'containerType' =>  $request->containerType,
            'containerParty' =>  $request->containerParty,

            'forwarderid' => $request->forwarder,
            'containerNumber' =>  $request->containerNumber,
            'containerSeal' =>  $request->containerSeal,
            'containerVessel' =>  $request->containerVessel,

            'linerId' => $request->liner,
            'bl' => $request->bl,
            'transactionDate' => $request->transactionDate,
            'departureDate' =>  $request->departureDate,
            'loadingDate' =>  $request->loadingDate,
            'arrivalDate' => $request->arrivalDate,
            
            'paymentTo' => $request->paymentTo,
            'paymentBank' => $request->bank,
            'paymentBankAddress' => $request->paymentBankAddress,
            'paymentAccount' => $request->account,
            'paymentAccountName' => $request->paymentAccountName,
            'paymentSwiftcode' => $request->swiftcode,
            'paymentValuta' =>  $request->valutaType,
            'paymentAmount' =>  $request->paymentAmount,
            'paymentAdvance' =>  $request->advanceAmount,
            'paymentStatus' =>  $request->paymentStatus
        ];
        
        $lastTransactionIdStored = DB::table('undernames')->insertGetId($data);
        
        //create PI Number
        $pinum = $this->createUndernamePinum($lastTransactionIdStored);
        return redirect('undernameList')
        ->with('status','Transaksi Undername berhasil ditambahkan.');
    }

    public function createUndernamePinum($undernameId){
        $bagian="PIU-ALS";
        $month = date('m');
        $year = date('Y');
        $isActive=1;

        $result = DB::table('document_numbers as dn')
        ->where('year', $year)
        ->where('bagian', $bagian)
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
            'undernameId'=>$undernameId,
            'bagian'=>$bagian,
            'month'=>$month,
            'year'=>$year,
            'isActive'=>$isActive
        ];
        $pinum = $nomor.'/'.$bagian.'/'.$month.'/'.$year;
        DB::table('document_numbers')->insert($data);
        DB::table('undernames')
        ->where('id', $undernameId)
        ->update(['pinum' => $pinum]);

        return $pinum;
    }

    public function undernameUpdate(Request $request)
    {   
        if (($request->status==2) and (($request->currentStatus==4) or ($request->currentStatus==1)) ){
            $request->validate(
                [
                    'shipper' => 'required',
                    'pebFile' => ['required', 'mimes:jpg,jpeg,png,pdf','max:2048'],
                    'pebNum' => 'required',
                    'pebDate' => 'required|date|after_or_equal:transactionDate',
                    'shipperAddress' => 'required',
                    'company' => 'required|gt:0',
                    'companydetail' => 'required',                
                    'countryId' => 'required|gt:0',
                    'packer' => 'required',
                    'loadingPort' => 'required',
                    'orderType' => 'required',                
                    'destinationPort' => 'required',                
                    'containerType' => 'required|gt:0',                
                    'containerParty' => 'required',
                    'forwarder' => 'required|gt:0',

                    'containerNumber' => 'required',
                    'containerSeal' => 'required',
                    'containerVessel' => 'required',
                    'liner' => 'required|gt:0',
                    'bl' => 'required',

                    'transactionDate' => 'required|date|before_or_equal:today',
                    'loadingDate' => 'required|date',
                    'departureDate' => 'required|date|after_or_equal:loadingDate',
                    'arrivalDate' => 'required|date|after:departureDate',

                    'paymentTo' => 'required|gt:0',
                    'bank' => 'required|gt:0',
                    'account' => 'required',
                    'swiftcode' => 'required',
                    'valutaType' => 'required|gt:0',
                    'paymentAmount' => 'required|gte:0',
                    'advanceAmount' => 'required|gte:0',
                    'paymentStatus' => 'required|gt:0',
                    'status' => 'required|gt:0'
                ],
                [
                    'pebFile.required' => 'File invoice harus ada',
                    'pebFile.max' => 'Ukuran file maksimal adalah 2 MB',
                    'pebNum.*' => 'Nomor PEB wajib diisi',
                    'pebDate.*' => 'Tanggal PEB setelah atau sama dengan tanggal Transaksi',
                    'rekening.gt'=> 'Pilih salah satu rekening',
                    'liner.*'=> 'Pilih salah satu Liner',
                    'bl.*'=> 'Nomor BL harus diisi',
                    'company.gt'=> 'Pilih salah satu perusahaan',
                    'containerType.gt'=> 'Pilih salah satu jenis pengiriman',
                    'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
                    'pinum.unique'=> 'Nomor pinum harus unik',
                    'transactionNum.unique'=> 'Nomor transaksi harus unik',
                    'countryId.gt'=>'Pilih salah satu negara',
                    'forwarder.gt'=>'Pilih forwarder',
                    'undernamePayment.*'=>'Pilih status jenis undername',
                    'paymentStatus.*'=>'Pilih status pembayaran',
                    'pinum.*'=> 'Nomor PI harus diisi',
                    'transactionNum.*'=> 'Nomor transaksi harus diisi',
                    'companydetail.*' => 'Detail perusahaan harus diisi',  
                    'packer.*' => 'Nama perusahaan packer harus diisi',
                    'loadingPort.*' => 'Port loading harus diisi',
                    'destinationPort.*' => 'Port tujuan harus diisi',
                    'containerNumber.*' => 'Nomor kontainer harus diisi',
                    'containerSeal.*' => 'Nomor seal harus diisi',
                    'containerVessel.*' => 'Nama vessel harus diisi',
                    'liner.*' => 'Liner harus dipilih',
                    'bl.*' => 'Nomor Bill of Lading harus diisi',
                    'status.*'=>'Pilih status transaksi'
                ]
            );
        } else {
           $request->validate(
            [
                'shipper' => 'required',
                'shipperAddress' => 'required',
                'company' => 'required|gt:0',
                'companydetail' => 'required',                
                'countryId' => 'required|gt:0',
                'packer' => 'required',
                'loadingPort' => 'required',
                'orderType' => 'required',                
                'destinationPort' => 'required',                
                'containerType' => 'required|gt:0',                
                'containerParty' => 'required',
                'forwarder' => 'required|gt:0',

                'containerNumber' => 'required',
                'containerSeal' => 'required',
                'containerVessel' => 'required',
                'liner' => 'required|gt:0',
                'bl' => 'required',

                'transactionDate' => 'required|date|before_or_equal:today',
                'loadingDate' => 'required|date',
                'departureDate' => 'required|date|after_or_equal:loadingDate',
                'arrivalDate' => 'required|date|after:departureDate',

                'paymentTo' => 'required|gt:0',
                'bank' => 'required|gt:0',
                'account' => 'required',
                'swiftcode' => 'required',
                'valutaType' => 'required|gt:0',
                'paymentAmount' => 'required|gte:0',
                'advanceAmount' => 'required|gte:0',
                'paymentStatus' => 'required|gt:0',
                'status' => 'required|gt:0'
            ],
            [
                'rekening.gt'=> 'Pilih salah satu rekening',
                'liner.*'=> 'Pilih salah satu Liner',
                'bl.*'=> 'Nomor BL harus diisi',
                'company.gt'=> 'Pilih salah satu perusahaan',
                'containerType.gt'=> 'Pilih salah satu jenis pengiriman',
                'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
                'pinum.unique'=> 'Nomor pinum harus unik',
                'transactionNum.unique'=> 'Nomor transaksi harus unik',
                'countryId.gt'=>'Pilih salah satu negara',
                'forwarder.gt'=>'Pilih forwarder',
                'undernamePayment.*'=>'Pilih status jenis undername',
                'paymentStatus.*'=>'Pilih status pembayaran',
                'pinum.*'=> 'Nomor PI harus diisi',
                'transactionNum.*'=> 'Nomor transaksi harus diisi',
                'companydetail.*' => 'Detail perusahaan harus diisi',  
                'packer.*' => 'Nama perusahaan packer harus diisi',
                'loadingPort.*' => 'Port loading harus diisi',
                'destinationPort.*' => 'Port tujuan harus diisi',
                'containerNumber.*' => 'Nomor kontainer harus diisi',
                'containerSeal.*' => 'Nomor seal harus diisi',
                'containerVessel.*' => 'Nama vessel harus diisi',
                'liner.*' => 'Liner harus dipilih',
                'bl.*' => 'Nomor Bill of Lading harus diisi',
                'status.*'=>'Pilih status transaksi'
            ]
        );
       }

       $data = [
        'userId' => auth()->user()->id,
        'pebNum' =>  $request->pebNum,
        'pebDate' =>  $request->pebDate,
        'pebFile' =>  $request->pebFile,
        'jenis' => 1,
        'pebNum' =>  $request->pebNum,
        'pebDate' =>  $request->pebDate,
        'shipper' =>  $request->shipper,
        'shipperAddress' =>  $request->shipperAddress,
        'companyId' =>  $request->company,
        'companydetail' =>  $request->companydetail,
        'status' =>  $request->status,
        'packer' => $request->packer,
        'loadingport' =>  $request->loadingPort,
        'destinationport' =>  $request->destinationPort,
        'orderType' => $request->orderType,
        'countryId' => $request->countryId,
        'packer' =>  $request->packer,
        'containerType' =>  $request->containerType,
        'containerParty' =>  $request->containerParty,
        'forwarderid' => $request->forwarder,
        'containerNumber' =>  $request->containerNumber,
        'containerSeal' =>  $request->containerSeal,
        'containerVessel' =>  $request->containerVessel,

        'linerId' => $request->liner,
        'bl' => $request->bl,

        'transactionDate' => $request->transactionDate,
        'departureDate' =>  $request->departureDate,
        'loadingDate' =>  $request->loadingDate,
        'arrivalDate' => $request->arrivalDate,

        'paymentTo' => $request->paymentTo,
        'paymentBank' => $request->bank,
        'paymentAccount' => $request->account,
        'paymentSwiftcode' => $request->swiftcode,
        'paymentValuta' =>  $request->valutaType,
        'paymentAmount' =>  $request->paymentAmount,
        'paymentAdvance' =>  $request->advanceAmount,
        'paymentStatus' =>  $request->paymentStatus
    ];


    $action = Undername::where('id', $request->undernameId)->update($data);

    $file="";
    $filename="";
    if($request->hasFile('pebFile')){
        $file = $request->pebFile;
        $filename = "PEB U ".$request->transactionDate." ".$request->pebNum." ".$request->undernameId.".".$file->getClientOriginalExtension();

        $file->move(base_path("storage/app/docs/"), $filename);
    }
    DB::table('undernames')
    ->where('id', '=', $request->undernameId)
    ->update(['pebFile' => $filename]);

    $retVal = $this->updatingUndernameData($request->undernameId, $request->currentStatus, $request->status, $data);

    return redirect('undernameList')
    ->with('status',$retVal['message'])      
    ->with('alertStatus',$retVal['alertStatus']);  
}


private function updatingUndernameData($undernameId, $currentStatus, $status, $data){
    $alertStatus=0;
    switch ($currentStatus){
        case 1 :
        switch ($status){
                case 1: //dari penawaran ke penawaran
                $action = Undername::where('id', $undernameId)->update($data);
                $message = "Update berhasil; Status tidak berubah";
                $alertStatus=0;
                break;
                case 2: 
                //dari penawaran ke finished
                $invController = new InvoiceController();
                $undernameNum = $invController->createtransactionnum($undernameId, 1, 1);

                $data = [
                    'transactionNum' => $undernameNum
                ];
                $action = Undername::where('id', $undernameId)->update($data);
                $message = "Update berhasil. Status selesai";
                $alertStatus=1;
                break;
                case 3:     //dari penawaran ke canceled
                $message = "Update tidak dilakukan: Transaksi dibatalkan";
                $alertStatus=0;
                break;
                case 4: 
                //dari penawaran ke sailing
                $invController = new InvoiceController();
                $undernameNum = $invController->createtransactionnum($undernameId, 1, 1);
                $data = [
                    'transactionNum' => $undernameNum
                ];
                $action = Undername::where('id', $undernameId)->update($data);
                $message = "Update Transaksi pengiriman berhasil diperbaharui.";
                $alertStatus=2;
                break;
                default : 
                $message = "Tidak ada perubahan.";
                $alertStatus=0;
                break;
            }
            break;
            case 2 :   
            $message = "Transaksi yang sudah selesai tidak dapat dikembalikan."; 
            $alertStatus=1;
            break;
            case 3 :  
            $message = "Transaksi yang sudah dibatalkan tidak dapat dikembalikan.";  
            $alertStatus=1;
            break;
            case 4 :
            switch ($status){
                case 1:     //sailing ke penawaran
                $action = Undername::where('id', $undernameId)->update($data);
                $message = "Status tidak bisa diubah ke Penawaran.";
                $alertStatus=1;
                break;
                case 2:     //sailing ke finished
                $action = Undername::where('id', $undernameId)->update($data);
                $message = "Update Berhasil: Transaksi pengiriman berhasil selesai.";
                $alertStatus=0;
                break;
                case 3:     //sailing ke canceled
                $message = "Update tidak dilakukan: Transaksi dibatalkan.";
                $alertStatus=0;
                break;
                case 4:     //dari sailing ke sailing   
                $action = Undername::where('id', $undernameId)->update($data);                
                $message = "Update berhasil: Status Transaksi masih tetap Sailing.";
                $alertStatus=0;
                break;
            }
            break;
            default: 
            $message = "Tidak ada perubahan.";
            break;
        }

        $retVal= [
            'message' => $message,
            'alertStatus' => $alertStatus
        ];
        return $retVal;
    }

    /*
    public function createUndernameNum($undernameId){
        $bagian="INVU-ALS";
        $month = date('m');
        $year = date('Y');
        $isActive=1;

        $result = DB::table('document_numbers as dn')
        ->where('year', $year)
        ->where('bagian', $bagian)

        ->orWhere(function (Builder $query) {
            $query->where('undernameId','!=', null)
            ->where('transactionId','!=', null);
        })
        ->max('nomor');

        if ($result>0){
            $nomor=$result+1;
        }
        else{
            $nomor=1;
        }

        $data = [
            'nomor'=>$nomor,
            'undernameId'=>$undernameId,
            'bagian'=>$bagian,
            //'documentType'=>$documentType,
            'month'=>$month,
            'year'=>$year,
            'isActive'=>$isActive
        ];
        $tnum = $nomor.'/'.$bagian.'/'.$month.'/'.$year;
        DB::table('document_numbers')->insert($data);


        $affected = DB::table('undernames')
        ->where('id', $undernameId)
        ->update([
            'transactionNum' => $tnum
        ]);

        return $tnum;
    }
    */

    public function getAllUndernameDocuments(Request $request){
        $query = DB::table('documents as d')
        ->select(
            'ud.id as id', 
            'ud.transactionnum as invnum', 
            'ud.pinum as pinum', 
            'u.name as name', 
            'd.created_at as tanggal',
            'd.filepath as filepath',
            DB::raw('(CASE 
                WHEN dn.bagian ="PIU-ALI" THEN "PI"
                WHEN dn.bagian ="PIU-ALS" THEN "PI"
                WHEN dn.bagian ="INVU-ALI" then "Invoice" 
                WHEN dn.bagian ="INVU-ALS" then "Invoice" 
                END) AS jenis')
        )
        ->join('document_numbers as dn', 'dn.id', '=', 'd.document_numbers_id')
        ->join('undernames as ud', 'ud.id', '=', 'dn.undernameId')
        ->join('users as u', 'u.id', '=', 'd.userId')
        ->where('ud.id', '=', $request->undernameId)
        ->orderBy('d.created_at', 'desc')
        ->get();  

        return datatables()->of($query)
        ->addColumn('documentNo', function ($row) {
            $html="";
            if ($row->jenis == "PI")
                $html = $row->pinum;
            if ($row->jenis == "Invoice")
                $html = $row->invnum;
            return $html;
        })
        //return Storage::download('file.jpg');

        ->addColumn('action', function ($row) {
            $html = '    
            <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Download" onclick="getFileDownload('."'".$row->filepath."'".')">Download
            </button>
            ';

            return $html;
        })
        ->rawColumns(['action', 'tanggal'])
        ->addIndexColumn()->toJson();
    }
}
