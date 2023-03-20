@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js" type="text/javascript" ></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function tambahStockItem(id){
        window.open(('{{ url("itemStockAdd") }}' + "/"+ id), '_self');
    }
    function kurangiStockItem(id){
        window.open(('{{ url("itemStockSubtract") }}' + "/"+ id), '_self');
    }
    function UpdateStockUnpacked(id){
        window.open(('{{ url("editUnpacked") }}' + "/"+ id), '_self');
    }
    function historyStockItem(id){
        window.open(('{{ url("itemStockView") }}' + "/"+ id), '_self');
    }
    function historyStockKurang(id){
        window.open(('{{ url("itemStockSubtractView") }}' + "/"+ id), '_self');
    }


    function myFunction(){
        var speciesId = document.getElementById("selectSpecies").value;
        var sizeId = document.getElementById("size").value;
        var gradeId = document.getElementById("grade").value;
        var weightbase = document.getElementById("weightbase").value;
        var shapeId = document.getElementById("shape").value;
        var packingId = document.getElementById("packing").value;
        var freezingId = document.getElementById("freezing").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getAllStockItem") }}',
                data: function (d){
                    d.speciesId=speciesId;
                    d.sizeId=sizeId;
                    d.gradeId=gradeId;
                    d.weightbase=weightbase;
                    d.shapeId=shapeId;
                    d.packingId=packingId;
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
                {   "width": "25%", "targets":  [1], "className": "text-left"   },
                {   "width": "5%", "targets":  [2], "className": "text-left" },
                {   "width": "20%", "targets":  [3], "className": "text-left" },
                {   "width": "10%", "targets":  [4], "className": "text-end" },
                {   "width": "10%", "targets":  [5], "className": "text-end" },
                {   "width": "15%", "targets":  [6], "className": "text-center" },
                {   "width": "10%", "targets":  [7], "className": "text-center" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'itemName', name: 'itemName'},
                {data: 'id', name: 'id'},
                {data: 'amount', name: 'amount'},
                {data: 'totalGudang', name: 'totalGudang'},
                {data: 'loading', name: 'loading'},
                {data: 'action1', name: 'action1', orderable: false, searchable: false},
                {data: 'action2', name: 'action2', orderable: false, searchable: false}
                ]
        });
    }

    $(document).ready(function() {
        $('.js-example-basic-single').select2();

        $('#selectSpecies').on('change', function() {
            var speciesId = $(this).val();
            if (speciesId>=0){
                $.ajax({
                    url: '{{ url("getSizesForSpecies") }}/'+speciesId,
                    type: "GET",
                    dataType: "json",
                    success:function(data){
                        if(data){
                            $('#size').empty();
                            var html = '';
                            var i;
                            html += '<option value="0">Semua size</option>';
                            for(i=0; i<data.length; i++){
                                html += '<option value='+data[i].sizeId+'>'+
                                data[i].name+
                                '</option>';
                                $('#size').html(html);
                            }
                        }
                    }
                });
            }else{
                $('#size').empty().append('<option value="0">Semua size</option>');
                $('#grade').empty().append('<option value="0">Semua grade</option>');
                $('#packing').empty().append('<option value="0">Semua bentuk packing</option>');
                $('#shape').empty().append('<option value="0">Semua bentuk olahan</option>');
                $('#freezing').empty().append('<option value="0">Semua bentuk freezing</option>');
                $('#weightbase').empty().append('<option value="0">Semua bentuk weightbase</option>');               
                swal.fire('warning','Choose Species first!','info');
            }
        });

        $('#size').on('change', function() {
            var sizeId = $(this).val();
            if (sizeId>=0){
                $.ajax({
                    url: '{{ url("getGradesForSize") }}/'+sizeId,
                    type: "GET",
                    dataType: "json",
                    success:function(data){
                        if(data){
                            $('#grade').empty();
                            var html = '';
                            var i;
                            html += '<option value="0">Semua grade</option>';
                            for(i=0; i<data.length; i++){
                                html += '<option value='+data[i].gradeId+'>'+
                                data[i].name+
                                '</option>';
                                $('#grade').html(html);
                            }
                        }
                    }
                });
            }else{
                $('#grade').empty().append('<option value="0">Semua grade</option>');
                $('#packing').empty().append('<option value="0">Semua bentuk packing</option>');
                $('#shape').empty().append('<option value="0">Semua bentuk olahan</option>');
                $('#freezing').empty().append('<option value="0">Semua bentuk freezing</option>');
                $('#weightbase').empty().append('<option value="0">Semua bentuk weightbase</option>');                
                swal.fire('warning','Choose Size first!','info');
            }
        });
        $('#grade').on('change', function() {
            var sizeId = document.getElementById("size").value;
            var gradeId = $(this).val();
            if (gradeId>=0){
                $.ajax({
                    url: '{{ url("getWeightbaseForSize") }}/'+sizeId+'/'+gradeId,
                    type: "GET",
                    dataType: "json",
                    success:function(data){
                        if(data){
                            $('#weightbase').empty();
                            var html = '';
                            var i;
                            html += '<option value="0">Semua weightbase</option>';
                            for(i=0; i<data.length; i++){
                                html += '<option value='+data[i].weightbase+'>'+
                                data[i].weightbase+
                                '</option>';
                                $('#weightbase').html(html);
                            }
                        }
                    }
                });

            }else{
                $('#weightbase').empty().append('<option value="0">Semua weightbase</option>');
                $('#shape').empty().append('<option value="0">Semua bentuk olahan</option>');
                $('#packing').empty().append('<option value="0">Semua bentuk packing</option>');
                $('#freezing').empty().append('<option value="0">Semua bentuk freezing</option>');
                swal.fire('warning','Choose grade first!','info');
            }
        });
        $('#weightbase').on('change', function() {
            var sizeId = document.getElementById("size").value;
            var gradeId = document.getElementById("grade").value;
            var weightbase = $(this).val();
            if (weightbase>=0){
                $.ajax({
                    url: '{{ url("getShapesForWeightbase") }}/'+sizeId+'/'+gradeId+'/'+weightbase,
                    type: "GET",
                    dataType: "json",
                    success:function(data){
                        if(data){
                            $('#shape').empty();
                            var html = '';
                            var i;
                            html += '<option value="0">Semua bentuk olahan</option>';
                            for(i=0; i<data.length; i++){
                                html += '<option value='+data[i].id+'>'+
                                data[i].name+
                                '</option>';
                                $('#shape').html(html);
                            }
                        }
                    }
                });

            }else{
                $('#shape').empty().append('<option value="0">Semua bentuk olahan</option>');
                $('#packing').empty().append('<option value="0">Semua bentuk packing</option>');
                $('#freezing').empty().append('<option value="0">Semua bentuk freezing</option>');
                swal.fire('warning','Choose weightbase first!','info');
            }
        });
        $('#shape').on('change', function() {
            var sizeId = document.getElementById("size").value;
            var gradeId = document.getElementById("grade").value;
            var weightbase = document.getElementById("weightbase").value;
            var shapeId = $(this).val();
            if (shapeId>=0){
                $.ajax({
                    url: '{{ url("getPackingsForShape") }}/'+sizeId+'/'+gradeId+'/'+weightbase+'/'+shapeId,
                    type: "GET",
                    dataType: "json",
                    success:function(data){
                        if(data){
                            $('#packing').empty();
                            var html = '';
                            var i;
                            html += '<option value="0">Semua bentuk olahan</option>';
                            for(i=0; i<data.length; i++){
                                html += '<option value='+data[i].id+'>'+
                                data[i].name+
                                '</option>';
                                $('#packing').html(html);
                            }
                        }
                    }
                });
            }else{
                $('#packing').empty().append('<option value="0">Semua bentuk packing</option>');
                $('#freezing').empty().append('<option value="0">Semua bentuk freezing</option>');
                swal.fire('warning','Choose grade first!','info');
            }
        });
        $('#packing').on('change', function() {
            var sizeId = document.getElementById("size").value;
            var gradeId = document.getElementById("grade").value;
            var weightbase = document.getElementById("weightbase").value;
            var shapeId = document.getElementById("shape").value;
            var packingId = $(this).val();
            if (packingId>=0){
                $.ajax({
                    url: '{{ url("getFreezingsForPacking") }}/'+sizeId+'/'+gradeId+'/'+weightbase+'/'+shapeId+'/'+packingId,
                    type: "GET",
                    dataType: "json",
                    success:function(data){
                        if(data){
                            $('#freezing').empty();
                            var html = '';
                            var i;
                            html += '<option value="0">Semua bentuk packing</option>';
                            for(i=0; i<data.length; i++){
                                html += '<option value='+data[i].id+'>'+
                                data[i].name+
                                '</option>';
                                $('#freezing').html(html);
                            }
                        }
                    }
                });
            }else{
                $('#freezing').empty().append('<option value="0">Semua bentuk freezing</option>');
                swal.fire('warning','Choose Freezing first!','info');
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
                <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Stock per-Barang</li>
                    </ol>
                </nav>
                <a href="{{ url('historyPerubahanStock')}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="History perubahan stock"><i class="fa fa-history">History Stock</i>
                </a>
            </div>
        </div>
        <div class="card card-header">
            <div class="row form-group">
                <div class="col-2 my-auto text-md-right">
                    <span class="label" id="statTran">Jenis Spesies</span>
                </div>
                <div class="col-6">
                    <select class="js-example-basic-single w-100" id="selectSpecies">
                        <option value="0">Semua Species</option>
                        @foreach ($speciesList as $species)
                        <option value="{{ $species->id }}">{{ $species->nameBahasa }} - {{ $species->name }}</option>
                        @endforeach
                        <option value="0" selected>Semua Spesies</option>
                    </select>
                </div>
                <div class="col-2 my-auto">
                    <span class="label" id="errSpan"></span>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-2 my-auto text-md-right">
                    <span class="label">Size*</span>
                </div>
                <div class="col-md-5">
                    <select class="form-select w-100" id="size" name="size">
                        <option value="0">Semua Size</option>
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-2 my-auto text-md-right">
                    <span class="label">Grade*</span>
                </div>
                <div class="col-md-5">
                    <select class="form-select w-100" id="grade" name="grade">
                        <option value="0">Semua Grade</option>
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-2 my-auto text-md-right">
                    <span class="label">Weightbase*</span>
                </div>
                <div class="col-md-5">
                    <select class="form-select w-100" id="weightbase" name="weightbase">
                        <option value="0">Semua Weightbase</option>
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-2 my-auto text-md-right">
                    <span class="label">Bentuk Olahan*</span>
                </div>
                <div class="col-md-5">
                    <select class="form-select w-100" id="shape" name="shape">
                        <option value="0">Semua bentuk olahan</option>
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-2 my-auto text-md-right">
                    <span class="label">Packing Type*</span>
                </div>
                <div class="col-md-5">
                    <select class="form-select w-100" id="packing" name="packing">
                        <option value="0">Semua bentuk packing</option>
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-2 my-auto text-md-right">
                    <span class="label">Freeze Type*</span>
                </div>
                <div class="col-md-5">
                    <select class="form-select w-100" id="freezing" name="freezing">
                        <option value="0">Semua bentuk freezing</option>
                    </select>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-2 my-auto text-md-right">
                </div>
                <div class="col-md-5">
                    <button type="submit" onclick="myFunction()" class="btn btn-primary">Cari</button>
                </div>
            </div>

        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>ID</th>
                            <th>Packed/Unpacked</th>
                            <th>Total (Kg)</th>
                            <th>Sailing (Kg)</th>
                            <th>Tambah</th>
                            <th>Kurang</th>
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