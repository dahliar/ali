@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function myFunction(){
        Swal.fire({
            title: 'Tambah detail transaksi pembelian?',
            text: "Simpan detil transaksi pembelian",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Detail ransaksi pembelian disimpan',
                    text: "Simpan detail transaksi pembelian",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("purchaseItemAddForm").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Pembuatan transaksi dibatalkan",
                    'info'
                    );
            }
        })
    }; 
    function selectOptionChange(speciesId, itemId){
        $.ajax({
            url: '{{ url("getItemsForSelectOption") }}'+'/0/'+'{{ $purchase->id }}'+"/"+speciesId
            ,
            type: "GET",
            data : {"_token":"{{ csrf_token() }}"},
            dataType: "json",
            success:function(data){
                if(data){
                    var html = '';
                    var i;
                    html += '<option value="-1">Pilih dulu</option>';
                    for(i=0; i<data.length; i++){
                        if (data[i].itemId != itemId){
                            html += '<option value='+data[i].itemId+'>'+
                            (i+1)+". "+data[i].itemName+
                            '</option>';
                        }
                        else{
                            html += '<option selected value='+data[i].itemId+'>'+
                            (i+1)+". "+data[i].itemName+
                            '</option>';
                        }
                    }
                    $('#item').html(html);
                }else{
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
                .append('<option value="-1">--Pilih spesies dulu--</option>');
                $('[name="amount"]').val(0);
                $('[name="harga"]').val(0);
                swal.fire('warning','Pilih species dulu!','info');
            }
        });
    });
</script>

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
<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/purchaseList') }}">Transaksi pembelian</a>
                        </li>
                        <li class="breadcrumb-item active">Tambah detil transaksi pembelian</li>
                    </ol>
                </nav>
            </div>
        </div>
        <form id="purchaseItemAddForm" action="{{url('purchaseItemStore')}}" method="get" name="purchaseItemAddForm">
            <div class="card card-body">
                <div class="row form-group mb-2">
                    <div class="col-md-5 form-inline">
                        <div class="col-md-6">                      
                            <input id="purchaseId" name="purchaseId" type="hidden" value="{{ old('purchaseId', $purchase->id) }}">
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                        <span class="label">Spesies*</span>
                    </div>
                    <div class="col-md-8">
                        <select class="form-select w-100" id="species" name="species">
                            <option value="-1">--Pilih species--</option>
                            @foreach ($species as $spec)
                            @if ( $spec->id == old('species') )
                            <option value="{{ $spec->id }}" selected>{{ $spec->nameBahasa }} - {{ $spec->name }}</option>
                            @else
                            <option value="{{ $spec->id }}">{{ $spec->nameBahasa }} - {{ $spec->name }}</option>                    
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <span class="label err" id="speciesListAddLabel"></span>
                    </div>
                </div>
                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                        <span class="label">Barang*</span>
                    </div>
                    <div class="col-md-8">
                        <select id="item" name="item" class="form-control" >
                            <option value="-1">--Pilih spesies dulu--</option>
                        </select>
                    </div>
                </div>
                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                        <span class="label">Jumlah*</span>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input id="amount" value="{{ old('amount',0) }}" name="amount" type="number" class="form-control text-end" step="0.01">
                            <span class="input-group-text">Kg</span>
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                        <span class="label">Harga*</span>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text">{{$marker}}</span>
                            <input id="harga" value="{{ old('harga',0) }}" name="harga" type="number" class="form-control text-end" step="0.01">
                            <span class="input-group-text">per Kg</span>
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-2">
                    <div class="col-md-2 text-end">
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Save</button>
                        <input type="reset" value="Reset" class="btn btn-secondary">
                    </div>
                </div>
            </div>
        </div>
    </form>
    <h3>Catatan : </h3>
    <ol>
        <li>Gunakan koma untuk untuk desimal</li>
    </ol>
</body>
@endsection