<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript"> 

    function myFunction(){
        Swal.fire({
            title: 'Tambah Dokumen?',
            text: "Simpan data dokumen",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("documentForm").submit();
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Penyimpanan data dokumen dibatalkan",
                    'info'
                    );
            }
        })
    };
    $(document).ready(function() {

    });
</script>
@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data dokumen berhasil ditambahkan", "info");
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
    <div class="modal-content">
        <div class="modal-header">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a class="white-text" href="{{ url('documentRepository')}}">Dokumen</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-body">
        <div class="col-12">
            <form id="documentForm" action="{{url('documentStore')}}"  method="post" name="documentForm" enctype="multipart/form-data">
                @csrf
                <div class="d-grid gap-1">
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Nama</span>
                        </div>
                        <div class="col-md-4">
                            <input id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="maksimal 20 karakter">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Keterangan</span>
                        </div>
                        <div class="col-md-7">
                            <textarea id="keterangan" name="keterangan" rows="4"  class="form-control">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Upload File Dokumen</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input class="form-control" type="file" id="fileDokumen" name="fileDokumen" accept="image/jpeg,image/jpg,image/png,application/pdf">
                            </div>
                            <span style="font-size:9px" class="label">File dalam bentuk image dengan ukuran maksimal 1MB</span>
                        </div>
                    </div>   
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('header')
@include('partial.header')
@endsection







