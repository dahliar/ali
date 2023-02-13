@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
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
<body">
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-9">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Presensi Harian</li>
                        </ol>
                    </nav>
                </div>
            </div>    
        </div>
        <form id="presenceHarianEdit" action="{{url('presenceHarianUpdate')}}" method="POST" name="presenceHarianEdit">
            @csrf
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeePresenceHarianModal">Edit Presensi tanggal {{ date('Y-m-d', strtotime($presence->start)) }}</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="empid" name="empid" class="form-control" value="{{$presence->employeeId}}">
                        <input type="hidden" id="presenceId" name="presenceId" class="form-control" value="{{$presence->id}}">
                        <input type="hidden" id="dailysalariesid" name="dailysalariesid" class="form-control" value="{{$dailysalaries->id}}">

                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">NIP</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="nip" name="nip" class="form-control" value="{{$employee->nip}}" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Nama</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="nama" name="nama" class="form-control" value="{{$employee->nama}}" readonly>
                            </div>
                        </div>                    
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Jenis Pegawai</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="nama" name="nama" class="form-control" value="{{$employee->jenis}}" disabled>
                            </div>
                        </div>                    
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Posisi</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="nama" name="nama" class="form-control" value="{{$employee->orgStructure}}" disabled>
                            </div>
                        </div>                    
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Bagian</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="nama" name="nama" class="form-control" value="{{$employee->bagian}}" disabled>
                            </div>
                        </div>                    
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Jam Masuk</span>
                            </div>
                            <div class="col-md-8">
                                <input type="datetime-local" id="start" name="start" class="form-control text-end" value="{{ date('Y-m-d\TH:i', strtotime($presence->start)) }}">
                                <input type="hidden" id="oldStart" name="oldStart" class="form-control text-end" value="{{ date('Y-m-d\TH:i', strtotime($presence->start)) }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Jam Pulang</span>
                            </div>
                            <div class="col-md-8">
                                <input type="datetime-local" id="end" name="end" class="form-control text-end" value="{{ date('Y-m-d\TH:i', strtotime($presence->end)) }}">
                                <input type="hidden" id="oldEnd" name="oldEnd" class="form-control text-end" value="{{ date('Y-m-d\TH:i', strtotime($presence->end)) }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label" id="spanPayment">Status Generate</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="statusGenerate" name="statusGenerate" class="form-control text-end" value="@if ($dailysalaries->isGenerated==1) Sudah Generate @else Belum Generate @endif" disabled>
                            </div>                    
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Lembur</span>
                            </div>
                            <div class="col-md-8">
                                <select id="lembur" name="lembur" class="form-select"  @if($presence->isGenerated==1) disabled @endif>
                                    <option value="1" @if($presence->isLembur==1) selected @endif>Lembur</option>
                                    <option value="0" @if($presence->isLembur==0) selected @endif>Tidak Lembur</option>
                                </select>

                            </div>
                        </div>
                        @if ($dailysalaries->isGenerated!=1)
                        <div class="row form-group">
                            <div class="col-md-2 text-md-right">
                                <span class="label" id="spanPayment">Status Pembelian*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="progressStatus" name="progressStatus" class="form-select">
                                    <option value="-1" >--Pilih salah satu--</option>
                                    <option value="1" >Update</option>
                                    <option value="2" >Hapus</option>
                                </select>
                            </div>                    
                        </div>
                        @endif

                    </div>
                    @if ($dailysalaries->isGenerated!=1)

                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Reset</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</body>
@endsection