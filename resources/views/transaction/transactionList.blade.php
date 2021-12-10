@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::check() and (Auth::user()->isAdmin() or Auth::user()->isMarketing()))
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
        window.open(('{{ url("transactionEdit") }}'+"/"+id), '_blank');
    }
    function tambahTransaksi(){
        window.open(('{{ url("transactionAdd") }}'), '_blank');
    }

    function cetakPI(id){
        window.open(('{{ url("transaction/pi") }}'+"/"+id), '_blank');
    }
    function cetakIPL(id){
        window.open(('{{ url("transaction/ipl") }}'+"/"+id), '_blank');
    }

    function myFunction(){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllTransaction") }}',
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
            {   "width": "15%", "targets":  [4], "className": "text-left" },
            {   "width": "15%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-center" },
            {   "width": "15%", "targets":  [7], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nation', name: 'nation'},
            {data: 'nosurat', name: 'nosurat'},
            {data: 'etd', name: 'etd'},
            {data: 'eta', name: 'eta'},
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
                        <li class="breadcrumb-item active">Transactions</li>
                    </ol>
                </nav>
                <button onclick="tambahTransaksi()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Transaksi"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="card-body">
                        <table class="table cell-border stripe hover row-border data-table"  id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Perusahaan</th>
                                    <th>Negara</th>
                                    <th>No Surat</th>
                                    <th>ETD</th>
                                    <th>ETA</th>
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