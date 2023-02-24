@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js" type="text/javascript" ></script>

<script type="text/javascript"> 
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function editBarcode(id){
        window.open(('{{ url("scanEditBarcode") }}'+"/"+id), '_self');
    }
    function functionStockMasuk(){
        Swal.fire({
            title: 'Scan masuk barang?',
            text: "Scan Produk.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ke laman scan masuk.'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(('{{ url("scanMasuk") }}'), '_blank');
            } else {
                Swal.fire(
                    'Batal scanning masuk!',
                    "Scan Produk.",
                    'info'
                    );
            }
        })
    }
    function functionStockKeluar(){
        Swal.fire({
            title: 'Scan keluar barang?',
            text: "Scan Produk.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ke laman scan keluar.'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(('{{ url("scanKeluar") }}'), '_blank');
            } else {
                Swal.fire(
                    'Batal scanning masuk!',
                    "Scan Produk.",
                    'info'
                    );
            }
        })

    }
    function selectOptionChange(speciesId, itemId){
        $.ajax({
            url: '{{ url("getItemsForScanPage") }}'+"/"+speciesId,
            type: "GET",
            data : {"_token":"{{ csrf_token() }}"},
            dataType: "json",
            success:function(data){
                if(data){
                    var html = '';
                    var i;
                    html += '<option value="-1">--Choose First--</option>';
                    for(i=0; i<data.length; i++){
                        if (data[i].itemId != itemId){
                            html += '<option value='+data[i].itemId+'>'+
                            (i+1)+". "+data[i].itemName+
                            '</option>';
                        } else {
                            html += '<option selected value='+data[i].itemId+'>'+
                            (i+1)+". "+data[i].itemName+
                            '</option>';
                        }
                        $('#item').html(html);
                    }
                }
            }
        });
    }

    function showDatatable(){
        var speciesId = document.getElementById("species").value;
        var itemId = document.getElementById("item").value;
        
        var e = document.getElementById("status");
        var status = e.options[e.selectedIndex].value;    

        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;

        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getAllBarcodeData") }}',
                data: function (d){
                    d.speciesId = speciesId;
                    d.status    = status;
                    d.itemId    = itemId;
                    d.start     = start;
                    d.end       = end;

                }
            },
            dataType: "JSON",            
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "35%", "targets":  [1], "className": "text-left"   },
                {   "width": "10%", "targets":  [2], "className": "text-center" },
                {   "width": "10%", "targets":  [3], "className": "text-end" },
                {   "width": "10%", "targets":  [4], "className": "text-end" },
                {   "width": "10%", "targets":  [5], "className": "text-end" },
                {   "width": "10%", "targets":  [6], "className": "text-end" },
                {   "width": "10%", "targets":  [7], "className": "text-center" }
                ], 
            columns: [
            {
                data: 'SrNo',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {data: 'fullcode', name: 'fullcode'},
            {data: 'status', name: 'status'},
            {data: 'productionDate', name: 'productionDate'},
            {data: 'packagingDate', name: 'packagingDate'},
            {data: 'storageDate', name: 'storageDate'},
            {data: 'loadingDate', name: 'loadingDate'},
            {data: 'action', name: 'action'}
            ]
        });
    }

    $(document).ready(function() {
        $('#species').on('change', function() {
            var speciesId = $(this).val();
            if (speciesId>0){
                selectOptionChange(speciesId, -1);
            }else{
                $('#item')
                .empty()
                .append('<option value="-1">--Choose Species First--</option>');
                swal.fire('warning','Choose Species first!','info');
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
                <div class="col-md-8 text-end">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Scanned Barcode</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <a onclick="functionStockMasuk()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tally masuk storage"><i class="fas fa-plus"> </i>
                    </a>
                    <a onclick="functionStockKeluar()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tally keluar storage"><i class="fas fa-minus"> </i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                        <span class="label">Spesies*</span>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select w-100" id="species" name="species">
                            <option value="-1">--Pilih dahulu--</option>
                            @foreach ($species as $spec)
                            @if ( $spec->id == old('species') )
                            <option value="{{ $spec->id }}" selected>{{ $spec->name }}</option>
                            @else
                            <option value="{{ $spec->id }}">{{ $spec->name }}</option>                    
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <span class="label err" id="speciesListAddLabel"></span>
                    </div>
                </div>
                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                        <span class="label">Barang*</span>
                    </div>
                    <div class="col-md-8">
                        <select id="item" name="item" class="form-select" >
                            <option value="-1">--Choose Species First--</option>
                        </select>
                    </div>
                </div>
                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                        <span class="label">Status Barcode*</span>
                    </div>
                    <div class="col-md-8">
                        <select class="form-select" id="status" name="status" >
                            <option value="-1">--Semua Status--</option>
                            <option value="0">Tercetak</option>
                            <option value="1">Storage</option>
                            <option value="2">Loaded</option>
                        </select>
                    </div>
                </div>

                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                        <span class="label">Rentang</span>
                    </div>
                    <div class="col-md-2 text-end">
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 month')))}}" > 
                    </div>
                    <div class="col-md-2 text-end">
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}" >
                    </div>
                </div>
                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="showDatatable()">Show</button>
                        <input type="reset" value="Reset" class="btn btn-secondary">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barcode</th>
                            <th>Status</th>
                            <th>Produksi</th>
                            <th>Packing</th>
                            <th>Storing</th>
                            <th>Loading</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px;">
                    </tbody>
                </table>                
            </div>
            <div class="card-footer">
                <ol>
                    <li>Loading : Jumlah barang yang saat ini dalam perjalanan ke buyer</li>
                    <li>Sailing adalah jumlah barang di storage dalam satuan Kilogram, hasil penjumlahan dari Packed + Unpacked</li>
                </ol>
            </div>
        </div>
    </div>
</body>
@endsection