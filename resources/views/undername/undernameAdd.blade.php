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
                            $('[name="companydetail"]').val(data.address+'. '+data.nation);
                        }else{
                        }
                    }
                });
            }else{
                $('[name="companydetail"]').val("");
                swal.fire('warning','Choose Company first!','info');
            }
        });
        $('#valutaType').on('change', function() {
            var e = document.getElementById("valutaType");
            setValutaSpan(e);
        });
        function setValutaSpan(e){
            var valutaType = e.options[e.selectedIndex].value;
            var valText = e.options[e.selectedIndex].text;

            if (valutaType>0){
                document.getElementById("spanAm").textContent=valText;
                document.getElementById("spanAd").textContent=valText;
            }else{
                swal.fire('warning','Choose Payment Valuta first!','info');
            }
        }
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
                            <a class="white-text" href="{{ url('undernameList')}}">Undername Ekspor</a>
                        </li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="modal-content">
            <div class="row form-group">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <form action="{{url('undernameStore')}}"  method="POST">
                        @csrf
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanLabel">Shipper*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="shipper" name="shipper"  class="form-control" value="{{ old('shipper', 'PT. ANUGRAH LAUT INDONESIA.') }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanLabel">Proforma Invoice Number*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="pinum" name="pinum"  class="form-control" value="{{ old('pinum') }}" placeholder="PI Number">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanLabel">Transaction Number*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="transactionNum" name="transactionNum"  class="form-control" value="{{ old('transactionNum') }}" placeholder="Transaction Number">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanLabel">Shipper Address*</span>
                            </div>
                            <div class="col-md-8">
                                <textarea id="shipperAddress" name="shipperAddress" rows="4"  class="form-control" style="min-width: 100%">{{ old('shipperAddress', 'Jl. Raya Rembang - Tuban KM 40, Desa Bancar, Kecamatan Bancar, Kabupaten Tuban, East Java - Indonesia') }}</textarea>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanBank">Registration Number*</span>
                            </div>
                            <div class="col-md-8">
                                <select class="form-select w-100" id="countryId" name="countryId">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($countryRegister as $register)
                                    @if ( $register->id == old('countryId') )
                                    <option value="{{ $register->id }}" selected>{{ $register->name }} - {{ $register->registration }}</option>
                                    @else
                                    <option value="{{ $register->id }}">{{ $register->name }} - {{ $register->registration }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="companyName">Consignee*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="company" name="company" class="form-select" >
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
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanConsignee">Consignee Details*</span>
                            </div>
                            <div class="col-md-8">
                                <textarea id="companydetail" name="companydetail" rows="4"  class="form-control" style="min-width: 100%" placeholder="Information about the company such as address, tax id, contact number etc">{{ old('companydetail') }}</textarea>
                            </div>  
                        </div>

                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPacker">Packer*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="packer" value="{{ old('packer') }}" name="packer" type="text" class="form-control" placeholder="Packer name">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanLoading">Port of Loading*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="loadingPort" value="{{ old('loadingPort','Surabaya Port, East Java Indonesia') }}" name="loadingPort" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanDestination">Port of Destination*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="destinationPort" name="destinationPort" value="{{ old('destinationPort') }}" type="text" class="form-control" placeholder="Destination port & country">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Order Type*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="orderType" name="orderType" class="form-select" >
                                    <option value="-1" selected>--Choose One--</option>
                                    <option value="1" @if(old('orderType') == 1) selected @endif>FOB</option>
                                    <option value="2" @if(old('orderType') == 2) selected @endif>CNF</option>
                                    <option value="3" @if(old('orderType') == 3) selected @endif>CFO</option>
                                </select>
                            </div>
                        </div>                
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanParty">Party*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="containerParty" name="containerParty" type="text" value="{{ old('containerParty') }}" class="form-control" placeholder="vessel size such as 1 x 40 or 1 x 20">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="forwarderName">Forwarder*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="forwarder" name="forwarder" class="form-select" >
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($forwarders as $forwarder)
                                    @if ( $forwarder->id == old('forwarder'))
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
                            <div class="col-md-3 my-auto">
                                <span class="label">Tanggal Transaksi*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="date" id="transactionDate" name="transactionDate" class="form-control text-end" value="{{ old('transactionDate', date('Y-m-d'))}}" >
                                </div>
                            </div>
                        </div>                
                        <div class="row form-group">
                            <div class="col-md-3 my-auto">
                                <span class="label">Tanggal Loading*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="date" id="loadingDate" name="loadingDate" value="{{ old('loadingDate', date('Y-m-d')) }}" class="form-control text-end">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 my-auto">
                                <span class="label">ETD*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="date" id="departureDate" name="departureDate" value="{{ old('departureDate', date('Y-m-d')) }}" class="form-control text-end">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 my-auto">
                                <span class="label">ETA*</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="date" id="arrivalDate" name="arrivalDate" class="form-control text-end" value="{{ old('arrivalDate', date('Y-m-d')) }}" >
                                </div>
                            </div>
                        </div>                
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPacker">Container Number*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="containerNumber" value="{{ old('containerNumber') }}" name="containerNumber" type="text" class="form-control" placeholder="Container number">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPacker">Seal Number*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="containerSeal" value="{{ old('containerSeal') }}" name="containerSeal" type="text" class="form-control" placeholder="Seal number">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPacker">Vessel*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="containerVessel" value="{{ old('containerVessel') }}" name="containerVessel" type="text" class="form-control" placeholder="Vessel name & number">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPayment">Container Type*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="containerType" name="containerType" class="form-select" >
                                    <option value="-1" selected>--Choose One--</option>
                                    <option value="1" @if(old('containerType') == 1) selected @endif>Dry</option>
                                    <option value="2" @if(old('containerType') == 2) selected @endif>Reefer</option>
                                </select>
                            </div>                    
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPayment">Liners*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="liner" name="liner" class="form-select">
                                    <option value="-1" selected>--Choose One--</option>
                                    @foreach ($liners as $liner)
                                    @if ( $liner->id == old('liner'))
                                    <option value="{{ $liner->id }}" selected>{{ $liner->name }}</option>
                                    @else
                                    <option value="{{ $liner->id }}">{{ $liner->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>                    
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label">Bill of Lading*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="bl" value="{{ old('bl') }}" name="bl" type="text" class="form-control" placeholder="Bill of Lading number">
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
                            <div class="col-md-3 text-md-right">
                                <span class="label">Payment to*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="paymentTo" name="paymentTo" class="form-select" >
                                    <option value="-1" selected>--Choose One--</option>
                                    <option value="1" @if(old('paymentTo') == 1) selected @endif>Internal</option>
                                    <option value="2" @if(old('paymentTo') == 2) selected @endif>Supplier</option>
                                </select>
                            </div>                    
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanBank">Bank*</span>
                            </div>
                            <div class="col-md-8">
                                <select class="form-select w-100" id="bank" name="bank">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($banks as $bank)
                                    @if ( $bank->id == old('bank') )
                                    <option value="{{ $bank->id }}" selected>{{ $bank->name }}</option>
                                    @else
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanConsignee">Bank Address*</span>
                            </div>
                            <div class="col-md-8">
                                <textarea id="paymentBankAddress" name="paymentBankAddress" rows="4"  class="form-control" style="min-width: 100%" placeholder="Bank Address">{{ old('paymentBankAddress') }}</textarea>
                            </div>  
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPayment">Accounts*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="account" name="account" class="form-control" value="{{ old('account') }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPayment">Accounts Name*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="paymentAccountName" name="paymentAccountName" class="form-control" value="{{ old('paymentAccountName') }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPayment">Swiftcode*</span>
                            </div>
                            <div class="col-md-8">
                                <input id="swiftcode" name="swiftcode" class="form-control" value="{{ old('swiftcode') }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPayment">Payment Valuta*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="valutaType" name="valutaType" class="form-select" >
                                    <option value="-1" selected>--Choose One--</option>
                                    <option value="1" @if(old('valutaType') == 1) selected @endif>Rupiah</option>
                                    <option value="2" @if(old('valutaType') == 2) selected @endif>US Dollar</option>
                                    <option value="3" @if(old('valutaType') == 3) selected @endif>Renminbi</option>
                                </select>
                            </div>                    
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPayment">Payment Amounts*</span>
                            </div>                    
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span name="spanAm" id="spanAm" class="input-group-text">-</span>
                                    <input id="paymentAmount" name="paymentAmount" type="number" step="0.01" value="{{ old('paymentAmount') }}" class="form-control text-end" placeholder="use commas for decimals">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPayment">Advance Amounts*</span>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span name="spanAd" id="spanAd" class="input-group-text">-</span>
                                    <input id="advanceAmount" name="advanceAmount" type="number" step="0.01" value="{{ old('advanceAmount') }}" class="form-control text-end" placeholder="use commas for decimals">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right">
                                <span class="label" id="spanPayment">Payment status*</span>
                            </div>
                            <div class="col-md-8">
                                <select id="paymentStatus" name="paymentStatus" class="form-select" >
                                    <option value="-1" selected>--Choose One--</option>
                                    <option value="1" @if(old('paymentStatus') == 1) selected @endif>Saldo belum diterima dari buyer</option>
                                    <option value="2" @if(old('paymentStatus') == 2) selected @endif>Saldo di internal</option>
                                    <option value="3" @if(old('paymentStatus') == 2) selected @endif>Saldo partial transfer ke supplier</option>
                                    <option value="4" @if(old('paymentStatus') == 3) selected @endif>Saldo full transfer ke supplier</option>
                                </select>
                            </div>                    
                        </div>
                        <div class="row form-group">
                            <div class="col-md-3 text-md-right"></div>
                            <div class="text-center col-md-8">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <input type="reset" value="Reset" class="btn btn-secondary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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







