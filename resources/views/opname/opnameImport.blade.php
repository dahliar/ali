<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getOpnameList(){
        Swal.fire({
            title: 'Stock Opname',
            text: 'Generate file import data stock opname?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, generate data import',
            cancelButtonText: 'Tidak, batalkan saja'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open('{{ url("getStockOpnameImportList")}}', '_blank');
            }
        })
    };


    $(document).on('click', '#buttonSubmit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Stock Opname',
            text: 'Upload File Stock Opname?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Upload saja',
            cancelButtonText: 'Tidak, batalkan saja'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#stockOpnameStore').submit();
            }
        });
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

<div class="container-fluid">
    <div class="modal-content">
        <div class="modal-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a class="white-text" href="{{ ('opname')}}">Opname</a>
                    </li>
                    <li class="breadcrumb-item active">Import Data Stock Opname</li>
                </ol>
            </nav>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary" onclick="getOpnameList()">Download daftar barang</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="stockOpnameStore" action="{{url('stockOpnameStore')}}" method="POST" name="stockOpnameStore" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">File</span>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="stockOpnameFile" name="stockOpnameFile">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Tanggal Stock Opname</span>
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="stockOpnameDate" name="stockOpnameDate" class="form-control text-end" value="{{ old('stockOpnameDate')}}">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" id="buttonSubmit" class="btn btn-primary">Upload dan Simpan Stock Opname</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <ol>
                <li>Hanya untuk digunakan melakukan proses stock opname</b></li>
                <li>Klik tombol "Download daftar barang"</li>
                <li>Edit file yang telah didownload, hanya diperbolehkan untuk mengedit 2 kolom saja</li>
                <ol type="A">
                    <li>Jumlah stock terbaru</li>
                    <ol type="i">
                        <li>Untuk barang dengan packing MC, isi besaran jumlah MC saja</li>
                        <li>Untuk barang dengan packing karung, isi besaran running weight jumlah kilogramnya</li>
                    </ol>
                    <li>Ubah kolom "Ubah data stock?" 
                        <ol type="i">
                            <li>dengan nilai 0 jika data <b>tidak</b> berubah</li>
                            <li>dengan nilai 1 untuk data yang akan diubah</li>
                        </ol>
                    </ol>
                    <li>Simpan File tersebut</li>
                    <li>Pilih Tanggal dilakukan stock opname</li>
                    <li>Klik "Choose File", dan pilih file yang telah diedit </li>
                    <li>Klik "Upload dan Simpan Stock Opname"</li>
                </ol>
            </div>
        </div>
    </div>
    @endsection