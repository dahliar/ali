@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::check() and (Auth::user()->isProduction() or Auth::user()->isAdmin()))
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
            {   "width": "3%",  "targets":  [0], "className": "text-center" },
            {   "width": "17%", "targets":  [1], "className": "text-left"   },
            {   "width": "5%",  "targets": [2], "className": "text-center" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "10%", "targets":  [4], "className": "text-left" },
            {   "width": "10%", "targets":  [5], "className": "text-left" },
            {   "width": "7%", "targets":  [6], "className": "text-left" },
            {   "width": "8%", "targets":  [7], "className": "text-end" },
            {   "width": "10%", "targets":  [8], "className": "text-end" },
            {   "width": "8%", "targets":  [9], "className": "text-center" }
            ], 
            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'item', name: 'item'},
            {data: 'datePackage', name: 'datePackage'},
            {data: 'dateProcess', name: 'dateProcess'},
            {data: 'dateInsert', name: 'dateInsert'},
            {data: 'username', name: 'username'},
            {data: 'amount', name: 'amount'},
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
                                <table class="table cell-border stripe hover row-border data-table"  id="datatable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Tanggal Package</th>
                                            <th>Tanggal Proses</th>
                                            <th>Tanggal Input</th>
                                            <th>Employee</th>
                                            <th>Amount</th>
                                            <th>Amount Packed</th>
                                            <th>Amount Unpacked</th>
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
@else
@include('partial.noAccess')
@endif

@endsection