<meta name="csrf-token" content="{{ csrf_token() }}" />

@extends('layouts.layout')

@section('content')
<script type="text/javascript"> 
    $(document).ready(function() {
        var i=1;
        $('#add').click(function(){
            i++;  
            $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td class="col-md-12"><div class="row form-group"><div class="col-md-4"><input id="contactName[]" placeholder="Nama Kontak" name="contactName[]" class="form-control" required></div><div class="col-md-3"><input id="phone[]" name="phone[]" class="form-control" placeholder="No Telepon" required></div><div class="col-md-4"><input id="email[]" name="email[]" class="form-control" placeholder="Email" type="email" required></div><div class="col-md-1"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove"><i class="fa fa-trash"></i></button></div></div></td></tr>'); 
        });
        $(document).on('click', '.btn_remove', function(){  
            var button_id = $(this).attr("id");   
            $('#row'+button_id+'').remove();  
        });
    });
</script>
@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data perusahaan berhasil ditambahkan", "info");
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
    <div class="modal-content">
        <div class="modal-header">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a class="white-text" href="{{ url('companyList')}}">Perusahaan</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-body">
        <div class="col-12">
            <form id="CompanyForm" action="{{route('companyStore')}}"  method="get" name="CompanyForm">
                @csrf
                <div class="d-grid gap-1">
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Nama</span>
                        </div>
                        <div class="col-md-4">
                            <input id="name" name="name" class="form-control" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Alamat</span>
                        </div>
                        <div class="col-md-7">
                            <textarea id="address" name="address" rows="4"  class="form-control">{{ old('address') }}</textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Negara</span>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select w-100" id="countryId" name="countryId">
                                <option value="-1">--Pilih dahulu--</option>
                                @foreach ($countries as $country)
                                @if ( $country->id == old('countryId') )
                                <option value="{{ $country->id }}" selected>{{ $country->name }}</option>
                                @else
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="npwp">NPWP</span>
                        </div>
                        <div class="col-md-4">
                            <input id="npwpnum" name="npwpnum" class="form-control" value="{{ old('npwpnum') }}" placeholder="NPWP Number">
                        </div>
                        <div class="col-md-6">
                            Biarkan kosong jika tidak memiliki NPWP
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Pajak terhitung</span>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select w-100" id="taxIncluded" name="taxIncluded">
                                <option value="-1" @if(old('taxIncluded') == 0) selected @endif>--Choose First--</option>
                                <option value="0" @if(old('taxIncluded') == 0) selected @endif>NO</option>
                                <option value="1" @if(old('taxIncluded') == 1) selected @endif>YES</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label" id="spanBank">Kontak person</span>
                        </div>
                        <div class="col-md-2">
                            <button type="button" name="add" id="add" class="btn btn-primary"><i class="fa fa-plus"></i> Kontak</button>
                        </div>
                    </div>


                    <div class="row form-group">
                        <div class="col-md-2 text-end"></div>
                        <div class="col-md-10">
                            <div class="table-responsive">  
                                <table class="table table-striped table-hover table-bordered" id="dynamic_field">
                                </table> 
                            </div>  
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@if ($errors->any())
@if (!empty(old('contactName')))
<script type="text/javascript">
    var $cn = @json(old('contactName'));
    var $p = @json(old('phone'));
    var $e = @json(old('email'));
    for ($i=0; $i<$cn.length; $i++){
        $('#dynamic_field').append('<tr id="row'+$i+'" class="dynamic-added"><td class="col-md-12"><div class="row form-group"><div class="col-md-4"><input id="contactName[]" placeholder="Nama Kontak" name="contactName[]" class="form-control" value="'+$cn[$i]+'" required></div><div class="col-md-3"><input id="phone[]" name="phone[]" class="form-control" placeholder="No Telepon" value="'+$p[$i]+'" required></div><div class="col-md-4"><input id="email[]" name="email[]" class="form-control" placeholder="Email" type="email" value="'+$e[$i]+'" required></div><div class="col-md-1"><button type="button" name="remove" id="'+$i+'" class="btn btn-danger btn_remove"><i class="fa fa-trash"></i></button></div></div></td></tr>');  
    }
</script>
@endif
@endif

@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('header')
@include('partial.header')
@endsection







