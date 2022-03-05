@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if ((Auth::user()->isProduction() or Auth::user()->isMarketing() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function tambahStockItem(id){
        window.open(('{{ url("itemStockAdd") }}' + "/"+ id), '_self');
    }
    function UpdateStockUnpacked(id){
        window.open(('{{ url("editUnpacked") }}' + "/"+ id), '_self');
    }
    function historyStockItem(id){
        window.open(('{{ url("itemStockView") }}' + "/"+ id), '_blank');
    }
    function unpackedHistory(id){
        window.open(('{{ url("itemStockViewUnpacked") }}' + "/"+ id), '_blank');
    }

    function myFunction(){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllSpeciesStock") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "15%", "targets":  [1], "className": "text-left"   },
            {   "width": "15%", "targets":  [2], "className": "text-end" },
            {   "width": "10%", "targets":  [3], "className": "text-end" },
            {   "width": "10%", "targets":  [4], "className": "text-end" },
            {   "width": "10%", "targets":  [5], "className": "text-end" },
            {   "width": "10%", "targets":  [6], "className": "text-end" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'packed', name: 'jumlahPacked'},
            {data: 'onProgress', name: 'onProgress'},
            {data: 'unpacked', name: 'jumlahUnpacked'},
            {data: 'total', name: 'total'},
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

<body onload="myFunction(0)">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Stock Species</li>
                    </ol>
                </nav>
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
                                        <th>Packed (Kg)</th>
                                        <th>On Progress (Kg)</th>
                                        <th>Unpacked (Kg)</th>
                                        <th>Total (Kg)</th>
                                        <th>Aksi</th>
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
    <li>On Progress : Jumlah barang yang saat ini tercatat dalam proses transaksi, namun transaksi belum terselesaikan. Jika transaksi di-cancel, barang akan kembali ke dalam daftar packed</li>
    <li>Total adalah jumlah total jumlah stock dalam satuan Kilogram, hasil penjumlahan dari Packed + On Progress + Unpacked</li>
</ol>
@else
@include('partial.noAccess')
@endif

@endsection