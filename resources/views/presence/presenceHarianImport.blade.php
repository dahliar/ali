<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getPresenceList(){
        var presenceDate = document.getElementById("presenceDate").value;
        Swal.fire({
          title: 'Generate file presensi tanggal '+presenceDate+'?',
          text: 'Generate file presensi tanggal '+presenceDate+'?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, generate it!'
      }).then((result) => {
          if (result.isConfirmed) {
            window.open('{{ url("getPresenceHarianImportList")}}'+"/"+presenceDate, '_blank');
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
                    <li class="breadcrumb-item active">Import Presensi Pegawai Harian/Bulanan</li>
                </ol>
            </nav>
        </div>
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <form id="presenceFileStore" action="{{url('presenceFileStore')}}" method="POST" name="presenceFileStore" autocomplete="off">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label">Presence Date</span>
                            </div>
                            <div class="col-md-8">
                                <input type="date" id="presenceDate" name="presenceDate" class="form-control text-end" value="{{ old('presenceDate', date('Y-m-d'))}}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary" onclick="getPresenceList()">Download daftar presensi</button>
                            </div>
                        </div>
                    </form>
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
        <div class="modal-body">
            <div class="modal-content">
                <div class="modal-body">
                    <ol>
                        <li>Hanya untuk digunakan untuk presensi pegawai <b>Non Borongan</b></li>
                        <li>Pilih tanggal Presensi</li>
                        <li>Klik tombol "Download daftar presensi"</li>
                        <li>Edit file yang telah didownload, hanya diperbolehkan untuk mengedit 3 kolom saja</li>
                        <ol>
                            <li>Jam Masuk. Gunakan format Jam dan Menit, dengan dipisahkan simbol ":". Contoh 08:00</li>
                            <li>Jam Keluar. Gunakan format Jam dan Menit, dengan dipisahkan simbol ":". Contoh 16:45</li>
                            <li>Status Masuk</li>
                            <ol>
                                <li>Nilai 1 : Masuk</li>
                                <li>Nilai 0 : Tidak Masuk</li>
                            </ol>
                            <li>Simpan File tersebut</li>
                        </ol>
                        <li>Klik "Choose File", dan pilih file yang telah diedit</li>
                        <li>Klik "Upload dan simpan presensi"</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

@else
@include('partial.noAccess')
@endif

@endsection