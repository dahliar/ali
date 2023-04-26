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
        <form id="formTambahStandar" action="{{url('standarBoronganStore')}}" method="post" name="formTambahStandar">
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
                            <li class="breadcrumb-item active">Tambah</li>
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
                                <input id="nama" name="nama" type="text" class="form-control text-md-left" value="{{old('nama')}}" placeholder="Nama harus unik">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Jenis*</span>
                            </div>
                            <div class="col-md-5">
                                <select class="form-select" id="jenis" name="jenis" >
                                    <option value="-1" selected>--Semua Status--</option>
                                    @foreach ($types as $type)
                                    @if ( $type->id == old('jenis') )
                                    <option value="{{ $type->id }}" selected>{{ $type->nama }}</option>
                                    @else
                                    <option value="{{ $type->id }}">{{ $type->nama }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Harga/Kg*</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="harga" name="harga" type="text" class="form-control text-end" value="{{old('harga')}}" placeholder="harga borongan per kilogram">
                                    <span name="spanAmount" id="spanAmount" class="input-group-text col-4"> per Kg</span>
                                </div>
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