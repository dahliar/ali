<?php

namespace App\Http\Controllers;

use App\Models\Borongan;
use Illuminate\Http\Request;

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
        return view('presence.presenceBoronganList');
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
        return view('presence.presenceBoronganPekerjaForm', compact('borongan', 'employees'));
    }

    public function getBorongans(Request $request)
    {
        $query = DB::table('borongans as b')
        ->select(
            'b.id as id', 
            'b.name as name', 
            'b.tanggalKerja as tanggalKerja',
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
            return view('presence.presenceBoronganForm');
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
        if ($borongan->jenis==2){
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
        } else {
            $price = ($borongan->hargaSatuan * $borongan->netweight) / $borongan->worker;
            $price=ceil($price/100) * 100;
            $dataSalary=[
                '1'=>$price, 
                '2'=>$price
            ];
        }
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
    /*
    public function storePekerja(Request $request, Borongan $borongan)
    {

        $limit=$request->worker;
        $request->validate([
            'boronganWorker' => ['required','array',"min:$limit","max:$limit"]
        ]);

        $a=0;
        $jmlCowok=0;
        $jmlCewek=0;
        dump($borongan);
        dd($request);

        foreach($request->boronganGender as $gender){
            if ($gender==1){
                $jmlCowok++;
            } else{
                $jmlCewek++;
            }
        }
        $percentage=0.2;
        $dataSalary[]="";
        if ($borongan->jenis==2){

            if (($jmlCowok==0) or ($jmlCewek==0)){
                $price = ($borongan->hargaSatuan * $borongan->netweight) / $borongan->worker;
                $price=ceil($price/100) * 100;
                $dataSalary=[
                    '1'=>$price, 
                    '2'=>$price
                ];
            }else{
                if($borongan->loading==0){
                    $percentage=0.15;
                }
                $dataSalary=$this->hitungBoronganPacking($borongan->netweight, $borongan->hargaSatuan, $jmlCowok, $jmlCewek, $percentage);
            }            
        } else {
            $price = ($borongan->hargaSatuan * $borongan->netweight) / $borongan->worker;
            $price=ceil($price/100) * 100;
            $dataSalary=[
                '1'=>$price, 
                '2'=>$price
            ];
        }
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
    */

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
                'hargaSatuan' => ['required','numeric','gte:1'],
                'jenis' => ['required','gt:0'],
                'bagiHasil' => ['required','gte:0', 'lte:20'],
                'netweight' => ['required','numeric','gte:1'],
                'worker' => ['required','numeric','gte:1']
            ],
            [
                'name.*' => "Nama harus diisi",
                'tanggalKerja.*' => "Tanggal harus diisi dan maksimal adalah tanggal hari ini",
                'jenis.*' => "Pilih salah satu jenis",
                'hargaSatuan.*' => "Harga satuan harus diisi dan minimal adalah 1",
                'bagiHasil.*' => "Bagi hasil antara 0-20%",
                'netweight.*' => "Berat bersih harus diisi dan minimal adalah 1",
                'worker.*' => "Jumlah Pekerja harus diisi dan minimal adalah 1",
            ]);
        $netPrice = (($request->hargaSatuan * $request->netweight) / $request->worker);

        $data = [
            'name' => $request->name,
            'tanggalKerja' => $request->tanggalKerja,
            'status' => 0,
            'jenis' => $request->jenis,
            'loading' => $request->cbval,
            'hargaSatuan' => $request->hargaSatuan,
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

        return view('presence.presenceBoronganWorkerList', compact('borongan', 'query'));
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
}
