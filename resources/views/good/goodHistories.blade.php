@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js" type="text/javascript" ></script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function myFunction(){
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        var isChecked = document.querySelector('input[name="showData"]:checked').value;
        $('#datatable').DataTable({
            ajax:'{{ url("getGoodHistories") }}'+"/"+isChecked+"/"+start+"/"+end,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "15%",  "targets": [1], "className": "text-left" },
                {   "width": "5%", "targets":  [2], "className": "text-center" },
                {   "width": "10%", "targets":  [3], "className": "text-left" },
                {   "width": "35%", "targets":  [4], "className": "text-left" },
                {   "width": "15%", "targets":  [5], "className": "text-left" },
                {   "width": "15%", "targets":  [6], "className": "text-center" }
            ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'jenis', name: 'jenis'},
                {data: 'jumlah', name: 'jumlah'},
                {data: 'info', name: 'info'},
                {data: 'user', name: 'user'},
                {data: 'waktu', name: 'waktu'}
            ]
        });
    }
    $(document).ready(function() {
       // myFunction();
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
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Barang-Barang Produksi</li>
                        <li class="breadcrumb-item active">Riwayat Perubahan</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="d-grid">
                        <div class="form-check form-check-inline">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="showData" id="r2" value="2" checked>
                                <label class="form-check-label" for="inlineRadio2">Pengurangan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="showData" id="r3" value="1">
                                <label class="form-check-label" for="inlineRadio3">Penambahan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="showData" id="r1" value="all">
                                <label class="form-check-label" for="inlineRadio1">Semua</label>
                            </div>
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
                        </div>

                    </div>
                    <div class="d-grid d-md-flex">
                        <button id="buttonShow" type="submit" class="btn btn-primary" onclick="myFunction()">Tampilkan Data</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="col-12">
                        <div class="card-body">
                            <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jenis</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
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