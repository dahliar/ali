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
    function getFileDownload(filepath){
        window.open(('{{ url("getFileDownload") }}'+"/"+filepath), '_blank');
    };

    function editCompany(id){
        window.open(('{{ url("companyEdit") }}'+"/"+id), '_self');
    }
    function daftarProdukBeli(companyId){
        $.ajax({
            url: '{{ url("getAllCompanyProducts") }}',
            type: "GET",
            data: {
                "_token":"{{ csrf_token() }}",
                companyId : companyId
            },
            dataType: "json",
            success:function(data){
                $('[name="taPenjualan"]').val(data[0].penjualan);
                $('[name="taPembelian"]').val(data[0].pembelian);
                $('#modalProduk').modal('show');
            }
        });
    }

    function myFunction(){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllCompany") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "30%", "targets":  [1], "className": "text-left"   },
                {   "width": "10%", "targets":  [2], "className": "text-left"   },
                {   "width": "15%", "targets":  [3], "className": "text-left" },
                {   "width": "20%", "targets":  [4], "className": "text-left" },
                {   "width": "5%", "targets":   [5], "className": "text-center" },
                {   "width": "10%", "targets":  [6], "className": "text-left" },
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'shortname', name: 'shortname'},
                {data: 'ktp', name: 'ktp'},
                {data: 'npwp', name: 'npwp'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
        });
    }

    $(document).ready(function() {
        myFunction(0)
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
                        <li class="breadcrumb-item active">Daftar Perusahaan Supplier dan Buyer</li>
                    </ol>
                </nav>
                <button onclick="location.href='{{ url('companyAdd') }}'" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Company"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="card-body">
                        <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Kode Perusahaan</th>
                                    <th>KTP</th>
                                    <th>NPWP</th>
                                    <th>Status</th>
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

    <div class="modal fade" id="modalProduk" tabindex="-1" aria-labelledby="modalProduk" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeePresenceHarianModal">Daftar Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-2 my-auto">
                            <span class="label">Pembelian</span>
                        </div>
                        <div class="col-md-10">
                            <textarea class="form-control" id="taPembelian" name="taPembelian" rows="3" disabled></textarea>
                        </div>  
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 my-auto">
                            <span class="label">Penjualan</span>
                        </div>
                        <div class="col-md-10">
                            <textarea class="form-control" id="taPenjualan" name="taPenjualan" rows="3" disabled></textarea>
                        </div>     
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 my-auto">
                        </div>
                        <div class="col-md-10">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>     
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection