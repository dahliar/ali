@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if ((Auth::user()->isAdmin() or Auth::user()->isMarketing()) and Session::has('employeeId') and (Session()->get('levelAccess') <= 3))
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function tambahDetail(id){
        window.open(('{{ url("purchaseItemAdd") }}'+"/"+id), '_self');
    }
    function deleteItem(dpid){
        Swal.fire({
            title: "Hapus item pembelian?",
            text: "Yakin hendak menghapus item pembelian?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus aja!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("itemDetailPurchaseDelete") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        dpid : dpid
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Terhapus!',
                            'Record item pembelian telah dihapus.',
                            'success'
                            );
                        myFunction({{$purchase->id}});
                    }
                });
            }
        })



       
    };

    function myFunction($id){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllPurchaseItems") }}' + "/"+ $id,
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
            {   "width": "10%", "targets":  [6], "className": "text-end" },
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
            {data: 'valutaPrice', name: 'valutaPrice'},
            {data: 'amount', name: 'amount'},
            {data: 'valutaBayar', name: 'valutaBayar'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }
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

<body onload="myFunction({{ $purchase->id }})">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Detil transaksi pembelian</li>
                    </ol>
                </nav>

                @if ($purchase->status == 1)
                <button onclick="tambahDetail({{$purchase->id}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Transaksi"><i class="fa fa-plus" style="font-size:20px"></i>
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
                                    <th>Berat</th>
                                    <th>Bayar</th>
                                    <th>Aksi</th>
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