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

<script type="text/javascript"> 
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function myFunction(){
        var e = document.getElementById("selectSpecies");
        var species = e.options[e.selectedIndex].value;       
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;

        $('#datatable').DataTable({
            ajax: '{{ url("getPerubahanStock") }}' + "/"+ species+ "/"+ start + "/"+ end,
            type: 'get',
            serverSide: false,
            processing: true,
            deferRender: true,
            pageLength: 50,
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "30%", "targets":  [1], "className": "text-left"   },
                {   "width": "20%", "targets":  [2], "className": "text-left" },
                {   "width": "25%", "targets":  [3], "className": "text-left" },
                {   "width": "5%", "targets":  [4], "className": "text-end" },
                {   "width": "10%", "targets":  [5], "className": "text-left" },
                {   "width": "5%", "targets":  [6], "className": "text-end" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'itemName', name: 'itemName'},
                {data: 'userPeubah', name: 'userPeubah'},
                {data: 'informasiTransaksi', name: 'informasiTransaksi'},
                {data: 'prevAmount', name: 'prevAmount'},
                {data: 'amount', name: 'amount'},
                {data: 'afterAmount', name: 'afterAmount'}
                ]

        });
    }

    $(document).ready(function() {
    });
</script>
@if (empty($speciesChoosen))
@php
$speciesChoosen=-1;
$itemChoosen=-1;
@endphp
@endif
<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/itemStockList') }}">Stock Per-Barang</a>
                        </li>
                        <li class="breadcrumb-item active">History perubahan stock</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-header">
            {{ csrf_field() }}
            <div class="container-fluid">
                <div class="row form-group">
                    <div class="col-3">
                        <select class="form-select w-100" id="selectSpecies">
                            @foreach ($speciesList as $species)
                            <option value="{{ $species->id }}">{{ $species->nameBahasa }}</option>
                            @endforeach
                            <option value="0" selected>All Species</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        @if(empty($start))
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{date('Y-m-d', strtotime('-3 month'))}}" > 
                        @else
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{$start}}" > 
                        @endif
                    </div>
                    <div class="col-md-2">
                        @if(empty($end))
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{date('Y-m-d')}}" > 
                        @else
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{$end}}" > 
                        @endif                                        
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="hitButton" class="form-control btn-primary" onclick="myFunction()">Show Data</button>
                    </div>                      

                </div>
                <div class="card card-body">
                    <table class="table table-striped table-hover table-bordered data-table" id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Peubah</th>
                                <th>Alasan</th>
                                <th>Sebelum (Kg)</th>
                                <th>Penambahan</th>
                                <th>Sesudah (Kg)</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 14px;">
                        </tbody>
                    </table>                
                </div>
                <div class="card card-body">
                    Laman ini menampilkan data 
                    <ol>
                        <li>Daftar perubahan stock</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
</body>
@endsection