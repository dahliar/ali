@extends('layouts.layout')

@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var i=0;
        $('#scannedCode').change(function(){
            var barcode = document.getElementById("scannedCode").value;
            var found1=false;

            $.ajax({
                url: '{{ url("submitScanPresensiKeluar") }}',
                type: "get",
                data: {
                    barcode: barcode
                },
                dataType: "json",
                success:function(data){
                    if (data['isError']==1){
                        Swal.fire({
                            icon: 'success',
                            title: 'Presensi berhasil dilakukan!',
                            text: data['message'],
                            timer: 5000
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Gagal scan dan input',
                            text: data['message'],
                            timer: 5000
                        });
                    }
                }
            });
            document.getElementById("scannedCode").value="";
            document.getElementById("scannedCode").focus();

        });
    })
</script>
<div class="container-fluid">
    <div class="row">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-9">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Presensi Keluar</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="modal-content">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="col-md-12 text-center">
                        <input id="scannedCode" name="scannedCode" type="text" class="form-control text-center" placeholder="Scan barcode pegawai" autofocus>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <br>
                <br>
                <br>
                <br>
                <br>
                <a href="{{url('home')}}" class="btn btn-primary btn-lg" type="button">Kembali ke halaman utama</a>
                <br>
                <br>
            </div>  

        </div>
    </div>
</div>
@endsection