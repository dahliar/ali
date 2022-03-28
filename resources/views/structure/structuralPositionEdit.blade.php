<!--BELUM-->
@php
$pageId = 61;
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
<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a class="white-text" href="{{ ('employeeList')}}">Jabatan</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body d-grid gap-1">
                <form id="StructureAddForm" action="{{url('structuralPositionUpdate')}}" method="POST" name="StructureAddForm" autocomplete="off">
                    @csrf
                    <div class="p-1 row form-group">
                        <div class="col-md-3 text-end">
                            <span class="label">Nama</span>
                        </div>
                        <div class="col-md-6">
                            <input id="name" name="name" type="text" class="form-control" autocomplete="none" value="{{ old('name', $structural_position->name) }}" readonly>
                            <input id="id" name="id" type="hidden" class="form-control" autocomplete="none" value="{{ old('id', $structural_position->id) }}" readonly>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-end">
                            <span class="label">Status Aktif*</span>
                        </div>
                        <div class="col-md-6">
                            <select id="isActive" name="isActive" class="form-select" >
                                <option value="-1">--Choose One--</option>
                                <option value="1" @if($structural_position->isActive == 1) selected @endif>Aktif</option>
                                <option value="0" @if($structural_position->isActive == 0) selected @endif>Non Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="p-1 row form-group">
                        <div class="col-md-3 text-end">
                        </div>
                        <div class="col-md-8">
                            <button class="btn btn-primary buttonConf" style="width:100px;" id="buttSubmit" type="submit">Ok</button>
                            <button type="Reset" class="btn btn-danger buttonConf" style="width:100px;" >Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

@else
@include('partial.noAccess')
@endif

@endsection