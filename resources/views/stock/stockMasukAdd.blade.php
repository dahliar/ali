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
            title: 'Simpan data pemrosesan?',
            text: "Simpan barang ke storage",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("formScanStore").submit();
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Penyimpanan dibatalkan.",
                    'info'
                    );
            }
        })
    };

    $(document).ready(function() {
        var i=0;
        $('#scannedCode').change(function(){
            var barcode = document.getElementById("scannedCode").value;
            var tbl = document.getElementById("dynamic_field");
            var rc = tbl.rows.length;
            var found1=false;
            var found2=false;

            var message="";
            if(rc>0){
                for ( var x = 0; row = tbl.rows[x]; x++ ) {
                    if (tbl.rows[x].cells[1].innerHTML == barcode){
                        found1=true;
                        Swal.fire(
                            "Gagal scan dan input",
                            "Data input "+barcode+" sudah ada dalam list ter-scan",
                            'warning'
                            );
                        break;
                    }
                }
            }

            if(!found1){
                $.ajax({
                    url: '{{ url("checkStatusBarcodeBarang") }}',
                    type: "get",
                    data: {
                        barcode: barcode
                    },
                    dataType: "json",
                    success:function(data){
                        if (data['found']==0){
                            i++;  
                            $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added border border-light"><td class="col-md-1">'+i+'</td><td class="col-md-2">'+barcode+'</td><td class="col-md-2"><input type="hidden" id="barcode[]" name="barcode[]" value="'+barcode+'"></td><td class="col-md-6">'+data['name']+'</td><td class="col-md-1 text-center"><button type="button" id="'+i+'" class="btn btn_remove"><i class="fa fa-trash"></i></button></td></tr>'); 
                            document.getElementById("totalScanned").value=i;
                        } else {
                            Swal.fire(
                                "Gagal scan dan input",
                                data['message'],
                                'warning'
                                );
                        }
                    }
                });
            }
            document.getElementById("scannedCode").value="";
            document.getElementById("scannedCode").focus();

        });
        $(document).on('click', '.btn_remove', function(){  
            var button_id = $(this).attr("id");   
            $('#row'+button_id+'').remove(); 
            i--;
            document.getElementById("totalScanned").value=i;
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
                            <a class="white-text" href="{{ url('scanList')}}">Scanner</a>
                        </li>
                        <li class="breadcrumb-item active">Scan barang masuk ke storage</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row form-group m-2">
                    <div class="col-md-2 text-md-end my-auto">
                        <span id="spanPacker">Scan a barcode*</span>
                    </div>
                    <div class="col-md-3">
                        <input id="scannedCode" name="scannedCode" type="text" class="form-control" placeholder="kode barcode ter-scan" autofocus>
                    </div>
                </div>   
                <div class="row form-group m-2">
                    <div class="col-md-2 text-md-end my-auto">
                        <span id="spanPacker">Total Scanned</span>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="totalScanned" id="totalScanned" class="form-control" disabled>
                            <span class="input-group-text"> MC</span>
                        </div>
                    </div>
                </div>   
            </div>
            <form id="formScanStore" action="{{url('scanStoreMasuk')}}" method="post" name="formScanStore">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="table table-responsive">  
                            <table class="table" id="dynamic_field">

                            </table>   
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection