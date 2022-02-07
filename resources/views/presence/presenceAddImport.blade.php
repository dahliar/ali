<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::user()->isAdmin())
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getPresenceList(){
        var presenceDate = document.getElementById("presenceDate").value;
        window.open('{{ url("getPresenceList")}}'+"/"+presenceDate, '_blank');
    }
</script>
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
                        <a class="white-text" href="{{ ('presenceEmployeeList')}}">Presensi</a>
                    </li>
                    <li class="breadcrumb-item active">Import Presensi Pegawai Harian/Bulanan</li>
                </ol>
            </nav>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="presenceFileStore" action="{{url('presenceFileStore')}}" method="POST" name="presenceFileStore" autocomplete="off">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Presence Date</span>
                            </div>
                            <div class="col-md-8">
                                <input type="date" id="presenceDate" name="presenceDate" class="form-control text-end" value="{{ old('presenceDate', date('Y-m-d'))}}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" onclick="getPresenceList()">Get Presence List</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="presenceFileStore" action="{{url('presenceFileStore')}}" method="POST" name="presenceFileStore" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">File</span>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="presenceFile" name="presenceFile">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@else
@include('partial.noAccess')
@endif

@endsection