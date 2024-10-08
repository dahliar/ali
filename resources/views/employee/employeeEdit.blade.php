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
<script type="text/javascript"> 
    function myFunction(){
        Swal.fire({
            title: 'Ubah karyawan?',
            text: "Ubah data karyawan.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Data karyawan baru diubah',
                    text: "Simpan data karyawan baru.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("EmployeeEditForm").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Peubahan data karyawan dibatalkan",
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
                        <li class="breadcrumb-item active">Edit data pegawai</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <form id="EmployeeEditForm" action="{{route('employeeUpdate')}}" method="POST" name="EmployeeEditForm" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Nama*</label>
                            </div>
                            <div class="col-md-8">
                                <input id="fullname" name="fullname" type="text" class="form-control" required autocomplete="off" value="{{old('fullname', $choosenUser->name)}}">
                                <input id="userid" name="userid" type="hidden" value="{{$choosenUser->id}}" readonly>
                                <input id="employeeId" name="employeeId" type="hidden" value="{{$employee->id}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Username*</label>
                            </div>
                            <div class="col-md-8">
                                <input id="username" name="username" type="text" class="form-control" required autocomplete="off" value="{{$choosenUser->username}}" disabled="true">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Email</label>
                            </div>
                            <div class="col-md-8">
                                <input id="email" name="email" type="email" class="form-control" autocomplete="off" value="{{old('email', $choosenUser->email)}}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Telepon</label>
                            </div>
                            <div class="col-md-8">
                                <input id="phone" name="phone" type="text" class="form-control" autocomplete="off" value="{{old('phone', $employee->phone)}}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Level akses</label>
                            </div>
                            <div class="col-md-4">
                                <input id="OldAccessLevel" name="OldAccessLevel" type="hidden" value="{{$choosenUser->accessLevel}}">
                                @php
                                $curUserAccessLevel=Auth::user()->accessLevel;
                                @endphp
                                <input id="curUserAccessLevel" name="curUserAccessLevel" type="hidden" value="{{$curUserAccessLevel}}">

                                @if (Auth::user()->accessLevel<=30)
                                <select id="accessLevel" name="accessLevel" class="form-select" required>
                                    @if ($curUserAccessLevel<=0)
                                    <option value="0" @if(old('accessLevel',$choosenUser->accessLevel) == 0) selected @endif>Superadmin</option>
                                    @endif
                                    @if ($curUserAccessLevel<=1)
                                    <option value="1" @if(old('accessLevel',$choosenUser->accessLevel) == 1) selected @endif>Admin</option>
                                    @endif
                                    @if ($curUserAccessLevel<=10)
                                    <option value="10" @if(old('accessLevel',$choosenUser->accessLevel) == 10) selected @endif>Direktur</option>
                                    @endif
                                    @if ($curUserAccessLevel<=20)
                                    <option value="20" @if(old('accessLevel',$choosenUser->accessLevel) == 20) selected @endif>Manager</option>
                                    @endif
                                    @if ($curUserAccessLevel<=30)
                                    <option value="30" @if(old('accessLevel',$choosenUser->accessLevel) == 30) selected @endif>Supervisor</option>
                                    @endif
                                    @if ($curUserAccessLevel<=40)
                                    <option value="40" @if(old('accessLevel',$choosenUser->accessLevel) == 40) selected @endif>Staff</option>
                                    @endif
                                    @if ($curUserAccessLevel<=99)
                                    <option value="99" @if(old('accessLevel',$choosenUser->accessLevel) == 99) selected @endif>Pekerja/Tamu</option>
                                    @endif
                                </select>
                                @else
                                @switch($choosenUser->accessLevel)
                                @case(0)
                                <input class="form-control" value="Superadmin" disabled>
                                @break
                                @case(1)
                                <input class="form-control" value="Admin" disabled>
                                @break
                                @case(10)
                                <input class="form-control" value="Direktur" disabled>
                                @break
                                @case(20)
                                <input class="form-control" value="Manager" disabled>
                                @break
                                @case(30)
                                <input class="form-control" value="Supervisor" disabled>
                                @break
                                @case(40)
                                <input class="form-control" value="Staf" disabled>
                                @break
                                @case(99)
                                <input class="form-control" value="Pekerja/Tamu" disabled>
                                @break
                                @endswitch
                                @endif
                            </div>
                        </div>        
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Nomor Induk Kependudukan*</span>
                            </div>
                            <div class="col-md-4">
                                <input id="nik" name="nik" type="text" class="form-control" required autocomplete="none" value="{{$employee->nik}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Nomor Induk Pegawai*</span>
                            </div>
                            <div class="col-md-4">
                                <input id="nip" name="nip" type="text" class="form-control" required autocomplete="none" value="{{$employee->nip}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Jenis Kelamin*</span>
                            </div>
                            <div class="col-md-4">
                                <select id="gender" name="gender" class="form-select" >
                                    <option value="1" @if(old('gender', $employee->gender) == 1) selected @endif>Laki-laki</option>
                                    <option value="2" @if(old('gender', $employee->gender) == 2) selected @endif>Perempuan</option>
                                </select>
                            </div>      
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Pendidikan*</span>
                            </div>
                            <div class="col-md-4">
                                <select id="pendidikan" name="pendidikan" class="form-select" >
                                    <option value="-1" @if(old('pendidikan', $employee->jenjangPendidikan) == -1) selected @endif>--Pilih Jenjang Pendidikan--</option>
                                    <option value="0" @if(old('pendidikan', $employee->jenjangPendidikan) == "0") selected @endif>Tidak Sekolah</option>
                                    <option value="1" @if(old('pendidikan', $employee->jenjangPendidikan) == "1") selected @endif>SD/Sederajat</option>
                                    <option value="2" @if(old('pendidikan', $employee->jenjangPendidikan) == "2") selected @endif>SMP/Sederajat</option>
                                    <option value="3" @if(old('pendidikan', $employee->jenjangPendidikan) == "3") selected @endif>SMA/Sederajat</option>
                                    <option value="4" @if(old('pendidikan', $employee->jenjangPendidikan) == "4") selected @endif>Diploma 1</option>
                                    <option value="5" @if(old('pendidikan', $employee->jenjangPendidikan) == "5") selected @endif>Diploma 2</option>
                                    <option value="6" @if(old('pendidikan', $employee->jenjangPendidikan) == "6") selected @endif>Diploma 3</option>
                                    <option value="7" @if(old('pendidikan', $employee->jenjangPendidikan) == "7") selected @endif>Diploma 4/Sarjana</option>
                                    <option value="8" @if(old('pendidikan', $employee->jenjangPendidikan) == "8") selected @endif>Master</option>
                                    <option value="9" @if(old('pendidikan', $employee->jenjangPendidikan) == "9") selected @endif>Doktor</option>
                                </select>
                            </div>      
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Bidang Pendidikan*</span>
                            </div>
                            <div class="col-md-8">
                                <textarea class="form-control" id="bidangPendidikan" name="bidangPendidikan" rows="4" cols="50" required autocomplete="none">{{old('bidangPendidikan', $employee->bidangPendidikan)}}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Tanggal Lahir*</span>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" id="birthdate" name="birthdate" type="date"  value="{{$employee->birthdate}}" readonly>  
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>      
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Tanggal Mulai Sebelumnya</span>
                            </div>
                            <div class="col-md-4">
                                <input id="startdate" name="startdateLama" class="form-control" type="date" value="{{$employee->startdate}}" readonly>
                            </div>      
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Tanggal Mulai Baru*</span>
                            </div>
                            <div class="col-md-4">
                                <input id="startdate" name="startdate" class="form-control" type="date" value="{{old('stardate',$employee->startdate)}}">
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>      
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Alamat*</span>
                            </div>
                            <div class="col-md-8">
                                <textarea class="form-control" id="address" name="address" rows="4" cols="50" required autocomplete="none">{{old('address', $employee->address)}}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Jenis Karyawan*</span>
                            </div>
                            <div class="col-md-4">
                                @if (Auth::user()->accessLevel<=40)
                                <select id="employmentStatus" name="employmentStatus" class="form-select" >
                                    <option value="-1" @if(old('employmentStatus',$employee->employmentStatus) == -1) selected @endif >--Choose First--</option>
                                    <option value="1" @if(old('employmentStatus',$employee->employmentStatus) == 1) selected @endif >Bulanan</option>
                                    <option value="2" @if(old('employmentStatus',$employee->employmentStatus) == 2) selected @endif >Harian</option>
                                    <option value="3" @if(old('employmentStatus',$employee->employmentStatus) == 3) selected @endif >Borongan</option>
                                </select>
                                @else
                                <input id="employmentStatus" name="employmentStatus" type="hidden" value="{{$employee->employmentStatus}}">
                                @switch($employee->employmentStatus)
                                @case(1)
                                <input class="form-control" value="Bulanan" disabled>
                                @break
                                @case(2)
                                <input class="form-control" value="Harian" disabled>
                                @break
                                @endswitch
                                @endif
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">No Rekening</span>
                            </div>
                            <div class="col-md-4">
                                <input id="noRekening" value="{{ old('noRekening',$employee->noRekening) }}" name="noRekening" type="text" class="form-control" autocomplete="none">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Bank</span>
                            </div>
                            <div class="col-md-8">
                                <select id="bankid" name="bankid" class="form-select" >
                                    <option value="-1">--Choose First--</option>
                                    @foreach ($banks as $bank)
                                    @if ( old('bankid', $employee->bankid) == $bank->id)
                                    <option value="{{ $bank->id }}" selected>{{ $bank->shortname }} - {{$bank->name}}</option>
                                    @else
                                    <option value="{{ $bank->id }}">{{ $bank->shortname }} - {{$bank->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($employee->ktp)
                        <div class="row form-group">
                            <div class="col-md-2 text-md-end">
                                <span class="label">File KTP</span>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <a href="{{ url('getFileDownload').'/'.$employee->ktp }}" target="_blank">{{$employee->ktp}}</a>
                                </div>
                            </div>
                        </div>       
                        @endif                    
                        <div class="row form-group">
                            <div class="col-md-2 text-md-end">
                                <span class="label">Upload KTP</span>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="ktp" name="ktp" accept="image/jpeg,image/jpg,image/png,application/pdf">
                                </div>
                                <span style="font-size:9px" class="label">File dalam bentuk image dengan ukuran maksimal 1MB</span>
                            </div>
                        </div>   
                        @if($employee->kk)
                        <div class="row form-group">
                            <div class="col-md-2 text-md-end">
                                <span class="label">File KK</span>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <a href="{{ url('getFileDownload').'/'.$employee->kk }}" target="_blank">{{$employee->kk}}</a>
                                </div>
                            </div>
                        </div>       
                        @endif                    
                        <div class="row form-group">
                            <div class="col-md-2 text-md-end">
                                <span class="label">Upload KK</span>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="kk" name="kk" accept="image/jpeg,image/jpg,image/png,application/pdf">
                                </div>
                                <span style="font-size:9px" class="label">File dalam bentuk image dengan ukuran maksimal 1MB</span>
                            </div>
                        </div>         

                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Status bekerja</span>
                            </div>
                            <div class="col-md-3">
                                @if (Auth::user()->accessLevel<=40)
                                <select id="isactive" name="isactive" class="form-select" >
                                    <option value="0" @if($employee->isActive == 0) selected @endif>Non Aktif</option>
                                    <option value="1" @if($employee->isActive == 1) selected @endif>Aktif</option>
                                </select>
                                <input id="isActiveCurrent" name="isActiveCurrent" type="hidden" value="{{$employee->isActive}}">
                                @else
                                <input id="isActiveCurrent" name="isActiveCurrent" type="hidden" value="{{$employee->isActive}}">
                                <input id="isactive" name="isactive" type="hidden" value="{{$employee->isActive}}">
                                <input class="form-control" value="@if ($employee->isActive==0) Non Aktif @else Aktif @endif" disabled>
                                @endif                                
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-8">
                                <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Ubah</button>
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