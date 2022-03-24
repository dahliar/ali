<!--BELUM-->
@php
$pageId = -1;
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
@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 2)

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
                        <a class="white-text" href="{{ ('structuralPositionList')}}">Bagian</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
        <div class="modal-body d-grid gap-1">
            <form id="StructureAddForm" action="{{url('workPositionStore')}}" method="POST" name="StructureAddForm" autocomplete="off">
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
                        <button class="btn btn-primary buttonConf" style="width:100px;" id="buttSubmit" type="submit">Ok</button>
                        <button type="Reset" class="btn btn-danger buttonConf" style="width:100px;" >Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

@else
@include('partial.noAccess')
@endif

@endsection