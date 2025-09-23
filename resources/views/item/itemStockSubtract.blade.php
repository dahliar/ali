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
        var packedLama = parseFloat(document.getElementById("packedLama").value);
        var packedKurang = parseFloat(document.getElementById("packedKurang").value);
        if ((packedLama - packedKurang) >= 0){
            document.getElementById("packedTotal").value = new Number(packedLama - packedKurang);
        } else{
            document.getElementById("packedTotal").value = new Number(packedLama);
            document.getElementById("packedKurang").value = new Number(0);
            swal.fire("Alert", "jumlah stock akhir harus lebih dari sama dengan 0", "alert");
        }
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
            title: 'Kurangi jumlah stock barang?',
            text: "Simpan perubahan jumlah stock",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Jumlah stock dikurangi',
                    text: "Simpan perubahan jumlah stock",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("formPenguranganStock").submit();
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
        <form id="formPenguranganStock" action="{{url('storeSubtract')}}" method="post" name="formPenguranganStock">
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
                            <li class="breadcrumb-item active">Kurangi Jumlah Stock - {{$oneItem->itemName}}</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <input id="itemId" value="{{$oneItem->itemId}}" name="itemId" type="hidden"  readonly>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Barang</span>
                            </div>
                            <div class="col-md-6">
                                <input id="itemName" name="itemName" type="text" class="form-control text-left" value="{{$oneItem->itemName}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Stock saat ini</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="packedLama" name="packedLama" type="number" class="form-control text-end" value="{{old('packedLama', number_format($oneItem->amount, 2, '.', ''))}}" disabled="true">
                                    <span class="input-group-text col-3">{{$oneItem->packingShortname}}</span>
                                </div>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Pengurangan*</span>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input oninput="totalAmount()" id="packedKurang" name="packedKurang" value="{{ old('packedKurang',0) }}" type="number" class="form-control text-end" step="0.01">
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
                                    <input id="packedTotal" name="packedTotal" type="number" class="form-control text-end" value="{{old('packedTotal', number_format($oneItem->amount, 2, '.', ''))}}" disabled="true">
                                    <span class="input-group-text col-3">{{$oneItem->packingShortname}}</span>
                                </div>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Alasan pengurangan*</span>
                            </div>
                            <div class="col-md-6">
                                <textarea id="alasan" name="alasan" rows="4"  class="form-control" style="min-width: 100%" placeholder="Tambahkan alasan pengurangan stock">{{ old('alasan') }}</textarea>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-3 text-end">
                                <span class="label">Tanggal Pengurangan*</span>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="date" id="tanggal" name="tanggal" class="form-control text-end" value="{{ old('tanggal', date('Y-m-d')) }}" >
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