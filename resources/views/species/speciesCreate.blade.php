<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
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
        <form id="formTambahSpecies" action="{{url('speciesStore')}}" method="post" name="formTambahSpecies">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('speciesList')}}">Species</a>
                            </li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Family*</span>
                            </div>
                            <div class="col-md-8">
                                <select class="form-select w-100" id="family" name="family">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($families as $family)
                                    @if ( $family->id == old('family') )
                                    <option value="{{ $family->id }}" selected>{{ $family->name }}</option>
                                    @else
                                    <option value="{{ $family->id }}">{{ $family->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Species Name - English</span>
                            </div>
                            <div class="col-md-8">
                                <input id="name" name="name" type="text" class="form-control text-md-right" value="{{old('name')}}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Species Name - Bahasa</span>
                            </div>
                            <div class="col-md-8">
                                <input id="nameBahasa" name="nameBahasa" type="text" class="form-control text-md-right" value="{{old('nameBahasa')}}">
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