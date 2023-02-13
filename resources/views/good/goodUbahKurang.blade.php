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
    $(document).ready(function() {
        $('#amountKurang').on('change', function() {
            var amountKurang = document.getElementById("amountKurang").value;
            var currentAmount = document.getElementById("currentAmount").value;
            if (amountKurang>currentAmount){
                swal.fire("warning", "Jumlah penggunaan harus kurang dari stok saat ini", "warning");
            }
        });
    });
</script>
@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data item berhasil ditambahkan", "info");
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
    <div class="row">
        <form id="formKurangBarang" action="{{url('goodUbahKurang')}}" method="post" name="formKurangBarang">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('goodList')}}">Barang Produksi</a>
                            </li>
                            <li class="breadcrumb-item active">Tambah jumlah barang</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <input id="idGood" name="idGood" type="hidden" value="{{old('idGood', $good->id)}}">
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nama*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="name" name="name" type="text" class="form-control text-md-left" value="{{old('name', $good->name)}}" placeholder="Nama harus unik untuk barang tersebut" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Kategori*</span>
                            </div>
                            <div class="col-md-3">
                                <input id="categories" name="categories" type="text" class="form-control text-left" value="{{ $categories }}" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Unit Satuan*</span>
                            </div>
                            <div class="col-md-3">
                                <input id="amount" name="amount" type="text" class="form-control text-left" value="{{ $unit }}" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Jumlah Saat ini*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input id="currentAmount" name="currentAmount" type="text" class="form-control text-end" value="{{old('currentAmount', $good->amount)}}" placeholder="gunakan titik untuk pecahan" disabled>
                                    <span name="spanAmount" id="spanAmount" class="input-group-text col-4">{{ $unit }}</span>
                                </div>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Jumlah pengurangan*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input id="amountKurang" name="amountKurang" type="text" class="form-control text-end" value="{{old('amountKurang',0)}}" placeholder="gunakan titik untuk pecahan" onchange="checkPengurangan()">
                                    <span name="spanMinimal" id="spanMinimal" class="input-group-text col-4">{{ $unit }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Tanggal penggunaan*</span>
                            </div>
                            <div class="col-md-3">
                                <input id="usageDate" name="usageDate" type="date" class="form-control text-end" value="{{old('usageDate')}}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Keterangan*</span>
                            </div>
                            <div class="col-md-3">
                                <textarea id="keterangan" placeholder="Informasi terkait penggunaan barang produksi" name="keterangan" rows="4"  class="form-control" style="min-width: 100%">{{old('amountKurang')}}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label"></span>
                            </div>
                            <div class="col-md-5">
                                <span id="info" style="color:red" class="col-4"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button id="buttonSubmit" type="submit" class="btn btn-primary">Save</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
    Catatan:<br>
    <ol>
        <li>Perubahan jumlah dilaman ini hanya digunakan untuk stock opname, bukan melakukan penambahan atau pengurangan stok reguler</li>
    </ol>
</div>
@endsection