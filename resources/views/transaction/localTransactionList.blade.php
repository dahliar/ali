@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if ( (Auth::user()->isProduction() or Auth::user()->isMarketing() or Auth::user()->isAdmin() ) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
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
        window.open(('{{ url("localTransactionEdit") }}'+"/"+id), '_self');
    }
    function tambahTransaksi(){
        window.open(('{{ url("localTransactionAdd") }}'), '_self');
    }

    function cetakPI(id){
        window.open(('{{ url("transaction/pi") }}'+"/"+id), '_blank');
    }
    function cetakIPL(id){
        window.open(('{{ url("transaction/localIpl") }}'+"/"+id), '_blank');
    }

    function myFunction(){
        var e = document.getElementById("statusTransaksi");
        var statusTransaksi = e.options[e.selectedIndex].value;       

        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;


        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getAllLocalTransaction") }}',
                data: function (data){
                    data.statusTransaksi = statusTransaksi,
                    data.start = start,
                    data.end = end
                }
            },
            dataType: 'json',            
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "10%", "targets":   [0], "className": "text-left" },
            {   "width": "30%", "targets":  [1], "className": "text-left" },
            {   "width": "15%",  "targets": [2], "className": "text-left" },
            {   "width": "15%",  "targets": [3], "className": "text-left" },
            {   "width": "15%", "targets":  [4], "className": "text-left" },
            {   "width": "15%", "targets":  [5], "className": "text-left" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'invnum', name: 'invnum'},
            {data: 'td', name: 'td'},
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
            @if (session('listBarang'))
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
<body onload="myFunction()">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Transaksi Lokal</li>
                    </ol>
                </nav>
                <button onclick="tambahTransaksi()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Transaksi">
                    <i class="fa fa-plus" style="font-size:20px"></i> Transaksi
                </button>
            </div>
            <br>
            <div class="row form-inline">
                <div class="row form-group">                    
                    <div class="col-md-2">
                        <select class="form-select" id="statusTransaksi" name="statusTransaksi" >
                            <option value="-1" selected>--Semua Status--</option>
                            <option value="1" @if(old('statusTransaksi') == 1) selected @endif>On Progress</option>
                            <option value="2" @if(old('statusTransaksi') == 2) selected @endif>Finished</option>
                            <option value="3" @if(old('statusTransaksi') == 2) selected @endif>Canceled</option>
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
                        <button onclick="myFunction()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Filter"><i class="fas fa-search-plus">Search</i>
                        </button>
                    </div>
                </div> 
            </div>
            <div class="row form-inline">
                <div class="card-body">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr style="font-size: 12px;">
                                <th>No</th>
                                <th>Perusahaan</th>
                                <th>No Surat</th>
                                <th>Tanggal Transaksi</th>
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