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
    function totalAmount(){
        var addedPacked = parseFloat(document.getElementById("amountPacked").value);
        var addedUnpacked = parseFloat(document.getElementById("amountUnpacked").value);
        var addedAmount = addedPacked+addedUnpacked;
        document.getElementById('newAmount').value = addedAmount;
    }
</script>
@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data item berhasil ditambahkan", "info");
</script>
@endif

<script type="text/javascript">
    function myFunction(){
        Swal.fire({
            title: 'Ubah jumlah stock barang?',
            text: "Simpan perubahan jumlah stock",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Perubahan jumlah stock disimpan',
                    text: "Simpan perubahan jumlah stock",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("formUbahStockItem").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Perubahan jumlah stock dibatalkan",
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


<div class="container-fluid">
    <div class="row">
        <form id="formUbahStockItem" action="{{route('storeUpdate')}}" method="get" name="formUbahStockItem">
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
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-8 form-inline">
                                <div class="col-md-6"> 
                                    <input id="itemId" value="{{$store->itemId}}" name="itemId" type="hidden" readonly>
                                    <input id="storeId" value="{{$store->id}}" name="storeId" type="hidden" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Tanggal Proses*</span>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="text" id="tanggalProses" name="tanggalProses" class="form-control text-end" value="{{ $store->dateProcess }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Item Name</span>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4">
                                    <input id="item" name="item" type="text" class="form-control text-end" value="{{$data->itemName}}" disabled="true">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Barang</span>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4">
                                    <input id="speciesName" name="speciesName" type="text" class="form-control text-end" value="{{$data->speciesName}}" readonly>
                                </div>
                                <div class="col-md-4">

                                    <input id="sizeName" name="sizeName" type="text" class="form-control text-end" value="{{$data->sizeName}}" readonly>
                                </div>
                                <div class="col-md-4">

                                    <input id="gradeName" name="gradeName" type="text" class="form-control text-end" value="{{$data->gradeName}}" readonly>
                                </div>
                            </div>
                        </div>                      
                        <div class="row form-group">

                            <div class="col-md-3 text-end">
                                <span class="label">Pack</span>
                            </div>
                            <div class="col-md-6 row">
                                <div class="col-md-4">
                                    <input id="packingName" name="packingName" type="text" class="form-control text-end" value="{{$data->packingName}}" readonly="">
                                </div>
                                <div class="col-md-4">
                                    <input id="frozenName" name="frozenName" type="text" class="form-control text-end" value="{{$data->freezingName}}"  disabled="true">
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input id="jumlahAdd" name="jumlahAdd" type="number" class="form-control text-end" value="{{$data->weightbase}}" disabled="true">
                                        <span class="input-group-text col-3">Kg</span>
                                    </div>
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
                                        <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>Amount</h4></td>
                                        <td><hr /></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Packed Add*</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text col-3">Before : </span>
                                    <input value="{{$store->amountPacked}}" type="number" class="form-control text-end" disabled>
                                    <span class="input-group-text col-3">MC or Bag</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text col-3">After : </span>
                                    <input oninput="totalAmount()" id="amountPacked" name="amountPacked" value="{{ old('amountPacked',$store->amountPacked) }}" type="number" class="form-control text-end">
                                    <span class="input-group-text col-3">MC or Bag</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Unpacked Add*</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text col-3">Before : </span>
                                    <input value="{{ $store->amountUnpacked }}" type="number" class="form-control text-end" disabled>
                                    <span class="input-group-text col-3">MC or Bag</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text col-3">After : </span>
                                    <input oninput="totalAmount()" id="amountUnpacked" name="amountUnpacked" value="{{ old('amountUnpacked',$store->amountUnpacked) }}" type="number" class="form-control text-end">
                                    <span class="input-group-text col-3">MC or Bag</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Add*</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text col-3">Before : </span>
                                    <input id="pastAmount" name="pastAmount" value="{{ old('pastAmount',($store->amountPacked+$store->amountUnpacked)) }}" type="number" class="form-control text-end" readonly>
                                    <span class="input-group-text col-3">MC or Bag</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text col-3">After : </span>
                                    <input oninput="totalAmount()" id="newAmount" name="newAmount" value="{{ old('amount',($store->amountPacked+$store->amountUnpacked)) }}" type="number" class="form-control text-end" readonly>
                                    <span class="input-group-text col-3">MC or Bag</span>
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
                                <span class="label">Tanggal Package*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="date" id="tanggalPacking" name="tanggalPacking" class="form-control text-end" value="{{ old('tanggalPacking', $store->datePackage) }}" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection