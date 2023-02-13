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

    $(document).ready(function() {
        $('#datatable').DataTable({
            ajax:'{{ url("getOpnameData") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            pageLength: 50,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "5%",  "targets":  [1], "className": "text-center" },
                {   "width": "30%", "targets":  [2], "className": "text-left" },
                {   "width": "15%", "targets":  [3], "className": "text-end" },
                {   "width": "12%", "targets":  [4], "className": "text-end" }
                ], 
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'id', name: 'id'},
                {data: 'itemName', name: 'itemName'},
                {data: 'jumlahPacked', name: 'jumlahPacked'},
                {data: 'amount', name: 'amount'}
                ]
        });
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
        <div class="modal-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb primary-color my-auto">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Stock Opname per-Barang</li>
                </ol>
            </nav>
            <a href="{{ url('opnameImport')}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Export Data Stock Opname">Import</a>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Jumlah (MC/Karung)</th>
                            <th>Jumlah (Kg)</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px;">
                    </tbody>
                </table>                
            </div>
            <div class="card-footer">
                <ol>
                    <li>Loading : Jumlah barang yang saat ini dalam perjalanan ke buyer</li>
                    <li>Sailing adalah jumlah barang di storage dalam satuan Kilogram, hasil penjumlahan dari Packed + Unpacked</li>
                </ol>
            </div>
        </div>
    </div>
</body>
@endsection