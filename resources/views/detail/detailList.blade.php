@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function tambahDetail(id){
        window.open(('{{ url("detailtransactionAdd") }}'+"/"+id), '_self');
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
            {   "width": "40%", "targets":  [1], "className": "text-left" },
            {   "width": "5%", "targets":  [2], "className": "text-center" },
            {   "width": "10%", "targets":  [3], "className": "text-end" },
            {   "width": "10%", "targets":  [4], "className": "text-end" },
            {   "width": "10%", "targets":  [5], "className": "text-end" },
            {   "width": "15%", "targets":  [6], "className": "text-end" },
            {   "width": "5%", "targets":  [7], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'itemName', name: 'itemName'},
            {data: 'pshortname', name: 'pshortname'},
            {data: 'price', name: 'price'},
            {data: 'amount', name: 'amount'},
            {data: 'weight', name: 'weight'},
            {data: 'harga', name: 'harga'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }

    $(document).ready(function() {
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

<body onload="myFunction({{ $transactionId }})">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
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

                @if ($tranStatus == 1)
                <button onclick="tambahDetail({{$transactionId}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Transaksi"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>
                @endif
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
                        <th>Jumlah Pack</th>
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
@endsection