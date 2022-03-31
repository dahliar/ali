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
        window.open(('{{ url("itemStockEdit") }}' + "/"+ id), '_blank');
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
            data : {},
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "30%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%",  "targets": [2], "className": "text-center" },
            {   "width": "10%", "targets":  [3], "className": "text-center" },
            {   "width": "10%", "targets":  [4], "className": "text-center" },
            {   "width": "15%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-end" },
            {   "width": "10%", "targets":  [7], "className": "text-end" }
            ], 
            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'item', name: 'item'},
            {data: 'datePackage', name: 'datePackage'},
            {data: 'dateProcess', name: 'dateProcess'},
            {data: 'dateInsert', name: 'dateInsert'},
            {data: 'username', name: 'username'},
            {data: 'amountPacked', name: 'amountUnpacked'},
            {data: 'amountPacked', name: 'amountPacked'}
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
                                            <th>Tanggal Proses</th>
                                            <th>Tanggal Input</th>
                                            <th>Employee</th>
                                            <th>Packed</th>
                                            <th>Unpacked</th>
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