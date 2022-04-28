@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (session('status'))
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            {{ session('status') }}
        </div>
        <div class="col-md-1 text-center">
            <span class="label"><strong >x</strong></span>
        </div>
    </div>
</div>
@endif
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
@isset ($payrollChart)
<script type="text/javascript">
    var payroll = @json($payrollChart);
    window.onload = function() {
        google.charts.load('current', {'packages':['line'], 'language': 'id'});
        google.charts.setOnLoadCallback(drawPayroll);
    };
    function drawPayroll() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Bulan');
        data.addColumn('number', 'Bulanan');
        data.addColumn('number', 'Harian');
        data.addColumn('number', 'Borongan');
        var arrValue = [ 
        ["Januari",0,0,0],
        ["Februari",0,0,0],
        ["Maret",0,0,0],
        ["April",0,0,0],
        ["Mei",0,0,0],
        ["Juni",0,0,0],
        ["Juli",0,0,0],
        ["Agustus",0,0,0],
        ["September",0,0,0],
        ["Oktober",0,0,0],
        ["November",0,0,0],
        ["Desember",0,0,0],
        ];

        for (var i = 0; i < payroll.length; i++) {
            arrValue[payroll[i].bulan-1][payroll[i].status] = Number(payroll[i].total);
        }
        for (var i = 0; i < 12; i++) {
            //document.write(new Array(arrValue[i]));
            data.addRows(new Array(arrValue[i]));
        }
        var options = {
            chart: {
                title: 'Payroll tahun 2022',
                subtitle: 'dalam satuan Juta Rupiah'
            },
            height: 500,
            axes: {
                x: {
                    0: {side: 'top'}
                }
            }
        };

        var chart = new google.charts.Line(document.getElementById('chartPayroll'));
        chart.draw(data, google.charts.Line.convertOptions(options));
    }
</script>
@endisset

<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Informasi penggajian</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-header">
            <form action="{{url('getRekapitulasiGaji')}}" method="post">
                {{ csrf_field() }}
                <div class="row form-group">
                    <div class="col-md-3">
                        @if(empty($tahun))
                        <select id="tahun" name="tahun" class="form-select" >
                            <option value="-1" selected>--Choose One--</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                        </select>
                        @else
                        <select id="tahun" name="tahun" class="form-select" >
                            <option value="-1" selected>--Choose One--</option>
                            <option value="2022" @if($tahun == 2022) selected @endif>2022</option>
                            <option value="2023" @if($tahun == 2023) selected @endif>2023</option>
                        </select>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <button type="submit" id="hitButton" class="form-control btn-primary"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card card-body">
            <div class="row">
                <div id="chartPayroll"></div>
            </div>
        </div>
        <div class="card card-body">
            <div class="row form-group">
             @if(!empty($payroll))
             <table style="width: 100%;" class="center table table-striped table-hover table-bordered">
                <thead style="text-align: center;">
                    <tr>
                        <th style="width: 20%;">Bulan</th>
                        <th style="width: 20%;">Pegawai Bulanan</th>
                        <th style="width: 20%;">Pegawai Harian</th>
                        <th style="width: 20%;">Pegawai Borongan</th>
                        <th style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody style="font-size:14px">
                    @php 
                    $no=1;
                    $totalBulanan=0;
                    $totalHarian=0;
                    $totalBorongan=0;
                    $total=0;
                    @endphp
                    @foreach($payroll as $paymonth)
                    @php
                    $totalBulanan+=$paymonth[1];
                    $totalHarian+=$paymonth[2];
                    $totalBorongan+=$paymonth[3];
                    $totalBulan=($paymonth[1]+$paymonth[2]+$paymonth[3]);
                    $total+=$totalBulan;
                    @endphp
                    <tr>
                        <td style="text-align: left;">
                            {{$paymonth[0]}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth[1], 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth[2], 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth[3], 2, ',', '.')}}</td>
                        <td style="text-align: right;">Rp. {{number_format($totalBulan, 2, ',', '.')}}</td>
                        @php $no+=1;    @endphp                                    
                    </tr>
                    @endforeach
                </tbody>
                <tfooter>
                    <tr>
                        <td style="text-align: left;">
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
                            Rp. {{number_format($total, 2, ',', '.')}}
                        </td>
                    </tr>
                </tfooter>
            </table>


            @endif
        </div>
    </div>
</div>
</form>
</div>
</div>
</div>
</body>
@endsection