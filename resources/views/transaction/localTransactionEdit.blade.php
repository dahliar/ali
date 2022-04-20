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
    var i=1;

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
<body onload="disableForm()">
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
                                <a class="white-text" href="{{ url('transactionList')}}">Transaksi</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="card card-body">
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <form id="TransactionForm" action="{{url('localTransactionUpdate')}}"  method="POST" name="TransactionForm">
                        @csrf
                        <div class="d-grid gap-1">
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanLabel">Nomor Transaksi*</span>
                                </div>
                                <div class="col-md-9">
                                    <input id="transactionNum" name="transactionNum"  class="form-control"  value="{{ $transaction->transactionNum}}" readonly>
                                    <input id="transactionId" name="transactionId"  class="form-control"  value="{{$transaction->id}}" type="hidden" readonly>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="companyName">Pembeli*</span>
                                </div>
                                <div class="col-md-9">
                                    <input id="company" value="{{ $transaction->companyId}}" name="company" type="hidden" class="form-control" readonly>
                                    <input id="companyName" value="{{ $companyName}}" name="companyName" type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanConsignee">Detil Pembeli*</span>
                                </div>
                                <div class="col-md-9">
                                    <textarea id="companydetail" name="companydetail" rows="4"  class="form-control" style="min-width: 100%">{{ $transaction->companydetail}}</textarea>
                                </div>  
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanLoading">Alamat loading*</span>
                                </div>
                                <div class="col-md-9">
                                    <input id="loadingPort" value="{{ $transaction->loadingport}}" name="loadingPort" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanDestination">Alamat pengiriman*</span>
                                </div>
                                <div class="col-md-9">
                                    <input id="destinationPort" name="destinationPort" type="text" value="{{ $transaction->destinationport}}" class="form-control">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanParty">Alat Pengiriman*</span>
                                </div>
                                <div class="col-md-9">
                                    <input id="containerParty" name="containerParty" type="text" value="{{ $transaction->containerParty}}" class="form-control">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 my-auto">
                                    <span class="label">Tanggal Transaksi*</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="date" id="transactionDate" name="transactionDate" class="form-control text-end" value="{{ date('Y-m-d', strtotime($transaction->transactionDate)) }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 my-auto">
                                    <span class="label">Tanggal Loading*</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="date" id="loadingDate" name="loadingDate" class="form-control text-end" value="{{ date('Y-m-d', strtotime($transaction->loadingDate)) }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanBank">Bank*</span>
                                </div>
                                <div class="col-md-6">
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
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text col-4">Valuta</span>
                                        <input id="valuta" name="valuta" rows="4"  class="form-control" value="{{ $transaction->valuta}}" readonly>
                                    </div>
                                </div>
                            </div>                       
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanPayment">Valuta bayar*</span>
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
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanPayment">Jumlah total*</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span name="spanAm" id="spanAm" class="input-group-text">-</span>

                                        <input id="payment" name="payment" type="number" step="0.01" value="{{ $transaction->payment }}" class="form-control text-end" placeholder="use commas">
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label" id="spanPayment">Uang Muka*</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span name="spanAd" id="spanAd" class="input-group-text">-</span>

                                        <input id="advance" name="advance" type="number" step="0.01" value="{{ $transaction->advance }}" class="form-control text-end" placeholder="use commas">
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-right">
                                    <span class="label">Status</span>
                                </div>
                                <div class="col-md-3">
                                    <input id="currentStatus" name="currentStatus" type="hidden" value="{{ $transaction->status }}">
                                    @if ($transaction->status == 1)
                                    <select id="status" name="status" class="form-select" >
                                        <option value="-1">--Choose One--</option>
                                        <option value="1" @if($transaction->status == 1) selected @endif>Transaksi baru</option>
                                        <option value="4" @if($transaction->status == 4) selected @endif>Dalam perjalanan</option>
                                        <option value="2" @if($transaction->status == 2) selected @endif>Selesai</option>
                                        <option value="3" @if($transaction->status == 3) selected @endif>Batal</option>
                                    </select>
                                    @endif
                                    @if (($transaction->status == 2) or ($transaction->status == 3))
                                    <select id="status" name="status" class="form-select" disabled>
                                        <option value="2" @if($transaction->status == 2) selected @endif>Selesai</option>
                                        <option value="3" @if($transaction->status == 3) selected @endif>Batal</option>
                                    </select>
                                    @endif
                                    @if($transaction->status == 4)
                                    <select id="status" name="status" class="form-select">
                                        <option value="4" @if($transaction->status == 4) selected @endif>Dalam Perjalanan</option>
                                        <option value="2" @if($transaction->status == 2) selected @endif>Selesai</option>
                                        <option value="3" @if($transaction->status == 3) selected @endif>Batal</option>
                                    </select>
                                    @endif    
                                </div>
                            </div>        

                            @if(($transaction->status == 1) or ($transaction->status == 4))
                            <div class="row form-group">
                                <div class="col-md-3 text-end">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <input type="reset" value="Reset" class="btn btn-secondary">
                                </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection