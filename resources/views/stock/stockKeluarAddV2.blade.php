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
            var transactionId = document.getElementById("transactionId").value;

            var message="";
            $.ajax({
                url: '{{ url("scanStoreBarcodeKeluar") }}',
                type: "get",
                data: {
                    barcode: barcode,
                    transactionId: transactionId
                },
                dataType: "json",
                success:function(data){
                    Swal.fire(
                        "Scanned",
                        data['message'],
                        'warning'
                        );
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
                            <a class="white-text" href="{{ url('scanList')}}">Scan Transaction</a>
                        </li>
                        <li class="breadcrumb-item active">Barang Keluar V2</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <form id="formScanStore" action="{{url('scanStoreKeluarV2')}}" method="post" name="formScanStore">
                    {{ csrf_field() }}
                    <div class="row form-group m-2">
                        <input type="hidden" name="transactionId" id="transactionId" class="form-control" value="{{$transaction->id}}" readonly>
                    </div>   
                    <div class="row form-group m-2">
                        <div class="col-md-2 text-md-end my-auto">
                            <span id="spanPacker">Perusahaan</span>
                        </div>
                        <div class="col-md-3">
                            <input type="hidden" name="companyId" id="companyId" class="form-control" value="{{$transaction->companyId}}" readonly>
                            <input type="text" name="companyName" id="companyName" class="form-control" value="{{$transaction->companyName}}" readonly>
                        </div>
                    </div>   
                    <div class="row form-group m-2">
                        <div class="col-md-2 text-md-end my-auto">
                            <span id="spanPacker">PI Number</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" name="pinum" id="pinum" class="form-control" value="{{$transaction->pinum}}" readonly>
                            </div>
                        </div>
                    </div>   

                    <div class="row form-group m-2">
                        <div class="col-md-2 text-md-end my-auto">
                            <span id="spanPacker">Scan a barcode*</span>
                        </div>
                        <div class="col-md-3">
                            <input id="scannedCode" name="scannedCode" type="text" class="form-control" placeholder="kode barcode ter-scan" autofocus>
                        </div>
                    </div>   
                </form>
            </div>
        </div>
    </div>
    @endsection