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

    function hapusBorongan(id){
        Swal.fire({
            title: 'Yakin menghapus?',
            text: "Data akan hilang!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("boronganDeleteRecord") }}'+"/"+id,
                    type: "GET",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        id : id
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Deleted!',
                            'Record borongan telah dihapus.',
                            'success'
                            );
                        myFunction();
                    }
                });
            }
        })
    }

    function myFunction(){
        var e = document.getElementById("status");
        var status = e.options[e.selectedIndex].value;       

        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getBorongans") }}',
                data: function (d){
                    d.status = status;
                    d.start = start;
                    d.end = end;
                }
            },
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "20%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%", "targets":  [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-end" },
            {   "width": "10%", "targets":  [4], "className": "text-end" },
            {   "width": "5%", "targets":  [5], "className": "text-end" },
            {   "width": "7%", "targets":  [6], "className": "text-end" },
            {   "width": "8%", "targets":  [7], "className": "text-end" },
            {   "width": "15%", "targets":  [8], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'tanggalKerja', name: 'tanggalKerja'},
            {data: 'hargaSatuan', name: 'hargaSatuan'},
            {data: 'netweight', name: 'netweight'},
            {data: 'worker', name: 'worker'},
            {data: 'countIsPaid', name: 'countIsPaid'},
            {data: 'statusText', name: 'statusText'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }
</script>

@if (session('status'))
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            {{ session('status') }}
        </div>
        <div class="col-md-1 text-center">
            <span class="label"><strong >x</strong></span>
        </div>
    </div>
</div>
@endif
<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-9">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Presensi Borongan</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-3 text-end">
                    <button onclick="location.href='{{ url('boronganCreate') }}'" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Borongan"><i class="fa fa-plus" style="font-size:20px"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="row form-group">                        
                        <div class="col-md-3">
                            <select class="form-select" id="status" name="status" >
                                <option value="-1" selected>--Semua Status Transaksi--</option>
                                <option value="0" @if(old('statusTransaksi') == 1) selected @endif>Tambah Pekerja</option>
                                <option value="1" @if(old('statusTransaksi') == 1) selected @endif>Generate Data</option>
                                <option value="2" @if(old('statusTransaksi') == 2) selected @endif>Pembayaran</option>
                                <option value="3" @if(old('statusTransaksi') == 2) selected @endif>Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 week')))}}" > 
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}" >
                        </div>
                        <div class="col-md-2">
                            <button onclick="myFunction()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Filter">Cari
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Harga/Kg</th>
                                <th>Berat</th>
                                <th>Pekerja</th>
                                <th>Bayar</th>
                                <th>Tahap</th>
                                <th>Act</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>                
                </div>
            </div>    
        </div>
    </div>
</body>
@endsection