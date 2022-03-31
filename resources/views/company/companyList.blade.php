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


    function editCompany(id){
        window.open(('{{ url("companyEdit") }}'+"/"+id), '_self');
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
            {   "width": "20%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%",  "targets": [2], "className": "text-left" },
            {   "width": "55%", "targets":  [3], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nation', name: 'nation'},
            {data: 'address', name: 'address'},
            {data: 'action', name: 'action', orderable: false, searchable: false}


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
                                    <th>Negara</th>
                                    <th>Alamat</th>
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
@endsection