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
        var i=0;
        $('#scannedCode').change(function(){
            var barcode = document.getElementById("scannedCode").value;
            var found1=false;

            $.ajax({
                url: '{{ url("submitPresensiKartuPegawai") }}',
                type: "get",
                data: {
                    barcode: barcode
                },
                dataType: "json",
                success:function(data){
                    if (data['found']==0){
                        Swal.fire(
                            'Batal disimpan!',
                            "Penyimpanan dibatalkan.",
                            'info'
                            );
                    } else {
                        Swal.fire(
                            "Gagal scan dan input",
                            data['message'],
                            'warning'
                            );
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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a class="white-text" href="">Presensi Keluar</a>
                        </li>
                        <li class="breadcrumb-item active">Scan Kartu Pegawai</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <form id="formScanStore" action="{{url('scanStoreMasuk')}}" method="post" name="formScanStore">
                    {{ csrf_field() }}
                    <div class="row form-group m-2">
                        <div class="col-md-2 text-md-end my-auto">
                            <span id="spanPacker">Scan kartu pegawai*</span>
                        </div>
                        <div class="col-md-3">
                            <input id="scannedCode" name="scannedCode" type="text" class="form-control" placeholder="kode barcode ter-scan" autofocus>
                        </div>
                    </div>   
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="table table-responsive">  
                            <table class="table" id="dynamic_field">

                            </table>   
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection