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
    
    function myFunction(itemId){
        //alert(itemId);
        $('#datatable').DataTable({ 
            ajax:'{{ url("getItemHistory") }}' + "/"+ itemId,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "30%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%",  "targets": [2], "className": "text-center" },
            {   "width": "15%", "targets":  [3], "className": "text-left" },
            {   "width": "10%", "targets":  [4], "className": "text-end" },
            {   "width": "10%", "targets":  [5], "className": "text-end" },
            {   "width": "10%", "targets":  [6], "className": "text-end" },
            {   "width": "10%", "targets":  [7], "className": "text-end" },
            {   "width": "10%", "targets":  [8], "className": "text-end" }
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
<body onload="myFunction({{$itemId}})">
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
                            <div class="card-body">
                                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Tanggal Package</th>
                                            <th>Input</th>
                                            <th>Approve</th>
                                            <th>Status</th>
                                            <th>Packed</th>
                                            <th>Unpacked</th>
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
    </div>
</body>
@endsection