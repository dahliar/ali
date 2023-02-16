@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (empty($speciesChoosen))
@php
$speciesChoosen=-1;
$itemChoosen=-1;
@endphp
@endif
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

<script type="text/javascript"> 
    function selectOptionChange(speciesId, itemId){
        $.ajax({
            url: '{{ url("getItemsForSelectOption") }}'+"/0/0/"+speciesId,
            type: "GET",
            data : {"_token":"{{ csrf_token() }}"},
            dataType: "json",
            success:function(data){
                if(data){
                    var html = '';
                    var i;
                    html += '<option value="-1">--Choose First--</option>';
                    for(i=0; i<data.length; i++){
                        if (data[i].itemId != itemId){
                            html += '<option value='+data[i].itemId+'>'+
                            data[i].speciesNameEng+
                            " "+data[i].gradeName+
                            " "+data[i].sizeName+
                            " "+data[i].pshortname+
                            " "+data[i].freezingName+
                            '</option>';
                        } else {
                            html += '<option selected value='+data[i].itemId+'>'+
                            data[i].speciesNameEng+
                            " "+data[i].gradeName+
                            " "+data[i].sizeName+
                            " "+data[i].pshortname+
                            " "+data[i].freezingName+
                            '</option>';
                        }
                        $('#item').html(html);
                    }
                }
            }
        });
    }
    $(document).ready(function() {
        selectOptionChange({{$speciesChoosen}}, {{$itemChoosen}});
        $('#species').on('change', function() {
            var speciesId = $(this).val();
            if (speciesId>0){
                selectOptionChange(speciesId, -1);
            }else{
                $('#item')
                .empty()
                .append('<option value="-1">--All Species--</option>');
            }
        });
    });
