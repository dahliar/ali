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
@if ((Auth::user()->isAdmin() or Auth::user()->isProduction()) and Session::has('employeeId') and (Session()->get('levelAccess') <= 3))
<script type="text/javascript"> 
    function selectOptionChange(speciesId, itemId){
        $.ajax({
            url: '{{ url("getItemsPurchaseForSelectOption") }}'+"/"+speciesId+"/"+'{{ $purchase->id }}',
            type: "GET",
            data : {"_token":"{{ csrf_token() }}"},
            dataType: "json",
            success:function(data){
                if(data){
                    var html = '';
                    var i;
                    html += '<option value="-1">--Choose First--</option>';
                    for(i=0; i<data.length; i++){
                        if (data[i].itemId != itemId){
                            html += '<option value='+data[i].itemId+'>'+
                            data[i].speciesName+
                            " "+data[i].gradeName+
                            " "+data[i].sizeName+
                            '</option>';
                        }
                        else{
                            html += '<option selected value='+data[i].itemId+'>'+
                            data[i].speciesName+
                            " "+data[i].gradeName+
                            " "+data[i].sizeName+
                            '</option>';
                        }
                    }
                    $('#item').html(html);
                }else{
                }
            }
        });
    }


    $(document).ready(function() {
        $('#species').on('change', function() {
            var speciesId = $(this).val();
            if (speciesId>0){
                selectOptionChange(speciesId, -1);
            }else{
                $('#item')
                .empty()
                .append('<option value="-1">--Pilih spesies dahulu--</option>');
                $('[name="amount"]').val(0);
                $('[name="harga"]').val(0);
                swal.fire('warning','Choose Species first!','info');
            }
        });
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
    selectOptionChange({{ old('species') }}, {{ old('item') }});
</script>
@endif

<div class="container-fluid">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb primary-color">
                <li class="breadcrumb-item">
                    <a class="white-text" href="{{ url('/home') }}">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="white-text" href="{{ url('/purchaseList') }}">Transaksi pembelian</a>
                </li>
                <li class="breadcrumb-item active">Tambah detil transaksi pembelian</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid">
    <div class="row form-group mb-2">
        <div class="d-grid gap">
            <form id="purchaseItemAddForm" action="{{url('purchaseItemStore')}}" method="get" name="purchaseItemAddForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah detil transaksi pembelian</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row form-group mb-2">
                            <div class="col-md-5 form-inline">
                                <div class="col-md-6">                      
                                    <input id="purchaseId" name="purchaseId" type="hidden" value="{{ old('purchaseId', $purchase->id) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group mb-2">
                            <div class="col-md-2 text-end">
                                <span class="label">Spesies*</span>
                            </div>
                            <div class="col-md-8">
                                <select class="form-select w-100" id="species" name="species">
                                    <option value="-1">--Pilih dahulu--</option>
                                    @foreach ($species as $spec)
                                    @if ( $spec->id == old('species') )
                                    <option value="{{ $spec->id }}" selected>{{ $spec->nameBahasa }}</option>
                                    @else
                                    <option value="{{ $spec->id }}">{{ $spec->nameBahasa }}</option>                    
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <span class="label err" id="speciesListAddLabel"></span>
                            </div>
                        </div>
                        <div class="row form-group mb-2">
                            <div class="col-md-2 text-end">
                                <span class="label">Barang*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="item" name="item" class="form-control" >
                                    <option value="-1">--Pilih Spesies dahulu--</option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group mb-2">
                            <div class="col-md-2 text-end">
                                <span class="label">Jumlah*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input id="amount" value="{{ old('amount',0) }}" name="amount" type="number" class="form-control text-end" step="0.01">
                                    <span class="input-group-text">Kg</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group mb-2">
                            <div class="col-md-2 text-end">
                                <span class="label">Harga*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text">{{$marker}}</span>
                                    <input id="harga" value="{{ old('harga',0) }}" name="harga" type="number" class="form-control text-end" step="0.01">
                                    <span class="input-group-text">per Kg</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group mb-2">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <input type="reset" value="Reset" class="btn btn-secondary">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <h3>Catatan : </h3>
            <ol>
                <li>Gunakan koma untuk untuk desimal</li>
            </ol>
        </div>
    </div>
</div>





@else
@include('partial.noAccess')
@endif

@endsection