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
    
    function editStoreDetail(id){
        window.open(('{{ url("itemStockEdit") }}' + "/"+ id), '_self');
    }
    
    function deleteStoreDetail(id){
        Swal.fire({
            title: 'Hapus penambahan stock',
            text: "Hapus?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus saja!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("deleteStockChange") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        storeId   : id
                    },
                    dataType: "json",
                    success:function(data){
                    }
                });
                Swal.fire(
                    'Dihapus!',
                    'Penambahan stok dibatalkan, data dihapus',
                    'success'
                    );
                myFunction();
            } else {
                Swal.fire(
                    'Batal!',
                    "Hapus dibatalkan",
                    'info'
                    );
            }
        })
    }

    function myFunction(){
        var itemId = document.getElementById("itemId").value;
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        var opsi = document.getElementById("opsi").value;

        $('#datatable').DataTable({ 
            ajax:'{{ url("getItemHistory") }}' + "/"+ itemId+ "/"+ start+ "/"+ end+ "/"+ opsi,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "25%", "targets":  [1], "className": "text-left"   },
            {   "width": "8%",  "targets": [2], "className": "text-center" },
            {   "width": "12%", "targets":  [3], "className": "text-left" },
            {   "width": "12%", "targets":  [4], "className": "text-left" },
            {   "width": "5%", "targets":  [5], "className": "text-center" },
            {   "width": "10%", "targets":  [6], "className": "text-end" },
            {   "width": "10%", "targets":  [7], "className": "text-end" },
            {   "width": "10%", "targets":  [8], "className": "text-center" }
            ], 
            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'itemName', name: 'itemName'},
            {data: 'datePackage', name: 'datePackage'},
            {data: 'userInputName', name: 'userInputName'},
            {data: 'userApproveName', name: 'userApproveName'},
            {data: 'isApproved', name: 'isApproved'},
            {data: 'amountPacked', name: 'amountPacked'},
            {data: 'amountUnpacked', name: 'amountUnpacked'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }
</script>
<body>
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a class="white-text" href="{{ url('itemList')}}">Items</a>
                        </li>
                        <li class="breadcrumb-item active">Item History</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row form-group">
                                    <input type="hidden" id="itemId" name="itemId" class="form-control text-end" value="{{ $itemId }}" >

                                    <div class="col-md-2">
                                        <input type="date" id="start" name="start" class="form-control text-end" value="{{ date('Y-m-d', strtotime('-1 week')) }}" > 
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ date('Y-m-d') }}" >
                                    </div>
                                    <div class="col-md-2">
                                        <select id="opsi" name="opsi" class="form-select" >
                                            <option value="-1">Semua Status</option>
                                            <option value="1">Approved</option>
                                            <option value="0">Unapproved</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" id="hitButton" class="form-control btn-primary" onclick="myFunction()">Cari</button>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Pack</th>
                                            <th>Input</th>
                                            <th>Approve</th>
                                            <th>Status</th>
                                            <th>Packed</th>
                                            <th>Unpacked</th>
                                            <th>Act</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 12px;">
                                    </tbody>
                                </table>                
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>
</body>
@endsection