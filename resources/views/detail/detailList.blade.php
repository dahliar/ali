@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js" type="text/javascript" ></script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function tambahDetail(id){
        window.open(('{{ url("detailtransactionAdd") }}'+"/"+id), '_self');
    }

    function functionUbahHarga(id){
        document.getElementById("modalDetailId").value = id;
        document.getElementById("modalHarga").value = 0;
        $('#modalUbahHarga').modal('show');
    }

    function simpanPerubahanHarga(){
        var detailId = document.getElementById("modalDetailId").value;
        var harga = document.getElementById("modalHarga").value;
        var hargafob = document.getElementById("modalHargaFOB").value;

        $.ajax({
            url: '{{ url("storePerubahanHargaDetailTransaksi") }}',
            type: "POST",
            data: {
                "_token":"{{ csrf_token() }}",
                detailId : detailId,
                harga: harga,
                hargafob: hargafob
            },
            dataType: "json",
            success:function(data){
                if(data.isError==="0"){
                    swal.fire('info',data.message,'info');
                    $('#modalUbahHarga').modal('hide');
                    myFunction({{ $transactionId }});
                }
                else{
                    swal.fire('warning',data.message,'warning');
                }
            }
        });
    }

    function deleteItem(detailTransaction){
        Swal.fire({
            title: "Hapus item penjualan?",
            text: "Yakin hendak menghapus item penjualan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus aja!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("itemDetailTransactionDelete") }}'+"/"+detailTransaction,
                    type: "GET",
                    data : {"_token":"{{ csrf_token() }}"},
                    dataType: "text",
                    success:function(data){
                        swal.fire("Deleted!", "Detail Transaksi berhasil dihapus", "success");
                        myFunction({{ $transactionId }});
                    }
                });
            }
        })
    };

    function myFunction($id){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllDetail") }}' + "/"+ $id,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "35%", "targets":  [1], "className": "text-left" },
                {   "width": "5%", "targets":   [2], "className": "text-center" },
                {   "width": "10%", "targets":  [3], "className": "text-end" },
                {   "width": "10%", "targets":  [4], "className": "text-end" },
                {   "width": "10%", "targets":  [5], "className": "text-end" },
                {   "width": "10%", "targets":  [6], "className": "text-end" },
                {   "width": "10%", "targets":  [7], "className": "text-end" },
                {   "width": "10%", "targets":   [8], "className": "text-center" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'itemName', name: 'itemName'},
                {data: 'pshortname', name: 'pshortname'},
                {data: 'price', name: 'price'},
                {data: 'pricefob', name: 'pricefob'},
                {data: 'amount', name: 'amount'},
                {data: 'weight', name: 'weight'},
                {data: 'harga', name: 'harga'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
        });
    }

    $(document).ready(function() {
        myFunction({{ $transactionId }});
        /*
        $('#selectSpecies').change(function(){ 
            var e = document.getElementById("selectSpecies");
            var speciesId = e.options[e.selectedIndex].value;
            if (speciesId >= 0){
                myFunction(speciesId);
            } else{
                swal.fire("Warning!", "Pilih jenis spesies dulu!", "info");
            }

        });
        */
    });
</script>

@if (session('status'))
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            {{ session('status') }}
        </div>
        <div class="col-md-1 text-center">
            <span class="label"><strong >x</strong></span>
        </div>
    </div>
</div>
@endif

<body>
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-8 text-end">

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/transactionList') }}">Transaksi Penjualan</a>
                            </li>
                            <li class="breadcrumb-item active">Detil Transaksi Penjualan</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    @if ($tranStatus == 1)
                    <button onclick="tambahDetail({{$transactionId}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah detil by Total"><i class="fa fa-plus"> Item satuan </i>
                    </button>
                        <!--
                        <a onclick="functionStockKeluar({{$transactionId}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah detil by scan storage"><i class="fas fa-barcode"> Item Barcode </i>
                        </a>
                    -->
                    @endif
                </div>
            </div>
        </div>
        <div class="card card-body">
            <table class="table table-striped table-hover table-bordered data-table" id="datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Packing</th>
                        <th>Harga</th>
                        <th>HargaFOB</th>
                        <th>Jumlah</th>
                        <th>Berat</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                </tbody>
            </table>                
        </div>
    </div>
</body>

<div class="modal fade" id="modalUbahHarga" tabindex="-1" aria-labelledby="modalUbahHarga" aria-hidden="true">
    <form id="formUbahHarga" method="POST" name="formUbahHarga">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeePresenceHarianModal">Harga jual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row form-group my-auto">
                        <div class="col-md-1 text-end">
                        </div>
                        <div class="col-md-4 my-auto">
                            <span class="label">ID Detail Transaksi</span>
                        </div>
                        <div class="col-md-6">
                            <input type="text" id="modalDetailId" name="modalDetailId" class="form-control text-end" readonly>
                        </div>
                        <div class="col-md-1 text-end">
                        </div>
                    </div>     
                    <div class="row form-group my-auto">
                        <div class="col-md-1 text-end">
                        </div>
                        <div class="col-md-4 my-auto">
                            <span class="label">Harga Invoice</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text" id="modalMarker" name="modalMarker">{{$marker}}</span>
                                <input type="number" id="modalHarga" name="modalHarga" class="form-control text-end" value="0" step="0.01">
                                <span class="input-group-text">per Kg</span>

                            </div>
                        </div>
                        <div class="col-md-1 text-end">
                        </div>
                    </div>
                    <div class="row form-group my-auto">
                        <div class="col-md-1 text-end">
                        </div>
                        <div class="col-md-4 my-auto">
                            <span class="label">Harga FOB</span>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text" id="modalMarker" name="modalMarker">{{$marker}}</span>
                                <input type="number" id="modalHargaFOB" name="modalHargaFOB" class="form-control text-end" value="0" step="0.01">
                                <span class="input-group-text">per Kg</span>

                            </div>
                        </div>
                        <div class="col-md-1 text-end">
                        </div>
                    </div>      
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="simpanPerubahanHarga()">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection