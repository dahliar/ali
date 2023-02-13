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

    function buatSurat(id){
        window.open(('{{ url("administrasiFormPilihSurat") }}'+"/"+id), '_self');
    }
    function employeePaperList(id){
        window.open(('{{ url("employeePaperList") }}'+"/"+id), '_self');
    }
    function getFileDownload(filename){
        window.open(('{{ url("getAdministrationFileDownload") }}'+"/"+filename), '_self');
    };

    $(document).ready(function() {
        $('#datatable').DataTable({
            ajax:'{{ url("getAllPapers") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "20%", "targets":  [1], "className": "text-left"   },
                {   "width": "10%", "targets":  [2], "className": "text-left" },
                {   "width": "15%", "targets":  [3], "className": "text-left" },
                {   "width": "10%", "targets":  [4], "className": "text-center" },
                {   "width": "15%", "targets":  [5], "className": "text-center" },
                {   "width": "8%", "targets":  [6], "className": "text-center" },
                {   "width": "7%", "targets":  [7], "className": "text-center" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'empName', name: 'empName'},
                {data: 'empNik', name: 'empNik'},
                {data: 'name', name: 'name'},
                {data: 'startdate', name: 'startdate'},
                {data: 'enddate', name: 'enddate'},
                {data: 'hariMasaBerlaku', name: 'hariMasaBerlaku'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
        });
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
                <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Surat Seluruh Pegawai</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Surat</th>
                                <th>Awal surat</th>
                                <th>Akhir surat</th>
                                <th>Masa</th>
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