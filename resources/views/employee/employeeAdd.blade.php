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

    function getOrgStructureSelectOptionList(workPos, structuralPos, orgstructure){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : "{{ route('orgStructureList') }}",
            type: "post",
            data: {
                structPosId : structuralPos,
                workPosId: workPos
            },
            dataType: "JSON",
            success: function(data){
                $("#OrgStructureOption").empty();
                $("#OrgStructureOption").append('<option value="-1">--Pilih Jabatan & Bagian dahulu--</option>');
                var html = '';
                var i;
                $.each(data, function(key, value) {
                    if (key != orgstructure){
                        $("#OrgStructureOption").append('<option value="' + key + '">' + value +
                            '</option>');
                    }else{
                        $("#OrgStructureOption").append('<option selected value="' + key + '">' + value +
                            '</option>');
                    }
                });
            },
            error: function (jqXHR, textStatus, errorThrown){
                alert('an error occured, contact administrator');
            }
        })
    }


    $(document).ready(function(e){ 
        $('#structural').change(function(){
            var x = document.getElementById("structural");
            var structuralPos = x.options[x.selectedIndex].value;
            x = document.getElementById("workPosition");
            var workPos = x.options[x.selectedIndex].value;
            if ((workPos!=-1) && (structuralPos!=-1)){
                getOrgStructureSelectOptionList(workPos, structuralPos, -1);
            }
        });
        $('#workPosition').change(function(){
            var x = document.getElementById("structural");
            var structuralPos = x.options[x.selectedIndex].value;
            x = document.getElementById("workPosition");
            var workPos = x.options[x.selectedIndex].value;
            if ((workPos!=-1) && (structuralPos!=-1)){
                getOrgStructureSelectOptionList(workPos, structuralPos, -1);
            }
        });
    });

    $(document).on("click", "#buttCancelAdd", function (e) {
        e.preventDefault();
        formResetClearAllInfo();
    });

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
<script type="text/javascript"> 
    getOrgStructureSelectOptionList({{ old('workPosition') }}, {{ old('structural') }}, {{ old('OrgStructureOption') }});
