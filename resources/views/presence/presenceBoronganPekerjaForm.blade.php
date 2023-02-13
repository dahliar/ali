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
    function myFunction(){
        Swal.fire({
            title: 'Tambah data pekerja presensi borongan?',
            text: "Simpan presensi borongan.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Presensi borongan disimpan',
                    text: "Simpan presensi borongan.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("formTambahPekerjaBorongan").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Pembuatan presensi borongan dibatalkan",
                    'info'
                    );
            }
        })
    };

    function workerAddedChange($a, $b){
        if ($a){
            document.getElementById("workerAdded").value++;
            document.getElementById("boronganGender["+$b+"]").checked = true; 
        } else{
            document.getElementById("workerAdded").value--;
            document.getElementById("boronganGender["+$b+"]").checked = false; 
        }
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
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb primary-color">
                <li class="breadcrumb-item">
                    <a class="white-text" href="{{ url('/home') }}">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="white-text" href="{{ url('/boronganList') }}">Borongan</a>
                </li>
                <li class="breadcrumb-item active">Tambah Pekerja Borongan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid">
    <div class="row form-group">
        <div class="d-grid gap">
            <form id="formTambahPekerjaBorongan" action="{{url('storePekerjaBorongan')}}/{{$borongan->id}}" method="POST" name="formTambahPekerjaBorongan">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="row form-inline">
                            <h5 class="modal-title" id="exampleModalLabel">Daftar Pekerja Borongan</h5>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row form-inline">
                            <input type="hidden" value="{{$borongan->id}}"id="boronganId" name="boronganId" class="form-control" readonly>
                            <div class="row form-group">
                                <div class="col-md-3 text-end">
                                    <label class="form-label">Upah bersih</label>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text text-end">Rp.</span>
                                        <input type="text" value="{{$borongan->netPrice}}"id="netPayment" name="netPayment" class="form-control text-end" readonly>
                                        <span class="input-group-text text-end">per Pekerja</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-end">
                                    <label class="form-label">Jumlah Pekerja Terpilih</label>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text"> Terpilih </span>
                                        <input type="text" value="0" id="workerAdded" name="workerAdded" class="form-control text-end" readonly>
                                        <span class="input-group-text"> orang dari </span>
                                        <input type="text" value="{{$borongan->worker}}"id="worker" name="worker" class="form-control text-end" readonly>
                                        <span class="input-group-text">Orang</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-content">
                    <div class="modal-body">
                        <table style="width: 40%;  margin-left: auto; margin-right: auto;" class="center table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 10%;text-align: center;">Masuk</th>
                                    <th style="width: 20%;">NIP</th>
                                    <th style="width: 45%;">Nama</th>
                                    <th style="width: 10%;">Gender</th>
                                    <th style="width: 10%;">Jenis</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                $no=1;
                                @endphp
                                @foreach($employees as $employee)
                                <tr>
                                    <td style="width: 5%;">
                                        @php echo $no @endphp 
                                    </td>
                                    <td style="width: 10%;text-align: center;">
                                        <input id="boronganWorker[{{$employee->empid}}]" type="checkbox" class="form-check-input" onchange="workerAddedChange(this.checked, {{$employee->empid}})" name="boronganWorker[{{$employee->empid}}]" value="{{$employee->empid}}">
                                        <input id="boronganGender[{{$employee->empid}}]" style="display:none" type="checkbox" class="form-check-input" type="hidden" name="boronganGender[{{$employee->empid}}]" value="{{$employee->genderValue}}">
                                    </td>
                                    <td style="width: 20%;">{{$employee->nip}}</td>
                                    <td style="width: 45%;">{{$employee->nama}}</td>
                                    <td style="width: 10%;">{{$employee->gender}}</td>
                                    <td style="width: 10%;">{{$employee->employmentStatus}}</td> 
                                    @php $no+=1;    @endphp                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row form-group text-center">
                    <div class="col-md-5">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                        <input type="reset" value="Reset" class="btn btn-secondary">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection