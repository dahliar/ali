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
        <form id="formTambahItem" action="{{url('stockSubtractUpdate')}}" method="post" name="formTambahItem">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('itemList')}}">Items</a>
                            </li>
                            <li class="breadcrumb-item active">Edit pengurangan stock</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <input id="itemId" value="{{$stockSubtract->itemId}}" name="itemId" type="hidden" readonly>
                        <input id="stockSubtractId" value="{{$stockSubtract->id}}" name="stockSubtractId" type="hidden" readonly>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Item Name</span>
                            </div>
                            <div class="col-md-6">
                                <input id="item" name="item" type="text" class="form-control" value="{{$data->speciesName}} {{$data->gradeName}} {{$data->sizeName}} {{$data->packingName}} {{$data->freezingName}} {{$data->weightbase}} Kg" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Packed Add*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text col-3">Lama : </span>
                                    <input value="{{$stockSubtract->amountSubtract}}"  name="oldAmountsubtract" type="number" class="form-control text-end" readonly>
                                    <span class="input-group-text col-3">{{$data->pShortname}}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text col-3">Baru : </span>
                                    <input id="amountSubtract" name="amountSubtract" value="{{ old('amountsubtract',$stockSubtract->amountSubtract) }}" type="number" class="form-control text-end">
                                    <span class="input-group-text col-3">{{$data->pShortname}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Tanggal pengurangan*</span>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="date" id="tanggal" name="tanggal" class="form-control text-end" value="{{ old('tanggal', $stockSubtract->tanggal) }}" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection