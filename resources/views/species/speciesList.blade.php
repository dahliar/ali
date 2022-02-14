@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::user()->isAdmin())
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function listItem(speciesId){
        window.open(('{{ url("itemList") }}' + "/"+ speciesId), '_self');
    }
    function sizeItem(speciesId){
        window.open(('{{ url("sizeList") }}' + "/"+ speciesId), '_self');
    }

    function myFunction(familyId){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllSpecies") }}' + "/"+ familyId,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "60%", "targets":  [1], "className": "text-left"   },
            {   "width": "25%",  "targets": [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'familyName', name: 'familyName'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }

    $(document).ready(function() {
        $('#selectFamily').change(function(){ 
            var e = document.getElementById("selectFamily");
            var familyId = e.options[e.selectedIndex].value;
            if (familyId >= 0){
                myFunction(familyId);
            } else{
                swal.fire("Warning!", "Pilih jenis family dulu!", "info");
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
                        <li class="breadcrumb-item active">Species</li>
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
                                        <span class="label" id="statTran">Jenis Family</span>
                                    </div>
                                    <div class="col-6">
                                        <select class="form-control w-100" id="selectFamily">
                                            <option value="-1">--Choose One--</option>
                                            <option value="0" selected>All</option>
                                            @foreach ($families as $family)
                                            <option value="{{ $family->id }}">{{ $family->name }}</option>
                                            @endforeach
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
                                        <th>Family</th>
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
@else
@include('partial.noAccess')
@endif

@endsection