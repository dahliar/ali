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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function myFunction(){
        Swal.fire({
            title: 'Upload file presensi?',
            text: "Upload file presensi harian",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'File presensi disimpan',
                    text: "Simpan file presensi harian.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("presenceFileStore").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Upload file presensi dibatalkan",
                    'info'
                    );
            }
        })
    };

    function getHonorariumList(){
        var presenceDate = document.getElementById("presenceDate").value;
        Swal.fire({
          title: 'Honorarium tanggal '+presenceDate,
          text: 'Generate file import data?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, generate data import',
          cancelButtonText: 'Tidak, batalkan saja'
      }).then((result) => {
          if (result.isConfirmed) {
            window.open('{{ url("getHonorariumImportList")}}'+"/"+presenceDate, '_blank');
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
                        <a class="white-text" href="{{ ('presenceHarianList')}}">Presensi</a>
                    </li>
                    <li class="breadcrumb-item active">Import Honorarium Pegawai</li>
                </ol>
            </nav>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="presenceFileStoreDownload" action="" method="POST" name="presenceFileStoreDownload" autocomplete="off">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Tanggal Honorarium</span>
                            </div>
                            <div class="col-md-8">
                                <input type="date" id="presenceDate" name="presenceDate" class="form-control text-end" value="{{ old('presenceDate', date('Y-m-d'))}}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" onclick="getHonorariumList()">Download daftar honorarium</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="presenceFileStore" action="{{url('honorariumImportStore')}}" method="POST" name="presenceFileStore" autocomplete="off" enctype="multipart/form-data">
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
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Upload dan simpan honorarium</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <ol>
                        <li>Pilih tanggal Presensi</li>
                        <li>Klik tombol "Download daftar honorarium"</li>
                        <li>Edit file yang telah didownload, hanya diperbolehkan untuk mengedit 3 kolom saja</li>
                        <ol>
                            <li>Status Masuk</li>
                            <ol>
                                <li>Nilai 1 : Dapat Honor</li>
                                <li>Nilai 0 : Tidak Dapat Honor</li>
                            </ol>
                            <li>Jumlah Honor dalam bentuk angka</li>
                            <li>Keterangan honorarium</li>
                            <li>Simpan File tersebut</li>
                        </ol>
                        <li>Klik "Choose File", dan pilih file yang telah diedit</li>
                        <li>Klik "Upload dan simpan honorarium"</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection