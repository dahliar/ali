<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data perusahaan berhasil ditambahkan", "info");
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
                            <a class="white-text" href="{{ url('companyList')}}">Page</a>
                        </li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-body">
            <form id="pageAddForm" action="{{url('pageStore')}}"  method="post" name="pageAddForm">
                @csrf
                <div class="d-grid gap-1">
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Application Name</span>
                        </div>
                        <div class="col-md-4">
                            <input id="appid" name="appid" class="form-control" value="{{ old('appid', $application->id) }}" type="hidden">
                            <input id="name" name="name" class="form-control" value="{{ old('name', $application->name) }}" type="text" readonly>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Page Name</span>
                        </div>
                        <div class="col-md-4">
                            <input id="name" name="name" class="form-control" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="npwp">Route</span>
                        </div>
                        <div class="col-md-4">
                            <input id="route" name="route" class="form-control" value="{{ old('route') }}"> 
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Minimum Access Level</span>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select w-100" id="level" name="level">
                                <option value="-1" selected>--Pilih dahulu--</option>
                                @foreach ($access_levels as $level)
                                @if ( $level->level == old('level', -1) )
                                <option value="{{ $level->level }}" selected>{{ $level->name }}</option>
                                @else
                                <option value="{{ $level->level }}">{{ $level->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="npwp">Icon</span>
                        </div>
                        <div class="col-md-4">
                            <input id="icon" name="icon" class="form-control" value="{{ old('icon') }}" placeholder="icon font awesome dalam format i"> 
                        </div>
                        <div class="col-md-6">
                            <a href="https://fontawesome.com/v5/search">get it here</a> atau biarkan kosong jika tidak memiliki icon
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('header')
@include('partial.header')
@endsection







