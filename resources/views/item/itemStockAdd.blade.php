<!--BELUM-->
@php
$pageId = -1;
@endphp

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
    function totalAmount(){
        var packedLama = parseFloat(document.getElementById("packedLama").value);
        var packedTambah = parseFloat(document.getElementById("packedTambah").value);
        document.getElementById("packedTotal").value = new Number(packedTambah + packedLama);

        var unpackedLama = parseFloat(document.getElementById("unpackedLama").value);
        var unpackedTambah = parseFloat(document.getElementById("unpackedTambah").value);
        document.getElementById("unpackedTotal").value = new Number(unpackedTambah + unpackedLama);

        var wb=parseFloat(document.getElementById("wb").value);
        var ap   = packedLama + packedTambah + ((unpackedLama+unpackedTambah)/wb);
        var am   = ((packedLama + packedTambah)*wb) + unpackedLama+unpackedTambah;
        document.getElementById("amountPacking").value = new Number(Math.round(ap*100)) / 100;
        document.getElementById("amountMetric").value = new Number(Math.round(am*100)) / 100;
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
        <form id="formTambahItem" action="{{route('storeAdd')}}" method="get" name="formTambahItem">
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
                            <li class="breadcrumb-item active">Tambah Item baru</li>
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
                                <span class="label">Nama Item</span>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4">
                                    <input id="itemName" name="itemName" type="text" class="form-control text-end" value="{{$oneItem->itemName}}" disabled="true">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Spesies</span>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4">
                                    <input id="speciesName" name="speciesName" type="text" class="form-control text-end" value="{{$oneItem->speciesName}}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <input id="sizeName" name="sizeName" type="text" class="form-control text-end" value="{{$oneItem->sizeName}}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <input id="gradeName" name="gradeName" type="text" class="form-control text-end" value="{{$oneItem->gradeName}}" readonly>
                                </div>
                            </div>
                        </div>                      
                        <div class="row form-group">

                            <div class="col-md-3 text-end">
                                <span class="label">Pack</span>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4">
                                    <input id="packingName" name="packingName" type="text" class="form-control text-end" value="{{$oneItem->packingName}}" readonly="">
                                </div>
                                <div class="col-md-4">
                                    <input id="frozenName" name="frozenName" type="text" class="form-control text-end" value="{{$oneItem->freezingName}}"  disabled="true">
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input id="wb" name="wb" type="number" class="form-control text-end" value="{{$oneItem->weightbase}}" disabled="true">
                                        <span class="input-group-text col-3">Kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        
                        <br>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                            </div>
                            <div class="col-md-6">
                                <table width="100%">
                                    <tr>
                                        <td><hr /></td>
                                        <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>Packed</h4></td>
                                        <td><hr /></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Stock saat ini</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="packedLama" name="packedLama" type="number" class="form-control text-end" value="{{old('packedLama', $oneItem->amount)}}" disabled="true">
                                    <span class="input-group-text col-3">{{$oneItem->packingShortname}}</span>
                                </div>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Penambahan*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input oninput="totalAmount()" id="packedTambah" name="packedTambah" value="{{ old('packedTambah',0) }}" type="number" class="form-control text-end" step="0.01">
                                    <span class="input-group-text col-3">{{$oneItem->packingShortname}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Total Packed*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="packedTotal" name="packedTotal" type="number" class="form-control text-end" value="{{old('packedTotal', $oneItem->amount)}}" disabled="true">
                                    <span class="input-group-text col-3">{{$oneItem->packingShortname}}</span>
                                </div>
                            </div>
                        </div> 

                        <br>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                            </div>
                            <div class="col-md-6">
                                <table width="100%">
                                    <tr>
                                        <td><hr /></td>
                                        <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>Unpacked</h4></td>
                                        <td><hr /></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Stock saat ini</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="unpackedLama" name="unpackedLama" type="number" class="form-control text-end" value="{{old('unpackedLama', $oneItem->amountUnpacked)}}" disabled="true">
                                    <span class="input-group-text col-3">Kg</span>
                                </div>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Penambahan*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input oninput="totalAmount()" id="unpackedTambah" name="unpackedTambah" value="{{ old('unpackedTambah',0) }}" type="number" class="form-control text-end" step="0.01">
                                    <span class="input-group-text col-3">Kg</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Total Unpacked*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input oninput="totalAmount()" id="unpackedTotal" name="unpackedTotal" value="{{ old('unpackedTotal',$oneItem->amountUnpacked) }}" type="number" class="form-control text-end" disabled="true">
                                    <span class="input-group-text col-3">Kg</span>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                            </div>
                            <div class="col-md-6">
                                <table width="100%">
                                    <tr>
                                        <td><hr /></td>
                                        <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>Total</h4></td>
                                        <td><hr /></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Total*</span>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input id="amountPacking" name="amountPacking" type="number" value="{{ old('amountPacking',($oneItem->amount+round(($oneItem->amountUnpacked/$oneItem->weightbase),2))) }}" class="form-control text-end" disabled="true">
                                    <span class="input-group-text col-3">{{$oneItem->packingShortname}}</span>
                                </div>
                            </div>
                            <div class="col-md-1" style="display: flex; justify-content: center; align-items: center;">
                                <h2>/</h2>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input id="amountMetric" name="amountMetric" type="number" value="{{ old('amountMetric',(($oneItem->amount*$oneItem->weightbase)+$oneItem->amountUnpacked)) }}" class="form-control text-end" disabled="true">
                                    <span class="input-group-text col-3">Kg</span>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                            </div>
                            <div class="col-md-6 text-end">
                                <table width="100%">
                                    <tr>
                                        <td><hr /></td>
                                        <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>Tanggal</h4></td>
                                        <td><hr /></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Proses*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="date" id="tanggalProses" name="tanggalProses" class="form-control text-end" value="{{ old('tanggalProses', date('Y-m-d', strtotime('-1 days'))) }}" >
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Package*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="date" id="tanggalPacking" name="tanggalPacking" class="form-control text-end" value="{{ old('tanggalPacking', date('Y-m-d')) }}" >
                                </div>
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
@else
@include('partial.noAccess')
@endif

@endsection