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

    function changedInPacking(){
        var unpackedLama = parseFloat(document.getElementById("unpackedLama").value);
        var unpackedPerubahan = parseFloat(document.getElementById("unpackedPerubahan").value);

        var unpackedAkhir = unpackedLama - unpackedPerubahan;

        if ( unpackedAkhir < 0 ) {
            Swal.fire(
                'Kurang dari 0.',
                "Jumlah akhir unpacked harus lebih dari sama dengan 0",
                'warning'
                );
            document.getElementById("buttSubmit").disabled = true;
        }
        else {
            document.getElementById("unpackedAkhir").value = unpackedAkhir;
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
        <form id="formEditUnpacked" action="{{route('unpackedUpdate')}}" method="POST" name="formEditUnpacked">
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
                    </div>  
                    <br>
                    <div class="row form-group">
                        <div class="col-md-3 text-end">
                            <span class="label">Unpacked saat ini</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input id="unpackedLama" name="unpackedLama" value="{{ old('unpackedLama',$oneItem->amountUnpacked) }}" type="number" class="form-control text-end" disabled="true">
                                <span class="input-group-text col-3">Kg</span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-end">
                            <span class="label">Pengurangan unpacked</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input oninput="changedInPacking()" id="unpackedPerubahan" name="unpackedPerubahan" type="number" value="{{ old('unpackedPerubahan',0) }}" class="form-control text-end">
                                <span class="input-group-text col-3">Kg</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row form-group">
                        <div class="col-md-3 text-end">
                            <span class="label">Unpacked akhir</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input id="unpackedAkhir" name="unpackedAkhir" type="number" class="form-control text-end" value="{{old('unpackedAkhir', $oneItem->amountUnpacked)}}">
                                <span class="input-group-text col-3">Kg</span>
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
@endsection