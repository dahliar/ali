@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js" type="text/javascript" ></script>


<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function BackToOffering(transactionId){
        Swal.fire({
            title: 'Revoke data transaksi local.',
            text: "Data stock barang akan dikembalikan?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("setTransactionToBeRevoked") }}',
                    type: "POST",
                    data : {
                        "_token":"{{ csrf_token() }}",
                        "transactionId":transactionId,
                        "uid":"{{ Auth::user()->id  }}",
                        "accessLevel":"{{ Auth::user()->accessLevel }}",
                    },
                    dataType: "json",
                    success:function(data){
                        if(data==0){
                            Swal.fire(
                                'Revoke transaksi!',
                                "Update transaksi, data barang dikembalikan!",
                                'info'
                                );
                        }else{
                            Swal.fire(
                                'Transaksi gagal diubah!',
                                "Kontak Admin",
                                'info'
                                );
                        }
                    }
                });
            } else {
                Swal.fire(
                    'Batal Revoke.',
                    "Status transaksi tidak berubah, stock barang tidak berubah",
                    'info'
                    );
            }
        })

    }

    function myFunction(){
        var e = document.getElementById("negara");
        var negara = e.options[e.selectedIndex].value;       
        var statusTransaksi = 2;   
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;


        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getAllExportTransactionToRevoke") }}',
                data: function (d){
                    d.negara = negara,
                    d.statusTransaksi = statusTransaksi,
                    d.start = start,
                    d.end = end
                }
            },
            dataType: 'json',            
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "30%", "targets": [0], "className": "text-left"   },
                {   "width": "20%", "targets": [1], "className": "text-left" },
                {   "width": "20%", "targets": [2], "className": "text-left" },
                {   "width": "20%", "targets": [3], "className": "text-left" }
                ], 

            columns: [
                {data: 'name', name: 'name'},
                {data: 'number', name: 'number'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
        });
    }
</script>
<body>
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Revoke Transactions</li>
                        </ol>
                    </nav>
                </div>               
            </div>
        </div>
        <div class="card card-header">
            <div class="row form-group">
                <input id="accessLevel" name="accessLevel"  class="form-control"  value="{{Auth::user()->accessLevel}}" type="hidden" readonly>
                <input id="uid" name="uid"  class="form-control"  value="{{Auth::user()->id}}" type="hidden" readonly>
                <div class="col-md-3">
                    <select class="form-select" id="negara" name="negara">
                        <option value="-1">--Semua Negara--</option>
                        @foreach ($nations as $nation)
                        @if ( $nation->id == old('negara') )
                        <option value="{{ $nation->id }}" selected>{{ $nation->name }} - {{ $nation->registration }}</option>
                        @else
                        <option value="{{ $nation->id }}">{{ $nation->name }} - {{ $nation->registration }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 year')))}}" > 
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}" >
                    </div>
                </div>
                <div class="col-md-1">
                    <button onclick="myFunction()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Filter"><i class="fas fa-search"></i>
                    </button>
                </div>
            </div> 
        </div>
        <div class="card card-body">
            <div class="row form-inline">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr style="font-size: 12px;">
                            <th>Perusahaan</th>
                            <th>No Surat</th>
                            <th>Tanggal</th>
                            <th>Act</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:14px">
                    </tbody>
                </table>                
            </div>
        </div>
    </div>    
</div>
</div>
</div>
</body>
@endsection