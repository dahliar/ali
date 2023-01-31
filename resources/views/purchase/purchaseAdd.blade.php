<meta name="csrf-token" content="{{ csrf_token() }}" />

@extends('layouts.layout')

@section('content')
<script type="text/javascript"> 
    $(document).ready(function() {

        $('.js-example-basic-single').select2();

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
                <div class="row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('purchaseTransactionList')}}">Transaksi Pembelian</a>
                            </li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="card card-body">
            <form id="PurchaseForm" action="{{route('purchaseStore')}}"  method="get" name="PurchaseForm" enctype="multipart/form-data">
                @csrf
                <div class="d-grid gap-1">
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label" id="companyName">Supplier*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="company" name="company" class="js-example-basic-single">
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
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Tanggal Penerimaan Barang*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="arrivalDate" name="arrivalDate" value="{{ old('arrivalDate', date('Y-m-d')) }}" class="form-control text-end" min="{{ old('arrivalDate', date('Y-m-d', strtotime('-1 month')))}}" max="{{ old('arrivalDate', date('Y-m-d', strtotime('+1 days')))}}">
                            </div>
                        </div>
                    </div>               
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Tanggal Transaksi*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="purchaseDate" name="purchaseDate" class="form-control text-end" value="{{ old('purchaseDate', date('Y-m-d'))}}" min="{{ old('purchaseDate', date('Y-m-d', strtotime('-1 month')))}}" max="{{ old('purchaseDate', date('Y-m-d', strtotime('+1 days')))}}">
                            </div>
                        </div>
                    </div>                
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Tanggal Jatuh tempo*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="dueDate" name="dueDate" class="form-control text-end" value="{{ old('dueDate', date('Y-m-d'))}}" min="{{ old('dueDate', date('Y-m-d'))}}" max="{{ old('dueDate', date('Y-m-d', strtotime('+2 month')))}}">
                            </div>
                        </div>
                    </div>                
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Persen Pajak</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input id="taxPercentage" name="taxPercentage" type="number"  step="0.01" class="form-control text-end" value="{{old('taxPercentage',0)}}" readonly>
                                <span class="input-group-text col-md-3">%</span>

                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label" id="spanPayment">Mata Uang Transaksi*</span>
                        </div>
                        <div class="col-md-3">
                            <select id="valutaType" name="valutaType" class="form-select" >
                                <option value="-1" selected>--Choose One--</option>
                                <option value="1" @if(old('valutaType') == 1) selected @endif>Rupiah</option>
                                <option value="2" @if(old('valutaType') == 2) selected @endif>US Dollar</option>
                                <option value="3" @if(old('valutaType') == 3) selected @endif>Renminbi</option>
                            </select>
                        </div>                    
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">File Invoice</span>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input class="form-control" type="file" id="imageurl" name="imageurl">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Catatan Transaksi</span>
                        </div>
                        <div class="col-md-8">
                            <textarea id="paymentTerms" name="paymentTerms" rows="4"  class="form-control" style="min-width: 100%" placeholder="Informasi umum tentang pembelian">{{ old('paymentTerms') }}</textarea>
                        </div>  
                    </div>
                    <div class="row form-group">
                        <div class="text-center col-md-8">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('header')
@include('partial.header')
@endsection







