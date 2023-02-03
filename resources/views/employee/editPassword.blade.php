<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
<script type="text/javascript"> 
    function myFunction(){
        Swal.fire({
            title: 'Ubah password?',
            text: "Ubah password pengguna.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Password pengguna diubah',
                    text: "Simpan peubahan password pengguna.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("EmployeeEditForm").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Peubahan password pengguna.",
                    'info'
                    );
            }
        })
    };
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
                        <li class="breadcrumb-item active">Edit password pegawai</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <form id="EmployeeEditForm" action="{{route('passUpdate')}}" method="POST" name="EmployeeEditForm" autocomplete="off">
                    @csrf
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Nama*</label>
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
                                <label class="form-label">Ulangi Password</label>
                            </div>

                            <div class="col-md-4">
                                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password"/>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                                <button type="Reset" class="btn btn-danger buttonConf">Reset</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
@endsection