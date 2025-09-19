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
            title: 'Tambah jenis pajak baru?',
            text: "Tambah jenis pajak baru.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, tambah saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Jenis pajak baru ditambahkan',
                    text: "Simpan data pajak baru.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("TaxesAddForm").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Penambahan data pajak baru dibatalkan",
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
                        <a class="white-text" href="{{ ('taxes')}}">Tax</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah Pajak</li>
                </ol>
            </nav>
        </div>
        <div class="modal-body">
            <form id="TaxesAddForm" action="{{url('taxesStore')}}" method="POST" name="TaxesAddForm" autocomplete="off">
                @csrf
                <div class="d-grid gap-1">
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <label class="form-label">Nama*</label>
                        </div>
                        <div class="col-md-8">
                            <input id="name" name="name" type="text" class="form-control" autocomplete="off" value="{{old('name')}}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Jenis Transaksi*</span>
                        </div>
                        <div class="col-md-4">
                            <select id="transactionType" name="transactionType" class="form-select" >
                                <option value="-1" @if(old('gender') == -1) selected @endif>--Pilih Jenis Transaksi--</option>
                                <option value="1" @if(old('gender') == 2) selected @endif>Pembelian</option>
                                <option value="2" @if(old('gender') == 1) selected @endif>Penjualan</option>
                            </select>
                        </div>      
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Persentase Pajak*</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input id="persentase" name="persentase" value="{{old('persentase',0)}}" type="text" class="form-control text-end" autocomplete="none">
                                <span class="input-group-text col-3">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                        </div>
                        <div class="col-md-8">
                            <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                            <button type="Reset" class="btn btn-danger buttonConf">Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection