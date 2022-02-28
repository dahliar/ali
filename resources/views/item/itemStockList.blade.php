@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if ((Auth::user()->isProduction() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
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

    function myFunction(speciesId){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllStockItem") }}' + "/"+ speciesId,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-end" },
            {   "width": "15%", "targets":  [1], "className": "text-left"   },
            {   "width": "15%", "targets":  [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-end" },
            {   "width": "10%", "targets":  [4], "className": "text-end" },
            {   "width": "10%", "targets":  [5], "className": "text-end" },
            {   "width": "10%", "targets":  [6], "className": "text-end" },
            {   "width": "10%", "targets":  [7], "className": "text-end" },
            {   "width": "10%", "targets":  [8], "className": "text-left" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'itemName', name: 'itemName'},
            {data: 'sizeblockgrade', name: 'sizeblockgrade'},
            {data: 'wb', name: 'wb'},
            {data: 'amountPacked', name: 'amountPacked'},
            {data: 'onProgress', name: 'onProgress'},
            {data: 'amountUnpacked', name: 'amountUnpacked'},
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
                        <li class="breadcrumb-item active">Items</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
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
                        </div>
                        <div class="card-body">
                            <table class="table cell-border stripe hover row-border data-table"  id="datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Size Block Grade</th>
                                        <th>Packaging</th>
                                        <th>Packed</th>
                                        <th>On Progress</th>
                                        <th>Unpacked</th>
                                        <th>Total</th>
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
</body>
<ol>
    <li>On Progress : Jumlah barang yang sudah tercatat dalam transaksi, namun transaksi belum finished. Jika transaksi di-cancel, barang akan kembali ke dalam daftar packed</li>
</ol>
@else
@include('partial.noAccess')
@endif

@endsection