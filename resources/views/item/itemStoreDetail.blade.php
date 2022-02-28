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

$data = json_decode($oneStore);

if ($data[0]->isApproved == 0)
    $isApproved = "Not Yet";
else if ($data[0]->isApproved == 1)
    $isApproved = "Approved";
else
    $isApproved = "Rejected";

?>
<script type="text/javascript">
</script>
<div class="container-fluid">
    <div class="row">
        <form id="FormDetilPembelian" method="post" name="FormDetilPembelian">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ route('itemList')}}">Items</a>
                            </li>
                            <li class="breadcrumb-item active">Store Details : {!!$data[0]->species.' - '.$data[0]->size!!}</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-8 form-inline">
                                <div class="col-md-6">                      
                                    <input id="itemId" value="{{$data[0]->id}}" name="itemId" type="hidden">
                                </div>
                                <div class="col-md-6">                      
                                    <input id="idItem" name="idItem" type="hidden" value="{{$data[0]->id}}" disabled="true">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Species Item</span>
                            </div>
                            <div class="col-md-5">
                                <input id="itemName" name="itemName" type="text" class="form-control text-md-right" value="{{$data[0]->species}} - {{$data[0]->name}}" disabled="true">
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="speciesListAddLabel"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Size</span>
                            </div>
                            <div class="col-md-5">
                                <input id="sizeName" name="sizeName" type="text" class="form-control text-md-right" value="{{$data[0]->size}}" disabled="true">
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="sizeListAddLabel"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Grade</span>
                            </div>
                            <div class="col-md-5">
                                <input id="gradeName" name="gradeName" type="text" class="form-control text-md-right" value="{{$data[0]->grade}}" disabled="true">
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="gradeListAddLabel"></span>
                            </div>
                        </div>                      
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Packing Type</span>
                            </div>
                            <div class="col-md-5">
                                <input id="packingName" name="packingName" type="text" class="form-control text-md-right" value="{{$data[0]->packing}}" disabled="true">
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="mcListAddLabel"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Freeze Type</span>
                            </div>
                            <div class="col-md-5">
                                <input id="frozenName" name="frozenName" type="text" class="form-control text-md-right" value="{{$data[0]->freezing}}"  disabled="true">
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="mcListAddLabel"></span>
                            </div>
                        </div>          
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Weight Base</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="jumlahAdd" name="jumlahAdd" type="text" class="form-control text-end" value="{{$data[0]->weightbase}}" disabled="true">
                                    <span class="input-group-text col-3">Kg</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="mcListAddLabel"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Tanggal Packing</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="jumlahTambah" name="jumlahTambah" value="{{$data[0]->datePackage}}" type="input" class="form-control" disabled="true">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="jumlahAddLabel"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Tanggal Proses</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="jumlahTambah" name="jumlahTambah" value="{{$data[0]->dateProcess}}" type="input" class="form-control" disabled="true">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="jumlahAddLabel"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Tanggal Input</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="jumlahTambah" name="jumlahTambah" value="{{$data[0]->dateInsert}}" type="input" class="form-control" disabled="true">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="jumlahAddLabel"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Input By</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="jumlahTotal" name="jumlahTotal" type="text" value="{{$data[0]->username}}" class="form-control" disabled="true">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="jumlahAddLabel"></span>
                            </div>
                        </div>                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Is Approved</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="jumlahTotal" name="jumlahTotal" type="text" value="{{$isApproved}}" class="form-control" disabled="true">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <span class="label err" id="jumlahAddLabel"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn btn-primary">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
@else
@include('partial.noAccess')
@endif

@endsection