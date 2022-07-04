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

    });
</script>

<body>
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
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/undernameList') }}">Undername export</a>
                        </li>
                        <li class="breadcrumb-item active">Tambah barang undername</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah barang undername</h5>
            </div>
            <div class="modal-body">
                <form action="{{url('itemDetailUndernameAdd')}}" method="POST">
                    @csrf
                    <input id="undernameId" name="undernameId" type="hidden" value="{{ old('undernameId', $undernameId) }}">                    
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                            <span class="label">Barang*</span>
                        </div>
                        <div class="col-md-6">
                            <input id="item" value="{{ old('item') }}" name="item" type="text" class="form-control text-left">
                        </div>
                    </div>                    
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                            <span class="label">Jumlah*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input id="amount" value="{{ old('amount',0) }}" name="amount" type="number" class="form-control text-end" step="0.01">
                                <span class="input-group-text">MC / Bag</span>
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
                </form>
            </div>
        </div>
        <h3>Catatan : </h3>
        <ol>
            <li>Gunakan koma untuk untuk desimal</li>
        </ol>
    </div>
</body>
@endsection