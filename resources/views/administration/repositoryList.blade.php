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

    function getFileDownload(filename){
        window.open(('{{ url("getDocumentFileDownload") }}'+"/"+filename), '_self');
    };

    function tambahDocument(){
        window.open(('{{ url("documentRepositoryAdd") }}'), '_self');
    }

    function myFunction(){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllDocuments") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "20%", "targets":  [1], "className": "text-left"   },
                {   "width": "30%", "targets":  [2], "className": "text-left"   },
                {   "width": "5%", "targets":   [3], "className": "text-center" },
                {   "width": "15%", "targets":  [4], "className": "text-left" },
                {   "width": "5%", "targets":  [5], "className": "text-center" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'status', name: 'status'},
                {data: 'uploader', name: 'uploader'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
        });
    }

    $(document).ready(function() {
        myFunction();
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

<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-6 text-end">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Administrasi Surat</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-end">
                    <button onclick="tambahDocument()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Pegawai"><i class="fa fa-plus" style="font-size:20px"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dokumen</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Uploader</th>
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