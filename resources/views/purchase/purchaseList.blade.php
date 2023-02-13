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

<meta name="csrf-token" content="{{ csrf_token() }}">
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
        window.open(('{{ url("purchaseEdit") }}'+"/"+id), '_self');
    }

    function documentList(id){
        window.open(('{{ url("purchaseDocument") }}'+"/"+id), '_blank');
    }

    function myFunction(){
        var e = document.getElementById("negara");
        var negara = e.options[e.selectedIndex].value;       

        var e = document.getElementById("statusTransaksi");
        var statusTransaksi = e.options[e.selectedIndex].value;       

        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;

        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax: '{{ url("getPurchaseList") }}' + "/"+ negara + "/"+ statusTransaksi + "/"+ start + "/"+ end,
            dataType: 'json',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "3%",  "targets":  [0], "className": "text-center" },
            {   "width": "20%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%", "targets":  [2], "className": "text-left" },
            {   "width": "20%", "targets":  [3], "className": "text-left" },
            {   "width": "8%",  "targets":  [4], "className": "text-end" },
            {   "width": "12%", "targets":  [5], "className": "text-end" },
            {   "width": "10%", "targets":  [6], "className": "text-left" },
            {   "width": "15%", "targets":  [7], "className": "text-left" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nation', name: 'nation'},
            {data: 'nosurat', name: 'nosurat'},
            {data: 'arrivaldate', name: 'arrivaldate'},
            {data: 'paymentAmount', name: 'paymentAmount'},
            {data: 'status', name: 'status'},
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
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Transaksi Pembelian</li>
                    </ol>
                </nav>
                <button onclick="purchaseAdd()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Pembelian">
                    <i class="fa fa-plus" style="font-size:20px"></i> Pembelian
                </button>
            </div>
        </div>

        <div class="card card-header">
            <div class="row form-group">
                <div class="col-md-4">
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
                <div class="col-md-3">
                    <select class="form-select" id="statusTransaksi" name="statusTransaksi" >
                        <option value="-1" selected>--Semua Status Transaksi--</option>
                        <option value="1" @if(old('statusTransaksi') == 1) selected @endif selected>On Progress</option>
                        <option value="2" @if(old('statusTransaksi') == 2) selected @endif>Selesai</option>
                        <option value="3" @if(old('statusTransaksi') == 2) selected @endif>Batal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 month')))}}" > 
                </div>
                <div class="col-md-2">
                    <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}" >
                </div>
                <div class="col-md-1">
                    <button onclick="myFunction()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Filter"><i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card card-body">
            <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Perusahaan</th>
                        <th>Negara</th>
                        <th>No Surat</th>
                        <th>Datang</th>
                        <th>Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size:14px">
                </tbody>
            </table>                
        </div>
    </div>
</body>
@endsection