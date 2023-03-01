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
    function myFunction($id){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllBarcodeItemDetail") }}' + "/"+ $id,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "10%",  "targets":  [0], "className": "text-center" },
                {   "width": "40%", "targets":  [1], "className": "text-left" },
                {   "width": "10%", "targets":   [2], "className": "text-center" },
                {   "width": "20%", "targets":  [3], "className": "text-end" },
                {   "width": "20%", "targets":  [4], "className": "text-end" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'itemName', name: 'itemName'},
                {data: 'pshortname', name: 'pshortname'},
                {data: 'amount', name: 'amount'},
                {data: 'weight', name: 'weight'}
                ]
        });
    }

    $(document).ready(function() {
        myFunction({{ $transactionId }});
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
                <div class="col-md-8 text-end">

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('scanList') }}">Scan Transaction</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('scanList') }}">{{$transactionId}}</a>
                            </li>
                            <li class="breadcrumb-item active">Detil Transaksi Penjualan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="card card-body">
            <table class="table table-striped table-hover table-bordered data-table" id="datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Packing</th>
                        <th>Jumlah</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                </tbody>
            </table>                
        </div>
    </div>
</body>
@endsection
