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
    var i=1;
    function myFunction(){
        Swal.fire({
            title: 'Edit data transaksi?',
            text: "Simpan transaksi penjualan",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("transactionForm").submit();
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Update transaksi dibatalkan",
                    'info'
                    );
            }
        })
    };
    function disableForm() { 
        @if(($transaction->status == 2) or ($transaction->status == 3))
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
            <form id="transactionForm" action="{{url('transactionUpdate')}}"  method="POST" name="transactionForm" enctype="multipart/form-data">
                @csrf
                <div class="d-grid gap-2">
                    <input id="isEdit" name="isEdit"  class="form-control"  value="isEdit" type="hidden" readonly>
                    <input id="transactionId" name="transactionId"  class="form-control"  value="{{$transaction->id}}" type="hidden" readonly>

                    <div class="row form-inline">
                        <div class="col-3 text-end">
                            <span class="label" id="spanLabel">Transaction Number*</span>
                        </div>
                        <div class="col-8">
                            <input id="transactionNum" name="transactionNum"  class="form-control"  value="{{ $transaction->transactionNum}}" readonly>
                        </div>
                    </div>                   
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanLabel">PI Number*</span>
                        </div>
                        <div class="col-8">
                            <input id="pinum" name="pinum"  class="form-control"  value="{{ $transaction->pinum}}" readonly>
                            <input id="transactionId" name="transactionId"  class="form-control"  value="{{$transaction->id}}" type="hidden" readonly>
                        </div>
                    </div>      
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label" id="spanLabel">Nomor PEB</span>
                        </div>
                        <div class="col-md-3">
                            <input id="pebNum" name="pebNum" class="form-control"
                            value="{{ old('pebNum', $transaction->pebNum) }}" placeholder="Nomor PEB">
                            <span style="font-size:9px" class="label">Wajib diisi ketika dilakukan penyelesaian transaksi</span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">Tanggal PEB*</span>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="pebDate" name="pebDate" class="form-control text-end" value="{{ old('pebDate', $transaction->pebDate) }}">
                            <span style="font-size:9px" class="label">Wajib diisi ketika dilakukan penyelesaian transaksi</span>
                        </div>
                    </div>     
                    @if($transaction->pebFile)
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
                            <span class="label">File PEB</span>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <a href="{{ url('getFileDownload').'/'.$transaction->pebFile }}" target="_blank">{{$transaction->pebFile}}</a>
                            </div>
                        </div>
                    </div>       
                    @endif                    
                    <div class="row form-group">
                        <div class="col-md-3 text-md-end">
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
                        <div class="col-3 text-end">
                            <span class="label" id="spanLabel">Shipper*</span>
                        </div>
                        <div class="col-8">
                            <input id="shipper" name="shipper"  class="form-control" value="{{ $transaction->shipper}}">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanLabel">Shipper Address*</span>
                        </div>
                        <div class="col-8">
                            <textarea id="shipperAddress" name="shipperAddress" rows="4"  class="form-control" style="min-width: 100%">{{ $transaction->shipperAddress}}</textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanBank">Bank*</span>
                        </div>
                        <div class="col-8">
                            <select class="form-select w-100" id="rekening" name="rekening">
                                <option value="-1">--Choose One--</option>
                                @foreach ($rekenings as $rekening)
                                @if ( $rekening->id == $transaction->rekeningid)
                                <option value="{{ $rekening->id }}" selected>{{ $rekening->bank }} - {{ $rekening->rekening }} - {{ $rekening->valuta }} </option>
                                @else
                                <option value="{{ $rekening->id }}">{{ $rekening->bank }} - {{ $rekening->rekening }} - {{ $rekening->valuta }}</option>                    
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanBank">Registration Number*</span>
                        </div>
                        <div class="col-8">
                            <select class="form-select w-100" id="countryId" name="countryId">
                                <option value="-1">--Choose One--</option>
                                @foreach ($countryRegister as $register)
                                @if ( $register->id == $transaction->countryId )
                                <option value="{{ $register->id }}" selected>{{ $register->name }} - {{ $register->registration }}</option>
                                @else
                                <option value="{{ $register->id }}">{{ $register->name }} - {{ $register->registration }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Swiftcode</span>
                                <input id="swiftcode" name="swiftcode" class="form-control" value="{{ $transaction->swiftcode}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text col-3 text-end">Valuta</span>
                                <input id="valuta" name="valuta" rows="4"  class="form-control" value="{{ $transaction->valuta}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label">Company*</span>
                        </div>
                        <div class="col-8">
                            <select id="company" name="company" class="form-select">
                                <option value="-1" selected>--Choose One--</option>
                                @foreach ($companies as $company)
                                @if ( $company->id == $transaction->companyId)
                                <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
                                @else
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanConsignee">Consignee Details*</span>
                        </div>
                        <div class="col-8">
                            <textarea id="companydetail" name="companydetail" rows="4"  class="form-control" style="min-width: 100%">{{ $transaction->companydetail}}</textarea>
                        </div>  
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPacker">Packer*</span>
                        </div>
                        <div class="col-8">
                            <input id="packer" value="{{ $transaction->packer}}" name="packer" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanLoading">Port of Loading*</span>
                        </div>
                        <div class="col-8">
                            <input id="loadingPort" value="{{ $transaction->loadingport}}" name="loadingPort" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanDestination">Port of Destination*</span>
                        </div>
                        <div class="col-8">
                            <input id="destinationPort" name="destinationPort" type="text" value="{{ $transaction->destinationport}}" class="form-control">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label">Order Type*</span>
                        </div>
                        <div class="col-8">
                            <select id="orderType" name="orderType" class="form-select" >
                                <option value="-1" selected>--Choose One--</option>
                                <option value="1" @if($transaction->orderType == 1) selected @endif>FOB</option>
                                <option value="2" @if($transaction->orderType == 2) selected @endif>CNF</option>
                                <option value="3" @if($transaction->orderType == 3) selected @endif>CFO</option>
                            </select>
                        </div>
                    </div>  

                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanParty">Party*</span>
                        </div>
                        <div class="col-8">
                            <input id="containerParty" name="containerParty" type="text" value="{{ $transaction->containerParty}}" class="form-control">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label">Created at</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="creationDate" name="creationDate" class="form-control text-end" value="{{ date('Y-m-d', strtotime($transaction->creationDate)) }}" readonly >
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label">Tanggal Transaksi*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="transactionDate" name="transactionDate" class="form-control text-end" value="{{ date('Y-m-d', strtotime($transaction->transactionDate)) }}" >
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label">Tanggal Loading*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="loadingDate" name="loadingDate" class="form-control text-end" value="{{ date('Y-m-d', strtotime($transaction->loadingDate)) }}" >
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label">ETD*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="departureDate" name="departureDate" value="{{ date('Y-m-d', strtotime($transaction->departureDate)) }}" class="form-control text-end">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end my-auto">
                            <span class="label">ETA*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" id="arrivalDate" name="arrivalDate" class="form-control text-end" value="{{ date('Y-m-d', strtotime($transaction->arrivaldate)) }}" >
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPacker">Container Number*</span>
                        </div>
                        <div class="col-8">
                            <input id="containerNumber" value="{{ $transaction->containerNumber }}" name="containerNumber" type="text" class="form-control" placeholder="Container number">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPacker">Seal Number*</span>
                        </div>
                        <div class="col-8">
                            <input id="containerSeal" value="{{ $transaction->containerSeal }}" name="containerSeal" type="text" class="form-control" placeholder="Seal number">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPacker">Vessel*</span>
                        </div>
                        <div class="col-8">
                            <input id="containerVessel" value="{{ $transaction->containerVessel }}" name="containerVessel" type="text" class="form-control" placeholder="Vessel name & number">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPayment">Container Type*</span>
                        </div>
                        <div class="col-md-3">
                            <select id="containerType" name="containerType" class="form-select" >
                                <option value="-1" selected>--Choose One--</option>
                                <option value="1" @if($transaction->containerType) == 1) selected @endif>Dry</option>
                                <option value="2" @if($transaction->containerType == 2) selected @endif>Reefer</option>
                            </select>
                        </div>                    
                    </div> 
                    <div class="row form-group">
                        <div class="col-md-3 text-end">
                            <span class="label" id="spanPayment">Liners*</span>
                        </div>
                        <div class="col-md-8">
                            <select id="liner" name="liner" class="form-select">
                                <option value="-1" selected>--Choose One--</option>
                                @foreach ($liners as $liner)
                                @if ( $liner->id == old('liner', $transaction->linerId))
                                <option value="{{ $liner->id }}" selected>{{ $liner->name }}</option>
                                @else
                                <option value="{{ $liner->id }}">{{ $liner->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>                    
                    </div>
                    <div class="row form-group">
                        <div class="col-md-3 text-end">
                            <span class="label">Bill of Lading*</span>
                        </div>
                        <div class="col-md-8">
                            <input id="bl" value="{{ old('bl', $transaction->bl) }}" name="bl" type="text" class="form-control" placeholder="Bill of Lading number">
                        </div>                    
                    </div>                           
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPayment">Payment Valuta*</span>
                        </div>
                        <div class="col-md-3">
                            <select id="valutaType" name="valutaType" class="form-select" >
                                <option value="-1" selected>--Choose One--</option>
                                <option value="1" @if($transaction->valutaType == 1) selected @endif>Rupiah</option>
                                <option value="2" @if($transaction->valutaType == 2) selected @endif>US Dollar</option>
                                <option value="3" @if($transaction->valutaType == 3) selected @endif>Renminbi</option>
                            </select>
                        </div>                    
                    </div>
                    @php
                    $valuta="";
                    switch($transaction->valutaType){
                        case("1") : $valuta = "Rupiah";     break;
                        case("2") : $valuta = "US Dollar";  break;
                        case("3") : $valuta = "Rmb";        break;
                    }
                    @endphp

                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPayment">Payment Amount*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span name="spanAm" id="spanAm" class="input-group-text">{{$valuta}}</span>

                                <input id="payment" name="payment" type="number" step="0.01" value="{{ $transaction->payment }}" class="form-control text-end" placeholder="use commas">
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPayment">Advance*</span>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span name="spanAd" id="spanAd" class="input-group-text">{{$valuta}}</span>

                                <input id="advance" name="advance" type="number" step="0.01" value="{{ $transaction->advance }}" class="form-control text-end" placeholder="use commas">
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="forwarderName">Forwarder*</span>
                        </div>
                        <div class="col-md-3">
                            <select id="forwarder" name="forwarder" class="form-select" >
                                <option value="-1">--Choose One--</option>
                                @foreach ($forwarders as $forwarder)
                                @if ( $forwarder->id == old('forwarder', $transaction->forwarderid))
                                <option value="{{ $forwarder->id }}" selected>{{ $forwarder->name }}</option>
                                @else
                                <option value="{{ $forwarder->id }}">{{ $forwarder->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label">Status</span>
                        </div>
                        <div class="col-md-3">
                            <input id="currentStatus" name="currentStatus" type="hidden" value="{{ $transaction->status }}">
                            @if ($transaction->status == 1)
                            <select id="status" name="status" class="form-select" >
                                <option value="1" @if($transaction->status == 1) selected @endif>Penawaran</option>
                                <option value="4" @if($transaction->status == 4) selected @endif>Sailing</option>
                                <option value="2" @if($transaction->status == 2) selected @endif>Selesai Pembayaran</option>
                                <option value="3" @if($transaction->status == 3) selected @endif>Batal</option>
                            </select>
                            @endif
                            @if (($transaction->status == 2) or ($transaction->status == 3))
                            <select id="status" name="status" class="form-select" disabled>
                                <option value="2" @if($transaction->status == 2) selected @endif>Selesai Pembayaran</option>
                                <option value="3" @if($transaction->status == 3) selected @endif>Batal</option>
                            </select>
                            @endif
                            @if($transaction->status == 4)
                            <select id="status" name="status" class="form-select">
                                <option value="4" @if($transaction->status == 4) selected @endif>Sailing</option>
                                <option value="2" @if($transaction->status == 2) selected @endif>Selesai Pembayaran</option>
                                <option value="3" @if($transaction->status == 3) selected @endif>Batal</option>
                            </select>
                            @endif
                        </div>
                    </div> 

                    <table width="100%">
                        <tr>
                            <td><hr /></td>
                            <td style="width:1px; padding: 0 10px; white-space: nowrap;"><h3>Proforma invoce additional data</h3></td>
                            <td><hr /></td>
                        </tr>
                    </table>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPacker">Shipped Date Plan*</span>
                        </div>
                        <div class="col-8">
                            <input id="shippedDatePlan" value="{{ $transaction->shippedDatePlan }}" name="shippedDatePlan" type="text" class="form-control" placeholder="such as latest shipment">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPacker">Payment Plan*</span>
                        </div>
                        <div class="col-8">
                            <input id="paymentPlan" value="{{ $transaction->paymentPlan }}" name="paymentPlan" type="text" class="form-control" placeholder="payment terms such as LC or direct transfers">
                        </div>
                    </div>                
                    <div class="row form-group">
                        <div class="col-3 text-end">
                            <span class="label" id="spanPayment">PI Notes</span>
                        </div>
                        <div class="col-8">
                            <span class="label">Used to describe terms and condition for the next transaction</span>
                            <button style="width:100%" type="button" name="add" id="add" class="btn btn-primary"><i class="fa fa-plus"></i> Add PI Notes</button>
                            <br>
                            <div class="table-responsive">  
                                <table class="table" id="dynamic_field">
                                    <td class="col-md-12">
                                    </td>  
                                </table>   
                            </div>
                        </div>
                    </div>                        
                    @if(($transaction->status == 1) or ($transaction->status == 4))
                    <div class="row form-group">
                        <div class="col-3 text-end">
                        </div>
                        <div class="col-8 text-center">
                            <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</body>

@if (!empty($pinotes))
<script type="text/javascript"> 
    $("#dynamic_field tr").remove(); 
</script>
@foreach ($pinotes as $note)
<script type="text/javascript"> 
    i++;  
    $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td class="col-md-11"><textarea id="pinotes[]" name="pinotes[]" rows="4"  class="form-control" style="min-width: 100%">'+{!! json_encode($note->note) !!}+'</textarea></td><td class="col-md-1"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove"><i class="fa fa-trash"></i></button></td></tr>');  
</script>
@endforeach
@endif


@if ($errors->any())
@if (!empty(old('pinotes')))
<script type="text/javascript">
    $("#dynamic_field tr").remove(); 
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