@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript">
    function myFunction(){
        Swal.fire({
            title: 'Generate?',
            text: "Generate gaji harian/borongan/honorarium",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Yakin generate?',
                    text: "Data tidak bisa dikembalikan",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan saja.'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Generate dilakukan',
                            text: "Generate gaji harian/borongan/honorarium",
                            icon: 'info',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok disimpan.'
                        }).then((result) => {
                          document.getElementById("generateGaji").submit();
                      })
                    } else {
                        Swal.fire(
                            'Batal disimpan!',
                            "Generate gaji harian/borongan/honorarium dibatalkan",
                            'info'
                            );
                    }
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Generate gaji harian/borongan/honorarium dibatalkan",
                    'info'
                    );
            }
        })
    };
</script>
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
                    <div class="col-md-3">
                        <input type="date" id="startDate" name="startDate" class="form-control text-end" value="{{old('startDate')}}">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-end">
                        <span class="label">Tanggal Akhir</span>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="endDate" name="endDate" class="form-control text-end" value="{{old('endDate')}}">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-end">
                        <span class="label">Tanggal penggajian</span>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="payDate" name="payDate" class="form-control text-end" value="{{old('payDate')}}">
                    </div>
                </div>
            </div>                   
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
            </div>
        </form>
    </div>


    <ol>
        <li>Laman ini digunakan untuk melakukan proses generate gaji</li>
        <ol>
            <li>Harian</li>
            <li>Borongan</li>
            <li>Honorarium</li>
        </ol>
        <li>Pilih tanggal awal proses generate, tanggal tersebut akan masuk kedalam perhitungan.</li>
        <li>Pilih tanggal akhir proses generate, tanggal tersebut akan masuk kedalam perhitungan.</li>
        <li>Pilih pembayaran minggu tersebut.</li>
    </ol>
</body>
@endsection