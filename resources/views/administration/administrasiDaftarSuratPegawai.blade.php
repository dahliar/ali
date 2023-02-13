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
        var employeeId = {!! json_encode($employee->employeeId) !!};
        $('#datatable').DataTable({
            ajax:'{{ url("getAllEmployeePaper") }}'+"/"+employeeId,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "30%", "targets":  [1], "className": "text-left"   },
                {   "width": "15%", "targets":  [2], "className": "text-center"   },
                {   "width": "15%", "targets":  [3], "className": "text-center" },
                {   "width": "15%", "targets":  [4], "className": "text-center" },
                {   "width": "15%", "targets":  [5], "className": "text-center" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
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
                        <li class="breadcrumb-item active">Administrasi Surat</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-1">
                    <input id="employeeId" name="employeeId" type="hidden" class="form-control text-left" value="{{old('name', $employee->employeeId)}}" readonly>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Nama</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input id="name" name="name" type="text" class="form-control text-left" value="{{$employee->name}}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Nomor Induk Kependudukan</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input id="nik" name="nik" type="text" class="form-control text-left" value="{{$employee->nik}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Nomor Induk Pegawai</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input id="nip" name="nip" type="text" class="form-control text-left" value="{{$employee->nip}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Telepon</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input id="phone" name="phone" type="text" class="form-control text-left" value="{{$employee->phone}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Alamat</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <textarea id="address" name="address" rows="4" cols="100" disabled>{{$employee->address}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Lama bekerja</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input id="lamaKerja" name="lamaKerja" type="text" class="form-control text-left" value="{{$employee->startdate}} - {{$employee->lamaKerja}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Penempatan</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input id="orgstructure" name="orgstructure" type="text" class="form-control text-left" value="{{$employee->structuralPosition}} - {{$employee->orgstructure}} - {{$employee->workPosition}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Jenis Penggajian*</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input id="jenisPenggajian" name="jenisPenggajian" type="text" class="form-control text-left" value="{{$employee->jenisPenggajian}}" disabled>
                            </div>
                        </div>
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
                                <th>Awal surat</th>
                                <th>Akhir surat</th>
                                <th>Sisa masa berlaku</th>
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