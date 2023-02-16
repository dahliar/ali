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

    function tambahItem(id){
        window.open(('{{ url("detailtransactionList") }}'+"/"+id), '_blank');
    }
    function editTransaksi(id){
        window.open(('{{ url("transactionEdit") }}'+"/"+id), '_self');
    }
    function tambahTransaksi(){
        window.open(('{{ url("transactionAdd") }}'), '_self');
    }
    function documentList(id){
        window.open(('{{ url("transactionDocument") }}'+"/"+id), '_blank');
    }



    function cetakPI(id){
        window.open(('{{ url("transaction/pi") }}'+"/"+id), '_blank');
    }
    function cetakIPL(id){
        window.open(('{{ url("transaction/ipl") }}'+"/"+id), '_blank');
    }

    function myFunction(){
        var e = document.getElementById("negara");
        var negara = e.options[e.selectedIndex].value;       
        //var companyName = e.options[e.selectedIndex].text;
        //var e = document.getElementById("jenis");
        //var jenis = e.options[e.selectedIndex].value;       

        var e = document.getElementById("statusTransaksi");
        var statusTransaksi = e.options[e.selectedIndex].value;       

        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;


        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getAllExportTransaction") }}',
                data: function (d){
                    d.negara = negara,
                    //d.jenis = jenis,
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
                {   "width": "7%", "targets":   [0], "className": "text-left" },
                {   "width": "25%", "targets":  [1], "className": "text-left"   },
                {   "width": "10%", "targets":  [2], "className": "text-left" },
                {   "width": "18%", "targets":  [3], "className": "text-left" },
                {   "width": "18%", "targets":  [4], "className": "text-left" },
                {   "width": "7%", "targets":   [5], "className": "text-left" },
                {   "width": "15%", "targets":  [6], "className": "text-left" }
                ], 

            columns: [
                {data: 'SrNo',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {data: 'name', name: 'name'},
            {data: 'nation', name: 'nation'},
            {data: 'number', name: 'number'},
            {data: 'tanggal', name: 'tanggal'},
            {data: 'status', name: 'status'},
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
                        <li class="breadcrumb-item active">Transactions</li>
                    </ol>
                </nav>
                <button onclick="tambahTransaksi()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Transaksi">
                    <i class="fa fa-plus"></i> Transaksi
                </button>
            </div>
        </div>
        <div class="card card-header">
            <div class="row form-group">
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
                    <!--
                    <div class="col-md-2">
                        <select class="form-select" id="jenis" name="jenis" >
                        <option value="-1" selected>--Semua Jenis--</option>
                        <option value="1" @if(old('jenis') == 1) selected @endif>Internal</option>
                        <option value="2" @if(old('jenis') == 2) selected @endif>Undername</option>
                        </select>
                    </div>
                -->
                <div class="col-md-2">
                    <select class="form-select" id="statusTransaksi" name="statusTransaksi" >
                        <option value="-1">--Semua Status--</option>
                        <option value="1" @if(old('statusTransaksi') == 1) selected @endif selected>Offering</option>
                        <option value="4" @if(old('statusTransaksi') == 4) selected @endif>Sailing</option>
                        <option value="2" @if(old('statusTransaksi') == 2) selected @endif>Finished</option>
                        <option value="3" @if(old('statusTransaksi') == 3) selected @endif>Canceled</option>
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
                            <th>No</th>
                            <th>Perusahaan</th>
                            <th>Negara</th>
                            <th>No Surat</th>
                            <th>Tanggal</th>
                            <th>Status</th>
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