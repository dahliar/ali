<meta name="csrf-token" content="{{ csrf_token() }}" />
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
    function myFunction(){
        Swal.fire({
            title: 'Tambah Jabatan?',
            text: "Simpan penambahan jabatan.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Jabatan ditambahkan.',
                    text: "Simpan penambahan jabatan.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("StructureAddForm").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Penambahan penempatan struktur organisasi dibatalkan",
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
    <div class="modal-content">
        <div class="modal-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a class="white-text" href="{{ ('structuralPositionList')}}">Jabatan</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
        <div class="modal-body d-grid gap-1">
            <form id="StructureAddForm" action="{{url('structuralPositionStore')}}" method="POST" name="StructureAddForm" autocomplete="off">
                @csrf
                <div class="p-1 row form-group">
                    <div class="col-md-2 text-end">
                        <span class="label">Nama*</span>
                    </div>
                    <div class="col-md-6">
                        <input id="name" name="name" type="text" class="form-control" autocomplete="none" value="{{ old('name') }}">
                    </div>
                </div>
                <div class="p-1 row form-group">
                    <div class="col-md-2 text-end">
                    </div>
                    <div class="col-md-8">
                        <button type="button" class="btn btn-primary" style="width:100px;" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                        <button type="Reset" class="btn btn-danger buttonConf" style="width:100px;" >Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection