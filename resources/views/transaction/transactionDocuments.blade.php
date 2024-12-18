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

    function getFileDownload(filepath){
        window.open(('{{ url("getFileDownload") }}'+"/"+filepath), '_blank');
    };
    function tambahDokumen(id){
        window.open(('{{ url("transactionDocumentAdd") }}'+"/"+id), '_self');
    }

    function myFunction(transactionId){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getAllExportDocuments") }}',
                data: function (d){
                    d.transactionId = transactionId
                }
            },
            dataType: 'json',            
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%", "targets":  [0], "className": "text-left"   },
                {   "width": "30%", "targets": [1], "className": "text-left" },
                {   "width": "10%", "targets": [2], "className": "text-left" },
                {   "width": "20%", "targets":  [3], "className": "text-left" },
                {   "width": "15%", "targets":  [4], "className": "text-left" },
                {   "width": "20%", "targets":   [5], "className": "text-left" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'companyName', name: 'companyName'},
                {data: 'jenis', name: 'jenis'},
                {data: 'nama', name: 'nama'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
        });
    }
    $(document).ready(function() {
        myFunction({{$transaction->id}});
    });

</script>



@if (session('status'))
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            {{ session('status') }}
            @if (session('listBarang'))
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
                        <li class="breadcrumb-item active">Export Documents</li>
                    </ol>
                </nav>
                <div>
                    <button onclick="tambahDokumen({{$transaction->id}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Dokumen Ekspor">
                        <i class="fa fa-plus"></i>Tambah Dokumen Ekspor
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
                            <th>Customer</th>
                            <th>Jenis</th>
                            <th>Nama Dokumen</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
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