@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
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
            <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Generate Gaji Bulanan</li>
                </ol>
            </nav>
        </div>
        <form method="POST" action="{{url('generateGajiBulananStore')}}">
            @csrf
            <div class="modal-body">
                <div class="row form-group">
                    <div class="col-md-3 text-end">
                        <span class="label">Periode</span>
                    </div>
                    <div class="col-md-2">
                        <select id="tahun" name="tahun" class="form-select w-100" >
                            <option selected value="-1" selected>--Choose First--</option>
                            <option value="2022" @if(old('tahun') == 1) selected @endif>2022</option>
                            <option value="2023" @if(old('tahun') == 2) selected @endif>2023</option>
                            <option value="2024" @if(old('tahun') == 3) selected @endif>2024</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="bulan" name="bulan" class="form-select w-100" >
                            <option selected value="-1" selected>--Choose First--</option>
                            <option value="1" @if(old('bulan') == 1) selected @endif>Januari</option>
                            <option value="2" @if(old('bulan') == 2) selected @endif>Februari</option>
                            <option value="3" @if(old('bulan') == 3) selected @endif>Maret</option>
                            <option value="4" @if(old('bulan') == 4) selected @endif>April</option>
                            <option value="5" @if(old('bulan') == 5) selected @endif>Mei</option>
                            <option value="6" @if(old('bulan') == 6) selected @endif>Juni</option>
                            <option value="7" @if(old('bulan') == 7) selected @endif>Juli</option>
                            <option value="8" @if(old('bulan') == 8) selected @endif>Agustus</option>
                            <option value="9" @if(old('bulan') == 9) selected @endif>September</option>
                            <option value="10" @if(old('bulan') == 10) selected @endif>Oktober</option>
                            <option value="11" @if(old('bulan') == 11) selected @endif>November</option>
                            <option value="12" @if(old('bulan') == 12) selected @endif>Desember</option>
                        </select>
                    </div>
                </div>                    
            </div>                   
            <div class="modal-footer">
                <button type="reset" class="btn btn-secondary">Reset</button>
                <button type="submit" class="btn btn-primary">Generate</button>
            </div>
        </form>
    </div>


    <ol>
        <li>Laman ini digunakan untuk melakukan proses generate gaji pegawai bulanan</li>
        <li>Tanggal batas akhir: Honorarium dan Lembur adalah hingga presensi tanggal 20 pada setiap bulannya</li>
        <li>Aplikasi akan melihat data presensi atau honorarium yang belum digenerate di tanggal-tanggal sebelumnya</li>
        <li>Pastikan data telah diinput ketika dilakukan Generate</li>
    </ol>
</body>
@endsection