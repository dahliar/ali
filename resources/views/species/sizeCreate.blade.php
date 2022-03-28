<!--BELUM-->
@php
$pageId = 44;
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if ((Auth::user()->isProduction() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
<script type="text/javascript">
    function totalAmount(){
        var lama = parseFloat(document.getElementById("jumlahLama").value);
        var tambah = parseFloat(document.getElementById("jumlahTambah").value);
        var total = lama+tambah;
        document.getElementById('jumlahTotal').value = total;
    }
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
        <form id="formTambahItem" action="{{url('sizeCreateStore')}}" method="get" name="formTambahItem">
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
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-8 form-inline">
                                <div class="col-md-6">
                                    <input id="speciesId" value="{{$species->id}}" name="speciesId" type="hidden" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Species Name</span>
                            </div>
                            <div class="col-md-5">
                                <input id="speciesName" name="speciesName" type="text" class="form-control text-md-right" value="{{$species->name}}" readonly >
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Size Name</span>
                            </div>
                            <div class="col-md-5">
                                <input id="name" name="name" type="text" class="form-control text-md-right" value="{{old('name')}}">
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
@else
@include('partial.noAccess')
@endif

@endsection