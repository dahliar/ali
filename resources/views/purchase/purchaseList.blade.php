@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::user()->isAdmin() or Auth::user()->isProduction())
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function purchaseAdd(){
        window.open(('{{ url("purchaseAdd") }}'), '_self');
    }
    function purchaseItems(id){
        window.open(('{{ url("purchaseItems") }}'+"/"+id), '_blank');
    }
    function purchaseEdit(id){
        window.open(('{{ url("transactionEdit") }}'+"/"+id), '_self');
    }
    function purchaseInvoice(id){
        window.open(('{{ url("purchase/notaPembelian") }}'+"/"+id), '_self');
    }

    function refreshTableTransactionList(){
        var e = document.getElementById("negara");
        var negara = e.options[e.selectedIndex].value;       
        //var companyName = e.options[e.selectedIndex].text;
        var e = document.getElementById("jenis");
        var jenis = e.options[e.selectedIndex].value;       

        var e = document.getElementById("statusTransaksi");
        var statusTransaksi = e.options[e.selectedIndex].value;       

        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;


        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ route("getAllPurchases") }}',
            dataType: 'json',
            data: {
                negara : negara,
                jenis: jenis,
                statusTransaksi : statusTransaksi,
                start : start,
                end : end
            },
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "4%",  "targets":  [0], "className": "text-center" },
            {   "width": "21%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%",  "targets": [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "10%", "targets":  [4], "className": "text-left" },
            {   "width": "10%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-left" },
            {   "width": "10%", "targets":  [7], "className": "text-center" },
            {   "width": "15%", "targets":  [8], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nation', name: 'nation'},
            {data: 'nosurat', name: 'nosurat'},
            {data: 'tanggalInput', name: 'tanggalInput'},
            {data: 'tanggaltransaksi', name: 'tanggaltransaksi'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }

    function myFunction(){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getPurchaseList") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "4%",  "targets":  [0], "className": "text-center" },
            {   "width": "21%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%",  "targets": [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "10%", "targets":  [4], "className": "text-left" },
            {   "width": "10%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-left" },
            {   "width": "10%", "targets":  [7], "className": "text-center" },
            {   "width": "15%", "targets":  [8], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nation', name: 'nation'},
            {data: 'nosurat', name: 'nosurat'},
            {data: 'arrivaldate', name: 'arrivaldate'},
            {data: 'purchasedate', name: 'purchasedate'},
            {data: 'paymentAmount', name: 'paymentAmount'},
            {data: 'status', name: 'status'},
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
<body onload="myFunction(0)">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Purchase</li>
                    </ol>
                </nav>
                
            </div>

            <div class="modal-body">
                <div class="row">
                    <p>
                        <button onclick="purchaseAdd()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Add purchase">
                            <i class="fa fa-plus" style="font-size:20px"></i> Purchase
                        </button>
                        <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapsePart" role="button" aria-expanded="false" aria-controls="collapsePart">
                            <i class="fas fa-filter" style="font-size:20px"></i> Purchase List
                        </a>
                    </p>

                    <div class="collapse" id="collapsePart">
                        <div class="card card-body">
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <span class="label">Negara</span>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="negara" name="negara">
                                        <option value="-1">--Pilih Negara--</option>
                                        @foreach ($nations as $nation)
                                        @if ( $nation->id == old('negara') )
                                        <option value="{{ $nation->id }}" selected>{{ $nation->name }} - {{ $nation->registration }}</option>
                                        @else
                                        <option value="{{ $nation->id }}">{{ $nation->name }} - {{ $nation->registration }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>                             
                            <div class="row form-group">
                                <div class="col-md-3">
                                    <span class="label">Status Transaksi</span>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="statusTransaksi" name="statusTransaksi" >
                                        <option value="-1" selected>--Pilih Status--</option>
                                        <option value="1" @if(old('statusTransaksi') == 1) selected @endif>On Progress</option>
                                        <option value="2" @if(old('statusTransaksi') == 2) selected @endif>Finished</option>
                                        <option value="3" @if(old('statusTransaksi') == 2) selected @endif>Canceled</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="row form-group">
                                <div class="col-md-3 ">
                                    <span class="label">Rentang Waktu</span>
                                </div>
                                <div class="col-md-8 row form-group">
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 year')))}}" > 
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="display: flex;
                                    justify-content: center;
                                    align-items: center;">
                                    <span>
                                        <i class="fas fa-arrows-alt-h"></i>
                                    </span>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}" >
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-3">
                                <span class="label"></span>
                            </div>
                            <div class="col-md-4">
                                <button onclick="refreshTableTransactionList()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Filter"><i class="fas fa-search-plus">Search</i>
                                </button>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>


            <div class="row form-inline">
                <div class="card-body">
                    <table class="table cell-border stripe hover row-border data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Perusahaan</th>
                                <th>Negara</th>
                                <th>No Surat</th>
                                <th>Arrival</th>
                                <th>Purchase</th>
                                <th>Payment</th>
                                <th>Status</th>
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
</div>
</div>
</body>
@else
@include('partial.noAccess')
@endif

@endsection