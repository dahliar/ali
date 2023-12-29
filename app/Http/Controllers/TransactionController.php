<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Company;
use App\Models\Rekening;
use App\Models\Countries;
use App\Models\TransactionNote;
use App\Models\Forwarder;
use App\Models\Liner;
use App\Models\Bank;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use DB;
use Illuminate\Validation\Rule;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->transaction = new Transaction();
    }
    public function index()
    {
        $nations = Countries::where('isActive',1)->get();
        return view('transaction.transactionList', compact('nations'));
    }

    public function indexTesting()
    {
        $nations = Countries::where('isActive',1)->get();
        return view('transaction.transactionListTesting', compact('nations'));
    }

    public function getAllExportTransaction(Request $request){
        return $this->transaction->getAllExportTransactionData($request);
    }
    public function getAllTransactionDocuments(Request $request){
        return $this->transaction->getAllTransactionDocuments($request);
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function create()
    {
        $companies = Company::all();
        $rekenings = Rekening::all();
        $liners = Liner::all();
        $forwarders = Forwarder::where('isActive', 1)->orderBy('name', 'ASC')->get();
        $countryRegister = Countries::where('isActive',1)->get();

        //$notes = TransactionNote::where('transactionId',$transaction->id)->get();

        return view('transaction.transactionAdd', compact('countryRegister', 'companies', 'rekenings', 'forwarders', 'liners'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {   
        //21 validasi inputan wajib
        //2 default value dari inputan : swiftcode, valuta
        //3 inputan default: userId, creationDate, status

        $request->validate(
            [
                //'transactionNum' => 'required|unique:transactions',
                'shipper' => 'required',
                'shipperAddress' => 'required',
                'rekening' => 'required|gt:0',
                'swiftcode' => 'required',
                'valuta' => 'required',
                'company' => 'required|gt:0',
                'companydetail' => 'required',
                'countryId' => 'required|gt:0',
                'packer' => 'required',
                'loadingPort' => 'required',
                'destinationPort' => 'required',
                'containerType' => 'required|gt:0',                
                'containerParty' => 'required',
                'containerNumber' => 'required',
                'containerSeal' => 'required',
                'containerVessel' => 'required',
                'liner' => 'required|gt:0',
                'bl' => 'required',
                'valutaType' => 'required|gt:0',
                'payment' => 'required|gt:0',
                'advance' => 'required|gte:0',
                'forwarder' => 'required|gt:0',
                'transactionDate' => 'required|date|before_or_equal:today',
                'loadingDate' => 'required|date',
                'departureDate' => 'required|date|after_or_equal:loadingDate',
                'arrivalDate' => 'required|date|after:departureDate',
                'shippedDatePlan' => 'required',
                'paymentPlan' => 'required',
            ],
            [
                'rekening.gt'=> 'Pilih salah satu rekening',
                'company.gt'=> 'Pilih salah satu perusahaan',
                'containerType.gt'=> 'Pilih salah satu jenis pengiriman',
                'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
                'transactionNum.unique'=> 'Nomor transaksi harus unik',
                'countryId.gt'=>'Pilih salah satu negara',
                'forwarder.gt'=>'Pilih forwarder',
                'undername.gt'=>'Pilih status jenis undername'
            ]
        );




        //21 validasi inputan wajib
        //2 default value dari inputan : swiftcode, valuta
        //3 inputan default: userId, creationDate, status
        $data = [
            'userId' => auth()->user()->id,
            //'transactionNum' => $request->transactionNum,
            'companyId' =>  $request->company,
            'companydetail' =>  $request->companydetail,
            'shipper' =>  $request->shipper,
            'shipperAddress' =>  $request->shipperAddress,
            'rekeningId' => $request->rekening,
            'swiftcode' => $request->swiftcode,
            'countryId' => $request->countryId,
            'valuta' => $request->valuta,
            'packer' =>  $request->packer,
            'loadingport' =>  $request->loadingPort,
            'destinationport' =>  $request->destinationPort,
            'containerParty' =>  $request->containerParty,
            'containerType' =>  $request->containerType,
            'containerNumber' =>  $request->containerNumber,
            'containerSeal' =>  $request->containerSeal,
            'orderType' => $request->orderType,
            'containerVessel' =>  $request->containerVessel,
            'linerId' =>  $request->liner,
            'bl' =>  $request->bl,
            'payment' =>  $request->payment,
            'advance' =>  $request->advance,
            'forwarderid' => $request->forwarder,
            'isundername' => 1,
            'valutaType' =>  $request->valutaType,
            'transactionDate' => $request->transactionDate,
            'departureDate' =>  $request->departureDate,
            'loadingDate' =>  $request->loadingDate,
            'arrivalDate' => $request->arrivalDate,
            'shippedDatePlan' => $request->shippedDatePlan,
            'paymentPlan' => $request->paymentPlan,
            'status' =>  1,
            'jenis' => 1
        ];
        
        $lastTransactionIdStored = DB::table('transactions')->insertGetId($data);
        //create PI Number
        $pinum = $this->createpinum($lastTransactionIdStored);

        if (!empty($request->pinotes)){
            $a=0;

            foreach ($request->pinotes as $notes){            
                $notesData[$a] = [
                    'transactionId' =>  $lastTransactionIdStored,
                    'note' => $notes
                ];
                $a=$a+1;
            }

            DB::table('transaction_notes')->insert($notesData);
        }
        return redirect('transactionList')
        ->with('status','Transaksi pengiriman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //return view('Transaction.viewTransaction');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */

    public function edit(Transaction $transaction)
    {
        $companies = Company::all();
        
        $liners = Liner::all();
        $rekenings = Rekening::all();
        $countryRegister = Countries::where('isActive',1)->get();
        $forwarders = Forwarder::where('isActive', 1)->orderBy('name', 'ASC')->get();
        
        $pinotes = TransactionNote::where('transactionId',$transaction->id)->get();

        return view('transaction.transactionEdit', compact('countryRegister', 'pinotes', 'forwarders', 'companies', 'rekenings', 'transaction', 'liners'));
    }

    public function transactionDocument(Transaction $transaction)
    {
        return view('transaction.transactionDocuments', compact('transaction'));
    }
    public function localTransactionDocument(Transaction $transaction)
    {
        return view('transaction.localTransactionDocuments', compact('transaction'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //21 validasi inputan wajib
        //2 default value dari inputan : swiftcode, valuta
        //2 inputan default: userId, status
        //creationDate tidak diupdate
        $data=[];

        if (($request->status==2) and ($request->currentStatus==4)){
            $request->validate([
                'pebFile' => ['required', 'mimes:jpg,jpeg,png,pdf','max:2048'],
                'pebNum' => 'required',
                'pebDate' => 'required|date|after_or_equal:transactionDate',
                'shipper' => 'required',
                'shipperAddress' => 'required',
                'rekening' => 'required|gt:0',
                'swiftcode' => 'required',
                'valuta' => 'required',
                'company' => 'required|gt:0',
                'companydetail' => 'required',
                'packer' => 'required',
                'loadingPort' => 'required',
                'destinationPort' => 'required',
                'containerType' => 'required|gt:0',      
                'countryId' => 'required|gt:0',            
                'containerParty' => 'required',
                'containerNumber' => 'required',
                'containerSeal' => 'required',
                'containerVessel' => 'required',
                'valutaType' => 'required|gt:0',
                'payment' => 'required|gt:0',
                'advance' => 'required|gte:0',
                'forwarder' => 'required|gt:0',
                'liner' => 'required|gt:0',
                'bl' => 'required',
                'transactionDate' => 'required|date|before_or_equal:today',
                'loadingDate' => 'required|date',
                'departureDate' => 'required|date|after_or_equal:loadingDate',
                'arrivalDate' => 'required|date|after:departureDate',
                'shippedDatePlan' => 'required',
                'paymentPlan' => 'required',
                'status' => 'required|gt:0'
            ],[
                'pebFile.required' => 'File PEB harus ada',
                'pebFile.max' => 'Ukuran file maksimal adalah 2 MB',
                'pebNum.*' => 'Nomor PEB wajib diisi',
                'pebDate.*' => 'Tanggal PEB setelah atau sama dengan tanggal Transaksi',
                'rekening.gt'=> 'Pilih salah satu rekening',
                'company.gt'=> 'Pilih salah satu perusahaan',
                'containerType.gt'=> 'Pilih salah satu jenis pengiriman',
                'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
                'status.gt'=> 'Pilih salah satu jenis status',
            ]);
        } else {
            $request->validate([
                'shipper' => 'required',
                'shipperAddress' => 'required',
                'rekening' => 'required|gt:0',
                'swiftcode' => 'required',
                'valuta' => 'required',
                'company' => 'required|gt:0',
                'companydetail' => 'required',
                'packer' => 'required',
                'loadingPort' => 'required',
                'destinationPort' => 'required',
                'containerType' => 'required|gt:0',      
                'countryId' => 'required|gt:0',            
                'containerParty' => 'required',
                'containerNumber' => 'required',
                'containerSeal' => 'required',
                'containerVessel' => 'required',
                'valutaType' => 'required|gt:0',
                'payment' => 'required|gt:0',
                'advance' => 'required|gte:0',
                'forwarder' => 'required|gt:0',
                'liner' => 'required|gt:0',
                'bl' => 'required',
                'transactionDate' => 'required|date|before_or_equal:today',
                'loadingDate' => 'required|date',
                'departureDate' => 'required|date|after_or_equal:loadingDate',
                'arrivalDate' => 'required|date|after:departureDate',
                'shippedDatePlan' => 'required',
                'paymentPlan' => 'required',
                'status' => 'required|gt:0'
            ],[
                'pebDate.*' => 'Tanggal PEB sama atau lebih dari tanggal Transaksi',
                'rekening.gt'=> 'Pilih salah satu rekening',
                'company.gt'=> 'Pilih salah satu perusahaan',
                'containerType.gt'=> 'Pilih salah satu jenis pengiriman',
                'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
                'status.gt'=> 'Pilih salah satu jenis status',
            ]);
        }

        $data = [
            'userId' => auth()->user()->id,
            'pebNum' =>  $request->pebNum,
            'pebDate' =>  $request->pebDate,
            'pebFile' =>  $request->pebFile,
            'companyId' =>  $request->company,
            'companydetail' =>  $request->companydetail,
            'shipper' =>  $request->shipper,
            'shipperAddress' =>  $request->shipperAddress,
            'rekeningId' => $request->rekening,
            'countryId' => $request->countryId,
            'swiftcode' => $request->swiftcode,
            'valuta' => $request->valuta,
            'packer' =>  $request->packer,
            'loadingport' =>  $request->loadingPort,
            'destinationport' =>  $request->destinationPort,
            'containerParty' =>  $request->containerParty,
            'containerType' =>  $request->containerType,
            'containerNumber' =>  $request->containerNumber,
            'containerSeal' =>  $request->containerSeal,
            'containerVessel' =>  $request->containerVessel,
            'payment' =>  $request->payment,
            'advance' =>  $request->advance,
            'forwarderid' => $request->forwarder,
            'isundername' => 1,
            'valutaType' =>  $request->valutaType,
            'transactionDate' => $request->transactionDate,
            'departureDate' =>  $request->departureDate,
            'loadingDate' =>  $request->loadingDate,
            'arrivalDate' => $request->arrivalDate,
            'shippedDatePlan' => $request->shippedDatePlan,
            'paymentPlan' => $request->paymentPlan,
            'linerId' => $request->liner,
            'bl' => $request->bl
        ];
        $retVal = $this->updatingTransactionData(1, $request->transactionId, $request->transactionNum, $request->currentStatus, $request->status, $data, $request->pinotes);

        $file="";
        $filename="";
        if($request->hasFile('pebFile')){
            $file = $request->pebFile;
            $filename = "PEB T ".$request->transactionDate." ".$request->pebNum." ".$request->transactionId.".".$file->getClientOriginalExtension();

            $file->move(base_path("storage/app/docs/"), $filename);
        }

        DB::table('transactions')
        ->where('id', '=', $request->transactionId)
        ->update(['pebFile' => $filename]);

        return redirect('transactionList')
        ->with('status',$retVal['message'])      
        ->with('alertStatus',$retVal['alertStatus'])      
        ->with('listBarang',$retVal['listBarang']);       
    }

    private function updatingTransactionData($jenisTransaction, $transactionId, $transactionNum, $currentStatus, $status, $data, $pinotes){
        $listBarang = "";
        $alertStatus=0;
        switch ($currentStatus){
            case 1 :
            switch ($status){
                case 1: 
                //dari penawaran ke penawaran
                $action = Transaction::where('id', $transactionId)->update($data);
                if($jenisTransaction == 1){
                    $this->updatePinotes($pinotes);
                }
                $message = "Update Berhasil; Status tidak berubah";
                $alertStatus=0;
                break;
                case 2: 
                //dari penawaran ke finished
                $action = Transaction::where('id', $transactionId)->update($data);
                $message = "Data lain diupdate. Status tidak berubah, ubah dulu menjadi Sailing atau dalam perjalanan";
                $alertStatus=1;
                break;
                case 3: 
                //dari penawaran ke canceled
                $affected = DB::table('transactions')->where('id', $transactionId)->update(['status' => 3]);
                $message = "Update berhasil: Transaksi dibatalkan";
                $alertStatus=0;
                break;
                case 4: 
                //dari penawaran ke sailing
                $listBarang=$this->isTheItemsAmountEnough($transactionId);
                if (count($listBarang)>0) {
                    $action = Transaction::where('id', $transactionId)->update($data);
                    $message = "Data lain diupdate. Status tidak berubah karena ada barang dengan jumlah stock yang tidak mencukupi";
                    $alertStatus=2;
                } else {

                    $transactionNum = $this->createtransactionnum($transactionId);

                    $jumlahDetilNol = DB::table('transactions as t')
                    ->join('detail_transactions as dt', 'dt.transactionId', '=', 't.id')
                    ->where('t.id', $transactionId)
                    ->where('dt.price', '=', 0)
                    ->count('dt.id');
                    if ($jumlahDetilNol<=0){
                        $totalPayment = $this->getExportTotalPayment($transactionId);

                        $dataTambahan = [
                            'payment' => $totalPayment,
                            'transactionNum' => $transactionNum,
                            'status' =>  4
                        ];


                        $data = array_merge($data, $dataTambahan);
                        $action = Transaction::where('id', $transactionId)->update($data);
                        $this->stockLoaded($transactionId);

                        $message = "Update Transaksi pengiriman berhasil diperbaharui.";
                        $alertStatus=0;                        
                    } else{
                        $message = "Terdapat detil barang yang harganya masih 0";
                        $alertStatus=3;
                    }
                }
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
                case 1: 
                $message = "Status tidak bisa diubah ke Penawaran.";
                $alertStatus=1;
                break;
                case 2:     //sailing ke finished
                $dataTambahan = [
                    'transactionNum' => $transactionNum,
                    'status' =>  2
                ];
                $data = array_merge($data, $dataTambahan);                
                $action = Transaction::where('id', $transactionId)->update($data);
                $message = "Update Berhasil: Transaksi penjualan selesai.";
                $alertStatus=0;
                break;
                case 3: //sailing ke canceled
                $affected = DB::table('transactions')->where('id', $transactionId)->update(['status' => 3]);
                $this->transactionCanceled($transactionId);
                $message = "Update berhasil: Transaksi dibatalkan, data stok dikembalikan.";
                $alertStatus=0;
                break;
                case 4: 
                //dari sailing ke sailing                
                $action = Transaction::where('id', $transactionId)->update($data);
                $message = "Update Berhasil: data pengiriman berhasil diperbaharui.";
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
            'listBarang' => $listBarang,
            'alertStatus' => $alertStatus
        ];
        return $retVal;
    }
    /*
    Function untuk check apakah daftar barang dalam detail_transaction cukup atau tidak untuk dilakukan transaksi.
    Input   : transactionId
    Output  : daftar barang yang tidak cukup
    Digunakan ketika ada perubahan dari status transaksi 
    Penawaran   ->  Sailing
    */        
    public function isTheItemsAmountEnough($transactionId){
        $result = DB::table('detail_transactions as dt')
        ->select(
            'dt.itemId as itemId', 
            DB::raw('concat(
                sp.name," ",
                sh.name," ",
                g.name," ", 
                s.name," ", 
                f.name," ",
                i.weightbase,"Kg/", 
                p.shortname
            ) as itemName'),
            DB::raw('sum(dt.amount) as amount'),
            'i.amount as currentAmount'
        )
        ->join('items as i', 'i.id', '=', 'dt.itemId')
        ->join('packings as p', 'i.packingId', '=', 'p.id')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('freezings as f', 'i.freezingId', '=', 'f.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->join('shapes as sh', 'i.shapeId', '=', 'sh.id')
        ->where('transactionId', $transactionId)
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderBy('s.name', 'asc')
        ->groupBy('i.id')
        ->get();

        $listBarang=array();
        foreach ($result as $dtitem){
            if ($dtitem->amount>$dtitem->currentAmount){
                array_push($listBarang, $dtitem->itemName);
            }
        }
        return $listBarang;
    }

    public function stockLoaded($transactionId){
        $result = DB::table('detail_transactions as dt')
        ->select(
            'dt.itemId as itemId', 
            'dt.amount as amount'
        )
        ->where('transactionId', $transactionId)
        ->get();

        foreach ($result as $itemDetail){
            DB::table('items')
            ->where('id', $itemDetail->itemId)
            ->decrement('amount', $itemDetail->amount);

            $this->stockChangeLog(2, "Transaction ID ".$transactionId." dari Penawaran ke Sailing", $itemDetail->itemId, $itemDetail->amount);
        }
    }

    public function transactionCanceled($transactionId){
        /*
        1. get all data from detailTransaction where transactionId=$transactionId
        2. Update Item foreach item result from poin1, increment the amount sebesar amount dari poin1
        3. update detailTransaction->status menjadi 0 semua (Tidak aktif)
        */

        $result1 = DB::table('detail_transactions as dt')
        ->select(
            'dt.id as id', 
            'dt.itemId as itemId', 
            'dt.transactionId as tranId', 
            'dt.amount as amount', 
        )
        ->where('transactionId', $transactionId)
        ->get();

        foreach ($result1 as $itemDetail){
            DB::table('items')
            ->where('id', $itemDetail->itemId)
            ->increment('amount', $itemDetail->amount);

            $this->stockChangeLog(2, "Transaction ID ".$transactionId." dari Sailing/Finished ke batal", $itemDetail->itemId, $itemDetail->amount);
        }
    }

    public function stockChangeLog($jenis, $info, $itemId, $amount){
        $data = [
            'userId'    => auth()->user()->name,
            'jenis'     => $jenis,
            'informasiTransaksi' => $info,
            'itemId'    =>  $itemId,
            'amount'    =>  $amount                
        ];
        DB::table('stock_histories')->insert($data);
    }
    public function updatePinotes($pinotes){
        //the plan is, delete all notes existed in the table, and insert the new edited list
        if (!empty($pinotes)){
            $a=0;
            foreach ($pinotes as $notes){            
                $notesData[$a] = [
                    'transactionId' =>  $request->transactionId,
                    'note' => $notes
                ];
                $a=$a+1;
            }
            DB::table('transaction_notes')->where('transactionId', $request->transactionId)->delete();
            DB::table('transaction_notes')->insert($notesData);
        }
    }
    

    /*
     * LOCAL Transaction
     * 
     * 
     */

    public function localIndex()
    {
        return view('transaction.localTransactionList');
    }
    public function getAllLocalTransaction(Request $request){
        return $this->transaction->getAllLocalTransactionData($request);
    }
    public function localCreate()
    {
        $companies = Company::all();
        $rekenings = Rekening::all();

        return view('transaction.localTransactionAdd', compact('companies', 'rekenings'));
    }

    public function localStore(Request $request)
    {   
        $request->validate(
            [
                'company' => 'required|gt:0',
                'companydetail' => 'required',
                'loadingPort' => 'required',
                'destinationPort' => 'required',
                'containerParty' => 'required',
                'transactionDate' => 'required|date|before_or_equal:today',
                'loadingDate' => 'required|date',
                'rekening' => 'required|gt:0',
                'valuta' => 'required',
                'valutaType' => 'required|gt:0',
                'payment' => 'required|gt:0',
                'advance' => 'required|gte:0',                
            ],
            [
                'rekening.gt'=> 'Pilih salah satu rekening',
                'company.gt'=> 'Pilih salah satu perusahaan',
                'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
            ]
        );

        //21 validasi inputan wajib
        //2 default value dari inputan : swiftcode, valuta
        //3 inputan default: userId, creationDate, status
        $data = [
            'countryId' => 10,
            'forwarderid' => 9,
            'status' =>  1,
            'orderType' => 1,
            'isundername' => 1,
            'userId' => auth()->user()->id,
            'rekeningId' => $request->rekening,
            'valuta' => $request->valuta,
            'companyId' =>  $request->company,
            'companydetail' =>  $request->companydetail,
            'loadingport' =>  $request->loadingPort,
            'destinationport' =>  $request->destinationPort,
            'containerParty' =>  $request->containerParty,
            'transactionDate' => $request->transactionDate,
            'loadingDate' =>  $request->loadingDate,
            'valutaType' =>  $request->valutaType,
            'payment' =>  $request->payment,
            'advance' =>  $request->advance,
            'jenis' => 2
        ];
        
        $lastTransactionIdStored = DB::table('transactions')->insertGetId($data);
        return redirect('localTransactionList')
        ->with('status','Transaksi penjualan berhasil ditambahkan.');
    }

    public function localEdit(Transaction $transaction)
    {
        //dd($transaction);
        $companyName = Company::where('id', '=', $transaction->companyId)->first()->name;
        $rekenings = Rekening::all();
        return view('transaction.localTransactionEdit', compact('companyName', 'rekenings', 'transaction'));
    }


    public function localUpdate(Request $request, Transaction $transaction)
    {
        if (($request->currentStatus!=2) && ($request->currentStatus!=3) ) {
            $request->validate([
                'companydetail' => 'required',
                'loadingPort' => 'required',
                'destinationPort' => 'required',
                'containerParty' => 'required',
                'transactionDate' => 'required|date|before_or_equal:today',
                'loadingDate' => 'required|date',
                'rekening' => 'required|gt:0',
                'valuta' => 'required',
                'valutaType' => 'required|gt:0',
                'payment' => 'required|gt:0',
                'advance' => 'required|gte:0',
                'status' => 'required|gt:0'
            ],[
                'rekening.gt'=> 'Pilih salah satu rekening',
                'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
                'status.gt'=> 'Pilih salah satu jenis status',
            ]);
        } else{
            $request->validate([
                'status' => 'required|gt:0'
            ],[
                'status.gt'=> 'Pilih salah satu jenis status',
            ]);
        }

        $data = [
            'userId' => auth()->user()->id,
            'companydetail' =>  $request->companydetail,
            'loadingport' =>  $request->loadingPort,
            'destinationport' =>  $request->destinationPort,
            'containerParty' =>  $request->containerParty,
            'transactionDate' => $request->transactionDate,
            'loadingDate' =>  $request->loadingDate,
            'rekeningId' => $request->rekening,
            'valuta' => $request->valuta,
            'valutaType' =>  $request->valutaType,
            'payment' =>  $request->payment,
            'advance' =>  $request->advance
        ];
        $retVal = $this->updatingTransactionData(2, $request->transactionId, $request->transactionNum, $request->currentStatus, $request->status, $data, "1");
        return redirect('localTransactionList')
        ->with('status',$retVal['message'])      
        ->with('alertStatus',$retVal['alertStatus'])      
        ->with('listBarang',$retVal['listBarang']);       

    }

    public function createtransactionnum($transactionId){
        $bagian="INV-ALS";
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
        $bagian="PI-ALS";
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


    public function getExportTotalPayment($transactionId){
        $totalPayment = DB::table('transactions as t')
        ->where('t.id', $transactionId)
        ->join('detail_transactions as dt', 'dt.transactionId', '=', 't.id')
        ->join('items as i', 'i.id', '=', 'dt.itemId')
        ->select(            
            DB::raw('(
                sum(dt.amount * dt.price * i.weightbase)
            ) as total')
        )->first()->total;
        return $totalPayment;
    }

}
