@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 2)
<meta name="csrf-token" content="{{ csrf_token() }}" />
@if (Session::has('val'))
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            {{ Session::get('val')[0]}}<br>{{ Session::get('val')[1]}}<br>{{ Session::get('val')[2]}}<br>
        </div>
        <div class="col-md-1 text-end">
            <span class="label"><strong >x</strong></span>
        </div>
    </div>
</div>
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

<body class="container-fluid">
    <div class="modal-content">
        <div class="modal-header">
            <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Generate Gaji Harian/Borongan/Honorarium</li>
                </ol>
            </nav>
        </div>
        <form id="generateGaji" method="POST" name="generateGaji" action="{{url('generateGajiStore')}}">
            @csrf
            <div class="modal-body">
                <div class="row form-group">
                    <div class="col-md-3 text-end">
                        <span class="label">Tanggal Awal</span>
                    </div>
                    <div class="col-md-6">
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{old('start', date('Y-m-d', strtotime('-1 week')))}}">
                    </div>
                </div>                    
                <div class="row form-group">
                    <div class="col-md-3 text-end">
                        <span class="label">Tanggal Akhir</span>
                    </div>
                    <div class="col-md-6">
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{old('end', date('Y-m-d'))}}">
                    </div>
                </div> 
            </div>                   
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Generate</button>
            </div>
        </form>
    </div>


    <ol>
        <li>Laman ini digunakan untuk melakukan proses generate gaji</li>
        <ol>
            <li>Harian</li>
            <li>Borongan</li>
            <!--<li>Lembur Pegawai Bulanan</li>-->
            <li>Honorarium</li>
        </ol>
        <li>Pilih tanggal batas awal dan batas akhir</li>
        <li>Pastikan tidak ada tanggal yang terlewat dari batas sebelumnya</li>
        <li>Jika sudah selesai, cek pada setiap laman penggajian untuk melakukan pencetakan slip penggajian dan menandai jika sudah dilakukan pembayaran</li>
    </ol>
</body>
@else
@include('partial.noAccess')
@endif

@endsection