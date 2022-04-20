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

    function myFunction(){
        var e = document.getElementById("selectSpecies");
        var selectSpecies = e.options[e.selectedIndex].value;       

        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        $('#datatable').DataTable({
            ajax: '{{ url("getPriceList") }}' + "/"+ selectSpecies + "/"+ start + "/"+ end,
            type: 'get',
            serverSide: false,
            processing: true,
            deferRender: true,
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "45%", "targets":  [1], "className": "text-left"   },
            {   "width": "15%", "targets":  [2], "className": "text-end" },
            {   "width": "15%", "targets":  [3], "className": "text-end" },
            {   "width": "15%", "targets":  [4], "className": "text-end" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'itemName', name: 'itemName'},
            {data: 'minPrice', name: 'minPrice'},
            {data: 'avgPrice', name: 'avgPrice'},
            {data: 'maxPrice', name: 'maxPrice'}
            ]

        });
    }

    $(document).ready(function() {
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
                        <li class="breadcrumb-item active">Harga Rata-Rata Pembelian Barang</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-header">
            <div class="row form-group">
                <div class="col-3">
                    <select class="form-select w-100" id="selectSpecies">
                        <option value="-1">----</option>
                        @foreach ($speciesList as $species)
                        <option value="{{ $species->id }}">{{ $species->name }}</option>
                        @endforeach
                        <option value="0" selected>All Species</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 year')))}}" > 
                </div>
                <div class="col-md-2">
                    <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}" >
                </div>
                <div class="col-md-2">
                    <button type="button" id="hitButton" name="end" class="form-control btn-primary" onclick="myFunction()">Cari</button>
                </div>

            </div>
        </div>
        <div class="card card-body">
            <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Minimal (Rp/Kg)</th>
                        <th>Rata-rata (Rp/Kg)</th>
                        <th>Maksimal (Rp/Kg)</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>                
        </div>
    </div>
</body>
<ol>
    <li>Harga diatas adalah harga dengan basis pembelian pada rentang tanggal yang dipilih</li>
    <li>Harga berasal dari berbagai supplier</li>
</ol>
@endsection