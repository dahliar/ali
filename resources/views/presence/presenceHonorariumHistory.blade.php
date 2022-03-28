<!--BELUM-->
@php
$pageId = 71;
@endphp

@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    function myFunction(){
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getPresenceHonorariumHistory") }}'+"/"+start+"/"+end,
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "15%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%", "targets":  [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "10%", "targets":  [4], "className": "text-left" },
            {   "width": "7%", "targets":  [5], "className": "text-left" },
            {   "width": "9%", "targets":  [6], "className": "text-left" },
            {   "width": "7%", "targets":  [7], "className": "text-left" },
            {   "width": "7%", "targets":  [8], "className": "text-left" },
            {   "width": "20%", "targets":  [9], "className": "text-left" },
            {   "width": "5%", "targets":  [10], "className": "text-left" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nip', name: 'nip'},
            {data: 'orgStructure', name: 'orgStructure'},
            {data: 'bagian', name: 'bagian'},
            {data: 'tanggalKerja', name: 'tanggalKerja'},
            {data: 'jumlah', name: 'jumlah'},
            {data: 'isGenerated', name: 'isGenerated'},
            {data: 'isPaid', name: 'isPaid'},
            {data: 'keterangan', name: 'keterangan'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }

    $(document).ready(function() {
    });
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
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ ('presenceHarianList')}}">Presensi</a>
                            </li>
                            <li class="breadcrumb-item active">Arsip Honorarium Seluruh Pegawai</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10 text-end">
                        <div class="card card-body">
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span name="spanAm" id="spanAm" class="input-group-text">Start</span>
                                        <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 month')))}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span name="spanAm" id="spanAm" class="input-group-text">End</span>
                                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button onclick="myFunction()" class="btn btn-primary">
                                        Search
                                    </button>
                                </div>
                            </div>
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
                                <th>NIP</th>
                                <th>Posisi</th>
                                <th>Bagian</th>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Generate</th>
                                <th>Bayar</th>
                                <th>Keterangan</th>
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
@else
@include('partial.noAccess')
@endif

@endsection