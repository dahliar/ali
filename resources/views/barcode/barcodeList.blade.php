@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<script type="text/javascript">
    function getFileDownload(filename){
        window.open(('{{ url("getBarcodeFileDownload") }}'+"/"+filename), '_self');
    };
    function myFunction(){
        var itemId = document.getElementById("item").value;
        $('#datatable').DataTable({
            ajax:'{{ url("getAllBarcodes") }}' + "/"+ itemId,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":[0], "className": "text-center"   },
                {   "width": "65%", "targets": [1], "className": "text-left"    },
                {   "width": "10%",  "targets": [2], "className": "text-left" },
                {   "width": "10%",  "targets": [3], "className": "text-center"  },
                {   "width": "10%",  "targets": [4], "className": "text-center"  },
                {   "width": "10%",  "targets": [5], "className": "text-center"  }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'productionDate', name: 'productionDate'},
                {data: 'amountPrinted', name: 'amountPrinted'},
                {data: 'startFrom', name: 'startFrom'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
        });
    }
    function selectOptionChange(speciesId, itemId){
        $.ajax({
            url: '{{ url("barcodeItemList") }}/'+speciesId,
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
                            data[i].itemName+
                            '</option>';
                        } else {
                            html += '<option selected value='+data[i].itemId+'>'+
                            data[i].itemName+
                            '</option>';
                        }
                        $('#item').html(html);
                    }
                }
            }
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
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Daftar Barcode</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-content">
                <div class="modal-body">
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
                    </div>
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                            <span class="label">Barang*</span>
                        </div>
                        <div class="col-md-4">
                            <select id="item" name="item" class="form-select" >
                                <option value="-1">--Choose Species First--</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" onclick="myFunction()" class="btn btn-primary">Tampilkan</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Awal</th>
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
@endsection