<meta name="csrf-token" content="{{ csrf_token() }}" />

@extends('layouts.layout')

@section('content')
@if ((Auth::user()->isMarketing() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)

<script type="text/javascript"> 
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
                            $('[name="companyName"]').val(data.name);
                        }else{
                        }
                    }
                });
            }else{
                $('[name="companydetail"]').val("");
                $('[name="companyName"]').val("");
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
        <form id="TransactionForm" action="{{url('localTransactionStore')}}"  method="POST" name="TransactionForm">
            @csrf
            <div class="d-grid gap-1">
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="companyName">Pembeli*</span>
                    </div>
                    <div class="col-md-4">
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
                        <input type="hidden" id="companyName" name="companyName" class="form-control" value="{{ old('companyName') }}" readonly>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanConsignee">Detil Pembeli*</span>
                    </div>
                    <div class="col-md-8">
                        <textarea id="companydetail" name="companydetail" rows="4"  class="form-control" style="min-width: 100%" placeholder="Informasi tentang pembeli seperti alamat, informasi pajak, no kontak dan lain-lain">{{ old('companydetail') }}</textarea>
                    </div>  
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanLoading">Alamat Loading*</span>
                    </div>
                    <div class="col-md-8">
                        <input id="loadingPort" value="{{ old('loadingPort','Surabaya Port, East Java Indonesia') }}" name="loadingPort" type="text" class="form-control">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanDestination">Alamat Pengiriman*</span>
                    </div>
                    <div class="col-md-8">
                        <input id="destinationPort" name="destinationPort" value="{{ old('destinationPort') }}" type="text" class="form-control" placeholder="Destination port & country">
                    </div>
                </div>                
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanParty">Alat pengiriman*</span>
                    </div>
                    <div class="col-md-8">
                        <input id="containerParty" name="containerParty" type="text" value="{{ old('containerParty') }}" class="form-control" placeholder="Alat kirim seperti ukuran thermo atau kontainer">
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
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanBank">Bank*</span>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text col-3">Valuta</span>
                            <input id="valuta" name="valuta" class="form-control" value="{{ old('valuta') }}" readonly>
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
                            <option value="1" @if(old('valutaType') == 1) selected @endif>Rupiah</option>
                            <option value="2" @if(old('valutaType') == 2) selected @endif>US Dollar</option>
                            <option value="3" @if(old('valutaType') == 3) selected @endif>Renminbi</option>
                        </select>
                    </div>                    
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-md-right">
                        <span class="label" id="spanPayment">Jumlah Total*</span>
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
                        <span class="label" id="spanPayment">Uang muka*</span>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span name="spanAd" id="spanAd" class="input-group-text">-</span>
                            <input id="advance" name="advance" type="number" step="0.01" value="{{ old('advance') }}" class="form-control text-end" placeholder="use commas for decimals">
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3 text-md-right"></div>
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <input type="reset" value="Reset" class="btn btn-secondary">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@else
@include('partial.noAccess')
@endif
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('header')
@include('partial.header')
@endsection






