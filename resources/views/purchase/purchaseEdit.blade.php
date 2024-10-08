<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript"> 
    function myFunction(){
        Swal.fire({
            title: 'Edit data pembelian?',
            text: "Simpan transaksi pembelian",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Transaksi disimpan',
                    text: "Simpan transaksi pembelian",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("purchaseForm").submit();
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
                    <form id="purchaseForm" action="{{url('purchaseUpdate')}}"  method="POST" name="purchaseForm" enctype="multipart/form-data">
                        @csrf
                        <div class="d-grid gap-1">
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label" id="companyName">No Pembelian</span>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" id="purchaseNum" name="purchaseNum" value="{{ $purchase->purchasingNum }}" class="form-control" disabled>
                                    <input type="hidden" id="purchaseId" name="purchaseId" value="{{ old('purchaseId' , $purchase->id) }}" class="form-control">
                                </div>
                            </div>      
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label" id="companyName">Supplier</span>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" id="companyName" name="companyName" value="{{ old('companyName' , $companyName->name) }}" class="form-control" readonly>
                                </div>
                            </div>      
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label">Tanggal Penerimaan Barang*</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="date" id="arrivalDate" name="arrivalDate" value="{{ old('arrivalDate', $purchase->arrivaldate) }}" class="form-control text-end" @if($purchase->status != 1) readonly @endif>
                                    </div>
                                </div>
                            </div>               
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label">Tanggal Transaksi*</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="date" id="purchaseDate" name="purchaseDate" class="form-control text-end" value="{{ old('purchaseDate', $purchase->purchaseDate) }}"  @if($purchase->status != 1) readonly @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label">Tanggal Batas Bayar*</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="date" id="dueDate" name="dueDate" class="form-control text-end" value="{{ old('dueDate', $purchase->dueDate) }}"  @if($purchase->status != 1) readonly @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label" id="spanPayment">Mata Uang Transaksi*</span>
                                </div>
                                <div class="col-md-3">
                                    <select id="valutaType" name="valutaType" class="form-select" disabled >
                                        <option value="-1" selected>--Choose One--</option>
                                        @foreach ($currencies as $currency)
                                        @if ( $currency->id == old('valutaType', $purchase->valutaType))
                                        <option value="{{ $currency->id }}" selected>{{ $currency->short }} - {{ $currency->name}}</option>
                                        @else
                                        <option value="{{ $currency->id }}">{{ $currency->short }} - {{ $currency->name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>                    
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
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
                                <div class="col-md-3 text-md-end">
                                    <span class="label">Down Payment</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input id="downPayment" name="downPayment" type="text" class="form-control text-end" value="@php echo number_format($purchase->downPayment, 2, ',', '.') @endphp" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label">Jumlah</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input id="paymentAmount" name="paymentAmount" type="text" class="form-control text-end" value="@php echo number_format($purchase->paymentAmount, 2, ',', '.') @endphp" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label">Pajak</span>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input id="tax" name="tax" type="text" class="form-control text-end" value="@php echo number_format($purchase->tax, 2, ',', '.') @endphp" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label" id="spanPayment">Potongan Pajak*</span>
                                </div>
                                <div class="col-md-3">
                                    <select id="taxIncluded" name="taxIncluded" class="form-select" disabled>
                                        <option value="0" @if($purchase->taxIncluded == 0) selected @endif>Tidak</option>
                                        <option value="1" @if($purchase->taxIncluded == 1) selected @endif>Ya</option>
                                    </select>
                                </div>                    
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label">File Invoice</span>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <a href="{{ url('getFileDownload').'/'.$purchase->realInvoiceFilePath }}" target="_blank">{{$purchase->realInvoiceFilePath}}</a>
                                    </div>
                                </div>
                            </div>        
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label">Upload File Invoice Baru</span>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input class="form-control" type="file" id="imageurlBaru" name="imageurlBaru" accept="image/*">
                                    </div>
                                    <span style="font-size:9px" class="label">File dalam bentuk image dengan ukuran maksimal 1MB</span>
                                </div>
                            </div>  
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label" id="spanPayment">Status Pembelian*</span>
                                </div>
                                <div class="col-md-3">

                                    @if (Auth::user()->accessLevel>1)
                                    <select id="progressStatus" name="progressStatus" class="form-select" @if($purchase->status != 1) disabled @endif>
                                        <option value="1" @if($purchase->status == 1) selected @endif>On Progress</option>
                                        <option value="2" @if($purchase->status == 2) selected @endif>Selesai</option>
                                        <option value="3" @if($purchase->status == 3) selected @endif>Batal</option>
                                    </select>
                                    @else
                                    <select id="progressStatus" name="progressStatus" class="form-select">
                                        <option value="1" @if($purchase->status == 1) selected @endif>On Progress</option>
                                        <option value="2" @if($purchase->status == 2) selected @endif>Selesai</option>
                                        <option value="3" @if($purchase->status == 3) selected @endif>Batal</option>
                                    </select>
                                    @endif
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-md-end">
                                    <span class="label">Catatan Transaksi</span>
                                </div>
                                <div class="col-md-8">
                                    <textarea id="paymentTerms" name="paymentTerms" rows="4"  class="form-control" style="min-width: 100%" placeholder="Informasi umum tentang pembelian"  @if($purchase->status != 1) readonly @endif>{{ old('paymentTerms', $purchase->paymentTerms) }}</textarea>
                                </div>  
                            </div>
                        </div>

                        @if(Auth::user()->accessLevel<=1)
                        <div class="row form-group">
                            <div class="text-center col-md-8">
                                <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                                <input type="reset" value="Reset" class="btn btn-secondary">
                            </div>
                        </div>
                        @else
                        @if ($purchase->status == 1)
                        <div class="row form-group">
                            <div class="text-center col-md-8">
                                <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Simpan</button>
                                <input type="reset" value="Reset" class="btn btn-secondary">
                            </div>
                        </div>
                        @endif
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







