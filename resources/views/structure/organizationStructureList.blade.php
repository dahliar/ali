<!--BELUM-->
@php
$pageId = 56;
@endphp

@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 2)

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function editStructure(id){
        window.open(('{{ url("organizationStructureEdit") }}'+"/"+id), '_self');
    }

    function tambahStrukturOrganisasi(){
        window.open(('{{ url("organizationStructureAdd") }}'), '_self');
    }
    
    function myFunction(){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllOrgStructure") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "3%",  "targets":[0], "className": "text-center"   },
            {   "width": "17%", "targets": [1], "className": "text-left"    },
            {   "width": "10%",  "targets": [2], "className": "text-left" },
            {   "width": "10%", "targets": [3], "className": "text-left"  },
            {   "width": "15%",  "targets": [4], "className": "text-left"   },
            {   "width": "15%",  "targets": [5], "className": "text-end"  },
            {   "width": "7%",  "targets": [6], "className": "text-center"  }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'spname', name: 'spname'},
            {data: 'wpname', name: 'wpname'},
            {data: 'reportToName', name: 'reportToName'},
            {data: 'maxemployee', name: 'maxemployee'},
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

<body onload="myFunction()">
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Pegawai</li>
                    </ol>
                </nav>
                <button onclick="tambahStrukturOrganisasi()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Struktur Organisasi"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Struktural</th>
                                <th>Bagian</th>
                                <th>report to</th>
                                <th>Max Employee</th>
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
</body>
@else
@include('partial.noAccess')
@endif

@endsection