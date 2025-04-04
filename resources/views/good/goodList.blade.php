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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function tambahBarang(){
        window.open(('{{ url("goodAdd") }}'), '_self');
    }
    function editBarang(id){
        window.open(('{{ url("goodEdit") }}' + "/"+ id), '_self');
    }
    function ubahTambah(id){
        window.open(('{{ url("goodUbahTambah") }}' + "/"+ id), '_self');
    }
    function ubahKurang(id){
        window.open(('{{ url("goodUbahKurang") }}' + "/"+ id), '_self');
    }

    function myFunction(){
        var isChecked = document.querySelector('input[name="showData"]:checked').value;
        $('#datatable').DataTable({
            ajax:'{{ url("getGoods") }}'+"/"+isChecked,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "30%",  "targets": [1], "className": "text-left" },
                {   "width": "10%", "targets":  [2], "className": "text-end" },
                {   "width": "10%", "targets":  [3], "className": "text-end" },
                {   "width": "10%", "targets":  [4], "className": "text-center" },
                {   "width": "10%", "targets":  [5], "className": "text-center" },
                {   "width": "10%", "targets":  [6], "className": "text-center" },
                {   "width": "15%", "targets":  [7], "className": "text-center" }
            ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'amount', name: 'amount'},
                {data: 'minimal', name: 'minimal'},
                {data: 'satuan', name: 'satuan'},
                {data: 'kategori', name: 'kategori'},
                {data: 'isactive', name: 'isactive'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }
    $(document).ready(function() {
       // myFunction();
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
                        <li class="breadcrumb-item active">Barang-Barang Produksi</li>
                    </ol>
                </nav>
                <button onclick="tambahBarang()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Barang"><i class="fa fa-plus"></i>
                </button>
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
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jumlah</th>
                                        <th>Minimal</th>
                                        <th>Satuan</th>
                                        <th>Kategori</th>
                                        <th>Aktif</th>
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
@endsection