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
            <div class="row form-group">
               @if(!empty($payroll))
               <table style="width: 100%;" class="center table table-striped table-hover table-bordered">
                <thead style="text-align: center;">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Bulanan</th>
                        <th style="width: 15%;">Harian</th>
                        <th style="width: 10%;">Borongan</th>
                        <th style="width: 15%;">Honorarium</th>
                        <th style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody style="font-size:14px">
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
                            {{$paymonth->bulan}}-{{$tahun}}
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
                <tfooter>
                    <tr>
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
</div>
</form>
</div>
</div>
</div>
</body>
@endsection