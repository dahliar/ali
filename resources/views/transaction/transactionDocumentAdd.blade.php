<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    function myFunction(){
        Swal.fire({
            title: 'Tambah Dokumen?',
            text: "Simpan dokumen eksport",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("transactionDocumentAddForm").submit();
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Pembuatan transaksi dibatalkan",
                    'info'
                    );
            }
        })
    };
</script>

@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data stock berhasil ditambahkan", "info");
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

<body>
    <div class="container-fluid">
        <div class="modal-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Transaksi Ekspor</li>
                    <li class="breadcrumb-item">Dokumen</li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>

        <form id="transactionDocumentAddForm" action="{{url('transactionDocumentAddStore')}}"  method="POST" name="transactionDocumentAddForm" enctype="multipart/form-data">
            @csrf
            <div class="card card-header">
                <div class="row form-group">

                    <div class="d-grid gap-2">
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Shipper</label>
                            </div>
                            <div class="col-md-8">
                                {{$shipper}}
                            </div>
                        </div>               
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Customer</label>
                            </div>
                            <div class="col-md-8">
                                {{$company}}
                            </div>
                        </div>               
                        <input id="transactionId" name="transactionId" type="hidden" class="form-control" autocomplete="off" value="{{$transactionId}}">
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Nama Dokumen*</label>
                            </div>
                            <div class="col-md-8">
                                <input id="nama" name="nama" type="text" class="form-control" autocomplete="off" value="{{old('nama')}}">
                            </div>
                        </div>               
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Jenis Dokumen</label>
                            </div>
                            <div class="col-md-4">
                                <select id="jenis" name="jenis" class="form-select" required>
                                    <option value="0">--choose one--</option>
                                    <option value="1">PEB</option>
                                    <option value="2">HC Mutu</option>
                                    <option value="2">HC BKIPM</option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <label class="form-label">Nomor Dokumen</label>
                            </div>
                            <div class="col-md-4">
                                <input id="nomor" name="nomor" type="text" class="form-control" autocomplete="off" value="{{old('nomor')}}">
                            </div>
                        </div>         
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Tanggal Dokumen*</span>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" id="tanggal" name="tanggal" type="date"  value="{{old('tanggal')}}">  
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>      
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-md-end">
                                <span class="label">Dokumen</span>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="dokumen" name="dokumen" accept="application/pdf">
                                </div>
                                <span style="font-size:9px" class="label">File dalam bentuk image dengan ukuran maksimal 1MB</span>
                            </div>
                        </div>  
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-8">
                                <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                                <button type="Reset" class="btn btn-danger buttonConf">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('header')
@include('partial.header')
@endsection







