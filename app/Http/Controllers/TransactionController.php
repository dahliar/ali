<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Company;
use App\Models\Rekening;
use App\Models\Countries;
use App\Models\TransactionNote;
use App\Models\Forwarder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use DB;


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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function create()
    {
        $companies = Company::all();
        $rekenings = Rekening::all();
        $forwarders = Forwarder::where('isActive', 1)->orderBy('name', 'ASC')->get();
        $countryRegister = Countries::where('isActive',1)->get();

        //$notes = TransactionNote::where('transactionId',$transaction->id)->get();

        return view('transaction.transactionAdd', compact('countryRegister', 'companies', 'rekenings', 'forwarders'));
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
                'valutaType' => 'required|gt:0',
                'payment' => 'required|gt:0',
                'advance' => 'required|gte:0',
                'forwarder' => 'required|gt:0',
                'undername' => 'required|gt:0',
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
            'containerType' =>  $request->containerParty,
            'containerNumber' =>  $request->containerNumber,
            'containerSeal' =>  $request->containerSeal,
            'orderType' => $request->orderType,
            'containerVessel' =>  $request->containerVessel,
            'payment' =>  $request->payment,
            'advance' =>  $request->advance,
            'forwarderid' => $request->forwarder,
            'isundername' => $request->undername,
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
        $this->inv = new InvoiceController();
        $pinum = $this->inv->createpinum($lastTransactionIdStored);


        //ketika transaksi adalah undername
        if ($request->undername==2){
            $tnum = $this->transaction->whenUndernameIsTrue($lastTransactionIdStored);
        }

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
        $rekenings = Rekening::all();
        $countryRegister = Countries::where('isActive',1)->get();
        $forwarders = Forwarder::where('isActive', 1)->orderBy('name', 'ASC')->get();
        
        $pinotes = TransactionNote::where('transactionId',$transaction->id)->get();

        return view('transaction.transactionEdit', compact('countryRegister', 'pinotes', 'forwarders', 'companies', 'rekenings', 'transaction'));
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
            'advance' => 'required|gt:0',
            'forwarder' => 'required|gt:0',
            'undername' => 'required|gt:0',

            'transactionDate' => 'required|date|before_or_equal:today',
            'loadingDate' => 'required|date',
            'departureDate' => 'required|date|after_or_equal:loadingDate',
            'arrivalDate' => 'required|date|after:departureDate',
            'shippedDatePlan' => 'required',
            'paymentPlan' => 'required',
            'status' => 'required|gt:0'
        ],[
            'rekening.gt'=> 'Pilih salah satu rekening',
            'company.gt'=> 'Pilih salah satu perusahaan',
            'containerType.gt'=> 'Pilih salah satu jenis pengiriman',
            'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
            'status.gt'=> 'Pilih salah satu jenis status',
        ]);

        if (($request->currentStatus==1)){
            if ($request->status == 1){
                //dari penawaran ke penawaran
                $data = [
                    'userId' => auth()->user()->id,
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
                    'containerType' =>  $request->containerParty,
                    'containerNumber' =>  $request->containerNumber,
                    'containerSeal' =>  $request->containerSeal,
                    'containerVessel' =>  $request->containerVessel,
                    'payment' =>  $request->payment,
                    'advance' =>  $request->advance,
                    'forwarderid' => $request->forwarder,
                    'isundername' => $request->undername,
                    'valutaType' =>  $request->valutaType,
                    'transactionDate' => $request->transactionDate,
                    'departureDate' =>  $request->departureDate,
                    'loadingDate' =>  $request->loadingDate,
                    'arrivalDate' => $request->arrivalDate,
                    'shippedDatePlan' => $request->shippedDatePlan,
                    'paymentPlan' => $request->paymentPlan
                ];
                $action = Transaction::where('id', $request->transactionId)
                ->update($data);
                $this->updatePinotes($request->pinotes);
                return redirect('transactionList')
                ->with('status','Update Berhasil: data pengiriman ke berhasil diperbaharui.');
            } else if ($request->status == 3){
                //dari penawaran ke canceled
                $affected = DB::table('transactions')
                ->where('id', $request->transactionId)
                ->update(['status' => 3]);
                return redirect('transactionList')
                ->with('status', 'Update berhasil: Transaksi dibatalkan');
            } else {
                //dari penawaran ke sailing atau finished
                $listBarang=$this->isTheItemsAmountEnough($request->transactionId);
                if (count($listBarang)>0) {
                    $data = [
                        'userId' => auth()->user()->id,
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
                        'containerType' =>  $request->containerParty,
                        'containerNumber' =>  $request->containerNumber,
                        'containerSeal' =>  $request->containerSeal,
                        'containerVessel' =>  $request->containerVessel,
                        'payment' =>  $request->payment,
                        'advance' =>  $request->advance,
                        'forwarderid' => $request->forwarder,
                        'isundername' => $request->undername,
                        'valutaType' =>  $request->valutaType,
                        'transactionDate' => $request->transactionDate,
                        'departureDate' =>  $request->departureDate,
                        'loadingDate' =>  $request->loadingDate,
                        'arrivalDate' => $request->arrivalDate,
                        'shippedDatePlan' => $request->shippedDatePlan,
                        'paymentPlan' => $request->paymentPlan
                    ];
                    $action = Transaction::where('id', $request->transactionId)
                    ->update($data);
                    return redirect('transactionList')
                    ->with('status', 'Update Status Transaksi gagal, jumlah stock tidak mencukupi')
                    ->with('listBarang', $listBarang);
                } else {
                    $this->inv = new InvoiceController();
                    $tnum = $this->inv->createtransactionnum($request->transactionId);
                    $data = [
                        'userId' => auth()->user()->id,
                        'transactionNum' => $tnum,
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
                        'containerType' =>  $request->containerParty,
                        'containerNumber' =>  $request->containerNumber,
                        'containerSeal' =>  $request->containerSeal,
                        'containerVessel' =>  $request->containerVessel,
                        'payment' =>  $request->payment,
                        'advance' =>  $request->advance,
                        'forwarderid' => $request->forwarder,
                        'isundername' => $request->undername,
                        'valutaType' =>  $request->valutaType,
                        'transactionDate' => $request->transactionDate,
                        'departureDate' =>  $request->departureDate,
                        'loadingDate' =>  $request->loadingDate,
                        'arrivalDate' => $request->arrivalDate,
                        'shippedDatePlan' => $request->shippedDatePlan,
                        'paymentPlan' => $request->paymentPlan,
                        'status' =>  $request->status
                    ];
                    $action = Transaction::where('id', $request->transactionId)
                    ->update($data);
                    $this->transactionLoadedOrFinished($request->transactionId);
                    return redirect('transactionList')
                    ->with('status','Update Transaksi pengiriman berhasil diperbaharui.');
                }
            }
        } else if (($request->currentStatus==4)){
            if ($request->status == 4){
                //dari sailing ke sailing
                $data = [
                    'userId' => auth()->user()->id,
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
                    'containerType' =>  $request->containerParty,
                    'containerNumber' =>  $request->containerNumber,
                    'containerSeal' =>  $request->containerSeal,
                    'containerVessel' =>  $request->containerVessel,
                    'payment' =>  $request->payment,
                    'advance' =>  $request->advance,
                    'forwarderid' => $request->forwarder,
                    'isundername' => $request->undername,
                    'valutaType' =>  $request->valutaType,
                    'transactionDate' => $request->transactionDate,
                    'departureDate' =>  $request->departureDate,
                    'loadingDate' =>  $request->loadingDate,
                    'arrivalDate' => $request->arrivalDate,
                    'shippedDatePlan' => $request->shippedDatePlan,
                    'paymentPlan' => $request->paymentPlan
                ];
                $action = Transaction::where('id', $request->transactionId)
                ->update($data);
                return redirect('transactionList')
                ->with('status','Update Berhasil: data pengiriman berhasil diperbaharui.');
            } else if ($request->status == 3){
                //sailing ke canceled
                $affected = DB::table('transactions')
                ->where('id', $request->transactionId)
                ->update(['status' => 3]);
                $this->transactionCanceled($request->transactionId);
                return redirect('transactionList')
                ->with('status', 'Update berhasil: data stok dikembalikan, transaksi dibatalkan');
            } else if ($request->status == 2) {
                //sailing ke finished
                $data = [
                    'userId' => auth()->user()->id,
                    'transactionNum' => $request->transactionNum,
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
                    'containerType' =>  $request->containerParty,
                    'containerNumber' =>  $request->containerNumber,
                    'containerSeal' =>  $request->containerSeal,
                    'containerVessel' =>  $request->containerVessel,
                    'payment' =>  $request->payment,
                    'advance' =>  $request->advance,
                    'forwarderid' => $request->forwarder,
                    'isundername' => $request->undername,
                    'valutaType' =>  $request->valutaType,
                    'transactionDate' => $request->transactionDate,
                    'departureDate' =>  $request->departureDate,
                    'loadingDate' =>  $request->loadingDate,
                    'arrivalDate' => $request->arrivalDate,
                    'shippedDatePlan' => $request->shippedDatePlan,
                    'paymentPlan' => $request->paymentPlan,
                    'status' =>  $request->status
                ];
                $action = Transaction::where('id', $request->transactionId)
                ->update($data);
                return redirect('transactionList')
                ->with('status','Update Berhasil: Transaksi pengiriman berhasil selesai.');
            }
        }
    }

    /*
    Function untuk check apakah daftar barang dalam detail_transaction cukup atau tidak untuk dilakukan transaksi.
    Input   : transactionId
    Output  : daftar barang yang tidak cukup
    Digunakan ketika ada perubahan dari status transaksi 
    Penawaran   ->  Sailing
    Penawaran   ->  Finished
    */        
    public function isTheItemsAmountEnough($transactionId){
        $result = DB::table('detail_transactions as dt')
        ->select(
            'dt.itemId as itemId', 
            DB::raw('concat(sp.name," ",g.name," ", s.name) as itemName'),
            DB::raw('sum(dt.amount) as amount'),
            'i.amount as currentAmount'
        )
        ->join('items as i', 'i.id', '=', 'dt.itemId')
        ->join('grades as g', 'i.gradeid', '=', 'g.id')
        ->join('sizes as s', 'i.sizeid', '=', 's.id')
        ->join('species as sp', 's.speciesId', '=', 'sp.id')
        ->where('transactionId', $transactionId)
        ->orderBy('sp.name', 'desc')
        ->orderBy('g.name', 'asc')
        ->orderByRaw('s.name+0', 'asc')
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

    public function transactionLoadedOrFinished($transactionId){
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

            $this->stockChangeLog(2, "Transaction ID ".$transactionId." dari Penawaran ke Sailing/Finished", $itemDetail->itemId, $itemDetail->amount);
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

            $this->stockChangeLog(1, "Transaction ID ".$transactionId." dari Sailing/Finished ke batal", $itemDetail->itemId, $itemDetail->amount);
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
        $companyName = Company::where('id', '=', $transaction->companyId)->first()->name;
        $rekenings = Rekening::all();
        return view('transaction.localTransactionEdit', compact('companyName', 'rekenings', 'transaction'));
    }

    public function localUpdate(Request $request, Transaction $transaction)
    {
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
            'advance' => 'required|gt:0',
            'status' => 'required|gt:0'
        ],[
            'rekening.gt'=> 'Pilih salah satu rekening',
            'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
            'status.gt'=> 'Pilih salah satu jenis status',
        ]);


        if (($request->currentStatus==1)){
            if ($request->status == 1){
                //dari penawaran ke penawaran
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
                $action = Transaction::where('id', $request->transactionId)
                ->update($data);
                return redirect('localTransactionList')
                ->with('status','Update Berhasil: data penjualan diperbarui.');
            } else if ($request->status == 3){
                //dari penawaran ke canceled
                $affected = DB::table('transactions')
                ->where('id', $request->transactionId)
                ->update(['status' => 3]);
                return redirect('localTransactionList')
                ->with('status', 'Update berhasil: Transaksi dibatalkan');
            } else {
                //dari penawaran ke sailing atau finished
                $listBarang=$this->isTheItemsAmountEnough($request->transactionId);
                if (count($listBarang)>0) {
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
                    $action = Transaction::where('id', $request->transactionId)
                    ->update($data);
                    return redirect('localTransactionList')
                    ->with('status', 'Update Status Transaksi gagal, jumlah stock tidak mencukupi')
                    ->with('listBarang', $listBarang);
                } else {
                    $this->inv = new InvoiceController();
                    $tnum = $this->inv->createtransactionnum($request->transactionId);
                    $data = [
                        'userId' => auth()->user()->id,
                        'transactionNum' => $tnum,
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
                        'advance' =>  $request->advance,
                        'status' =>  $request->status
                    ];
                    $action = Transaction::where('id', $request->transactionId)
                    ->update($data);
                    $this->transactionLoadedOrFinished($request->transactionId);
                    return redirect('localTransactionList')
                    ->with('status','Update Transaksi pengiriman berhasil diperbaharui.');
                }
            }
        } else if (($request->currentStatus==4)){
            if ($request->status == 4){
                //dari sailing ke sailing
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
                $action = Transaction::where('id', $request->transactionId)
                ->update($data);
                return redirect('localTransactionList')
                ->with('status','Update Berhasil: data pengiriman berhasil diperbaharui.');
            } else if ($request->status == 3){
                //sailing ke canceled
                $affected = DB::table('transactions')
                ->where('id', $request->transactionId)
                ->update(['status' => 3]);
                $this->transactionCanceled($request->transactionId);
                return redirect('localTransactionList')
                ->with('status', 'Update berhasil: Transaksi dibatalkan, data stok dikembalikan');
            } else if ($request->status == 2) {
                //sailing ke finished
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
                    'advance' =>  $request->advance,
                    'status' =>  $request->status
                ];
                $action = Transaction::where('id', $request->transactionId)
                ->update($data);
                return redirect('localTransactionList')
                ->with('status','Update Berhasil: Transaksi penjualan berhasil selesai.');
            }
        }
    }

}
