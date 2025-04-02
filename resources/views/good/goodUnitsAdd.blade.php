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
        <form id="formTambahBarang" action="{{url('goodUnitsStore')}}" method="post" name="formTambahBarang">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('goodUnits')}}">Barang Produksi</a>
                            </li>
                            <li class="breadcrumb-item active">Tambah Satuan</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nama Satuan*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="name" name="name" type="text" class="form-control text-md-left" value="{{old('name')}}" placeholder="Nama harus unik untuk satuan tersebut">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Nama pendek satuan*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="shortname" name="shortname" type="text" class="form-control text-md-left" value="{{old('shortname')}}" placeholder="Nama pendek harus unik untuk satuan tersebut">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-1"></div>
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
</div>
@endsection