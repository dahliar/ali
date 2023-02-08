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
    function cutiDateChecker(){
        var $startDate = document.getElementById("startDate").value;
        var $endDate = document.getElementById("endDate").value;
        var $employeeId = document.getElementById("employeeId").value;

        $.ajax({
            url: '{{ url("dateCounterChecker") }}',
            type: "POST",
            data: {
                "_token":"{{ csrf_token() }}",
                startDate: $startDate,
                endDate: $endDate
            },
            dataType: "json",
            success:function(data){
                document.getElementById('jumlahHari').value = data;
                if (data <= 0 ){
                    document.getElementById("btn-submit").disabled = true;
                    document.getElementById("spanInfo").innerHTML="<span style='color: red;'>Jumlah cuti kurang dari 0, terdapat hari libur</span>";

                } else {
                    document.getElementById("btn-submit").disabled = false;
                    document.getElementById("spanInfo").innerHTML="";

                }
            }
        });

        $.ajax({
            url: '{{ url("dateOverlapExist") }}',
            type: "POST",
            data: {
                "_token":"{{ csrf_token() }}",
                employeeId: $employeeId,
                startDate: $startDate,
                endDate: $endDate
            },
            dataType: "json",
            success:function(data){
                if (data > 0 ){
                    document.getElementById("btn-submit").disabled = true;
                    document.getElementById("spanAwal").innerHTML="<span style='color: red;'>Tanggal cuti bentrok</span>";
                    document.getElementById("spanAkhir").innerHTML="<span style='color: red;'>Tanggal cuti bentrok</span>";
                } else {
                    document.getElementById("btn-submit").disabled = false;
                    document.getElementById("spanAwal").innerHTML="";
                    document.getElementById("spanAkhir").innerHTML="";
                }
            }
        });

        return false;
    }

    function myFunction(){
        Swal.fire({
            title: 'Ajukan cuti pegawai?',
            text: "Pengajuan cuti pegawai.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ajukan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'ok, cuti diajukan',
                    text: "Pengajuan cuti pegawai.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok.'
                }).then((result) => {
                    document.getElementById("formPengajuanCuti").submit();
                })
            } else {
                Swal.fire(
                    'Pengajuan cuti dibatalkan!',
                    "Pengajuan cuti pegawai.",
                    'info'
                    );
            }
        });
    }
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
        <form id="formPengajuanCuti" action="{{url('cutiStore')}}" method="post" name="formPengajuanCuti">
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
                                <span class="label">Penempatan</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="jabatan" name="jabatan" type="text" class="form-control text-left" value="{{$employee->penempatan}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Tanggal Awal*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="date" id="startDate" name="startDate" class="form-control text-end" value="{{ old('startDate', date('Y-m-d'))}}" min="{{date('Y-m-d')}}" max="{{date('Y-m-d', strtotime('+2 month'))}}" onchange="cutiDateChecker()" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <span class="label" id="spanAwal"><strong></strong></span>
                            </div>

                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Tanggal Akhir*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="date" id="endDate" name="endDate" class="form-control text-end" value="{{ old('endDate', date('Y-m-d'))}}" min="{{date('Y-m-d')}}" max="{{date('Y-m-d', strtotime('+3 month'))}}" onchange="cutiDateChecker()" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <span class="label" id="spanAkhir"><strong></strong></span>
                                <span class="label" id="spanInfo"><strong></strong></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Jumlah hari</span>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input id="jumlahHari" name="jumlahHari" type="text" class="form-control text-left" value="{{old('jumlahHari', '')}}" readonly>
                                    <span class="input-group-text col-3">Hari</span>
                                </div>
                            </div>
                        </div>    
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label" id="spanLabel">Alamat selama cuti*</span>
                            </div>
                            <div class="col-md-8">
                                <textarea id="alamatCuti" name="alamatCuti" rows="4"  class="form-control" style="min-width: 100%">{{ old('alamatCuti') }}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label" id="spanLabel">Alasan cuti*</span>
                            </div>
                            <div class="col-md-8">
                                <textarea id="alasan" name="alasan" rows="4"  class="form-control" style="min-width: 100%">{{ old('alasan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Ajukan Cuti</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection