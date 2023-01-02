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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getOpnameList(){
        Swal.fire({
            title: 'Stock Opname',
            text: 'Generate file import data stock opname?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, generate data import',
            cancelButtonText: 'Tidak, batalkan saja'
        }).then((result) => {
          if (result.isConfirmed) {
            window.open('{{ url("getStockOpnameImportList")}}', '_blank');
        }
    })

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
    <div class="modal-content">
        <div class="modal-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a class="white-text" href="{{ ('opname')}}">Opname</a>
                    </li>
                    <li class="breadcrumb-item active">Import Data Stock Opname</li>
                </ol>
            </nav>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary" onclick="getOpnameList()">Download daftar barang</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="presenceFileStore" action="{{url('presenceHarianImportStore')}}" method="POST" name="presenceFileStore" autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">File</span>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="presenceFile" name="presenceFile">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Lembur</span>
                            </div>
                            <div class="col-md-8">
                                <div class="form-check form-switch">
                                    <input id="isLembur" type="checkbox" class="form-check-input" name="isLembur" checked>
                                </div>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Upload dan Simpan Presensi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection