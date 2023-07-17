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

    function barcodeList(transactionId, loadingDate, itemId){
        window.open(('{{ url("scanRekapKeluarBarcodeList") }}'+"/"+transactionId+"/"+loadingDate+"/"+itemId), '_self');
    }
    $(document).ready(function() {
        myFunction({{json_encode($transaction->id)}}, document.getElementById("loadingDate").value);
    });
    function myFunction(id, loadingDate){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getScannedKeluarTransaksiHari") }}',
                data: function (d){
                    d.transactionId = id
                    d.tanggal = loadingDate
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
                {   "width": "50%", "targets":  [2], "className": "text-left" },
                {   "width": "10%", "targets":  [3], "className": "text-center" },
                {   "width": "15%", "targets":  [4], "className": "text-center" }
                ], 

            columns: [
                {data: 'SrNo',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {data: 'speciesName', name: 'speciesName'},
            {data: 'itemName', name: 'itemName'},
            {data: 'jumlahBarcode', name: 'jumlahBarcode'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
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
                            <a class="white-text" href="{{ url('scanRekapKeluar') }}">Rekapitulasi Scan Keluar Harian</a>
                        </li>
                        <li class="breadcrumb-item active">Tanggal {{$loadingDate}}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-body">
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
                    <input id="loadingDate" name="loadingDate"  class="form-control"  value="{{$loadingDate}}" readonly>
                </div>
            </div> 
            <div class="row form-inline">
                <input type="hidden" id="transactionId" value="{{$transaction->id}}">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr style="font-size: 12px;">
                            <th>No</th>
                            <th>Spesies</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
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