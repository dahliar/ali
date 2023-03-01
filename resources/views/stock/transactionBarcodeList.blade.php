@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js" type="text/javascript" ></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#datatable').DataTable({
            data: {!! json_encode($transactions->toArray()) !!},
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "35%", "targets":  [1], "className": "text-left"   },
                {   "width": "10%", "targets":  [2], "className": "text-end" },
                {   "width": "10%", "targets":  [3], "className": "text-end" },
                {   "width": "10%", "targets":  [4], "className": "text-end" },
                {   "width": "10%", "targets":  [5], "className": "text-end" },
                {   "width": "10%", "targets":  [6], "className": "text-end" },
                {   "width": "10%", "targets":  [7], "className": "text-end" }
                ], 
            columns: [
                {data: 'SrNo',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {data: 'name', name: 'name'},
            {data: 'fullcode', name: 'fullcode'},
            {data: 'productionDate', name: 'productionDate'},
            {data: 'packagingDate', name: 'packagingDate'},
            {data: 'storageDate', name: 'storageDate'},
            {data: 'loadingDate', name: 'loadingDate'},
            {data: 'expiringDate', name: 'expiringDate'}
            ]
        });
    });
</script>
<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Daftar Barcode Transaksi</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kode</th>
                                <th>Produksi</th>
                                <th>Packing</th>
                                <th>Storing</th>
                                <th>Loading</th>
                                <th>Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>                
                </div>
            </div>    
        </div>
    </div>
</body>
@endsection