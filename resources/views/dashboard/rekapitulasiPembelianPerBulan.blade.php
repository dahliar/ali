@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function cetak(){
        $bulan = document.getElementById("bulan").value;
        $tahun = document.getElementById("tahun").value;
        $opsi = document.getElementById("opsi").value;

        $valBulan = document.getElementById("valBulan").value;
        $valTahun = document.getElementById("valTahun").value;
        
        if (($bulan=="-1") || ($tahun=="-1")) {
            Swal.fire(
                'Pilihan kosong!',
                "Pilih data dulu",
                'warning'
                );
        } else {
            if (($bulan!=$valBulan) || ($tahun!=$valTahun)){
                Swal.fire(
                    'Terdapat perubahan opsi pilihan.',
                    "Cari data dulu!",
                    'info'
                    );

            }
            else {
                openWindowWithPost('{{ url("cetakRekapPembelianPerBulan") }}', {
                    '_token': "{{ csrf_token() }}" ,
                    bulan: $bulan,
                    tahun: $tahun,
                    opsi: $opsi
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
                        @if(empty($tahun))
                        <input type="hidden" name="valTahun" id="valTahun" value="-1">
                        <select id="tahun" name="tahun" class="form-select" >
                            <option value="-1" selected>--Tahun--</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                        </select>
                        @else
                        <input type="hidden" name="valTahun" id="valTahun" value="{{$tahun}}">

                        <select id="tahun" name="tahun" class="form-select" >
                            <option value="-1" selected>-- Tahun--</option>
                            <option value="2022" @if($tahun == 2022) selected @endif>2022</option>
                            <option value="2023" @if($tahun == 2023) selected @endif>2023</option>
                        </select>
                        @endif
                    </div>
                    <div class="col-md-2">
                        @if(empty($bulan))
                        <input type="hidden" name="valBulan" id="valBulan" value="-1">

                        <select id="bulan" name="bulan" class="form-select" >
                            <option value="-1" selected>--Pilih Bulan--</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        @else
                        <input type="hidden" name="valBulan" id="valBulan" value="{{$bulan}}">
                        <select id="bulan" name="bulan" class="form-select" >
                            <option value="-1" selected>--Pilih Bulan--</option>
                            <option value="1" @if($bulan == 1) selected @endif>Januari</option>
                            <option value="2" @if($bulan == 2) selected @endif>Februari</option>
                            <option value="3" @if($bulan == 3) selected @endif>Maret</option>
                            <option value="4" @if($bulan == 4) selected @endif>April</option>
                            <option value="5" @if($bulan == 5) selected @endif>Mei</option>
                            <option value="6" @if($bulan == 6) selected @endif>Juni</option>
                            <option value="7" @if($bulan == 7) selected @endif>Juli</option>
                            <option value="8" @if($bulan == 8) selected @endif>Agustus</option>
                            <option value="9" @if($bulan == 9) selected @endif>September</option>
                            <option value="10" @if($bulan == 10) selected @endif>Oktober</option>
                            <option value="11" @if($bulan == 11) selected @endif>November</option>
                            <option value="12" @if($bulan == 12) selected @endif>Desember</option>
                        </select>
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
                            <td style="text-align: center;">{{$paymonth->tanggal}}</td>
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