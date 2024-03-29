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
            title: 'Tambah presensi borongan?',
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
                    document.getElementById("tambahBorongan").submit();
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

    $(document).ready(function() {
        $('#jenis').on('change', function() {
            var jenis = $(this).val();
            if (jenis<2){
                document.getElementById("cbval").value = "0";
                document.getElementById("loading").disabled = true;
                document.getElementById("loading").checked = false;
                document.getElementById("bagiHasil").readOnly = true;
            }else{
                document.getElementById("loading").disabled = false;
                document.getElementById("bagiHasil").readOnly = false;
            }
        });
        $('#loading').on('change', function() {
            var loading = document.getElementById("loading").checked;
            
            if (loading){
                document.getElementById("cbval").value = "1";
            }else{
                document.getElementById("cbval").value = "0";
            }
            
        });
    });
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
                <li class="breadcrumb-item active">Tambah Borongan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid">
    <div class="row form-group mb-2">
        <form id="tambahBorongan" method="POST" action="{{url('boronganStore')}}" name="tambahBorongan">
            @csrf
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Kerja Borongan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row form-group mb-2">
                            <div class="col-md-3 text-end">
                                <span class="label">Nama</span>
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="name" name="name" class="form-control" placeholder="Isi keterangan/nama untuk mempermudah pencarian, misal borongan cumi kupas dan tanggal" value="{{old('name')}}">
                            </div>
                        </div>                    
                        <div class="row form-group mb-2">
                            <div class="col-md-3 text-end">
                                <span class="label">Tanggal pembayaran transaksi</span>
                            </div>
                            <div class="col-md-4">
                                <input type="date" id="tanggalKerja" name="tanggalKerja" class="form-control text-end" value="{{date('Y-m-d')}}" value="{{old('tanggalKerja')}}">
                            </div>
                        </div>                    
                        <div class="row form-group mb-2">
                            <div class="col-md-3 text-end">
                                <span class="label">Honor per Kg</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text text-end">Rp.</span>
                                    <input type="number" id="hargaSatuan" name="hargaSatuan" class="form-control text-end" value="{{old('hargaSatuan',0)}}">
                                    <span class="input-group-text text-end"> per Kg</span>
                                </div>
                            </div>
                        </div>                    
                        <div class="row form-group mb-2">
                            <div class="col-md-3 text-end">
                                <span class="label">Berat timbangan</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="number" id="netweight" name="netweight" class="form-control text-end" value="{{old('netweight',0)}}" step="0.01">
                                    <span class="input-group-text text-end"> Kg</span>
                                </div>
                            </div>   
                        </div>
                        <div class="row form-group mb-2">
                            <div class="col-md-3 text-end">
                                <span class="label" id="spanPayment">Jenis</span>
                            </div>
                            <div class="col-md-4">
                                <select id="jenis" name="jenis" class="form-select">
                                    <option value="-1" selected>--Pilih salah satu--</option>
                                    <option value="1" @if(old('jenis') == 1) selected @endif>Fillet</option>
                                    <option value="2" @if(old('jenis') == 2) selected @endif>Packing</option>
                                </select>
                            </div>                    
                        </div>         
                        <div class="row form-group mb-2">
                            <div class="col-md-3 text-end">
                                <span class="label" id="spanPayment">Loading</span>
                            </div>
                            <div class="col-md-4">
                                <input id="loading" type="checkbox" class="form-check-input loading" name="loading" disabled>
                                <input type="hidden" id="cbval" name="cbval" value="0">
                            </div>                    
                        </div>
                        <div class="row form-group mb-2">
                            <div class="col-md-3 text-end">
                                <span class="label">Potongan bagi hasil</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="number" id="bagiHasil" name="bagiHasil" class="form-control text-end" value="{{old('bagiHasil',0)}}" step="1" min="0" max="20" readonly>
                                    <span class="input-group-text text-end"> %</span>
                                </div>
                            </div>   
                        </div>         

                        <div class="row form-group mb-2">
                            <div class="col-md-3 text-end">
                                <span class="label">Jumlah Pekerja</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="number" id="worker" name="worker" class="form-control text-end" value="{{old('worker',0)}}">
                                    <span class="input-group-text text-end"> Orang</span>
                                </div>
                            </div>
                        </div>                    

                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Reset</button>
                        <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection