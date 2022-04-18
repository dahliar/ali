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
    $(document).ready(function() {
        $('#unit').on('change', function() {
            var sel = document.getElementById("unit");
            var unit= sel.options[sel.selectedIndex].value;
            var unitText= sel.options[sel.selectedIndex].text;
            if (unit>0){
                document.getElementById("spanAmount").textContent=unitText;
                document.getElementById("spanMinimal").textContent=unitText;
            }
        });

    });
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
        <form id="formTambahBarang" action="{{url('goodUpdate')}}" method="post" name="formTambahBarang" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('goodList')}}">Barang Produksi</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <input id="idGood" name="idGood" type="hidden" value="{{old('idGood', $good->id)}}">
                        <div class="row form-group">

                            <div class="col-md-3 text-md-end">
                                <span class="label">Nama*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="name" name="name" type="text" class="form-control text-md-left" value="{{old('name', $good->name)}}" placeholder="Nama harus unik untuk barang tersebut" disabled>
                            </div>
                        </div>
                        <div class="row form-group">

                            <div class="col-md-3 text-md-end">
                                <span class="label">Kategori*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="categories" name="categories" type="text" class="form-control text-left" value="{{ $categories }}" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Unit Satuan*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="amount" name="amount" type="text" class="form-control text-left" value="{{ $unit }}" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Jumlah Saat ini*</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="amount" name="amount" type="text" class="form-control text-end" value="{{old('amount', $good->amount)}}" placeholder="gunakan titik untuk pecahan">
                                    <span name="spanAmount" id="spanAmount" class="input-group-text col-4">{{ $unit }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Jumlah minimal info alert*</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="minimal" name="minimal" type="text" class="form-control text-end" value="{{old('minimal', $good->minimalAmount)}}" placeholder="gunakan titik untuk pecahan">
                                    <span name="spanMinimal" id="spanMinimal" class="input-group-text col-4">{{ $unit }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-end">
                                <span class="label">Gambar</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="imageurl" name="imageurl">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Status Barang</span>
                            </div>
                            <div class="col-md-5">
                                @if (Auth::user()->accessLevel<-30)
                                <select id="isactive" name="isactive" class="form-select" >
                                    <option value="0" @if($good->isActive == 0) selected @endif>Non Aktif</option>
                                    <option value="1" @if($good->isActive == 1) selected @endif>Aktif</option>
                                </select>
                                <input id="isActiveCurrent" name="isActiveCurrent" type="hidden" value="{{$good->isActive}}">
                                @else
                                <input id="isActiveCurrent" name="isActiveCurrent" type="hidden" value="{{$good->isActive}}">
                                <input id="isactive" name="isactive" type="hidden" value="{{$good->isActive}}">
                                <input class="form-control" value="@if ($good->isActive==0) Non Aktif @else Aktif @endif" disabled>
                                @endif                                
                            </div>
                        </div>
                        <div class="row form-group">

                            <div class="col-md-3 text-md-end">
                                <span class="label"></span>
                            </div>
                            <div class="col-md-5">
                                <span id="info" style="color:red" class="col-4"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button id="buttonSubmit" type="submit" class="btn btn-primary">Save</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
    Catatan:<br>
    <ol>
        <li>Perubahan jumlah dilaman ini hanya digunakan untuk stock opname, bukan melakukan penambahan atau pengurangan stok reguler</li>
    </ol>
</div>
@endsection