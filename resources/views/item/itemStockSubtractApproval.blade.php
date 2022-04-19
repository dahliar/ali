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
    function approveStockSubtract(id){
        Swal.fire({
            title: 'Setuju perubahan pengurangan Stock',
            text: "Approve?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, approve saja!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("approveStockSubtractChange") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        stockSubtractId         : id,
                        approveStockSubtract    : "1"
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Done!',
                            'Approval selesai',
                            'success'
                            );
                    }
                });
                myFunction();
            } else {
                Swal.fire(
                    'Batal!',
                    "Approval dibatalkan",
                    'warning'
                    );
            }
        })
    }
    function tolakStockSubtract(id){
        Swal.fire({
            title: 'Penolakan perubahan pengurangan Stock',
            text: "Tolak?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, tolak saja!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("approveStockSubtractChange") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        stockSubtractId   : id,
                        approveStockSubtract    : "2"
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Ditolak!',
                            'Perubahan stok ditolak',
                            'success'
                            );
                    }
                });
                myFunction();
            } else {
                Swal.fire(
                    'Batal!',
                    "Approval dibatalkan",
                    'warning'
                    );
            }
        })
    }
    function myFunction(){
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        var opsi = document.getElementById("opsi").value;
        var speciesId = document.getElementById("species").value;

        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getStorekSubtractRecord") }}',
                type: 'post',
                data:{
                    "_token":"{{ csrf_token() }}",
                    start : start,
                    end : end,
                    opsi : opsi,
                    speciesId : speciesId
                }
            },
            serverSide: false,
            processing: true,
            deferRender: true,
            destroy:true,
            columnDefs: [
            {   "width": "21%",  "targets":  [0], "className": "text-left" },
            {   "width": "10%", "targets":  [1], "className": "text-end" },
            {   "width": "8%", "targets":  [2], "className": "text-center" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "20%", "targets":  [4], "className": "text-left" },
            {   "width": "8%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-left" },
            {   "width": "12%", "targets":  [7], "className": "text-center" }
            ], 
            columns: [
            {data: 'itemName', name: 'itemName'},
            {data: 'amountSubtract', name: 'amountSubtract'},
            {data: 'tanggal', name: 'tanggal'},
            {data: 'userInputName', name: 'userInputName'},
            {data: 'alasan', name: 'alasan'},
            {data: 'isApproved', name: 'isApproved'},
            {data: 'userApproveName', name: 'userApproveName'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }

    $(document).ready(function() {
        $('#selectSpecies').change(function(){ 
            var e = document.getElementById("selectSpecies");
            var speciesId = e.options[e.selectedIndex].value;
            if (speciesId >= 0){
                myFunction(speciesId);
            } else{
                swal.fire("Warning!", "Pilih jenis spesies dulu!", "info");
            }

        });
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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a class="white-text" href="{{ url('itemStockList')}}">Items</a>
                        </li>
                        <li class="breadcrumb-item active">Approval pengurangan Stock</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <input type="date" id="start" name="start" class="form-control text-end" value="{{ date('Y-m-d', strtotime('-1 week')) }}" > 
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ date('Y-m-d') }}" >
                                    </div>
                                    <div class="col-2">
                                        <select class="form-select w-100" id="species">
                                            @foreach ($speciesList as $species)
                                            <option value="{{ $species->id }}">{{ $species->nameBahasa }}</option>
                                            @endforeach
                                            <option value="0" selected>All</option>
                                        </select>
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
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Packed</th>
                                        <th>Tanggal</th>
                                        <th>Input</th>
                                        <th>Alasan</th>
                                        <th>Approved</th>
                                        <th>Oleh</th>
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
</body>
@endsection