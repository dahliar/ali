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
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function cetak(){
        $bulanTahun = document.getElementById("bulanTahun").value;
        $valBulanTahun = document.getElementById("valBulanTahun").value;
        $opsi = document.getElementById("opsi").value;
        $company = document.getElementById("company").value;
        
        if ($bulanTahun=="") {
            Swal.fire(
                'Pilihan kosong!',
                "Pilih data dulu",
                'warning'
                );
        } else {
            if ($valBulanTahun!=$bulanTahun){
                Swal.fire(
                    'Terdapat perubahan opsi pilihan.',
                    "Cari data dulu!",
                    'info'
                    );

            }
            else {
                openWindowWithPost('{{ url("cetakRekapPembelianPerBulan") }}', {
                    '_token': "{{ csrf_token() }}" ,
                    bulanTahun: $bulanTahun,
                    opsi: $opsi,
                    company: $company
                });
            }
        }
    }


    function openWindowWithPost(url, data) {
        var form = document.createElement("form");
        form.target = "_blank";
        form.method = "POST";
        form.action = url;
        form.style.display = "none";

        for (var key in data) {
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = key;
            input.value = data[key];
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
</script>
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
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Informasi rekap pembelian per bulan</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-header">
            <form action="{{url('getRekapitulasiPembelianPerBulan')}}" method="post">
                {{ csrf_field() }}
                <div class="row form-group">
                    <div class="col-md-2">
                        @if(!empty($bulanTahun))
                        <input type="month" name="bulanTahun" id="bulanTahun" class="form-control" value={{$bulanTahun}} max="{{date('Y-m')}}">
                        <input type="hidden" name="valBulanTahun" id="valBulanTahun" value={{$bulanTahun}}>
                        @else
                        <input type="month" name="bulanTahun" id="bulanTahun" class="form-control" max="{{date('Y-m')}}">
                        <input type="hidden" name="valBulanTahun" id="valBulanTahun" value="0000-00">
                        @endif
                    </div>
                    <div class="col-md-2">
                        <select id="opsi" name="opsi" class="form-select" >
                            @if(empty($opsi))
                            <option value="1" selected>Detil</option>
                            <option value="2">Per Supplier</option>
                            @else
                            <option value="1" @if($opsi == 1) selected @endif>Detil</option>
                            <option value="2" @if($opsi == 2) selected @endif>Per Supplier</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if(!empty($companyChoosen))
                        <select id="company" name="company" class="form-select js-example-basic-single" >
                            <option value="0" selected>Semua Perusahaan</option>
                            @foreach ($companies as $company)
                            @if ( $company->id == $companyChoosen)
                            <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
                            @else
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endif
                            @endforeach
                        </select>
                        @else
                        <select id="company" name="company" class="form-select js-example-basic-single" >
                            <option value="0" selected>Semua Perusahaan</option>
                            @foreach ($companies as $company)
                            @if ( $company->id == old('company'))
                            <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
                            @else
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endif
                            @endforeach
                        </select>                        
                        @endif
                    </div>
                    <div class="col-md-1">
                        <button type="submit" id="hitButton" class="form-control btn-primary">Cari</button>
                    </div>
                    @if(!empty($payroll))
                    @if(count($payroll)>1)
                    <div class="col-md-1">
                        <button type="button" id="hitButton" class="form-control btn-primary" onclick="cetak()">Print</button>
                    </div>               
                    @endif                 
                    @endif
                </div>
            </form>
        </div>
        <div class="card card-body">
            <div class="row form-group">
                @if(!empty($payroll))
                <input type="hidden" name="payroll" id="payroll" value="{{$payroll}}">

                <table style="width: 100%;" class="center table table-striped table-hover table-bordered">
                    <thead style="text-align: center;">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 15%;">Supplier</th>
                            <th style="width: 10%;">NPWP</th>
                            @if($opsi==1)
                            <th style="width: 14%;">Nomor</th>
                            <th style="width: 7%;">Tanggal</th>
                            @endif
                            <th style="width: 12%;">Jumlah</th>
                            <th style="width: 5%;">Pajak</th>
                            <th style="width: 10%;">Potongan</th>
                            <th style="width: 5%;">Include</th>
                            <th style="width: 15%;">Bayar</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:12px">
                        @php 
                        $no=1;
                        $totalAmount=0;
                        $totalAfterTax=0;
                        @endphp
                        @foreach($payroll as $paymonth)

                        @if ($paymonth->taxIncluded=="Ya")
                        @php
                        $amountAfterTax = $paymonth->jumlah - $paymonth->pajak;
                        @endphp
                        @else
                        @php
                        $amountAfterTax = $paymonth->jumlah;
                        @endphp
                        @endif
                        @php
                        $totalAmount+=$paymonth->jumlah;
                        $totalAfterTax+=$amountAfterTax;
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{$no}}</td>
                            <td style="text-align: left;">{{$paymonth->name}}</td>
                            <td style="text-align: right;">{{$paymonth->npwp}}</td>
                            @if($opsi==1)
                            <td style="text-align: right;">{{$paymonth->nomor}}</td>
                            <td style="text-align: center;">{{$paymonth->tanggalFinish}}</td>
                            @endif
                            <td style="text-align: right;">Rp. {{number_format($paymonth->jumlah, 2, ',', '.')}}</td>
                            <td style="text-align: right;">{{$paymonth->persen*10}}%</td>
                            <td style="text-align: right;">Rp. {{number_format($paymonth->pajak, 2, ',', '.')}}</td>
                            <td style="text-align: center;">{{$paymonth->taxIncluded}}</td>
                            <td style="text-align: right;">Rp. 
                                {{number_format($amountAfterTax, 2, ',', '.')}}
                            </td>
                            @php $no+=1;    @endphp                                    
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="font-size:12px">
                            @if(!empty($opsi))
                            @if($opsi==1)
                            <td colspan="5" style="text-align: center;">
                            </td>
                            @else
                            <td colspan="3" style="text-align: center;">
                            </td>
                            @endif
                            @endif
                            <td style="text-align: right;">
                                Rp. {{number_format($totalAmount, 2, ',', '.')}}
                            </td>
                            <td colspan="3" style="text-align: center;">
                            </td>
                            <td style="text-align: right;">
                                Rp. {{number_format($totalAfterTax, 2, ',', '.')}}
                            </td>
                        </tr>
                    </tfoot>
                </table>
                @endif
            </div>
        </div>
    </div>
</body>
@endsection