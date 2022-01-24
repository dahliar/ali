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
    function totalAmount(){
        var lama = parseFloat(document.getElementById("jumlahLama").value);
        var tambah = parseFloat(document.getElementById("jumlahTambah").value);
        var total = lama+tambah;
        document.getElementById('jumlahTotal').value = total;
    }
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
    @if (session('status'))
    <script type="text/javascript">
        swal("Success",  "{{session('status')}}" , "info");
    </script>
    @endif

    <div class="row">
        <form id="formTambahItem" action="{{url('sizeEditStore')}}" method="get" name="formTambahItem">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('sizeList')}}">Size</a>
                            </li>
                            <li class="breadcrumb-item active">Ubah</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-8 form-inline">
                                <div class="col-md-6">                      
                                    <input id="sizeId" value="{{$size->id}}" name="sizeId" type="hidden" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Species Name</span>
                            </div>
                            <div class="col-md-5">
                                <input id="speciesName" name="speciesName" type="text" class="form-control text-md-right" value="{{$size->speciesName}}" readonly >
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Size Name</span>
                            </div>
                            <div class="col-md-5">
                                <input id="name" name="name" type="text" class="form-control text-md-right" value="{{$size->name}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Status</span>
                            </div>
                            <div class="col-md-3">
                                <select id="isActive" name="isActive" class="form-select" >
                                    <option value="1" @if($size->isActive == 1) selected @endif>Aktif</option>
                                    <option value="0" @if($size->isActive == 0) selected @endif>Non-Aktif</option>
                                </select>
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