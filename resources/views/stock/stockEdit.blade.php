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
<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('scanList')}}">Barcode</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="card card-body">
            <form id="formEditBarcode" action="{{url('')}}"  method="POST" name="formEditBarcode">
                @csrf
                <div class="d-grid gap-1">
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label" id="spanLabel">Item name*</span>
                        </div>
                        <div class="col-8">
                            <input id="itemName" name="itemName"  class="form-control"  value="{{$barcode->name}}" readonly>
                        </div>
                    </div>   
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label" id="spanLabel">Barcode Number</span>
                        </div>
                        <div class="col-3">
                            <input id="barcode" name="barcode"  class="form-control"  value="{{$barcode->barcode}}" readonly>
                        </div>
                    </div>   
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label" id="spanLabel">Code ID</span>
                        </div>
                        <div class="col-3">
                            <input id="pinum" name="pinum"  class="form-control"  value="{{$barcode->id}}" readonly>
                        </div>
                    </div>   
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label" id="spanLabel">Tanggal Produksi</span>
                        </div>
                        <div class="col-3">
                            <input id="productionDate" name="productionDate"  class="form-control"  value="{{$barcode->productionDate}}" readonly>
                        </div>
                    </div>   
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label" id="spanLabel">Tanggal Packing</span>
                        </div>
                        <div class="col-3">
                            <input id="packagingDate" name="packagingDate"  class="form-control"  value="{{$barcode->packagingDate}}" readonly>
                        </div>
                    </div>   
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label" id="spanLabel">Tanggal Storing</span>
                        </div>
                        <div class="col-3">
                            <input id="storingDate" name="storingDate"  class="form-control"  value="{{$barcode->storageDate}}" readonly>
                        </div>
                    </div>   
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label" id="spanLabel">Tanggal Loading</span>
                        </div>
                        <div class="col-3">
                            <input id="loadingDate" name="loadingDate"  class="form-control"  value="{{$barcode->loadingDate}}" readonly>
                        </div>
                    </div>   
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label">Status</span>
                        </div>
                        <div class="col-md-3">
                            <select id="status" name="status" class="form-select" disabled>
                                <option value="0" @if($barcode->status == 0) selected @endif>Cetak</option>
                                <option value="1" @if($barcode->status == 1) selected @endif>Storage</option>
                                <option value="2" @if($barcode->status == 2) selected @endif>Load</option>
                                <option value="99" @if($barcode->status == 99) selected @endif>Delete</option>
                            </select>
                        </div>
                    </div> 
                    <div class="row form-group">
                        <div class="col-3 text-end">
                        </div>
                        <div class="col-8 text-center">
                                <!--
                                <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                                <input type="reset" value="Reset" class="btn btn-secondary">
                            -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
@endsection