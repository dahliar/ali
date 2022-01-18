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
    public function getAllTransaction(){
        return $this->transaction->getAllItemData();
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
        $forwarders = Forwarder::orderBy('name', 'ASC')->get();
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
                'advance' => 'required|gt:0',
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
            'creationDate' =>  date('Y-m-d'),
            'transactionDate' => $request->transactionDate,
            'departureDate' =>  $request->departureDate,
            'loadingDate' =>  $request->loadingDate,
            'arrivalDate' => $request->arrivalDate,
            'shippedDatePlan' => $request->shippedDatePlan,
            'paymentPlan' => $request->paymentPlan,
            'status' =>  1
        ];
        
        $lastTransactionIdStored = $this->transaction->storeOneTransaction($data);
        $companyName=Company::select('name')->where('id', $request->company)->value('name');

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
            $this->transaction->storeNotes($notesData);
        }
        return redirect('transactionList')
        ->with('status','Transaksi pengiriman ke '.$companyName.' berhasil ditambahkan.');
        

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
        $forwarders = Forwarder::orderBy('name', 'ASC')->get();


        //$pinotes=TransactionNote::select('note')->where('transactionId', $transaction->id)->get();

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
            //'transactionNum' => [
            //Rule::exists('transactions', 'transactionNum')->where('transactionNum', $request->transactionNum),],
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

        $result = DB::table('transactions')
        ->select('status')
        ->where('id', $request->transactionId)
        ->first();

        $curStatus=$result->status;
        $tnum="";
        if (($curStatus==1) and ($request->status == 2)){
            $this->inv = new InvoiceController();
            $tnum = $this->inv->createtransactionnum($request->transactionId);
        }

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

        $transactionId = $request->transactionId;            
        $oneStore = $this->transaction->updateOneTransaction($data, $transactionId);

        //the plan is, delete all notes existed in the table, and insert the new edited list
        if (!empty($request->pinotes)){
            $a=0;

            foreach ($request->pinotes as $notes){            
                $notesData[$a] = [
                    'transactionId' =>  $request->transactionId,
                    'note' => $notes
                ];
                $a=$a+1;
            }
            DB::table('transaction_notes')->where('transactionId', $request->transactionId)->delete();
            $this->transaction->storeNotes($notesData);
        }


        $companyName=Company::select('name')->where('id', $request->company)->value('name');
        return redirect('transactionList')
        ->with('status','Update Transaksi pengiriman ke '.$companyName.' berhasil diperbaharui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
