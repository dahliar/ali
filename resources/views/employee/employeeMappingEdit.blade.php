<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if ((Auth::user()->isAuthenticatedUserSameAsUserIdChoosen($choosenUser->id) or Auth::user()->isAdmin() or Auth::user()->isHumanResources()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
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
@endif
<body onload="getOrgStructureSelectOptionList({{$orgstructure->workPosition}},{{$orgstructure->structuralPosition}},{{$orgstructure->idorgstructure}})">
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
                        <li class="breadcrumb-item active">Ubah penempatan pegawai</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <form id="EmployeeEditForm" action="{{route('employeeMappingUpdate')}}" method="POST" name="EmployeeEditForm" autocomplete="off">
                    @csrf
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Name*</label>
                            </div>
                            <div class="col-md-4">
                                <input id="name" name="name" type="text" class="form-control" value="{{$choosenUser->name}}" readonly>
                                <input id="empid" name="empid" type="hidden" value="{{$employee->id}}" readonly>
                                <input id="mappingid" name="mappingid" type="hidden" value="{{$orgstructure->id}}" readonly>
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
                                <span class="label">NIK*</span>
                            </div>
                            <div class="col-md-4">
                                <input id="nik" name="nik" type="text" class="form-control" required autocomplete="none" value="{{$employee->nik}}" readonly>
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
                                <span class="label">Tanggal Mulai*</span>
                            </div>
                            <div class="col-md-4">
                                <input id="startdate" name="startdate" class="form-control" type="date"  value="{{$employee->startdate}}" readonly>  
                                <span class="add-on"><i class="icon-th"></i></span>
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
                                    @if ( $position->id == $orgstructure->structuralPosition )
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
                                    @if ( $workpo->id == $orgstructure->workPosition)
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
                                <span class="label">Gaji Pokok*</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text text-end">Rp. </span>
                                    <input id="gajiPokok" name="gajiPokok" value="{{$orgstructure->gp}}" type="text" class="form-control text-end" required autocomplete="none">
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
                                    <input id="uangHarian" name="uangHarian" value="{{$orgstructure->uh}}" type="text" class="form-control text-end" required autocomplete="none">
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
                                    <input id="uangLembur" name="uangLembur" type="text" value="{{$orgstructure->ul}}" class="form-control text-end" required autocomplete="none">
                                    <span class="input-group-text col-3">per jam</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-8">
                                <button class="btn btn-primary buttonConf" id="buttSubmit" type="submit">Simpan</button>
                                <button type="Reset" class="btn btn-danger buttonConf">Reset</button>
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