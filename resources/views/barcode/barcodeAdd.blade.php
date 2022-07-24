<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<script type="text/javascript"> 
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

<body>
    @if ($errors->any())
    <div class="alert alert-success">
        <div class="row form-inline" onclick='$(this).parent().remove();'>
            <div class="col-11">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-1 text-center">
                <span class="label"><strong >x</strong></span>
            </div>
        </div>
    </div>
    <script type="text/javascript"> 
        selectOptionChange({{ old('species') }}, {{ old('item') }});
    </script>
    @endif

    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/barcodeList') }}">Barcode</a>
                        </li>
                        <li class="breadcrumb-item active">Generate Barcode</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Generate Barcode</h5>
            </div>
            <div class="modal-body">
                <form action="{{url('barcodeImageGenerate')}}" method="post">
                    @csrf
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
                            <span class="label">Tanggal produksi*</span>
                        </div>
                        <div class="col-md-4">
                            <input type="date" id="transactionDate" name="transactionDate" class="form-control text-end" value="{{ old('transactionDate', date('Y-m-d'))}}" >
                        </div>
                    </div>
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                            <span class="label">Supplier*</span>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select w-100" id="company" name="company">
                                <option value="-1">--Pilih dahulu--</option>
                                @foreach ($companies as $company)
                                @if ( $spec->id == old('company') )
                                <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
                                @else
                                <option value="{{ $company->id }}">{{ $company->name }}</option>                    
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                            <span class="label">Jumlah barcode*</span>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="jumlahBarcode" name="jumlahBarcode" class="form-control text-end" value="{{ old('jumlahBarcode')}}" >
                        </div>
                    </div>
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">Generate</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <h3>Catatan : </h3>
        <ol>
            <li>Gunakan koma untuk untuk desimal</li>
        </ol>
    </div>
</body>
@endsection