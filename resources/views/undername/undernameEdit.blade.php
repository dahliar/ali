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
    var i=1;
    function disableForm() { 
        @if(($undername->status == 2) or ($undername->status == 3))
        var inputs = document.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].disabled = true;
        }
        var selects = document.getElementsByTagName("select");
        for (var i = 0; i < selects.length; i++) {
            selects[i].disabled = true;
        }
        var textareas = document.getElementsByTagName("textarea");
        for (var i = 0; i < textareas.length; i++) {
            textareas[i].disabled = true;
        }
        var textareas = document.getElementsByTagName("button");
        for (var i = 0; i < textareas.length; i++) {
            textareas[i].disabled = true;
        }
        @endif
    }

    $(document).ready(function() {
        disableForm();
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
                            $('[name="companydetail"]').val(data.address+'. '+data.nation);
                        }else{
                        }
                    }
                });
            }else{
                swal.fire('warning','Choose Company first!','info');
            }
        });
        $('#valutaType').on('change', function() {
            var e = document.getElementById("valutaType");
            var valutaType = e.options[e.selectedIndex].value;
            var valText = e.options[e.selectedIndex].text;

            if (valutaType>0){
                document.getElementById("spanAm").textContent=valText;
                document.getElementById("spanAd").textContent=valText;
            }else{
                swal.fire('warning','Choose Payment Valuta first!','info');
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
                            <a class="white-text" href="{{ url('transactionList')}}">Transaksi</a>
                        </li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-body">
            <form action="{{route('undernameUpdate')}}" method="POST" enctype="multipart/form-data">
                <div class="d-grid gap-2">
                    <input id="undernameId" name="undernameId"  class="form-control"  value="{{$undername->id}}" type="hidden">
                    @csrf
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanLabel">Shipper*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="shipper" name="shipper"  class="form-control" value="{{ old('shipper', $undername->shipper) }}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanLabel">Proforma Invoice Number*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="pinum" name="pinum"  class="form-control" value="{{$undername->pinum }}"readonly>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanLabel">Transaction Number*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="transactionNum" name="transactionNum"  class="form-control" value="{{$undername->transactionNum }}" readonly>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanLabel">Nomor PEB</span>
                        </div>
                        <div class="col-md-3">
                            <input id="pebNum" name="pebNum" class="form-control" value="{{ old('pebNum', $undername->pebNum) }}" placeholder="Nomor PEB">
                            <span style="font-size:9px" class="label">Wajib diisi ketika dilakukan penyelesaian transaksi</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">Tanggal PEB</span>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="pebDate" name="pebDate" class="form-control" value="{{ old('pebDate', $undername->pebDate) }}">
                            <span style="font-size:9px" class="label">Wajib diisi ketika dilakukan penyelesaian transaksi</span>
                        </div>
                    </div>
                    @if($undername->pebFile)
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">File PEB</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <a href="{{ url('getFileDownload').'/'.$undername->pebFile }}" target="_blank">{{$undername->pebFile}}</a>
                            </div>
                        </div>
                    </div>       
                    @endif                    
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">Upload File PEB Baru</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input class="form-control" type="file" id="pebFile" name="pebFile" accept="image/*">
                            </div>
                            <span style="font-size:9px" class="label">File dalam bentuk image dengan ukuran maksimal 1MB</span>
                        </div>
                    </div>  
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanLabel">Shipper Address*</span>
                        </div>
                        <div class="col-md-8">
                            <textarea id="shipperAddress" name="shipperAddress" rows="4"  class="form-control" style="min-width: 100%">{{ old('shipperAddress', $undername->shipperAddress) }}</textarea>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanBank">Registration Number*</span>
                        </div>
                        <div class="col-md-8">
                            <select class="form-select w-100" id="countryId" name="countryId">
                                <option value="-1">--Choose One--</option>
                                @foreach ($countryRegister as $register)
                                @if ( $register->id == old('countryId', $undername->countryId) )
                                <option value="{{ $register->id }}" selected>{{ $register->name }} - {{ $register->registration }}</option>
                                @else
                                <option value="{{ $register->id }}">{{ $register->name }} - {{ $register->registration }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="companyName">Consignee*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="company" name="company" class="form-select" >
                                <option value="-1">--Choose One--</option>
                                @foreach ($companies as $company)
                                @if ( $company->id == old('company', $undername->companyId))
                                <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
                                @else
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanConsignee">Consignee Details*</span>
                        </div>
                        <div class="col-md-8">
                            <textarea id="companydetail" name="companydetail" rows="4"  class="form-control" style="min-width: 100%" placeholder="Information about the company such as address, tax id, contact number etc">{{ old('companydetail', $undername->companydetail) }}</textarea>
                        </div>  
                    </div>

                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPacker">Packer*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="packer" value="{{ old('packer', $undername->packer) }}" name="packer" type="text" class="form-control" placeholder="Packer name">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanLoading">Port of Loading*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="loadingPort" value="{{ old('loadingPort',$undername->loadingport) }}" name="loadingPort" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanDestination">Port of Destination*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="destinationPort" name="destinationPort" value="{{ old('destinationPort', $undername->destinationport) }}" type="text" class="form-control" placeholder="Destination port & country">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">Order Type*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="orderType" name="orderType" class="form-select" >
                                <option value="-1" selected>--Choose One--</option>
                                <option value="1" @if(old('orderType', $undername->orderType) == 1) selected @endif>FOB</option>
                                <option value="2" @if(old('orderType', $undername->orderType) == 2) selected @endif>CNF</option>
                                <option value="3" @if(old('orderType', $undername->orderType) == 3) selected @endif>CFO</option>
                            </select>
                        </div>
                    </div>                
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanParty">Party*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="containerParty" name="containerParty" type="text" value="{{ old('containerParty', $undername->containerParty) }}" class="form-control" placeholder="vessel size such as 1 x 40 or 1 x 20">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="forwarderName">Forwarder*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="forwarder" name="forwarder" class="form-select" >
                                <option value="-1">--Choose One--</option>
                                @foreach ($forwarders as $forwarder)
                                @if ( $forwarder->id == old('forwarder', $undername->forwarderid))
                                <option value="{{ $forwarder->id }}" selected>{{ $forwarder->name }}</option>
                                @else
                                <option value="{{ $forwarder->id }}">{{ $forwarder->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <table width="100%">
                        <tr>
                            <td><hr /></td>
                            <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>Shipment</h4></td>
                            <td><hr /></td>
                        </tr>
                    </table>

                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">Tanggal Transaksi*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="transactionDate" name="transactionDate" class="form-control text-end" value="{{ old('transactionDate', $undername->transactionDate)}}" >
                            </div>
                        </div>
                    </div>                
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">Tanggal Loading*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="loadingDate" name="loadingDate" value="{{ old('loadingDate', $undername->loadingDate) }}" class="form-control text-end">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">ETD*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="departureDate" name="departureDate" value="{{ old('departureDate', $undername->departureDate) }}" class="form-control text-end">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">ETA*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="arrivalDate" name="arrivalDate" class="form-control text-end" value="{{ old('arrivaldate', $undername->arrivaldate) }}" >
                            </div>
                        </div>
                    </div>                
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPacker">Container Number*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="containerNumber" value="{{ old('containerNumber', $undername->containerNumber) }}" name="containerNumber" type="text" class="form-control" placeholder="Container number">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPacker">Seal Number*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="containerSeal" value="{{ old('containerSeal', $undername->containerSeal) }}" name="containerSeal" type="text" class="form-control" placeholder="Seal number">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPacker">Vessel*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="containerVessel" value="{{ old('containerVessel', $undername->containerVessel) }}" name="containerVessel" type="text" class="form-control" placeholder="Vessel name & number">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPayment">Container Type*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="containerType" name="containerType" class="form-select" >
                                <option value="-1" selected>--Choose One--</option>
                                <option value="1" @if(old('containerType', $undername->containerType) == 1) selected @endif>Dry</option>
                                <option value="2" @if(old('containerType', $undername->containerType) == 2) selected @endif>Reefer</option>
                            </select>
                        </div>                    
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPayment">Liners*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="liner" name="liner" class="form-select">
                                <option value="-1" selected>--Choose One--</option>
                                @foreach ($liners as $liner)
                                @if ( $liner->id == old('liner', $undername->linerId))
                                <option value="{{ $liner->id }}" selected>{{ $liner->name }}</option>
                                @else
                                <option value="{{ $liner->id }}">{{ $liner->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>                    
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">Bill of Lading*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="bl" value="{{ old('bl', $undername->bl) }}" name="bl" type="text" class="form-control" placeholder="Bill of Lading number">
                        </div>                    
                    </div>

                    <br>
                    <table width="100%">
                        <tr>
                            <td><hr /></td>
                            <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h4>Payment</h4></td>
                            <td><hr /></td>
                        </tr>
                    </table>

                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">Payment to*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="paymentTo" name="paymentTo" class="form-select" >
                                <option value="-1" selected>--Choose One--</option>
                                <option value="1" @if(old('paymentTo', $undername->paymentTo) == 1) selected @endif>Internal</option>
                                <option value="2" @if(old('paymentTo', $undername->paymentTo) == 2) selected @endif>Supplier</option>
                            </select>
                        </div>                    
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanBank">Bank*</span>
                        </div>
                        <div class="col-md-8">
                            <select class="form-select w-100" id="bank" name="bank">
                                <option value="-1">--Choose One--</option>
                                @foreach ($banks as $bank)
                                @if ( $bank->id == old('bank', $undername->paymentBank) )
                                <option value="{{ $bank->id }}" selected>{{ $bank->name }}</option>
                                @else
                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanConsignee">Bank Address*</span>
                        </div>
                        <div class="col-md-8">
                            <textarea id="paymentBankAddress" name="paymentBankAddress" rows="4"  class="form-control" style="min-width: 100%" placeholder="Bank Address">{{ old('paymentBankAddress', $undername->paymentBankAddress) }}</textarea>
                        </div>  
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPayment">Accounts*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="account" name="account" class="form-control" value="{{ old('account', $undername->paymentAccount) }}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPayment">Accounts Name*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="paymentAccountName" name="paymentAccountName" class="form-control" value="{{ old('paymentAccountName', $undername->paymentAccountName) }}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPayment">Swiftcode*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="swiftcode" name="swiftcode" class="form-control" value="{{ old('swiftcode', $undername->paymentSwiftcode) }}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPayment">Payment Valuta*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="valutaType" name="valutaType" class="form-select" >
                                <option value="-1" selected>--Choose One--</option>
                                <option value="1" @if(old('valutaType', $undername->paymentValuta) == 1) selected @endif>Rupiah</option>
                                <option value="2" @if(old('valutaType', $undername->paymentValuta) == 2) selected @endif>US Dollar</option>
                                <option value="3" @if(old('valutaType', $undername->paymentValuta) == 3) selected @endif>Renminbi</option>
                            </select>
                        </div>                    
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPayment">Payment Amounts*</span>
                        </div>                    
                        <div class="col-md-8">
                            <input id="paymentAmount" name="paymentAmount" type="number" step="0.01" value="{{ old('paymentAmount', $undername->paymentAmount) }}" class="form-control text-end" placeholder="use commas for decimals">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPayment">Advance Amounts*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="advanceAmount" name="advanceAmount" type="number" step="0.01" value="{{ old('advanceAmount', $undername->paymentAdvance) }}" class="form-control text-end" placeholder="use commas for decimals">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label" id="spanPayment">Payment status*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="paymentStatus" name="paymentStatus" class="form-select" >
                                <option value="-1" selected>--Choose One--</option>
                                <option value="1" @if(old('paymentStatus', $undername->paymentStatus) == 1) selected @endif>Saldo belum diterima dari buyer</option>
                                <option value="2" @if(old('paymentStatus', $undername->paymentStatus) == 2) selected @endif>Saldo di internal</option>
                                <option value="3" @if(old('paymentStatus', $undername->paymentStatus) == 2) selected @endif>Saldo partial transfer ke supplier</option>
                                <option value="4" @if(old('paymentStatus', $undername->paymentStatus) == 3) selected @endif>Saldo full transfer ke supplier</option>
                            </select>
                        </div>                    
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end">
                            <span class="label">Status</span>
                        </div>
                        <div class="col-md-3">
                            <input id="currentStatus" name="currentStatus" type="hidden" value="{{ $undername->status }}">
                            @if ($undername->status == 1)
                            <select id="status" name="status" class="form-select" >
                                <option value="1" @if($undername->status == 1) selected @endif>Penawaran</option>
                                <option value="4" @if($undername->status == 4) selected @endif>Sailing</option>
                                <option value="2" @if($undername->status == 2) selected @endif>Selesai Pembayaran</option>
                                <option value="3" @if($undername->status == 3) selected @endif>Batal</option>
                            </select>
                            @endif
                            @if (($undername->status == 2) or ($undername->status == 3))
                            <select id="status" name="status" class="form-select" disabled>
                                <option value="2" @if($undername->status == 2) selected @endif>Selesai Pembayaran</option>
                                <option value="3" @if($undername->status == 3) selected @endif>Batal</option>
                            </select>
                            @endif
                            @if($undername->status == 4)
                            <select id="status" name="status" class="form-select">
                                <option value="4" @if($undername->status == 4) selected @endif>Sailing</option>
                                <option value="2" @if($undername->status == 2) selected @endif>Selesai Pembayaran</option>
                                <option value="3" @if($undername->status == 3) selected @endif>Batal</option>
                            </select>
                            @endif
                        </div>
                    </div> 
                    @if(($undername->status == 1) or ($undername->status == 4))
                    <div class="row form-group">
                        <div class="col-md-2 text-md-end"></div>
                        <div class="text-center col-md-8">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</body>
@endsection