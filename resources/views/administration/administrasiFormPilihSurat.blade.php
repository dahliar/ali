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
        <form id="formPemilihanSurat" action="{{url('administrasiSuratStore')}}" method="post" name="formPemilihanSurat">
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
                            <li class="breadcrumb-item active">Pilih Surat</li>
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
                                <span class="label">Tanggal mulai bekerja</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="startdate" name="startdate" type="text" class="form-control text-left" value="{{$employee->startdate}}" disabled>
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
                                    <input id="lamaKerja" name="lamaKerja" type="text" class="form-control text-left" value="{{$employee->lamaKerja}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Status kepegawaian</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="statusKepegawaian" name="statusKepegawaian" type="text" class="form-control text-left" value="{{$employee->statusKepegawaian}}" disabled>
                                </div>
                            </div>
                        </div>


                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Jabatan</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="jabatan" name="jabatan" type="text" class="form-control text-left" value="{{$employee->structuralPosition}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Penempatan</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="orgstructure" name="orgstructure" type="text" class="form-control text-left" value="{{$employee->orgstructure}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Bagian*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="workPosition" name="workPosition" type="text" class="form-control text-left" value="{{$employee->workPosition}}" disabled>
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
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Pilih Surat*</span>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select w-100" id="paper" name="paper">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($papers as $paper)
                                    @if ( $paper->id == old('paper') )
                                    <option value="{{ $paper->id }}" selected>{{ $paper->name }}</option>
                                    @else
                                    <option value="{{ $paper->id }}">{{ $paper->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button id="buttonSubmit" type="submit" class="btn btn-primary">Save</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection