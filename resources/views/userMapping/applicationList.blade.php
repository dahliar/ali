<meta name="_token" content="{{ csrf_token() }}">
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

    function editAplikasi($id){
        alert("Akses langsung");
    }
    function kelolaPages($id){
        window.open(('{{ url("pageList") }}'+"/"+$id), '_self');
    }

    function myFunction(){
        $('#datatable').DataTable({
            ajax:'{{ url("getApplicationList") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'post',
            destroy:true,
            columnDefs: [
            {   "width": "10%",  "targets": [0], "className": "text-center" },
            {   "width": "40%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%", "targets":  [2], "className": "text-center" },
            {   "width": "10%", "targets":  [3], "className": "text-center" },
            {   "width": "10%", "targets":  [4], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'jumlahPage', name: 'jumlahPage'},
            {data: 'isActive', name: 'isActive'},
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

<body onload="myFunction()">
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Pemetaan Aplikasi Pengguna</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table" id="datatable" style="font-size:14px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jumlah page</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>                
                </div>
            </div>    
        </div>
    </div>
</body>
@endsection