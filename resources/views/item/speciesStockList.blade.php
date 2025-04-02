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
    function myFunction(){
        var isChecked = document.querySelector('input[name="showData"]:checked').value;
        $('#datatable').DataTable({
            ajax:'{{ url("getAllSpeciesStock") }}'+"/"+isChecked,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "4%",  "targets":  [0], "className": "text-center" },
                {   "width": "20%", "targets":  [1], "className": "text-left"   },
                {   "width": "20%", "targets":  [2], "className": "text-left"   },
                {   "width": "14%", "targets":  [3], "className": "text-end" },
                {   "width": "14%", "targets":  [4], "className": "text-end" },
                {   "width": "14%", "targets":  [5], "className": "text-end" },
                {   "width": "14%", "targets":  [6], "className": "text-end" }
            ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'nameBahasa', name: 'nameBahasa'},
                {data: 'packed', name: 'jumlahPacked'},
                {data: 'unpacked', name: 'jumlahUnpacked'},
                {data: 'total', name: 'total'},
                {data: 'jumlahOnLoading', name: 'jumlahOnLoading'}
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
                        <li class="breadcrumb-item active">Stock per-Species</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="d-grid">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="showData" id="r2" value="1" checked>
                            <label class="form-check-label" for="inlineRadio2">Yang ada barangnya saja</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="showData" id="r3" value="0">
                            <label class="form-check-label" for="inlineRadio3">Yang habis saja</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="showData" id="r1" value="all">
                            <label class="form-check-label" for="inlineRadio1">Semua</label>
                        </div>
                    </div>
                    <div class="d-grid d-md-flex">
                        <button id="buttonShow" type="submit" class="btn btn-primary" onclick="myFunction()">Tampilkan Data</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="col-12">
                        <div class="card-body">
                            <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Nama</th>
                                        <th>Packed (Kg)</th>
                                        <th>Unpacked (Kg)</th>
                                        <th>Stock in hand (Kg)</th>
                                        <th>Sailing (Kg)</th>
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
<ol>
    <li>Loading : Jumlah barang yang saat ini dalam perjalanan ke buyer</li>
    <li>Stock In Hand adalah jumlah barang di storage dalam satuan Kilogram, hasil penjumlahan dari Packed + Unpacked</li>
</ol>
@endsection