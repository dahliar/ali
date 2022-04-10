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
<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-9">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Daftar gaji rentang tanggal</li>
                        </ol>
                    </nav>
                </div>
            </div>            
            <div class="modal-body">
                <form action="{{url('getPayrollByDateRange')}}" method="post">
                    {{ csrf_field() }}

                    <div class="row form-inline">
                        <div class="col-md-2">
                            <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d'))}}" > 
                        </div>
                        <div class="col-md-2">
                            <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}" >
                        </div>                       
                        <div class="col-md-2">
                            <button type="submit" id="hitButton" class="form-control btn-primary">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-body">
                @if(!empty($third))
                <table style="width: 100%;" class="center table table-striped table-hover table-bordered">
                    <thead style="text-align: center;">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 15%;">Nama</th>
                            <th style="width: 30%;">Keterangan</th>
                            <th style="width: 10%;">Tanggal</th>
                            <th style="width: 10%;">Harian</th>
                            <th style="width: 10%;">Lembur</th>
                            <th style="width: 10%;">Borongan</th>
                            <th style="width: 10%;">Honorarium</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:14px">
                        @php 
                        $no=1;
                        $totalHarian=0;
                        $totalLembur=0;
                        $totalBorongan=0;
                        $totalHonorarium=0;
                        $total=0;
                        @endphp
                        @foreach($third as $paymonth)
                        @php
                        $totalHarian+=$paymonth->uh;
                        $totalLembur+=$paymonth->ul;
                        $totalBorongan+=(int)$paymonth->borongan;
                        $totalHonorarium+=(int)$paymonth->honorarium;
                        $total+=($paymonth->uh + $paymonth->ul + (int)$paymonth->borongan + (int)$paymonth->honorarium);
                        @endphp
                        <tr>
                            <td style="text-align: center;">
                                {{$no}}
                            </td>
                            <td style="text-align: left;">
                                {{$paymonth->name}}
                            </td>
                            <td style="text-align: left;">
                                {{$paymonth->keterangan}}
                            </td>
                            <td style="text-align: left;">{{$paymonth->tanggal}}</td>
                            <td style="text-align: right;">Rp. {{number_format($paymonth->uh, 2, ',', '.')}}
                            </td>
                            <td style="text-align: right;">Rp. {{number_format($paymonth->ul, 2, ',', '.')}}
                            </td>
                            <td style="text-align: right;">Rp. {{number_format($paymonth->borongan, 2, ',', '.')}}</td>
                            <td style="text-align: right;">Rp. {{number_format($paymonth->honorarium, 2, ',', '.')}}</td>
                            @php $no+=1;    @endphp                                    
                        </tr>
                        @endforeach
                    </tbody>
                    <tfooter>
                        <tr>
                            <td style="text-align: center;" colspan="4">
                            </td>
                            <td style="text-align: right;">
                                Rp. {{number_format($totalHarian, 2, ',', '.')}}
                            </td>
                            <td style="text-align: right;">
                                Rp. {{number_format($totalLembur, 2, ',', '.')}}
                            </td>
                            <td style="text-align: right;">
                                Rp. {{number_format($totalBorongan, 2, ',', '.')}}
                            </td>
                            <td style="text-align: right;">
                                Rp. {{number_format($totalHonorarium, 2, ',', '.')}}
                            </td>
                        </tr>
                    </tfooter>
                </table>
                <div class="row form-inline">
                    <div class="col-md-8 text-end">
                        <h2>Total</h2>
                    </div>
                    <div class="col-md-4 text-end">
                        <h2>
                            @php
                            $total = $totalHarian+$totalLembur+$totalBorongan+$totalHonorarium;
                            @endphp
                            Rp. {{number_format($total, 2, ',', '.')}}
                        </h2>
                    </div>
                </div>
                @endif
            </div>    
        </div>
    </div>
</body>
@endsection