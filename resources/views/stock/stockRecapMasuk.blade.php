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

    function barcodeItemList(storageDate){
        window.open(('{{ url("scanRekapMasukHari") }}'+"/"+storageDate), '_self');
    }

    function functionStockKeluar(id){
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
                window.open(('{{ url("scanKeluar") }}'+"/"+id), '_self');
            } else {
                Swal.fire(
                    'Batal scanning keluar!',
                    "Scan Produk.",
                    'info'
                    );
            }
        })

    }
    function myFunction(){
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;

        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getScanMasukHarian") }}',
                data: function (d){
                    d.start = start,
                    d.end = end
                }
            },
            dataType: 'json',            
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%", "targets":   [0], "className": "text-left" },
                {   "width": "25%", "targets":  [1], "className": "text-left"   },
                {   "width": "20%", "targets":  [2], "className": "text-left" },
                {   "width": "12%", "targets":  [3], "className": "text-center" }
                ], 

            columns: [
                {data: 'SrNo',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {data: 'storageDate', name: 'storageDate'},
            {data: 'jumlahBarcode', name: 'jumlahBarcode'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }
</script>



@if(session('status'))
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            {{ session('status') }}
            @if(session('listBarang'))
            <ol>
                @foreach(session('listBarang') as $barang)
                <li>
                    {{$barang}}
                </li>
                @endforeach
            </ol>
            @endif
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
                        <li class="breadcrumb-item active">Rekapitulasi Scan Masuk Harian</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-header">
            <div class="row form-group">
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 week')))}}" > 
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group">
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}" >
                    </div>
                </div>
                <div class="col-md-1">
                    <button onclick="myFunction()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Filter"><i class="fas fa-search"></i>
                    </button>
                </div>
            </div> 
        </div>
        <div class="card card-body">
            <div class="row form-inline">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr style="font-size: 12px;">
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jumlah (Barcode)</th>
                            <th>Act</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:14px">
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