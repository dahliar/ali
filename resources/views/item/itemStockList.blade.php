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

    function tambahStockItem(id){
        window.open(('{{ url("itemStockAdd") }}' + "/"+ id), '_self');
    }
    function kurangiStockItem(id){
        window.open(('{{ url("itemStockSubtract") }}' + "/"+ id), '_self');
    }
    function UpdateStockUnpacked(id){
        window.open(('{{ url("editUnpacked") }}' + "/"+ id), '_self');
    }
    function historyStockItem(id){
        window.open(('{{ url("itemStockView") }}' + "/"+ id), '_self');
    }
    function historyStockKurang(id){
        window.open(('{{ url("itemStockSubtractView") }}' + "/"+ id), '_self');
    }


    function myFunction(speciesId){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllStockItem") }}' + "/"+ speciesId,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "3%",  "targets":  [0], "className": "text-center" },
            {   "width": "23%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%", "targets":  [2], "className": "text-end" },
            {   "width": "10%", "targets":  [3], "className": "text-end" },
            {   "width": "10%", "targets":  [4], "className": "text-end" },
            {   "width": "10%", "targets":  [5], "className": "text-end" },
            {   "width": "12%", "targets":  [6], "className": "text-center" },
            {   "width": "12%", "targets":  [7], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'itemName', name: 'itemName'},
            {data: 'amountPacked', name: 'amountPacked'},
            {data: 'amountUnpacked', name: 'amountUnpacked'},
            {data: 'stockOnHand', name: 'stockOnHand'},
            {data: 'loading', name: 'loading'},
            {data: 'action1', name: 'action1', orderable: false, searchable: false},
            {data: 'action2', name: 'action2', orderable: false, searchable: false}
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

<body onload="myFunction(0)">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="card card-body">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb primary-color my-auto">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Stock per-Barang</li>
                </ol>
            </nav>
        </div>
        <div class="card card-header">
            <div class="row form-group">
                <div class="col-2 my-auto text-md-right">
                    <span class="label" id="statTran">Jenis Spesies</span>
                </div>
                <div class="col-6">
                    <select class="form-control w-100" id="selectSpecies">
                        <option value="-1">--Choose One--</option>
                        @foreach ($speciesList as $species)
                        <option value="{{ $species->id }}">{{ $species->name }}</option>
                        @endforeach
                        <option value="0" selected>All</option>
                    </select>
                </div>
                <div class="col-2 my-auto">
                    <span class="label" id="errSpan"></span>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Packed</th>
                            <th>Unpacked</th>
                            <th>Stock gudang</th>
                            <th>Sailing</th>
                            <th>Tambah Stock</th>
                            <th>Kurang Stock</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px;">
                    </tbody>
                </table>                
            </div>
            <div class="card-footer">
                <ol>
                    <li>Loading : Jumlah barang yang saat ini dalam perjalanan ke buyer</li>
                    <li>Stock In Hand adalah jumlah barang di storage dalam satuan Kilogram, hasil penjumlahan dari Packed + Unpacked</li>
                </ol>
            </div>
        </div>
    </div>
</body>
@endsection