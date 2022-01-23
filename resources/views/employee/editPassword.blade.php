<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')

@if (Auth::check() and Auth::user()->isAdmin())
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
                            <a class="white-text" href="{{ ('employeeList')}}">Pegawai</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <form id="EmployeeEditForm" action="{{route('passUpdate')}}" method="POST" name="EmployeeEditForm" autocomplete="off">
                    @csrf
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Name*</label>
                            </div>
                            <div class="col-md-4">
                                <input id="name" name="name" type="text" class="form-control" required autocomplete="off" value="{{$choosenUser->name}}" disabled="true">
                                <input id="userid" name="userid" type="hidden" value="{{$choosenUser->id}}" readonly>
                                <input id="employeeId" name="employeeId" type="hidden" value="{{$employee->id}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Username*</label>
                            </div>
                            <div class="col-md-4">
                                <input id="username" name="username" type="text" class="form-control" required autocomplete="off" value="{{$choosenUser->username}}" disabled="true">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Email</label>
                            </div>
                            <div class="col-md-4">
                                <input id="email" name="email" type="email" class="form-control" autocomplete="off" value="{{old('email', $choosenUser->email)}}" disabled>
                            </div>
                        </div>       
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">NIK*</span>
                            </div>
                            <div class="col-md-4">
                                <input id="nik" name="nik" type="text" class="form-control" required autocomplete="none" value="{{$employee->nik}}" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Password</label>
                            </div>

                            <div class="col-md-4">
                                <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password"/>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Re-Password</label>
                            </div>

                            <div class="col-md-4">
                                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password"/>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary buttonConf" id="buttSubmit" type="submit">Ok</button>
                                <button type="Reset" class="btn btn-danger buttonConf"ß>Reset</button>
                            </div>
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