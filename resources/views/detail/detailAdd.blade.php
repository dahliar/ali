<meta name="csrf-token" content="{{ csrf_token() }}" />
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
            title: 'Tambah detil transaksi?',
            text: "Simpan detil transaksi penjualan",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Detil transaksi disimpan',
                    text: "Simpan detil transaksi penjualan eksport",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("formDetilPenjualan").submit();
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
            url: '{{ url("getItemsForSelectOption") }}/'+'{{ $transactionId }}'+"/0/"+speciesId,
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
        $('#species').on('change', function() {
            var speciesId = $(this).val();
            if (speciesId>0){
                selectOptionChange(speciesId, -1);
            }else{
                $('#item')
                .empty()
                .append('<option value="-1">--Choose Species First--</option>');
                $('[name="existingStock"]').val("");
                $('[name="weightbase"]').val("");
                $('[name="amount"]').val(0);
                swal.fire('warning','Choose Species first!','info');
            }
        });
        $('#item').on('change', function() {
            var e = document.getElementById("item");
            var itemId = e.options[e.selectedIndex].value;
            if (itemId>0){
                $.ajax({
                    url: '{{ url("getItemAmount") }}'+"/"+itemId,
                    type: "GET",
                    data : {"_token":"{{ csrf_token() }}"},
                    dataType: "json",
                    success:function(data){
                        $('[name="existingStock"]').val(data['amount']);
                        $('[name="weightbase"]').val(data['weightbase']);
                    }
                });
            }else{
                swal.fire('warning','Choose Item first!','info');
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
                            <a class="white-text" href="{{ url('/transactionList') }}">Transaksi Penjualan</a>
                        </li>
                        <li class="breadcrumb-item active">Tambah detail transaksi penjualan</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah detail transaksi penjualan</h5>
            </div>
            <div class="modal-body">
                <form id="formDetilPenjualan" action="{{route('itemDetailTransactionAdd')}}" method="get" name="formDetilPenjualan">
                    <input id="transactionId" name="transactionId" type="hidden" value="{{ old('transactionId', $transactionId) }}">
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
                        <div class="col-md-2">
                            <span class="label err" id="speciesListAddLabel"></span>
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
                            <span class="label">Stok saat ini</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input id="existingStock" value="{{ old('existingStock') }}" name="existingStock" type="text" class="form-control text-end" readonly>
                                <span class="input-group-text">MC / Bag</span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                            <span class="label">Weightbase</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input id="weightbase" value="{{ old('weightbase') }}" name="weightbase" type="text" class="form-control text-end" readonly>
                                <span class="input-group-text">Kg per MC/Bag</span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group mb-2">
                        <div class="col-md-2 text-end">
                            <span class="label">Jumlah*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input id="amount" value="{{ old('amount',0) }}" name="amount" type="number" class="form-control text-end" step="0.01">
                                <span class="input-group-text">MC / Bag</span>
                            </div>
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
        <h3>Catatan : </h3>
        <ol>
            <li>Gunakan koma untuk untuk desimal</li>
        </ol>
    </div>
</body>
@endsection