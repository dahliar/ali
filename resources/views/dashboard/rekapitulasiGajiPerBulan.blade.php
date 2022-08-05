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
                openWindowWithPost('{{ url("cetakRekapGajiBulanan") }}', {
                    '_token': "{{ csrf_token() }}" ,
                    bulan: $bulan,
                    tahun: $tahun
                });
            }
        }
    }


    function openWindowWithPost(url, data) {
        var form = document.createElement("form");
        form.target = "_blank";
        form.method = "GET";
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
                        <li class="breadcrumb-item active">Informasi penggajian per bulan</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-header">
            <form action="{{url('getRekapitulasiGajiPerBulan')}}" method="get">
                {{ csrf_field() }}
                <div class="row form-group">
                    <div class="col-md-3">
                        @if(empty($tahun))
                        <input type="hidden" name="valTahun" id="valTahun" value="-1">
                        <select id="tahun" name="tahun" class="form-select" >
                            <option value="-1" selected>--Pilih Tahun--</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                        </select>
                        @else
                        <input type="hidden" name="valTahun" id="valTahun" value="{{$tahun}}">

                        <select id="tahun" name="tahun" class="form-select" >
                            <option value="-1" selected>--Pilih Tahun--</option>
                            <option value="2022" @if($tahun == 2022) selected @endif>2022</option>
                            <option value="2023" @if($tahun == 2023) selected @endif>2023</option>
                        </select>
                        @endif
                    </div>
                    <div class="col-md-3">
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
                        <button type="submit" id="hitButton" class="form-control btn-primary">Cari</button>
                    </div>
                    @if(!empty($payroll))
                    @if(count($payroll)>1)
                    <div class="col-md-2">
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
                        <th style="width: 20%;">Nama</th>
                        <th style="width: 15%;">Bulanan</th>
                        <th style="width: 15%;">Harian</th>
                        <th style="width: 15%;">Borongan</th>
                        <th style="width: 15%;">Honorarium</th>
                        <th style="width: 15%;">Total</th>
                    </tr>
                </thead>
                <tbody style="font-size:12px">
                    @php 
                    $no=1;
                    $totalBulanan=0;
                    $totalHarian=0;
                    $totalBorongan=0;
                    $totalHonorarium=0;
                    $total=0;
                    @endphp
                    @foreach($payroll as $paymonth)
                    @php
                    $totalBulanan+=$paymonth->bulanan;
                    $totalHarian+=$paymonth->harian;
                    $totalBorongan+=$paymonth->borongan;
                    $totalHonorarium+=$paymonth->honorarium;
                    $totalBulan=($paymonth->bulanan+$paymonth->harian+$paymonth->borongan+$paymonth->honorarium);
                    $total+=$totalBulan;
                    @endphp
                    <tr>
                        <td style="text-align: center;">
                            {{$no}}
                        </td>
                        <td style="text-align: left;">
                            {{$paymonth->name}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->bulanan, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->harian, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->borongan, 2, ',', '.')}}</td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->honorarium, 2, ',', '.')}}</td>
                        <td style="text-align: right;">{{number_format($totalBulan, 2, ',', '.')}}</td>
                        @php $no+=1;    @endphp                                    
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size:14px">
                    <tr>
                        <td style="text-align: center;">
                        </td>
                        <td style="text-align: center;">
                        </td>
                        <td style="text-align: right;">
                            Rp. {{number_format($totalBulanan, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">
                            Rp. {{number_format($totalHarian, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">
                            Rp. {{number_format($totalBorongan, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">
                            Rp. {{number_format($totalHonorarium, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">
                            Rp. {{number_format($total, 2, ',', '.')}}
                        </td>
                    </tr>
                </tfooter>
            </table>


            @endif
        </div>
    </div>
    Laman ini akan menampilkan data 
    <ol>
        <li>Dalam rentang tanggal terpilih</li>
        <li>Data gaji yang dihitung melihat proses generate gaji</li>
    </ol>
</body>
@endsection