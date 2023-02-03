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

<script type="text/javascript">
    function myFunction(){
        var x = document.getElementById("paperworkTypeId");
        var jenisSurat = x.options[x.selectedIndex].text;
        var val = x.options[x.selectedIndex].value;
        if(val == 0){
            Swal.fire(
                'Pilih jenis surat dulu',
                "Pembuatan surat administrasi",
                'info'
                );

        } else if (val == 1){
            Swal.fire({
                title: 'Buat '+jenisSurat,
                text: "Pembuatan surat administrasi",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan saja.'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Ok '+jenisSurat+' dibuatkan',
                        text: "Pembuatan surat administrasi",
                        icon: 'info',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok disimpan.'
                    }).then((result) => {
                        document.getElementById("formPemilihanSurat").submit();
                    })
                } else {
                    Swal.fire(
                        jenisSurat+' batal dibuat!',
                        "Pembuatan surat administrasi",
                        'info'
                        );
                }
            });
        } else{
            Swal.fire({
                title: 'Buat '+jenisSurat,
                text: "Pembuatan surat administrasi",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, buatkan saja.'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'ok, ke tahap selanjutnya',
                        text: "Pembuatan surat administrasi",
                        icon: 'info',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok.'
                    }).then((result) => {
                        document.getElementById("formPemilihanSurat").submit();
                    })
                } else {
                    Swal.fire(
                        jenisSurat+' batal dibuat!',
                        "Pembuatan surat administrasi",
                        'info'
                        );
                }
            });
        }
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
                        <input id="employeeId" name="employeeId" type="hidden" class="form-control text-left" value="{{old('employeeId', $employee->employeeId)}}" readonly>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nama</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="name" name="name" type="text" class="form-control text-left" value="{{old('name', $employee->name)}}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nomor Induk Kependudukan</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="nik" name="nik" type="text" class="form-control text-left" value="{{old('nik',$employee->nik)}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nomor Induk Pegawai</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="nip" name="nip" type="text" class="form-control text-left" value="{{old('nip',$employee->nip)}}" readonly>
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
                                    <input id="jabatan" name="jabatan" type="text" class="form-control text-left" value="{{old('jabatan', $employee->structuralPosition)}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Penempatan</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="orgstructure" name="orgstructure" type="text" class="form-control text-left" value="{{old('orgstructure', $employee->orgstructure)}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Bagian*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="workPosition" name="workPosition" type="text" class="form-control text-left" value="{{old('workPosition', $employee->workPosition)}}" readonly>
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
                                <select class="form-select w-100" id="paperworkTypeId" name="paperworkTypeId">
                                    <option value="0">--Choose One--</option>
                                    @foreach ($paperworkTypes as $paperwork)
                                    @if ( $paperwork->id == old('paperworkTypeId') )
                                    <option value="{{ $paperwork->id }}" selected>{{ $paperwork->name }}</option>
                                    @else
                                    <option value="{{ $paperwork->id }}">{{ $paperwork->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Buat Surat</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection