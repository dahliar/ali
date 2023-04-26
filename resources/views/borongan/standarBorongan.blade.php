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

<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function showBoronganList(){
        var jenis = document.getElementById("jenis").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax: '{{ url("getBoronganStandardList") }}'+"/"+jenis,
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "10%",  "targets":  [0], "className": "text-center" },
                {   "width": "30%", "targets":  [1], "className": "text-left" },
                {   "width": "20%", "targets":  [2], "className": "text-left" },
                {   "width": "10%", "targets":  [3], "className": "text-center" },
                {   "width": "10%", "targets":  [4], "className": "text-center" },
                {   "width": "20%", "targets":  [5], "className": "text-center" }
                ], 

            columns: [
                {data: 'DT_RowIndex',   name: 'DT_RowIndex'},
                {data: 'nama',       name: 'nama'},
                {data: 'jenisTeks',     name: 'jenisTeks'},
                {data: 'harga',  name: 'harga'},
                {data: 'status',  name: 'status'},
                {data: 'action',        name: 'action', orderable: false, searchable: false}
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
@if ($errors->any())
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-1 text-center">
            <span class="label"><strong >x</strong></span>
        </div>
    </div>
</div>
@endif

<body class="container-fluid">
    <div class="modal-content">
        <div class="modal-header">
            <div class="col-md-6 text-end">
                <nav aria-label="breadcrumb" class="navbar navbar-expand-xs navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Pengaturan Standar Honor Borongan</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end">
                <button onclick="window.location='{{ url('standarBoronganTambah')}}'" target="_blank" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Standar Gaji Borongan">
                    <i class="fa fa-plus"></i> Standar Harga
                </button>
            </div>  
        </div>
        @csrf
        <div class="modal-header">
            <div class="row form-group">
                <div class="col-md-8">
                    <select class="form-select" id="jenis" name="jenis" >
                        <option value="-1" selected>--Semua Status--</option>
                        @foreach ($types as $type)
                        @if ( $type->id == old('jenis') )
                        <option value="{{ $type->id }}" selected>{{ $type->nama }}</option>
                        @else
                        <option value="{{ $type->id }}">{{ $type->nama }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" onclick="showBoronganList()" class="btn btn-primary">Tampilkan</button> 
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="row form-inline">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Harga/Kg</th>
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
</body>
@endsection