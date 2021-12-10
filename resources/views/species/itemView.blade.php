@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::check() and Auth::user()->isAdmin())

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    /*
    function storesDetail(id){
        window.open(('{{ url("itemStoreDetail") }}' + "/"+ id), '_blank');
    }
    */
    function getStoreDetail(id){
        $.ajax({
            url : '{{ url("getOneStore") }}' + "/"+ id,
            method : "GET",
            async : true,
            dataType : 'json',
            success: function(data){
                document.getElementById('exampleModalLabel').innerHTML = "Detail "+data[0].name;
                document.getElementById('itemName').value = data[0].name;
                document.getElementById('sizeName').value = data[0].size;
                document.getElementById('gradeName').value = data[0].grade;
                document.getElementById('packingName').value = data[0].packing;
                document.getElementById('frozenName').value = data[0].freezing;
                document.getElementById('weightbase').value = data[0].weightbase;
                document.getElementById('tanggalPacking').value = data[0].datePackage;
                document.getElementById('tanggalProcess').value = data[0].dateProcess;
                document.getElementById('tanggalInsert').value = data[0].dateInsert;
                document.getElementById('inputby').value = data[0].username;

                $approval = "Approved";
                if (data[0].isApproved===0){
                    $approval = "Un Approved";
                } else{
                    $approval = "Rejected";

                }
                document.getElementById('isApproved').value = $approval;
                document.getElementById('amount').value = data[0].amount;
            }
        });
        return false;
    }

    function myFunction(itemId){
        //alert(itemId);
        $('#datatable').DataTable({
            ajax:'{{ url("getItemHistory") }}' + "/"+ itemId,
            serverSide: true,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            data : {},
            columns: [
            {data: 'id', name: 'id'},
            {data: 'item', name: 'item'},
            {data: 'datePackage', name: 'datePackage'},
            {data: 'dateProcess', name: 'dateProcess'},
            {data: 'dateInsert', name: 'dateInsert'},
            {data: 'username', name: 'username'},
            {data: 'isApproved', name: 'isApproved'},
            {data: 'amount', name: 'amount'},
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
                                            <th>is Approved</th>
                                            <th>Amount</th>
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
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" name="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-1"></div>                        
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Species Item</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <input id="itemName" name="itemName" type="text" class="form-control"readonly>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-1"></div>                        
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Size</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <input id="sizeName" name="sizeName" type="text" class="form-control"readonly>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-1"></div>                        
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Grade</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <input id="gradeName" name="gradeName" type="text" class="form-control"readonly>
                        </div>
                    </div>                      
                    <div class="row form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Packing Type</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <input id="packingName" name="packingName" type="text" class="form-control"readonly>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Freeze Type</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <input id="frozenName" name="frozenName" type="text" class="form-control" readonly>
                        </div>
                    </div>          
                    <div class="row form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Weight Base</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input id="weightbase" name="weightbase" type="text" class="form-control text-end my-auto"readonly>
                                <span class="input-group-text col-3">Kg</span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Tanggal Packing</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input id="tanggalPacking" type="input" class="form-control"readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Tanggal Proses</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input id="tanggalProcess" type="input" class="form-control"readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Tanggal Input</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input id="tanggalInsert" type="input" class="form-control"readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Jumlah</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <div class="input-group my-auto">
                                <input id="amount" type="text" class="form-control text-end" readonly>
                                <span class="input-group-text col-3">Kg</span>
                            </div>
                        </div>
                    </div>                        
                    <div class="row form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Input By</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input id="inputby" type="text" class="form-control"readonly>
                            </div>
                        </div>
                    </div>                        
                    <div class="row form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-3 text-end my-auto">
                            <span class="label">Is Approved</span>
                        </div>
                        <div class="col-md-1 text-center my-auto"> : </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <input id="isApproved" type="text" class="form-control"readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
@else
@include('partial.noAccess')
@endif

@endsection