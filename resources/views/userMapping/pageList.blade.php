<meta name="_token" content="{{ csrf_token() }}">
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function tambahPage(id){
        window.open(('{{ url("pageAdd") }}'+"/"+id), '_self');        
    }
    function pemetaanPage(id){
        window.open(('{{ url("pageMapping") }}'+"/"+id), '_self');        
    }

    function editAplikasi($id){
        alert("ke laman edit page dengan id : "+$id);
    }

    function myFunction($appid){
        $('#datatable').DataTable({
            ajax:'{{ url("getPageList")}}' + "/"+ $appid,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'post',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets": [0], "className": "text-center" },
            {   "width": "15%", "targets": [1], "className": "text-left" },
            {   "width": "25%", "targets": [2], "className": "text-left" },
            {   "width": "25%", "targets": [3], "className": "text-left" },
            {   "width": "5%", "targets": [4], "className": "text-center" },
            {   "width": "5%", "targets": [5], "className": "text-center" },
            {   "width": "5%", "targets": [6], "className": "text-center" },
            {   "width": "5%", "targets": [7], "className": "text-center" },
            {   "width": "10%", "targets": [8], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'appName', name: 'appName'},
            {data: 'name', name: 'name'},
            {data: 'route', name: 'route'},
            {data: 'id', name: 'id'},
            {data: 'level', name: 'level'},
            {data: 'icon', name: 'icon'},
            {data: 'isActive', name: 'isActive'},
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

<body onload="myFunction({{$application->id}})">
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Daftar Page Aplikasi {{$application->name}}</li>
                    </ol>
                </nav>
                <button class="btn btn-primary" onclick="tambahPage({{$application->id}})" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Aplikasi"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table" id="datatable" style="font-size:14px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Aplikasi</th>
                                <th>Name</th>
                                <th>Route</th>
                                <th>ID</th>
                                <th>Level</th>
                                <th>Icon</th>
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
</body>
@endsection