</script>
<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Harga Pokok Produksi</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-header">
            <form id="formHpp" action="{{url('getHpp')}}" method="post" name="formHpp">
                {{ csrf_field() }}
                <div class="row form-group">
                    <div class="col-md-2">
                        @if(empty($start))
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{date('Y-m-d', strtotime('-1 week'))}}" > 
                        @else
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{$start}}" > 
                        @endif
                    </div>
                    <div class="col-md-2">
                        @if(empty($end))
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{date('Y-m-d')}}" > 
                        @else
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{$end}}" > 
                        @endif                                        
                    </div>
                    <div class="col-md-2">
                        @if(empty($speciesChoosen))
                        @php
                        $speciesChoosen=-1;
                        @endphp
                        @endif
                        <select class="form-select w-100" id="species" name="species">
                            <option value="-1">--All Species--</option>
                            @foreach ($species as $spec)
                            @if ( $spec->id == $speciesChoosen )
                            <option value="{{ $spec->id }}" selected>{{ $spec->name }}</option>
                            @else
                            <option value="{{ $spec->id }}">{{ $spec->name }}</option>                    
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="item" name="item" class="form-select" >
                            <option value="-1">--All Items--</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="align-middle">
                            @if(empty($showDetail))

                            @php
                            $showDetail=1;
                            @endphp
                            
                            @endif
                            <select class="form-select w-100" id="showDetail" name="showDetail">
                                <option value="1" @if ($showDetail == 1 ) selected @endif>Hide Detil Barang</option>
                                <option value="2" @if ($showDetail == 2 ) selected @endif>Show Detil Barang</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="form-control btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card card-body">
            @if(!empty($dataHarian))
            <div class="row form-group">
                <div class="col-md-2">
                    Pembayaran Harian
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text col-3">Rp. </span>
                        <input type="input" class="form-control text-end" value="{{number_format($dataHarian['total'], 2, ',', '.')}}" disabled="true">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="input" class="form-control text-end" value="{{$dataHarian['orang']}}" disabled="true">
                        <span class="input-group-text col-3">Pegawai</span>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($dataBorongan))
            <div class="row form-group">
                <div class="col-md-2">
                    Pembayaran Borongan
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text col-3">Rp. </span>
                        <input type="input" class="form-control text-end" value="{{number_format($dataBorongan['total'], 2, ',', '.')}}" disabled="true">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="input" class="form-control text-end" value="{{$dataBorongan['orang']}}" disabled="true">
                        <span class="input-group-text col-3">Pegawai</span>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($dataHonorarium))
            <div class="row form-group">
                <div class="col-md-2">
                    Pembayaran Honorarium
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text col-3">Rp. </span>
                        <input type="input" class="form-control text-end" value="{{number_format($dataHonorarium['total'], 2, ',', '.')}}" disabled="true">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="input" class="form-control text-end" value="{{$dataHonorarium['orang']}}" disabled="true">
                        <span class="input-group-text col-3">Pegawai</span>
                    </div>
                </div>
            </div>
            @endif
            @if(!empty($purchases))
            <div class="row form-group">
                <div class="col-md-2">
                    Pembelian Barang
                </div>

                @if($showDetail==1)
                @php 
                $no=1;
                $totalBerat=0;
                $totalHarga=0;
                @endphp
                @foreach($purchases as $purchase)
                @php
                $totalSatuan = $purchase->price*$purchase->amount;
                $totalBerat+=$purchase->amount;
                $totalHarga+=$totalSatuan;
                @endphp
                @endforeach
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text col-3">Total Berat </span>
                        <input type="input" class="form-control text-end" value="{{number_format($totalBerat, 2, ',', '.')}} Kg" disabled="true">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text col-3">Total Bayar </span>
                        <input type="input" class="form-control text-end" value="Rp. {{number_format($totalHarga, 2, ',', '.')}}" disabled="true">
                    </div>
                </div>
                @else
                <div class="col-md-10">
                    <table style="width: 100%;" class="center table table-striped table-hover table-bordered">
                        <thead style="text-align: center;">
                            <tr>
                                <th style="width: 5%;">No</th>

                                <th style="width: 15%;">Perusahaan</th>
                                <th style="width: 15%;">Barang</th>
                                <th style="width: 10%;">Tanggal</th>
                                <th style="width: 15%;">Jumlah</th>
                                <th style="width: 20%;">Harga</th>
                                <th style="width: 20%;">Total</th>
                            </tr>
                        </thead>
                        <tbody style="font-size:12px">
                            @php 
                            $no=1;
                            $totalBerat=0;
                            $totalHarga=0;
                            @endphp
                            @foreach($purchases as $purchase)
                            @php
                            $totalSatuan = $purchase->price*$purchase->amount;
                            $totalBerat+=$purchase->amount;
                            $totalHarga+=$totalSatuan;
                            @endphp
                            <tr>
                                <td style="text-align: center;">
                                    @php echo $no @endphp 
                                </td>
                                <td style="">{{$purchase->perusahaan}}</td>
                                <td style="">{{$purchase->name}}</td>
                                <td style="text-align: center">{{$purchase->tanggal}}</td>
                                <td style="text-align: right">{{number_format($purchase->amount, 2, ',', '.')}} Kg</td>
                                <td style="text-align: right;">Rp. {{number_format($purchase->price, 2, ',', '.')}}</td>
                                <td style="text-align: right;">Rp. {{number_format(($totalSatuan), 2, ',', '.')}}</td>
                                @php $no+=1;    @endphp                                    
                            </tr>
                            @endforeach
                        </tbody>
                        <tfooter>
                            <tr>
                                <th colspan="4"></th>
                                <th style="width: 15%;text-align: right;">
                                    {{number_format($totalBerat, 2, ',', '.')}} Kg
                                </th>
                                <th style="width: 15%;"></th>
                                <th style="width: 15%;text-align: right;">
                                    Rp. {{number_format($totalHarga, 2, ',', '.')}}
                                </th>
                            </tr>
                        </tfooter>
                    </table>
                </div>
                @endif
            </div>
            @endif
        </div>    
    </div>
</body>
@endsection