</script>
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
                        <a class="white-text" href="{{ ('employeeList')}}">Pegawai</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah pegawai</li>
                </ol>
            </nav>
        </div>
        <div class="modal-body">

            <form id="EmployeeAddForm" action="{{url('employeeStore')}}" method="POST" name="EmployeeAddForm" autocomplete="off">
                @csrf

                <div class="d-grid gap-1">
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <label class="form-label">Nama*</label>
                        </div>
                        <div class="col-md-8">
                            <input id="name" name="name" type="text" class="form-control" autocomplete="off" value="{{old('name')}}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <label class="form-label">Username*</label>
                        </div>
                        <div class="col-md-8">
                            <input id="username" name="username" type="text" class="form-control" autocomplete="off" value="{{old('username')}}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <label class="form-label">Email</label>
                        </div>
                        <div class="col-md-8">
                            <input id="email" name="email" type="email" class="form-control" autocomplete="off" value="{{old('email')}}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <label class="form-label">Telepon</label>
                        </div>
                        <div class="col-md-8">
                            <input id="phone" name="phone" type="text" class="form-control" autocomplete="off" value="{{old('phone')}}">
                        </div>
                    </div>                    
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <label class="form-label">Level Akses</label>
                        </div>
                        <div class="col-md-4">
                            <select id="accessLevel" name="accessLevel" class="form-select" required>
                                @switch(Auth::user()->accessLevel)
                                @case (0)
                                <option value="0" @if(old('accessLevel',99) == 0) selected @endif>00 - Superadmin</option>

                                @case (1)
                                <option value="1" @if(old('accessLevel',99) == 1) selected @endif>01 - Admin</option>

                                @case (10)
                                <option value="10" @if(old('accessLevel',99) == 10) selected @endif>10 - Lite Admin</option>

                                @case (20)
                                <option value="20" @if(old('accessLevel',99) == 20) selected @endif>20 - Superuser</option>

                                @default
                                <option value="30" @if(old('accessLevel',99) == 30) selected @endif>30 - Advanced User</option>

                                <option value="40" @if(old('accessLevel',99) == 40) selected @endif>40 - User</option>

                                <option value="99" @if(old('accessLevel',99) == 99) selected @endif>99 - Tamu</option>
                                @endswitch
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <label class="form-label">Password</label>
                        </div>

                        <div class="col-md-8">
                            <input id="password" class="form-control" type="password" name="password" autocomplete="new-password"/>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <label class="form-label">Ulangi Password</label>
                        </div>

                        <div class="col-md-8">
                            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" autocomplete="new-password"/>
                        </div>
                    </div>        
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Nomor Induk Kependudukan*</span>
                        </div>
                        <div class="col-md-4">
                            <input id="nik" name="nik" type="text" class="form-control" autocomplete="none" value="{{old('nik')}}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Jenis Kelamin*</span>
                        </div>
                        <div class="col-md-4">
                            <select id="gender" name="gender" class="form-select" >
                                <option value="-1" @if(old('gender') == -1) selected @endif>--Pilih Jenis Kelamin--</option>
                                <option value="1" @if(old('gender') == 1) selected @endif>Laki-laki</option>
                                <option value="2" @if(old('gender') == 2) selected @endif>Perempuan</option>
                            </select>
                        </div>      
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Tanggal Lahir*</span>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" id="birthdate" name="birthdate" type="date"  value="{{old('birthdate')}}">  
                            <span class="add-on"><i class="icon-th"></i></span>
                        </div>      
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Pendidikan*</span>
                        </div>
                        <div class="col-md-4">
                            <select id="pendidikan" name="pendidikan" class="form-select" >
                                <option value="-1" @if(old('pendidikan') == -1) selected @endif>--Pilih Jenjang Pendidikan--</option>
                                <option value="0" @if(old('pendidikan') == 0) selected @endif>Tidak Sekolah</option>
                                <option value="1" @if(old('pendidikan') == 1) selected @endif>SD/Sederajat</option>
                                <option value="2" @if(old('pendidikan') == 2) selected @endif>SMP/Sederajat</option>
                                <option value="3" @if(old('pendidikan') == 3) selected @endif>SMA/Sederajat</option>
                                <option value="4" @if(old('pendidikan') == 4) selected @endif>Diploma 1</option>
                                <option value="5" @if(old('pendidikan') == 5) selected @endif>Diploma 2</option>
                                <option value="6" @if(old('pendidikan') == 6) selected @endif>Diploma 3</option>
                                <option value="7" @if(old('pendidikan') == 7) selected @endif>Diploma 4/Sarjana</option>
                                <option value="8" @if(old('pendidikan') == 8) selected @endif>Master</option>
                                <option value="9" @if(old('pendidikan') == 9) selected @endif>Doktor</option>
                            </select>
                        </div>      
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Bidang Pendidikan*</span>
                        </div>
                        <div class="col-md-8">
                            <textarea class="form-control" id="bidangPendidikan" name="bidangPendidikan" rows="4" cols="50" autocomplete="none">{{old('bidangPendidikan')}}</textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Tanggal Mulai*</span>
                        </div>
                        <div class="col-md-4">
                            <input id="startdate" name="startdate" class="form-control" type="date"  value="{{old('startdate')}}">  
                            <span class="add-on"><i class="icon-th"></i></span>
                        </div>      
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Alamat*</span>
                        </div>
                        <div class="col-md-8">
                            <textarea class="form-control" id="address" name="address" rows="4" cols="50" autocomplete="none">{{old('address')}}</textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Jabatan*</span>
                        </div>
                        <div class="col-md-4">
                            <select id="structural" name="structural" class="form-select" >
                                <option value="-1" selected>--Pilih dahulu--</option>
                                @foreach ($structpos as $position)
                                @if ( $position->id == old('structural') )
                                <option value="{{ $position->id }}" selected>{{ $position->name }}</option>
                                @else
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Bagian*</span>
                        </div>
                        <div class="col-md-4">
                            <select id="workPosition" name="workPosition" class="form-select" >
                                <option value="-1" selected>--Pilih dahulu--</option>
                                @foreach ($workpos as $workpo)
                                @if ( $workpo->id == old('workPosition') )
                                <option value="{{ $workpo->id }}" selected>{{ $workpo->name }}</option>
                                @else
                                <option value="{{ $workpo->id }}">{{ $workpo->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Penempatan*</span>
                        </div>
                        <div class="col-md-4">
                            <select id="OrgStructureOption" name="OrgStructureOption" class="form-select" >
                                <option value="">--Pilih Jabatan & Bagian dahulu--</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Jenis Karyawan*</span>
                        </div>
                        <div class="col-md-4">
                            <select id="employmentStatus" name="employmentStatus" class="form-select" >
                                <option value="-1" @if(old('employmentStatus') == -1) selected @endif>--Pilih Jenis Karyawan--</option>
                                <option value="1" @if(old('employmentStatus') == 1) selected @endif>Bulanan</option>
                                <option value="2" @if(old('employmentStatus') == 2) selected @endif>Harian</option>
                                <option value="3" @if(old('employmentStatus') == 3) selected @endif>Borongan</option>

                                
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Gaji Pokok*</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text text-end">Rp. </span>
                                <input id="gajiPokok" name="gajiPokok" value="{{old('gajiPokok',0)}}" type="text" class="form-control text-end" autocomplete="none">
                                <span class="input-group-text col-3">per bulan</span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Uang Harian*</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text text-end">Rp. </span>
                                <input id="gajiHarian" name="gajiHarian" value="{{old('gajiHarian',0)}}" type="text" class="form-control text-end" autocomplete="none">
                                <span class="input-group-text col-3">per hari</span>
                            </div>
                        </div>        
                    </div>                    
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Uang Lembur*</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">Rp. </span>
                                <input id="uangLembur" name="uangLembur" type="text" value="{{old('uangLembur',0)}}" class="form-control text-end" autocomplete="none">
                                <span class="input-group-text col-3">per jam</span>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">No Rekening</span>
                        </div>
                        <div class="col-md-4">
                            <input id="noRekening" value="{{old('noRekening')}}" name="noRekening" type="text" class="form-control" autocomplete="none">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Bank</span>
                        </div>
                        <div class="col-md-8">
                            <select id="bankid" name="bankid" class="form-control" >
                                <option value="">--Choose First--</option>
                                @foreach ($banks as $bank)
                                @if ($bank->id == old('bankid'))
                                <option value="{{ $bank->id }}" selected>{{ $bank->shortname }} - {{$bank->name}}</option>
                                @else
                                <option value="{{ $bank->id }}">{{ $bank->shortname }} - {{$bank->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                        </div>
                        <div class="col-md-8">
                            <button class="btn btn-primary buttonConf" id="buttSubmit" type="submit">Ok</button>
                            <button type="Reset" class="btn btn-danger buttonConf"ß>Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection