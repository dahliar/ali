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

<div class="container-fluid">
    <div class="row">
        <form id="formEditStandar" action="{{url('standarBoronganUpdate')}}" method="post" name="formEditStandar">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('standarBorongan')}}">Standar Borongan</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nama*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="idStandar" name="idStandar" type="hidden" value="{{$id}}" readonly>
                                <input id="namaView" name="namaView" type="text" class="form-control text-md-left" value="{{old('nama', $nama)}}" disabled>
                                <input id="nama" name="nama" type="hidden" class="form-control text-md-left" value="{{old('nama', $nama)}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Tipe*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="tipeView" name="tipeView" type="text" class="form-control text-md-left" value="{{old('tipeView', $namaType)}}" disabled>
                                <input id="idType" name="idType" type="hidden" class="form-control text-md-left" value="{{old('idType', $idType)}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Harga/Kg*</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="harga" name="harga" type="text" class="form-control text-end" value="{{old('harga', $harga)}}" placeholder="harga borongan per kilogram">
                                    <span name="spanAmount" id="spanAmount" class="input-group-text col-4"> per Kg</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Status*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="prevStatus" name="prevStatus" type="hidden" value="{{old('prevStatus', $status)}}" readonly>
                                <select class="form-select" id="status" name="status" >
                                    <option value="0" @if(old('status', $status) == 0) selected @endif>Tidak Aktif</option>
                                    <option value="1" @if(old('status', $status) == 1) selected @endif>Aktif</option>
                                </select>
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
</div>
@endsection