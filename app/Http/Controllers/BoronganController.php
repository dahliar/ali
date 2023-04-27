<?php

namespace App\Http\Controllers;

use App\Models\Borongan;
use Illuminate\Http\Request;
use Carbon\Carbon;


use DB;

class BoronganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('borongan.presenceBoronganList');
    }

    public function indexStandarBorongan()
    {
        $types = DB::table('borongan_types as bt')
        ->select(
            'bt.id as id',
            'bt.nama as nama',
        )
        ->where('bt.status', '=', 1)
        ->orderBy('bt.nama')
        ->get(); 
        return view('borongan.standarBorongan', compact('types'));
    }

    public function tambahDetailPekerjaBorongan(Borongan $borongan)
    {
        $employees = DB::table('employees as e')
        ->select(
            'e.id as empid',
            'e.nip as nip',
            'u.name as nama',
            'e.gender as genderValue',
            DB::raw('(CASE 
                WHEN e.gender="1" THEN "Laki" 
                WHEN e.gender="2" THEN "Perempuan" 
                END) AS gender'),
            DB::raw('(CASE 
                WHEN e.employmentStatus="1" THEN "Bulanan" 
                WHEN e.employmentStatus="2" THEN "Harian" 
                WHEN e.employmentStatus="3" THEN "Borongan" 
                END) AS employmentStatus'),
        )
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->where('employmentStatus', 3)
        ->where('isActive', '=', 1)
        ->orderBy('u.name')
        ->get();
        return view('borongan.presenceBoronganPekerjaForm', compact('borongan', 'employees'));
    }

    public function getBorongans(Request $request)
    {
        $query = DB::table('borongans as b')
        ->select(
            'b.id as id', 
            'b.name as name', 
            'b.tanggalKerja as tanggalKerja',
            'b.bagiHasil as bagiHasil',
            'b.status',
            DB::raw('(CASE 
                WHEN b.status="0" THEN "Tambah Pekerja" 
                WHEN b.status="1" THEN "Generate" 
                WHEN b.status="2" THEN "Pembayaran" 
                WHEN b.status="3" THEN "Selesai" 
                END) AS statusText'),
            'b.hargaSatuan as hargaSatuan',
            'b.netweight as netweight',
            DB::raw('(b.hargaSatuan * b.netweight) AS total'),
            'b.worker as worker')
        ->leftjoin('detail_borongans as db', 'db.boronganId', '=', 'b.id')
        ->whereBetween('tanggalKerja', [$request->start, $request->end])
        ->orderBy('b.created_at', 'desc')
        ->groupBy('b.id');

        if($request->status != -1){
            $query->where('b.status', '=', $request->status);
        }
        $query->get();

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html='';
            if ($row->status == 0){
                $html .= '
                <a data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah detail pekerja" href="boronganWorkerAdd/'.$row->id.'"><i class="fa fa-plus"></i>
                </a>
                <button class="btn btn-xs btn-secondary" disabled>
                <i class="fa fa-list" style="font-size:20px"></i>
                </button>
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Perhitungan Borongan" onclick="hapusBorongan('."'".$row->id."'".')">
                <i class="fa fa-trash" style="font-size:20px"></i>
                </button>
                ';
            }
            if ($row->status == 1){
                $html .= '                
                <button class="btn btn-xs btn-secondary" disabled>
                <i class="fa fa-plus" style="font-size:20px"></i>
                </button>
                <a  data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Detail pekerja borongan" href="boronganWorkerList/'.$row->id.'"><i class="fa fa-list"></i>
                </a>
                <button  data-rowid="'.$row->id.'" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" data-container="body" title="Hapus Perhitungan Borongan" onclick="hapusBorongan('."'".$row->id."'".')">
                <i class="fa fa-trash" style="font-size:20px"></i>
                </button>
                ';
            }
            if (($row->status == 2) or ($row->status == 3)) {
                $html .= '
                <button class="btn btn-xs btn-secondary" disabled>
                <i class="fa fa-plus" style="font-size:20px"></i>
                </button>
                <a  data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Detail pekerja borongan" href="boronganWorkerList/'.$row->id.'"><i class="fa fa-list"></i>
                </a>
                <button class="btn btn-xs btn-secondary" disabled>
                <i class="fa fa-trash" style="font-size:20px"></i>
                </button>
                ';
            }
            return $html;
        })->addIndexColumn()->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = DB::table('borongan_types as bt')
        ->select(
            'bt.id as id',
            'bt.nama as nama',
        )
        ->where('bt.status', '=', 1)
        ->orderBy('bt.nama')
        ->get(); 
        return view('borongan.presenceBoronganForm', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePekerja(Request $request, Borongan $borongan)
    {

        $limit=$request->worker;
        $request->validate([
            'boronganWorker' => ['required','array',"min:$limit","max:$limit"]
        ]);

        $a=0;
        $jmlCowok=0;
        $jmlCewek=0;

        foreach($request->boronganGender as $gender){
            if ($gender==1){
                $jmlCowok++;
            } else{
                $jmlCewek++;
            }
        }
        $percentage=$borongan->bagiHasil/100;
        $dataSalary[]="";
        //if ($borongan->jenis==2){
        if (($jmlCowok==0) or ($jmlCewek==0)){
            $price = ($borongan->hargaSatuan * $borongan->netweight) / $borongan->worker;
            $price=ceil($price/100) * 100;
            $dataSalary=[
                '1'=>$price, 
                '2'=>$price
            ];
        }else{
            $dataSalary=$this->hitungBoronganPacking($borongan->netweight, $borongan->hargaSatuan, $jmlCowok, $jmlCewek, $percentage);
        } 
        /*          
        } else {
            $price = ($borongan->hargaSatuan * $borongan->netweight) / $borongan->worker;
            $price=ceil($price/100) * 100;
            $dataSalary=[
                '1'=>$price, 
                '2'=>$price
            ];
        }
        */
        $data[]="";
        foreach($request->boronganWorker as $bw){
            $price = $dataSalary[$request->boronganGender[$bw]];
            $data[$a] = [
                'employeeId' => $bw,
                'boronganId' => $request->boronganId,
                'isFullday' => 1,
                'netPayment' => $price
            ];
            $a++;
        }

        DB::table('detail_borongans')->insert($data);
        DB::table('borongans')
        ->where('id', $request->boronganId)
        ->update(['status' => 1]);

        return redirect('boronganList')
        ->with('status','Item berhasil ditambahkan.');
    }

    function hitungBoronganPacking($berat, $hargaperkg, $jmlCowok, $jmlCewek, $percentage){
        $x=$berat*$hargaperkg;
        $y=$jmlCowok+$jmlCewek;
        $z=($percentage*( ( ($x) / ($y) ) * $jmlCewek));

        $honorCowok=( ($x) / ($y) ) - ( $z / $jmlCewek);
        $honorCewek=( ($x) / ($y) ) + ( $z / $jmlCowok);

        $harga=[
            '1'=>ceil($honorCewek/100) * 100, 
            '2'=>ceil($honorCowok/100) * 100
        ];

        return $harga;

    }


    public function storeBorongan(Request $request)
    {
        $request->validate(
            [
                'name' => ['required'],
                'tanggalKerja' => ['required','date','before_or_equal:today'],
                'standarBorongan' => ['required','gt:0'],
                'bagiHasil' => ['required','gte:0', 'lte:20'],
                'netweight' => ['required','numeric','gte:1'],
                'worker' => ['required','numeric','gte:1']
            ],
            [
                'name.*' => "Nama harus diisi",
                'tanggalKerja.*' => "Tanggal harus diisi dan maksimal adalah tanggal hari ini",
                'standarBorongan.*' => "Pilih salah satu jenis",
                'bagiHasil.*' => "Bagi hasil antara 0-20%",
                'netweight.*' => "Berat bersih harus diisi dan minimal adalah 1",
                'worker.*' => "Jumlah Pekerja harus diisi dan minimal adalah 1",
            ]);

        $hargaSatuan = DB::table('borongan_standards as bs')
        ->select(
            'bs.harga as harga'
        )
        ->where('bs.id', '=', $request->standarBorongan)
        ->orderBy('bs.nama')
        ->first()->harga; 
        //$netPrice = (($request->hargaSatuan * $request->netweight) / $request->worker);

        $data = [
            'name' => $request->name,
            'tanggalKerja' => $request->tanggalKerja,
            'status' => 0,
            'jenis' => $request->jenis,
            'boronganStandard' => $request->standarBorongan,
            //'loading' => $request->cbval,
            'hargaSatuan' => $hargaSatuan,
            'netweight' => $request->netweight,
            'bagiHasil' => $request->bagiHasil,
            'worker' => $request->worker,
        ];
        DB::table('borongans')->insert($data);

        return redirect('boronganList')
        ->with('status','Item berhasil ditambahkan.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Borongan  $borongan
     * @return \Illuminate\Http\Response
     */
    public function show(Borongan $borongan)
    {
        $query = DB::table('borongans as b')
        ->select([
            'db.id as id',
            'e.nip as nip',
            DB::raw('sum(db.netPayment) as netPayment'),
                //'db.netPayment as netPayment', 
            'u.name as nama', 
            'os.name as osname'
        ])
        ->join('detail_borongans as db', 'b.id', '=', 'db.boronganId')
        ->join('employees as e', 'e.id', '=', 'db.employeeId')
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->join('employeeorgstructuremapping as eos', 'eos.idemp', '=', 'e.id') 
        ->join('organization_structures as os', 'os.id', '=', 'eos.idorgstructure')
        ->where('eos.isactive', '=', 1)
        ->where('boronganId', '=', $borongan->id)
        ->groupBy('e.id')
        ->get();

        return view('borongan.presenceBoronganWorkerList', compact('borongan', 'query'));
    }

    public function destroy(Borongan $borongan)
    {
        $deleted = DB::table('detail_borongans')->where('boronganId', '=', $borongan->id)->delete();
        $deleted = DB::table('borongans')->where('id', '=', $borongan->id)->delete();
        return $retValue = [
            'message'       => "Record telah dihapus",
            'isError'       => "0"
        ];
    }

    public function getBoronganStandardList($jenis)
    {
        $query = DB::table('borongan_standards as bs')
        ->select(
            'bs.id as id', 
            'bs.nama as nama', 
            'bs.harga as harga',
            'bs.jenis as jenis',
            'bs.status as status',
            'bt.nama as jenisTeks'
        )
        ->join('borongan_types as bt', 'bt.id', '=', 'bs.jenis')
        ->orderBy('bs.nama', 'asc');
        
        if($jenis != -1){
            $query->where('bs.jenis', '=', $jenis);
        }
        $query->get();

        return datatables()->of($query)
        ->editColumn('status', function ($row) {
            $html = '';
            if ($row->status==1){
                $html.='<i class="far fa-check-square" style="font-size:20px" data-toggle="tooltip" data-placement="top" data-container="body" title="Aktif"></i>';
            } else {
                $html.='<i class="far fa-times-circle" style="font-size:20px" data-toggle="tooltip" data-placement="top" data-container="body" title="Non-Aktif"></i>';
            }
            return $html;
        })
        ->addColumn('action', function ($row) {
            $html='
            <a data-rowid="'.$row->id.'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Edit data borongan" href="standarBoronganEdit/'.$row->id.'"><i class="fa fa-edit"></i>
            </a>
            ';
            return $html;
        })
        ->rawColumns(['status', 'action'])
        ->addIndexColumn()->toJson();
    }
    public function standarBoronganAdd(){
        $types = DB::table('borongan_types as bt')
        ->select(
            'bt.id as id',
            'bt.nama as nama',
        )
        ->where('bt.status', '=', 1)
        ->orderBy('bt.nama')
        ->get(); 
        return view('borongan.standarBoronganTambah', compact('types'));
    }
    public function standarBoronganEdit($id){
        $query = DB::table('borongan_standards as bs')
        ->select(
            'bs.id as id', 
            'bs.nama as nama', 
            'bs.harga as harga',
            'bs.status as status',
            'bt.nama as namaTipe',      //nama borongan_types
            'bt.id as idType'        //tipe borongan
        )
        ->join('borongan_types as bt', 'bt.id', '=', 'bs.jenis')
        ->where('bs.id', '=', $id)
        ->first();
        $id=$query->id;
        $nama=$query->nama;
        $harga=$query->harga;
        $status=$query->status;
        $idType=$query->idType;
        $namaType=$query->namaTipe;

        return view('borongan.standarBoronganEdit', compact('id','nama','harga','status','idType', 'namaType'));
    }
    public function standarBoronganStore(Request $request)   {
        $request->validate(
            [
                'nama' => ['required', 'unique:borongan_standards'],
                'jenis' => ['required','gt:0'],
                'harga' => ['required','gt:0']
            ],
            [
                'nama.required' => "Nama harus diisi",
                'nama.unique' => "Nama telah digunakan, gunakan nama yang berbeda",
                'jenis.*' => "Pilih salah satu jenis",
                'harga.*' => "Harga harus diisi dan minimal adalah 1"
            ]);
        $data = [
            'id_borongan_standard'  => null,
            'jenis'         => $request->jenis,
            'nama'          => $request->nama,
            'harga'         => $request->harga,
            'status'        => 1,
            'createdBy'     => auth()->user()->id,
            'approvedBy'    => null,
            'created_at'    => Carbon::now(),
            'approved_at'   => null,
            'isApproved'    => 0,
            'recordType'    => 1
        ];
        DB::table('borongan_standard_histories')->insert($data);

        return redirect('standarBorongan')->with('status','Penambahan disimpan, menunggu approval.');

    }
    public function standarBoronganUpdate(Request $request)   {
        $request->validate(
            [
                'status' => ['required','gte:0'],
                'harga' => ['required','gt:0']
            ],
            [
                'status.*' => "Pilih salah satu status",
                'harga.*' => "Harga harus diisi dan minimal adalah 1"
            ]);

        $data = [
            'id_borongan_standard'  => $request->idStandar,
            'jenis'         => $request->idType,
            'nama'          => $request->nama,
            'harga'         => $request->harga,
            'status'        => $request->status,
            'createdBy'     => auth()->user()->id,
            'approvedBy'    => null,
            'created_at'    => Carbon::now(),
            'approved_at'   => null,
            'isApproved'    => 0,
            'recordType'    => 2
        ];

        DB::table('borongan_standard_histories')->insert($data);

        return redirect('standarBorongan')->with('status','Update disimpan, menunggu approval.');
    }
    public function standarBoronganApproval()   {
        return view('borongan.standarBoronganApproval');
    }

    public function getStandarBoronganApproval()
    {
        $query = DB::table('borongan_standard_histories as bsh')
        ->select(
            'bsh.id as id',
            'bsh.id_borongan_standard as bsId',
            DB::raw('concat(bt.nama," - ",bsh.nama) as nama'),
            'bsh.harga as harga',
            DB::raw('(CASE 
                WHEN bsh.status="0" THEN "Tidak Aktif" 
                WHEN bsh.status="1" THEN "Aktif" 
                END) AS status'),            
            'u.name as oleh',
            'bsh.created_at as pada',
            'bsh.isApproved as isApproved',
            DB::raw('(CASE 
                WHEN bsh.recordType="1" THEN "Baru" 
                WHEN bsh.recordType="2" THEN "Update" 
                END) AS jenisRecord')
        )
        ->join('borongan_types as bt', 'bt.id', '=', 'bsh.jenis')
        ->join('users as u', 'u.id', '=', 'bsh.createdBy')
        ->where('bsh.isApproved', '=', 0)
        ->whereDate('bsh.created_at', '>=', now()->subDays(7)->setTime(0, 0, 0)->toDateTimeString())
        ->orderBy('bsh.created_at')
        ->get(); 

        return datatables()->of($query)
        ->addColumn('action', function ($row) {
            $html = '
            <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Setujui perubahan" onclick="approveStore('."'".$row->id."',".')">
            <i class="fa fa-check"></i>
            </button>
            <button class="btn btn-xs btn-light" data-toggle="tooltip" data-placement="top" data-container="body" title="Tolak perubahan" onclick="tolakStore('."'".$row->id."',".')">
            <i class="fa fa-times"></i>
            </button>
            ';
            return $html;
        })
        ->rawColumns(['status', 'action'])
        ->addIndexColumn()->toJson();
    }

    
    public function approveStandarChange(Request $request)
    {
        if($request->approveStore == 1){
            //approve
            try{
                DB::beginTransaction();
                $query = DB::table('borongan_standard_histories')
                ->select(
                    'id_borongan_standard as idBoronganStandar',
                    'jenis',
                    'nama',
                    'harga',
                    'status',
                    'createdBy',
                    'approvedBy',
                    'created_at',
                    'approved_at',
                    'isApproved',
                    'recordType'
                )
                ->where('id', $request->bshId)
                ->first();

                if ($query->recordType == 1){
                    //baru
                    $data = [
                        'jenis'         => $query->jenis,
                        'nama'          => $query->nama,
                        'harga'         => $query->harga,
                        'status'        => $query->status,
                        'createdBy'     => $query->createdBy,
                        'approvedBy'     => auth()->user()->id
                    ];
                    DB::table('borongan_standards')->insert($data);
                } else {
                    $data = [
                        'harga'         => $query->harga,
                        'status'        => $query->status,
                        'createdBy'     => $query->createdBy,
                        'approvedBy'     => auth()->user()->id
                    ];
                    DB::table('borongan_standards as bs')
                    ->where('bs.id', '=', $query->idBoronganStandar)
                    ->update($data);
                }

                DB::table('borongan_standard_histories')
                ->where('id', $request->bshId)
                ->update([
                    'isApproved' => 2,
                    'approvedBy' => auth()->user()->id,
                    'approved_at' => Carbon::now()
                ]);
                DB::commit();
                return true;
            }
            catch(\Exception $e){
                DB::rollBack();
                return "Gagal Update, kontak administrator";
            }
        } else if($request->approveStore == 2){
            //reject
            $approved = DB::table('borongan_standard_histories')
            ->where('id', $request->bshId)
            ->update([
                'isApproved' => 1,
                'approvedBy' => auth()->user()->id,
                'approved_at' => Carbon::now()
            ]);
            return true;
        }
    }

    public function getStandarBoronganPrice($id){
        $query = DB::table('borongan_standards as bs')
        ->select(
            'bs.id as id',
            'bs.harga as harga',
            'bs.nama as nama'
        )
        ->where('bs.status', '=', 1)
        ->where('bs.jenis', '=', $id)
        ->orderBy('bs.nama')
        ->get(); 
        return $query;  
    }
}