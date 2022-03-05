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
            DB::raw('(CASE 
                WHEN e.employmentStatus="1" THEN "Bulanan" 
                WHEN e.employmentStatus="2" THEN "Harian" 
                WHEN e.employmentStatus="3" THEN "Borongan" 
                END) AS employmentStatus'),
        )
        ->join('users as u', 'u.id', '=', 'e.userid')
        ->whereIn('employmentStatus', [2,3])
        ->where('isActive', '=', 1)
        ->orderBy('u.name')
        ->get();
        return view('presence.presenceBoronganPekerjaForm', compact('borongan', 'employees'));
    }

    public function getBorongans()
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
            DB::raw('
                concat(
                count(db.isPaid), 
                " dari ", 
                count(db.id)
                )
                AS countIsPaid'),
            DB::raw('(b.hargaSatuan * b.netweight) AS total'),
            'b.worker as worker')
        ->leftjoin('detail_borongans as db', 'db.boronganId', '=', 'b.id')
        ->orderBy('b.created_at')
        ->groupBy('b.id');
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

            if ($row->status == 2){
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
            if ($row->status == 3){
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


            $fullWorker=count($request->boronganWorker);
            $halfDay=$fullWorker - count($request->boronganType);
            $satuanHalfday = (($fullWorker-$halfDay)*2)+$halfDay;
            $netPriceHalf = ($borongan->hargaSatuan * $borongan->netweight) / $satuanHalfday;

            foreach($request->boronganWorker as $boronganWorker){
                $price=$netPriceHalf;
                $isFullday = 0;
                if(in_array($boronganWorker, $request->boronganType)){
                    $price=$netPriceHalf * 2;
                    $isFullday = 1;
                }
                $data[$a] = [
                    'employeeId' => $boronganWorker,
                    'boronganId' => $request->boronganId,
                    'isFullday' => $isFullday,
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
        public function storeBorongan(Request $request)
        {
            $request->validate(
                [
                    'name' => ['required'],
                    'tanggalKerja' => ['required','date','before_or_equal:today'],
                    'hargaSatuan' => ['required','numeric','gte:1'],
                    'netweight' => ['required','numeric','gte:1'],
                    'worker' => ['required','numeric','gte:1']
                ],
                [
                    'name.*' => "Nama harus diisi",
                    'tanggalKerja.*' => "Tanggal harus diisi dan maksimal adalah tanggal hari ini",
                    'hargaSatuan.*' => "Harga satuan harus diisi dan minimal adalah 1",
                    'netweight.*' => "Berat bersih harus diisi dan minimal adalah 1",
                    'worker.*' => "Jumlah Pekerja harus diisi dan minimal adalah 1",
                ]);
            $netPrice = (($request->hargaSatuan * $request->netweight) / $request->worker);

            $data = [
                'name' => $request->name,
                'tanggalKerja' => $request->tanggalKerja,
                'status' => 0,
                'hargaSatuan' => $request->hargaSatuan,
                'netweight' => $request->netweight,
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
            $query = DB::table('detail_borongans as db')
            ->select([
                'db.id as id',
                'e.nip as nip',
                'db.netPayment as netPayment', 
                'u.name as nama', 
                'e.noRekening as noRekening', 
                'bank.name as bankname',
                'os.name as osname'
            ])
            ->join('employees as e', 'e.id', '=', 'db.employeeId')
            ->join('users as u', 'u.id', '=', 'e.userid')
            ->join('banks as bank', 'bank.id', '=', 'e.bankid')
            ->join('employeeorgstructuremapping as eos', 'eos.idemp', '=', 'e.id') 
            ->join('organization_structures as os', 'os.id', '=', 'eos.idorgstructure')           
            ->where('boronganId', '=', $borongan->id)->get();

            return view('presence.presenceBoronganWorkerList', compact('borongan', 'query'));
        }

        /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Borongan  $borongan
     * @return \Illuminate\Http\Response
     */
        public function edit(Borongan $borongan)
        {
        //
        }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Borongan  $borongan
     * @return \Illuminate\Http\Response
     */
        public function update(Request $request, Borongan $borongan)
        {
        //
        }

        /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Borongan  $borongan
     * @return \Illuminate\Http\Response
     */
        public function destroy(Borongan $borongan)
        {
            $deleted = DB::table('detail_borongans')->where('boronganId', '=', $borongan->id)->delete();
            $deleted = DB::table('borongans')->where('id', '=', $borongan->id)->delete();
            return $retValue = [
                'message'       => "ecord telah dihapus",
                'isError'       => "0"
            ];
        }
    }
