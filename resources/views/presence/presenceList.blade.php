@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if (Auth::user()->isAdmin())
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function tambahPresensiBatchInput(){
        window.open(('{{ url("presenceAddBatch") }}'), '_self');
    }
    function tambahPresensiImport(){
        window.open(('{{ url("presenceAddImport") }}'), '_self');
    }

    function presenceHistory(id){
        window.open(('{{ url("presenceHistory") }}'+"/"+id), '_blank');
    }

    
    function myFunction(){
        var presenceDate = document.getElementById("presenceDate").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getAllEmployeesForPresence") }}'+"/"+presenceDate,
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "25%", "targets":  [1], "className": "text-left"   },
            {   "width": "15%", "targets":  [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "15%", "targets":  [4], "className": "text-left" },
            {   "width": "10%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-left" },
            {   "width": "10%", "targets":  [7], "className": "text-left" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nik', name: 'nik'},
            {data: 'jenisPenggajian', name: 'jenisPenggajian'},
            {data: 'orgStructure', name: 'orgStructure'},
            {data: 'jabatan', name: 'jabatan'},
            {data: 'bagian', name: 'bagian'},
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
                <div class="col-md-9">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Pegawai</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-3 text-end">
                    <button onclick="tambahPresensiBatchInput()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Presensi Satuan"><i class="fa fa-user-check" style="font-size:20px"></i>
                    </button>
                    <button onclick="tambahPresensiImport()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Presensi Import"><i class="fa fa-upload" style="font-size:20px"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-1">
                        <p>
                            <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapsePart" role="button" aria-expanded="false" aria-controls="collapsePart">
                                <i class="fas fa-filter" style="font-size:20px"></i>
                            </a>
                        </p>
                    </div>
                    <div class="col-md-10 text-end">
                        <div class="collapse" id="collapsePart">
                            <div class="card card-body">
                                <div class="row form-group">
                                    <div class="col-md-2 text-end">
                                        <button onclick="myFunction()" class="btn btn-primary">
                                            <i class="fas fa-search-plus">Search</i>
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span name="spanAm" id="spanAm" class="input-group-text">Presence Date</span>
                                            <input type="date" id="presenceDate" name="presenceDate" class="form-control text-end" value="{{ old('presenceDate', date('Y-m-d'))}}">
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table cell-border stripe hover row-border data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Jenis Karyawan</th>
                                <th>Posisi</th>
                                <th>Jabatan</th>
                                <th>Bagian</th>
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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

@else
@include('partial.noAccess')
@endif

@endsection