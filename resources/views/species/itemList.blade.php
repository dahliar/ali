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

    function editSpeciesItem(itemId){
        window.open(('{{ url("editSpeciesItem") }}' + "/"+ itemId), '_self');
    }
    function tambahItem(speciesId){
        window.open(('{{ url("addSpeciesItem") }}' + "/"+ speciesId), '_self');
    }


    function myFunction(speciesId){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllSpeciesItem") }}' + "/"+ speciesId,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "3%",  "targets":  [0], "className": "text-center" },
            {   "width": "30%",  "targets": [1], "className": "text-left" },
            {   "width": "15%", "targets":  [2], "className": "text-left" },
            {   "width": "8%", "targets":  [3], "className": "text-center" },
            {   "width": "15%", "targets":  [4], "className": "text-left" },
            {   "width": "5%", "targets":  [5], "className": "text-center" },
            {   "width": "9%", "targets":  [6], "className": "text-center" },
            {   "width": "10%", "targets":  [7], "className": "text-left" },
            {   "width": "5%", "targets":  [8], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'itemName', name: 'itemName'},
            {data: 'speciesName', name: 'speciesName'},
            {data: 'weightbase', name: 'weightbase'},
            {data: 'sizeName', name: 'sizeName'},
            {data: 'gradeName', name: 'gradeName'},
            {data: 'freezingName', name: 'freezingName'},
            {data: 'packingName', name: 'packingName'},
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

<body onload="myFunction({{$speciesId}})">
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
                <button onclick="tambahItem({{$speciesId}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Barang"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="col-12">
                        <div class="card-body">
                            <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Item</th>
                                        <th>Species</th>
                                        <th>WB (Kg)</th>
                                        <th>Size</th>
                                        <th>Grade</th>
                                        <th>Freeze</th>
                                        <th>Package</th>
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