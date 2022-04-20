<meta name="csrf-token" content="{{ csrf_token() }}" />

@extends('layouts.layout')

@section('content')
<script type="text/javascript"> 
    $(document).ready(function() {
        $('#company').on('change', function() {
            var company = $(this).val();
            if (company>0){
                $.ajax({
                    url: '{{ url("getOneCompany") }}'+"/"+company,
                    type: "GET",
                    data : {"_token":"{{ csrf_token() }}"},
                    dataType: "json",
                    success:function(data){
                        if(data){
                            if(data.npwp===null){
                                $('[name="taxPercentage"]').val('0.5');
                            } else{
                                $('[name="taxPercentage"]').val('0.25');
                            }
                        }else{
                        }
                    }
                });
            }else{
                swal.fire('warning','Choose Company first!','info');
            }
        });
    });
</script>

@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data stock berhasil ditambahkan", "info");
</script>
@endif

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
                        <li class="breadcrumb-item active">
                            <a class="white-text" href="{{ url('purchaseTransactionList')}}">Transaksi Pembelian</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card card-body">
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <form id="PurchaseForm" action="{{url('purchaseUpdate')}}"  method="post" name="PurchaseForm">
                        @csrf
                        <div class="d-grid gap-1">
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="companyName">No Pembelian</span>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" id="purchaseNum" name="purchaseNum" value="{{ $purchase->purchasingNum }}" class="form-control" disabled>
                                    <input type="hidden" id="purchaseId" name="purchaseId" value="{{ old('purchaseId' , $purchase->id) }}" class="form-control">
                                </div>
                            </div>      
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="companyName">Supplier</span>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" id="companyName" name="companyName" value="{{ old('companyName' , $companyName->name) }}" class="form-control" readonly>
                                </div>
                            </div>      
                            <div class="row form-group">
                                <div class="col-md-3 my-auto">
                                    <span class="label">Tanggal Penerimaan Barang*</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="date" id="arrivalDate" name="arrivalDate" value="{{ old('arrivalDate', $purchase->arrivaldate) }}" class="form-control text-end" @if($purchase->status != 1) readonly @endif>
                                    </div>
                                </div>
                            </div>               
                            <div class="row form-group">
                                <div class="col-md-3 my-auto">
                                    <span class="label">Tanggal Transaksi*</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="date" id="purchaseDate" name="purchaseDate" class="form-control text-end" value="{{ old('purchaseDate', $purchase->purchaseDate) }}"  @if($purchase->status != 1) readonly @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanPayment">Mata Uang Transaksi*</span>
                                </div>
                                <div class="col-md-3">
                                    <select id="valutaType" name="valutaType" class="form-select" disabled >
                                        <option value="-1" selected>--Choose One--</option>
                                        <option value="1" @if(old('valutaType', $purchase->valutaType) == 1) selected @endif>Rupiah</option>
                                        <option value="2" @if(old('valutaType', $purchase->valutaType) == 2) selected @endif>US Dollar</option>
                                        <option value="3" @if(old('valutaType', $purchase->valutaType) == 3) selected @endif>Renminbi</option>
                                    </select>
                                </div>                    
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 my-auto">
                                    <span class="label">Persen Pajak</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input id="taxPercentage" name="taxPercentage" type="number"  step="0.01" class="form-control text-end" value="{{old('taxPercentage', ($purchase->taxPercentage)) }}" disabled>
                                        <span class="input-group-text col-md-2">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 my-auto">
                                    <span class="label">Jumlah</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input id="paymentAmount" name="paymentAmount" type="text" class="form-control text-end" value="@php echo number_format($purchase->paymentAmount, 2, ',', '.') @endphp" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 my-auto">
                                    <span class="label">Pajak</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input id="tax" name="tax" type="text" class="form-control text-end" value="@php echo number_format($purchase->tax, 2, ',', '.') @endphp" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanPayment">Potongan Pajak*</span>
                                </div>
                                <div class="col-md-3">
                                    <select id="valutaType" name="valutaType" class="form-select" disabled>
                                        <option value="0" @if($purchase->taxIncluded == 0) selected @endif>Tidak</option>
                                        <option value="1" @if($purchase->taxIncluded == 1) selected @endif>Ya</option>
                                    </select>
                                </div>                    
                            </div>                
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanPayment">Status Pembelian*</span>
                                </div>
                                <div class="col-md-3">
                                    <select id="progressStatus" name="progressStatus" class="form-select" @if($purchase->status != 1) disabled @endif>
                                        <option value="1" @if($purchase->taxIncluded == 1) selected @endif>On Progress</option>
                                        <option value="2" @if($purchase->taxIncluded == 2) selected @endif>Selesai</option>
                                        <option value="3" @if($purchase->taxIncluded == 3) selected @endif>Batal</option>
                                    </select>
                                </div>                    
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label">Catatan Transaksi</span>
                                </div>
                                <div class="col-md-8">
                                    <textarea id="paymentTerms" name="paymentTerms" rows="4"  class="form-control" style="min-width: 100%" placeholder="Informasi umum tentang pembelian"  @if($purchase->status != 1) readonly @endif>{{ old('paymentTerms', $purchase->paymentTerms) }}</textarea>
                                </div>  
                            </div>
                        </div>

                        @if($purchase->status == 1)
                        <div class="row form-group">
                            <div class="text-center col-md-8">
                                <button type="submit" class="btn btn-primary">Ubah</button>
                                <input type="reset" value="Reset" class="btn btn-secondary">
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('header')
@include('partial.header')
@endsection







