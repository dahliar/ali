@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::check() and (Auth::user()->isMarketing() or Auth::user()->isAdmin()))
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
        // e.preventDefault(); // <--- prevent form from submitting
        swal({
            title: "Are you sure?",
            text: "Yakin hendak menghapus item detail transaksi?",
            icon: "warning",
            buttons: [
            'Cancel it!',
            'Delete it!'
            ],
            dangerMode: true,
        }).then(function(isConfirm) {
        //window.open(('{{ url("itemDetailTransactionDelete") }}'+"/"+detailTransaction), '_blank');
        if (isConfirm) {
            $.ajax({
                url: '{{ url("itemDetailTransactionDelete") }}'+"/"+detailTransaction,
                type: "GET",
                data : {"_token":"{{ csrf_token() }}"},
                dataType: "text",
                success:function(data){
                    swal("Deleted!", "Detail Transaksi berhasil dihapus", "success");
                    myFunction({{ $transactionId }});
                }
            });
        } else {
            swal("Cancelled", "Detail Transaksi batal dihapus", "error");
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
            {   "width": "3%",  "targets":  [0], "className": "text-center" },
            {   "width": "15%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%",  "targets": [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "15%", "targets":  [4], "className": "text-left" },
            {   "width": "12%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-left" },
            {   "width": "10%", "targets":  [7], "className": "text-end" },
            {   "width": "10%", "targets":  [8], "className": "text-end" },
            {   "width": "5%", "targets":  [9], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'itemName', name: 'itemName'},
            {data: 'sizeName', name: 'sizeName'},
            {data: 'gradeName', name: 'gradeName'},
            {data: 'packingName', name: 'packingName'},
            {data: 'freezingName', name: 'freezingName'},
            {data: 'price', name: 'price'},
            {data: 'amount', name: 'amount'},
            {data: 'weight', name: 'weight'},
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
                swal("Warning!", "Pilih jenis spesies dulu!", "info");
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
                            <a class="white-text" href="{{ url('/transactionList') }}">Transaction</a>
                        </li>
                        <li class="breadcrumb-item active">Detail Transaction</li>
                    </ol>
                </nav>

                @if ($tranStatus == 1)
                <button onclick="tambahDetail({{$transactionId}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Transaksi"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>
                @endif
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="card-body">
                        <table class="table cell-border stripe hover row-border data-table"  id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Size</th>
                                    <th>Grade</th>
                                    <th>Packing</th>
                                    <th>Freezing</th>
                                    <th>Harga (/kg)</th>
                                    <th>Jumlah</th>
                                    <th>Weight</th>
                                    <th>Act</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>                
                    </div>
                </div>
            </div>    
        </div>
    </div>
</div>
</body>
@else
@include('partial.noAccess')
@endif
@endsection