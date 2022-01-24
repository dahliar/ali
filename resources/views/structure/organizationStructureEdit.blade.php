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
                $("#reportTo").empty();
                $("#reportTo").append('<option value="-1">--Pilih Jabatan & Bagian dahulu--</option>');
                var html = '';
                var i;
                $.each(data, function(key, value) {
                    if (key != orgstructure){
                        $("#reportTo").append('<option value="' + key + '">' + value +
                            '</option>');
                    }else{
                        $("#reportTo").append('<option selected value="' + key + '">' + value +
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
        $('#filterWorkPosition').change(function(){
            var x = document.getElementById("filterStructuralPosition");
            var structuralPos = x.options[x.selectedIndex].value;
            x = document.getElementById("filterWorkPosition");
            var workPos = x.options[x.selectedIndex].value;
            if ((workPos!=-1) && (structuralPos!=-1)){
                getOrgStructureSelectOptionList(workPos, structuralPos, -1);
            }
        });
        $('#filterStructuralPosition').change(function(){
            var x = document.getElementById("filterStructuralPosition");
            var structuralPos = x.options[x.selectedIndex].value;
            x = document.getElementById("filterWorkPosition");
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
    getOrgStructureSelectOptionList(
        {{ old('filterWorkPosition', $reportTo->idworkpos) }}, 
        {{ old('filterStructuralPosition', $reportTo->idstructuralpos) }}, 
        {{ old('reportTo', $reportTo->id) }} 
        );
    </script>
    @endif
    <body onload="getOrgStructureSelectOptionList(
        {{ old('filterWorkPosition', $reportTo->idworkpos) }}, 
        {{ old('filterStructuralPosition', $reportTo->idstructuralpos) }}, 
        {{ old('reportTo', $reportTo->id) }} 
        )">
        <div class="container-fluid">
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ ('employeeList')}}">Structure</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body d-grid gap-1">
                    <form id="StructureAddForm" action="{{url('organizationStructureUpdate')}}" method="POST" name="StructureAddForm" autocomplete="off">
                        @csrf
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Nama*</span>
                            </div>
                            <div class="col-md-6">
                                <input id="name" name="name" type="text" class="form-control" autocomplete="none" value="{{ old('name', $organization_structure->name) }}" readonly>
                                <input id="idStructure" name="idStructure" type="hidden" class="form-control" autocomplete="none" value="{{ old('idStructure', $organization_structure->id) }}" readonly>
                            </div>
                        </div>
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Bagian*</span>
                            </div>
                            <div class="col-md-6">
                                <select id="workPosition" name="workPosition" class="form-control w-100" disabled>
                                    <option selected value="-1">--Choose First--</option>
                                    @foreach ($workpos as $row)
                                    @if ( $row->id == old('workPosition', $organization_structure->idworkpos) )
                                    <option value="{{ $row->id }}" selected>{{ $row->name }}</option>
                                    @else
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Jabatan*</span>
                            </div>
                            <div class="col-md-6">
                                <select id="structuralPosition" name="structuralPosition" class="form-control w-100" disabled>
                                    <option selected value="-1">--Choose First--</option>
                                    @foreach ($structpos as $row)
                                    @if ( $row->id == old('structuralPosition', $organization_structure->idstructuralpos) )
                                    <option value="{{ $row->id }}" selected>{{ $row->name }}</option>
                                    @else
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Report To*</span>
                            </div>
                            <div class="col-md-6 border rounded p-2 row form-group" style="margin-left: 11;margin-right: 11;">
                                <select id="filterWorkPosition" name="filterWorkPosition" class="form-control w-100" >
                                    <option selected value="-1">--Choose First--</option>
                                    @foreach ($workpos as $row)
                                    @if ( $row->id == old('filterWorkPosition', $reportTo->idworkpos) )
                                    <option value="{{ $row->id }}" selected>{{ $row->name }}</option>
                                    @else
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <select id="filterStructuralPosition" name="filterStructuralPosition" class="form-control w-100" >
                                    <option selected value="-1">--Choose First--</option>
                                    @foreach ($structpos as $row)                             
                                    @if ( $row->id == old('filterStructuralPosition', $reportTo->idstructuralpos) )
                                    <option value="{{ $row->id }}" selected>{{ $row->name }}</option>
                                    @else
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <select id="reportTo" name="reportTo" class="form-control" >
                                    <option value="-1">--Choose First--</option>
                                </select>
                            </div>
                        </div>
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Max Employee</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input id="maxemployee" name="maxemployee" type="number" class="form-control text-end" autocomplete="none" value="{{ old('maxemployee', $organization_structure->maxemployee) }}">
                                    <span class="input-group-text" id="basic-addon2">posisi</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Gaji Pokok*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input id="gajiPokok" name="gajiPokok" type="number" class="form-control text-end" autocomplete="none" value="{{ old('gajiPokok', $organization_structure->defGajiPokok) }}">
                                    <span class="input-group-text" id="basic-addon2">per bulan</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Uang Harian*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <div class="input-group">
                                        <input id="uangHarian" name="uangHarian" type="number" class="form-control text-end" autocomplete="none" value="{{ old('uangHarian', $organization_structure->defUangHarian) }}">
                                        <span class="input-group-text" id="basic-addon2">per hari</span>
                                    </div>
                                </div>
                            </div>
                        </div>                      
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Uang Lembur*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input id="uangLembur" name="uangLembur" type="number" class="form-control text-end" autocomplete="none" value="{{ old('uangLembur', $organization_structure->defUangLembur) }}">
                                    <span class="input-group-text" id="basic-addon2">per jam</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Uang transport*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input id="uangTransport" name="uangTransport" type="number" class="form-control text-end" autocomplete="none" value="{{ old('uangTransport', $organization_structure->defUangTransport) }}">
                                    <span class="input-group-text" id="basic-addon2">per hari</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Uang Makan*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input id="uangMakan" name="uangMakan" type="number" class="form-control text-end" autocomplete="none"value="{{ old('uangMakan', $organization_structure->defUangMakan) }}">
                                    <span class="input-group-text" id="basic-addon2">per hari</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-1 row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-8">
                                <button class="btn btn-primary buttonConf" id="buttSubmit" type="submit">Ok</button>
                                <button type="Reset" class="btn btn-danger buttonConf"ß>Reset</button>
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