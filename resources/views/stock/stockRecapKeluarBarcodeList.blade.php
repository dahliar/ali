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

    $(document).ready(function() {
        myFunction(document.getElementById("transactionId").value,document.getElementById("loadingDate").value, document.getElementById("itemId").value);

    });
    function myFunction(transactionId, loadingDate, itemId){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getScanKeluarBarcodeList") }}',
                data: function (d){
                    d.transactionId = transactionId,
                    d.tanggal = loadingDate,
                    d.itemId = itemId
                }
            },
            dataType: 'json',            
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%", "targets":   [0], "className": "text-center" },
                {   "width": "20%", "targets":  [1], "className": "text-left"   },
                {   "width": "10%", "targets":  [2], "className": "text-center" },
                {   "width": "10%", "targets":  [3], "className": "text-center" },
                {   "width": "10%", "targets":  [4], "className": "text-center" },
                {   "width": "10%", "targets":  [5], "className": "text-center" },
                {   "width": "10%", "targets":  [6], "className": "text-center" },
                {   "width": "10%", "targets":  [7], "className": "text-center" }
                ], 

            columns: [
                {data: 'SrNo',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {data: 'fullcode', name: 'fullcode'},
            {data: 'status', name: 'status'},
            {data: 'productionDate', name: 'productionDate'},
            {data: 'packagingDate', name: 'packagingDate'},
            {data: 'storageDate', name: 'storageDate'},
            {data: 'loadingDate', name: 'loadingDate'},
            {data: 'expiringDate', name: 'expiringDate'}
            ]
        });
    }
</script>



@if(session('status'))
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            {{ session('status') }}
            @if(session('listBarang'))
            <ol>
                @foreach(session('listBarang') as $barang)
                <li>
                    {{$barang}}
                </li>
                @endforeach
            </ol>
            @endif
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
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('scanRekapMasuk') }}">Rekapitulasi Scan Keluar Transaksi</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-body">
            <div class="row form-inline">
                <div class="row form-group">
                    <div class="col-3 text-end my-auto">
                        <span class="label" id="spanLabel">No Transaksi</span>
                    </div>
                    <div class="col-3">
                        <input id="transactionNum" name="transactionNum"  class="form-control"  value="{{$transaction->transactionNum}}" readonly>
                    </div>
                </div> 
                <div class="row form-group">
                    <div class="col-3 text-end my-auto">
                        <span class="label" id="spanLabel">No Proforma Invoice</span>
                    </div>
                    <div class="col-3">
                        <input id="pinum" name="pinum"  class="form-control"  value="{{$transaction->pinum}}" readonly>
                    </div>
                </div> 
                <div class="row form-group">
                    <div class="col-3 text-end my-auto">
                        <span class="label" id="spanLabel">Buyer</span>
                    </div>
                    <div class="col-3">
                        <input id="companyName" name="companyName"  class="form-control"  value="{{$transaction->companyName}}" readonly>
                    </div>
                </div> 
                <div class="row form-group">
                    <div class="col-3 text-end my-auto">
                        <span class="label" id="spanLabel">Loading Date</span>
                    </div>
                    <div class="col-3">
                        <input id="loadingDate" name="loadingDate"  class="form-control"  value="{{$transaction->loadingDate}}" readonly>
                    </div>
                </div> 
                <div class="row form-group">
                    <div class="col-3 text-end my-auto">
                        <span class="label" id="spanLabel">Barang</span>
                    </div>
                    <div class="col-8">
                        <input id="itemName" name="itemName"  class="form-control"  value="{{$itemName}}" readonly>
                    </div>
                </div> 
            </div>
            <div class="row form-inline">
                <input type="hidden" id="transactionId" value="{{$transaction->id}}">
                <input type="hidden" id="itemId" value="{{$itemId}}">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr style="font-size: 12px;">
                            <th>No</th>
                            <th>Kode Barcode</th>
                            <th>Status</th>
                            <th>Produksi</th>
                            <th>Packing</th>
                            <th>Storing</th>
                            <th>Loading</th>
                            <th>Expire</th>
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