<meta name="csrf-token" content="{{ csrf_token() }}" />

@extends('layouts.layout')

@section('content')
<script type="text/javascript"> 
    $(document).ready(function() {
        var i=1;
        $('#add').click(function(){
            i++;  
            $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td class="col-md-11"><textarea id="pinotes[]" name="pinotes[]" rows="4"  class="form-control" style="min-width: 100%">notes</textarea></td><td class="col-md-1"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove"><i class="fa fa-trash"></i></button></td></tr>'); 
        });
        $(document).on('click', '.btn_remove', function(){  
            var button_id = $(this).attr("id");   
            $('#row'+button_id+'').remove();  
        });
        $('#rekening').on('change', function() {
            var rekening = $(this).val();
            if (rekening>0){
                $.ajax({
                    url: '{{ url("getOneRekening") }}'+"/"+rekening,
                    type: "GET",
                    data : {"_token":"{{ csrf_token() }}"},
                    dataType: "json",
                    success:function(data){
                        if(data){
                            $('[name="swiftcode"]').val(data.swiftcode);
                            $('[name="valuta"]').val(data.valuta);
                        }else{
                        }
                    }
                });
            }else{
                swal.fire('warning','Choose Rekening first!','info');
            }
        });
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


<div class="container-fluid">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb primary-color">
                <li class="breadcrumb-item">
                    <a class="white-text" href="{{ url('/home') }}">Home</a>
                </li>
                <li class="breadcrumb-item active">
                    <a class="white-text" href="{{ url('transactionList')}}">Transaksi</a>
                </li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-1"></div>
    <div class="col-10">
        <form id="TransactionForm" action="{{url('transactionStore')}}"  method="POST" name="TransactionForm">
            @csrf
            <div class="d-grid gap-1">
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
                        <span class="label" id="spanLabel">Shipper Address*</span>
                    </div>
                    <div class="col-md-8">
                        <textarea id="shipperAddress" name="shipperAddress" rows="4"  class="form-control" style="min-width: 100%">{{ old('shipperAddress', 'Jl. Raya Rembang - Tuban KM 40, Desa Bancar, Kecamatan Bancar, Kabupaten Tuban, East Java - Indonesia') }}</textarea>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanBank">Bank*</span>
                    </div>
                    <div class="col-md-8">
                        <select class="form-select w-100" id="rekening" name="rekening">
                            <option value="-1">--Choose One--</option>
                            @foreach ($rekenings as $rekening)
                            @if ( $rekening->id == old('rekening') )
                            <option value="{{ $rekening->id }}" selected>{{ $rekening->bank }} - {{ $rekening->rekening }} - {{ $rekening->valuta }}</option>
                            @else
                            <option value="{{ $rekening->id }}">{{ $rekening->bank }} - {{ $rekening->rekening }} - {{ $rekening->valuta }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">Swiftcode</span>
                            <input id="swiftcode" name="swiftcode" class="form-control" value="{{ old('swiftcode') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text col-3">Valuta</span>
                            <input id="valuta" name="valuta" rows="4"  class="form-control" value="{{ old('valuta') }}" readonly>
                        </div>
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
                        <input id="packer" value="{{ old('packer','PT. ANUGRAH LAUT INDONESIA') }}" name="packer" type="text" class="form-control">
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
                    <div class="col-md-3">
                        <select id="containerType" name="containerType" class="form-select" >
                            <option value="-1" selected>--Choose One--</option>
                            <option value="1" @if(old('containerType') == 1) selected @endif>Dry</option>
                            <option value="2" @if(old('containerType') == 2) selected @endif>Reefer</option>
                        </select>
                    </div>                    
                </div>                
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanPayment">Payment Valuta*</span>
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
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanPayment">Payment Amounts*</span>
                    </div>                    
                    <div class="col-md-5">
                        <div class="input-group">
                            <span name="spanAm" id="spanAm" class="input-group-text">-</span>
                            <input id="payment" name="payment" type="number" step="0.01" value="{{ old('payment') }}" class="form-control text-end" placeholder="use commas for decimals">
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanPayment">Advance Amounts*</span>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span name="spanAd" id="spanAd" class="input-group-text">-</span>
                            <input id="advance" name="advance" type="number" step="0.01" value="{{ old('advance') }}" class="form-control text-end" placeholder="use commas for decimals">
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="forwarderName">Forwarder*</span>
                    </div>
                    <div class="col-md-3">
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
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="undername">Undername*</span>
                    </div>
                    <div class="col-md-3">
                        <select id="undername" name="undername" class="form-select" >
                            <option value="-1" selected>--Choose One--</option>
                            <option value="1" @if(old('undername') == 1) selected @endif>Internal</option>
                            <option value="2" @if(old('undername') == 2) selected @endif>Undername</option>
                        </select>
                    </div>                    
                </div>
                <br>
                <br>
                <table width="100%">
                    <tr>
                        <td><hr /></td>
                        <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h3>Proforma invoce additional data</h3></td>
                        <td><hr /></td>
                    </tr>
                </table>
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanPacker">Shipped Date Plan*</span>
                    </div>
                    <div class="col-md-9">
                        <input id="shippedDatePlan" value="{{ old('shippedDatePlan') }}" name="shippedDatePlan" type="text" class="form-control" placeholder="such as latest shipment">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanPacker">Payment Plan*</span>
                    </div>
                    <div class="col-md-9">
                        <input id="paymentPlan" value="{{ old('paymentPlan') }}" name="paymentPlan" type="text" class="form-control" placeholder="payment terms such as LC or direct transfers">
                    </div>
                </div>                
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanPayment">PI Notes</span>
                    </div>
                    <div class="col-md-9">
                        <span class="label">Used to describe terms and condition for the next transaction</span>
                        <button style="width:100%" type="button" name="add" id="add" class="btn btn-primary"><i class="fa fa-plus"></i> Add PI Notes</button>
                        <br>
                        <div class="table-responsive">  
                            <table class="table" id="dynamic_field">
                            </tr>  
                        </table>   
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-3 text-md-right"></div>
                <div class="text-center col-md-8">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </div>
    </form>
</div>
</div>

@if ($errors->any())
@if (!empty(old('pinotes')))
<script type="text/javascript">
    var i=1;
    var $arr = @json(old('pinotes'));
    for ($note of $arr){
        i++;  
        $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td class="col-md-11"><textarea id="pinotes[]" name="pinotes[]" rows="4"  class="form-control" style="min-width: 100%">'+$note+'</textarea></td><td class="col-md-1"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove"><i class="fa fa-trash"></i></button></td></tr>');  
    }
</script>
@endif
@endif

@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('header')
@include('partial.header')
@endsection







