<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Countries;
use App\Models\Company;
use App\Models\Currency;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;
use File;


class PurchaseController extends Controller
{
    public function __construct(){
        $this->purchase = new Purchase();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nations = Countries::where('isActive',1)->orderBy('name')->get();
        return view('purchase.purchaseList', compact('nations'));
    }

    public function getPurchaseList($negara, $statusTransaksi, $start, $end)
    {
        $query = DB::table('purchases as p')
        ->select(
            'p.id as id', 
            'p.purchasingnum as nosurat', 
            'c.name as name', 
            'n.name as nation', 
            'p.purchasedate as purchasedate',
            'p.arrivaldate as arrivaldate',
            'p.dueDate as dueDate',
            'p.paymentAmount as paymentAmount',
            'p.status as statusCheck',
            'p.realInvoiceFilePath as realInvoiceFilePath',
            DB::raw('(CASE WHEN p.status ="0" THEN "New Submission"
                WHEN p.status ="1" then "On Progress"
                WHEN p.status ="2" then "Finished"
                ELSE "Cancelled" END) AS status')
        )
        ->join('companies as c', 'c.id', '=', 'p.companyid')
        ->join('countries as n', 'n.id', '=', 'c.nation')
        ->whereBetween('purchaseDate', [$start, $end])
        ->orderBy('p.status', 'asc')
        ->orderBy('p.created_at', 'asc')
        ->orderBy('p.id', 'asc');

        if($negara != -1){
            $query->where('n.id', '=', $negara);
        }
        if($statusTransaksi != -1){
            $query->where('p.status', '=', $statusTransaksi);
        }

        $query->get();  


        return datatables()->of($query)
        ->addIndexColumn()
        ->editColumn('paymentAmount', function ($row) {
            return number_format($row->paymentAmount, 2);
        })
        ->addColumn('action', function ($row) {
            $html='<button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Download" onclick="getFileDownload('."'".$row->realInvoiceFilePath."'".')"><i class="fas fa-file-invoice-dollar"></i>
            </button>
            <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Daftar beli item" onclick="purchaseItems('."'".$row->id."'".')">
            <i class="fa fa-list"></i></button>
            ';
            if($row->statusCheck != 3){
                $html=$html.'<button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit data pembelian " onclick="purchaseEdit('."'".$row->id."'".')"><i class="fa fa-edit"></i></button>
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Document List" onclick="documentList('."'".$row->id."'".')"><i class="fa fa-file-pdf"></i>
                </button>
                ';
            }

            return $html;
        })
        ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::orderBy('name')->where('isActive','1')->get();
        $currencies = Currency::orderBy('name')->get();
        return view('purchase.purchaseAdd', compact('companies', 'currencies'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'company'       => ['required','gt:0'],
                'valutaType'    => ['required','gt:0'],
                'purchaseDate'  => [
                    'required', 
                    'date', 
                    'before_or_equal:today', 
                    'after:-1 month'
                ],
                'arrivalDate'   => [
                    'required',
                    'date',
                    'before_or_equal:purchaseDate',
                    'after:-1 month'
                ],
                'dueDate'   => [
                    'required',
                    'date',
                    'after:purchaseDate'
                ],
                'downPayment' => ['required','gte:0'],
                'taxPercentage' => ['required','gt:0'],
                'imageurl' => ['mimes:jpg,jpeg,png,pdf','max:2048'],
            ],
            [
                'company.gt'=> 'Pilih salah satu perusahaan',
                'valutaType.gt'=> 'Pilih salah satu jenis valuta pembayaran',
                'company.gt'=>'Pilih salah satu perusahaan',
                'purchaseDate.after' => 'Maksimal 1 bulan yang lalu',
                'dueDate.after' => 'Tanggal deadline bayar harus lebih dari tanggal penerimaan',
                'arrivalDate.after' => 'Maksimal 1 bulan yang lalu',
                'imageurl.max' => 'Ukuran file maksimal adalah 1 MB'
            ]
        );

        $company=Company::select('name', 'taxIncluded')->where('id', $request->company)
        ->first();



        $data = [
            'userId' => auth()->user()->id,
            'companyId' =>  $request->company,
            'valutaType' => $request->valutaType,
            'paymentTerms' => $request->paymentTerms,
            'purchaseDate' => $request->purchaseDate,
            'arrivalDate' => $request->arrivalDate,
            'dueDate' => $request->dueDate,
            'downPayment' => $request->downPayment,
            'taxIncluded' => $company->taxIncluded,
            'taxPercentage' => $request->taxPercentage,
            'status' =>  1
        ];

        $lastPurchaseIdStored = $this->purchase->storeOnePurchase($data);
        $file="";
        $filename="";
        if($request->hasFile('imageurl')){
            $file = $request->imageurl;
            $filename = "Purchase Invoice ".$request->purchaseDate." ".$lastPurchaseIdStored.".".$file->getClientOriginalExtension();
            $file->move(base_path("/storage/app/docs/"), $filename);
        }

        //create Purchase Number
        $purchaseNum = $this->getPurchaseNumber($lastPurchaseIdStored);
        $purchase = Purchase::find($lastPurchaseIdStored);
        $purchase->purchasingNum = $purchaseNum;
        $purchase->realInvoiceFilePath = $filename;
        $purchase->save();

        return redirect('purchaseList')
        ->with('status','Transaksi pembelian ke '.$company->name.' berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        $currencies = Currency::orderBy('name')->get();
        $companyName = DB::table('companies')->select('name as name')->where('id','=', $purchase->companyId)->first();

        return view('purchase.purchaseEdit', compact('purchase', 'companyName', 'currencies'));
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
        public function update(Request $request)
        {
            if (!$request->exists('imageurlBaru')){
                $request->validate(
                    [
                        'progressStatus' => 'required|gt:0',
                        'purchaseDate.before_or_equal' => 'Maksimal 1 bulan yang lalu',
                        'dueDate.after' => 'Tanggal deadline bayar harus lebih dari tanggal penerimaan',
                        'arrivalDate.after_or_equal' => 'Maksimal 1 bulan yang lalu',
                    ],
                    [                
                        'purchaseDate.after' => 'Maksimal 1 bulan yang lalu',
                        'dueDate.after' => 'Tanggal deadline bayar harus lebih dari tanggal penerimaan',
                        'arrivalDate.after' => 'Maksimal 1 bulan yang lalu',
                    ]
                );
                $purchase = Purchase::find($request->purchaseId);
                $purchase->arrivalDate = $request->arrivalDate;
                $purchase->purchaseDate = $request->purchaseDate;
                $purchase->dueDate = $request->dueDate;
                $purchase->finishedDate = Carbon::now()->toDateString();
                $purchase->status = $request->progressStatus;
                $purchase->paymentTerms = $request->paymentTerms;
                $purchase->save();
            } else{
                $request->validate(
                    [
                        'progressStatus' => 'required|gt:0',
                        'purchaseDate' => 'required|date|before_or_equal:today',
                        'arrivalDate' => 'required|date|before_or_equal:purchaseDate',
                        'dueDate' => 'required|date|after:purchaseDate',
                        'imageurlBaru' => ['required','image', 'max:2048']
                    ],
                    [
                        'purchaseDate.before_or_equal' => 'Maksimal 1 bulan yang lalu',
                        'dueDate.after' => 'Tanggal deadline bayar harus lebih dari tanggal penerimaan',
                        'arrivalDate.after_or_equal' => 'Maksimal 1 bulan yang lalu',
                        'imageurl.required' => 'File invoice harus ada',
                        'imageurl.max' => 'Ukuran file maksimal adalah 1 MB',
                        'imageurl.image' => 'File invoice harus berupa image'
                    ]
                );

                $purchase = Purchase::find($request->purchaseId);
                $file="";
                $filename="";
                if($request->hasFile('imageurlBaru')){
                    $file = $request->imageurlBaru;
                    $filename = "Purchase Invoice ".$request->purchaseDate." ".$purchase->id.".".$file->getClientOriginalExtension();
                    $file->move(base_path("/storage/app/docs/"), $filename);
                }
                $purchase->arrivalDate = $request->arrivalDate;
                $purchase->purchaseDate = $request->purchaseDate;
                $purchase->dueDate = $request->dueDate;
                $purchase->finishedDate = Carbon::now()->toDateString();
                $purchase->status = $request->progressStatus;
                $purchase->realInvoiceFilePath = $filename;
                $purchase->paymentTerms = $request->paymentTerms;
                $purchase->save();
            }


            return redirect('purchaseList')
            ->with('status','Transaksi pembelian ke '.$request->companyName.' berhasil diperbaharui.');
        }
        public function purchaseDocument(Purchase $purchase)
        {
            return view('purchase.purchaseDocuments', compact('purchase'));
        }
        public function getAllPurchaseDocuments(Request $request){
            $query = DB::table('documents as d')
            ->select(
                'p.id as id', 
                'p.purchasingnum as invnum', 
                'u.name as name', 
                'd.created_at as tanggal',
                'd.filepath as filepath',
                DB::raw('(CASE WHEN dn.bagian ="PI-ALI" THEN "PI"
                    WHEN dn.bagian ="PURCHASE-ALI" then "Invoice" END) AS jenis')
            )
            ->join('document_numbers as dn', 'dn.id', '=', 'd.document_numbers_id')
            ->join('purchases as p', 'p.id', '=', 'dn.purchaseId')
            ->join('users as u', 'u.id', '=', 'd.userId')
            ->where('p.id', '=', $request->purchaseId)
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
        public function getPurchaseNumber($purchaseId){
            $bagian="PURCHASE-ALS";
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

    }
