<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data item berhasil ditambahkan", "info");
</script>
@endif

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function myFunction(){
        Swal.fire({
            title: 'Buat surat penugasan?',
            text: "Pembuatan surat penugasan dinas",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Buat saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Surat dibuat',
                    text: "Pembuatan surat penugasan",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok dibuat.'
                }).then((result) => {
                    document.getElementById("formSuratPerjalanan").submit();
                })
            } else {
                Swal.fire(
                    'Batal dibuat!',
                    "Pembuatan surat penugasan dinas",
                    'info'
                    );
            }
        })
    };
</script>


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


<div class="container-fluid">
    <div class="row">
        <form id="formSuratPerjalanan" action="{{url('administrasiSuratPerjalananDinasStore')}}" method="post" name="formSuratPerjalanan">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('administrasi')}}">Administrasi</a>
                            </li>
                            <li class="breadcrumb-item active">Surat Perjalanan Dinas</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <input id="employeeId" name="employeeId" type="hidden" class="form-control text-left" value="{{old('employeeId', $employeeId)}}" readonly>
                        <input id="paperworkTypeId" name="paperworkTypeId" type="hidden" class="form-control text-left" value="{{old('paperworkTypeId', $paperworkTypeId)}}" readonly>
                        <input id="masaBerlaku" name="masaBerlaku" type="hidden" class="form-control text-left" value="{{old('masaBerlaku', $masaBerlaku)}}" readonly>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nama</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="name" name="name" type="text" class="form-control text-left" value="{{$name}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nomor Induk Kependudukan</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="nik" name="nik" type="text" class="form-control text-left" value="{{$nik}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nomor Induk Pegawai</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="nip" name="nip" type="text" class="form-control text-left" value="{{$nip}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Jabatan</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="jabatan" name="jabatan" type="text" class="form-control text-left" value="{{$jabatan}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Penempatan</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="orgStructure" name="orgStructure" type="text" class="form-control text-left" value="{{$orgStructure}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Bagian*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="workPosition" name="workPosition" type="text" class="form-control text-left" value="{{$workPosition}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Ditujukan Kepada</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="kepada" name="kepada" class="form-control" rows="4" cols="100" value="{{old('kepada')}}">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Kegiatan</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="kegiatan" name="kegiatan" class="form-control" rows="4" cols="100" value="{{old('kegiatan')}}">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Tanggal Mulai</span>
                            </div>
                            <div class="col-md-4">
                                <input id="startdate" name="startdate" type="date" class="form-control" value="{{old('startdate')}}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Tanggal Selesai</span>
                            </div>
                            <div class="col-md-4">
                                <input id="enddate" name="enddate" type="date" class="form-control" value="{{old('enddate')}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Buat surat</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection