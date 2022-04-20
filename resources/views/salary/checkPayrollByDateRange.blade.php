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
        </div>           
        <div class="card card-header">
            <form action="{{url('getPayrollByDateRange')}}" method="post">
                {{ csrf_field() }}
                <div class="row form-inline">
                    @if(!empty($start))
                    <div class="col-md-2">
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{ $start }}" > 
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ $end }}" >
                    </div>                       
                    @else
                    <div class="col-md-2">
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{ date('Y-m-d') }}" > 
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ date('Y-m-d') }}" >
                    </div>                       
                    @endif
                    <div class="col-md-2">
                        <select id="opsi" name="opsi" class="form-select" >
                            @if(empty($opsi))
                            <option value="1" selected>Detil per tanggal</option>
                            <option value="2">Rekap</option>
                            @else
                            <option value="1" @if($opsi == 1) selected @endif>Detil per tanggal</option>
                            <option value="2" @if($opsi == 2) selected @endif>Rekap</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" id="hitButton" class="form-control btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card card-body">
            @if(!empty($third))
            <table style="width: 100%;" class="center table table-striped table-hover table-bordered">
                <thead style="text-align: center;">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Nama</th>
                        @if (!empty($opsi))
                        @if ($opsi==1)
                        <th style="width: 10%;">Tanggal</th>
                        @endif
                        @endif
                        <th style="width: 10%;">Harian</th>
                        <th style="width: 10%;">Lembur</th>
                        <th style="width: 10%;">Borongan</th>
                        <th style="width: 10%;">Honorarium</th>
                        <th style="width: 10%;">Total</th>
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

                        @if (!empty($opsi))
                        @if ($opsi==1)
                        <td style="text-align: left;">{{$paymonth->tanggal}}</td>
                        @endif
                        @endif


                        <td style="text-align: right;">Rp. {{number_format($paymonth->uh, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->ul, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->borongan, 2, ',', '.')}}</td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->honorarium, 2, ',', '.')}}</td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->total, 2, ',', '.')}}</td>
                        @php $no+=1;    @endphp                                    
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        @if (!empty($opsi))
                        @if ($opsi==1)
                        <td style="text-align: center;" colspan="3"></td>
                        @else
                        <td style="text-align: center;" colspan="2"></td>
                        @endif
                        @endif

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
                        <td style="text-align: right;">
                            Rp. {{number_format($total, 2, ',', '.')}}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </div>  
        Laman ini akan menampilkan data 
        <ol>
            <li>Dalam rentang tanggal terpilih</li>
            <li>Data gaji yang dihitung <b>tidak</b> memperhatikan proses generate gaji</li>
        </ol>
    </div>
</body>
@endsection