<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript"> 
    function myFunction(){
        Swal.fire({
            title: 'Tambah barcode?',
            text: "Simpan pembuatan barcode.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, buat saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Barcode dibuatkan.',
                    text: "Simpan pembuatan barcode.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok dibuat.'
                }).then((result) => {
                    document.getElementById("barcodeGeneratorForm").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Pembuatan barcode dibatalkan",
                    'info'
                    );
            }
        })
    };
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
                            (i+1)+". "+data[i].itemName+
                            '</option>';
                        } else {
                            html += '<option selected value='+data[i].itemId+'>'+
                            (i+1)+". "+data[i].itemName+
                            '</option>';
                        }
                        $('#item').html(html);
                    }
                }
            }
        });
    }

    $(document).ready(function() {
        $('.selectSearch').select2();

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
                <form id="barcodeGeneratorForm" name="barcodeGeneratorForm" action="{{url('barcodeImageGenerate')}}" method="post">
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
                            <span class="label">Jumlah barcode*</span>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="jumlahBarcode" name="jumlahBarcode" class="form-control text-end" value="{{ old('jumlahBarcode',0)}}" >
                        </div>
                        <div class="col-md-4">
                            *Antara 1-100
                        </div>
                    </div>
                        <!--
                        <div class="row form-group mb-2">
                            <div class="col-md-2 text-end">
                                <span class="label">Jumlah Inner Karton barcode*</span>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="innerBarcode" name="innerBarcode" class="form-control text-end" value="{{ old('innerBarcode', 0)}}" >
                            </div>
                        </div>
                    -->
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                            <span class="label">Printer*</span>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select w-100" id="printer" name="printer">
                                <option value="-1">--Pilih dahulu--</option>
                                <option value="1">Postek</option>
                                <option value="2">Zebra ZT411CN</option>
                                
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="companyName">Supplier*</span>
                        </div>
                        <div class="col-md-4">
                            <select id="company" name="company" class="selectSearch">
                                <option value="-1">--Choose One--</option>
                                @foreach ($companies as $company)
                                @if ( $company->id == old('company'))
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
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
@endsection