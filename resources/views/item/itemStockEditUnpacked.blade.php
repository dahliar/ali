<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if ((Auth::user()->isProduction() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
<script type="text/javascript">

    function changedInPacking(){
        var packed = parseFloat(document.getElementById("amountPacking").value);
        var wb = parseFloat(document.getElementById("wb").value);

        var packedKg = packed*wb;

        var unpackedLama = parseFloat(document.getElementById("unpackedLama").value);
        var packedLama = parseFloat(document.getElementById("packedLama").value);

        var packedBaru = packedLama + packed;
        var unpackedBaru = unpackedLama - packedKg;

        if ((packed < 1) || (packed > (unpackedLama/wb))) {
            alert('Jumlah harus antara 1 dan ' + Math.floor(unpackedLama/wb));
            document.getElementById("buttSubmit").disabled = true;
        }
        else {
            document.getElementById("amountMetric").value = packedKg;
            document.getElementById("packedBaru").value = packedBaru;
            document.getElementById("unpackedBaru").value = unpackedBaru;
            document.getElementById("buttSubmit").disabled = false;
        }
    }
</script>
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
        <form id="formEditUnpacked" action="{{route('unpackedUpdate')}}" method="get" name="formEditUnpacked">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('itemList')}}">Items</a>
                            </li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-8 form-inline">
                                <div class="col-md-6">                      
                                    <input id="itemId" value="{{$oneItem->itemId}}" name="itemId" type="hidden"  readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Item Name</span>
                            </div>
                            <div class="col-md-8 row">
                                <div class="col-md-8">
                                    <input id="itemName" name="itemName" type="text" class="form-control" value="{{$oneItem->itemName}}" disabled="true">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                            </div>
                            <div class="col-md-8 row">
                                <div class="col-md-4">
                                    <input id="speciesName" name="speciesName" type="text" class="form-control" value="{{$oneItem->speciesName}}" readonly>
                                </div>
                                <div class="col-md-4">

                                    <input id="sizeName" name="sizeName" type="text" class="form-control" value="{{$oneItem->sizeName}}" readonly>
                                </div>
                                <div class="col-md-4">

                                    <input id="gradeName" name="gradeName" type="text" class="form-control" value="{{$oneItem->gradeName}}" readonly>
                                </div>
                            </div>
                        </div>                      
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                            </div>
                            <div class="col-md-8 row">
                                <div class="col-md-4">
                                    <input id="packingName" name="packingName" type="text" class="form-control" value="{{$oneItem->packingName}}" readonly="">
                                </div>
                                <div class="col-md-4">
                                    <input id="frozenName" name="frozenName" type="text" class="form-control" value="{{$oneItem->freezingName}}"  disabled="true">
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input id="wb" name="wb" type="number" class="form-control" value="{{$oneItem->weightbase}}" disabled="true">
                                        <span class="input-group-text col-3">Kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Tanggal Packing*</span>
                            </div>
                            <div class="col-md-8 row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="date" id="tanggalPacking" name="tanggalPacking" class="form-control text-end" value="{{ old('tanggalPacking', date('Y-m-d')) }}" >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                            </div>
                            <div class="col-md-8">
                                <table width="100%">
                                    <tr>
                                        <td><hr /></td>
                                        <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>Stock</h4></td>
                                        <td><hr /></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Unpacked<i class="fas fa-arrow-right"></i>Packed*</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input oninput="changedInPacking()" id="amountPacking" name="amountPacking" type="number" value="{{ old('amountPacking',0) }}" class="form-control text-end" min="1" max="@php floor($oneItem->amountUnpacked/$oneItem->weightbase); @endphp">
                                    <span class="input-group-text col-3">{{$oneItem->packingShortname}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input id="amountMetric" name="amountMetric" type="number" value="{{ old('amountMetric',0 ) }}" class="form-control text-end" readonly>
                                    <span class="input-group-text col-3">Kg</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label"></span>
                            </div>
                            <div class="col-md-4">
                                <span class="label">
                                    <input id="maxUpdate" name="maxUpdate" value="@php
                                     echo floor($oneItem->amountUnpacked/$oneItem->weightbase);
                                     @endphp" type="hidden">
                                    Antara : 1 <i class="fas fa-arrows-alt-h"></i>
                                    @php
                                     echo floor($oneItem->amountUnpacked/$oneItem->weightbase);
                                     @endphp
                                </span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                            </div>
                            <div class="col-md-4">
                                <table width="100%">
                                    <tr>
                                        <td><hr /></td>
                                        <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>Before</h4></td>
                                        <td><hr /></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table width="100%">
                                    <tr>
                                        <td><hr /></td>
                                        <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>After</h4></td>
                                        <td><hr /></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Packed</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input id="packedLama" name="packedLama" value="{{ old('packedLama',$oneItem->amount) }}" type="number" class="form-control text-end" disabled="true">
                                    <span class="input-group-text col-3">{{$oneItem->packingShortname}}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input id="packedBaru" name="packedBaru" value="{{ old('packedBaru',$oneItem->amount) }}" type="number" class="form-control text-end" disabled="true">
                                    <span class="input-group-text col-3">{{$oneItem->packingShortname}}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Unpacked</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input id="unpackedLama" name="unpackedLama" type="number" class="form-control text-end" value="{{old('unpackedLama', $oneItem->amountUnpacked)}}" disabled="true">
                                    <span class="input-group-text col-3">Kg</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input id="unpackedBaru" name="unpackedBaru" type="number" class="form-control text-end" value="{{old('unpackedBaru', $oneItem->amountUnpacked)}}" disabled="true">
                                    <span class="input-group-text col-3">Kg</span>
                                </div>
                            </div>
                        </div>                       
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button name="buttSubmit" id="buttSubmit" type="submit" class="btn btn-primary">Save</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
</div>
@else
@include('partial.noAccess')
@endif

@endsection