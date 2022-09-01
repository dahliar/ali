<!doctype html>
    <html lang="en">
    <style type="text/css">
        #invoice {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #invoice td, #invoice th {
            border: 1px solid #ddd;
            vertical-align: top;
        }

        #invoice tr:nth-child(even){background-color: #f2f2f2;}

        #invoice tr:hover {background-color: #ddd;}

        #invoice th {
            padding-top: 1px;
            padding-bottom: 1px;
            text-align: center;
            background-color: #040aaa;
            color: white;
        }
        
        body {
            margin-left: 1cm;
            margin-right: 1cm;
        }
        @page {
            margin: 150px 10px;
        }

        header {
            position: fixed;
            top: -160px;
            text-align: center;
        }
    </style>
    <head>
        <meta charset="UTF-8">
        <title>Slip Gaji Pegawai - {{$employee->nip}} - {{$employee->name}}</title>
    </head>
    <body>
        <header>
            <table width="90%" style="margin-bottom: 0;">
                <tr>
                    <td width="30%" align="center">
                        <img src="{{ asset('/images/ali-logo.png') }}" alt="Logo" width="120" class="logo"/>
                    </td>
                    <td width="70%" style="text-align: center; vertical-align: top;">
                        <h4 align="center">
                            PT. ANUGRAH LAUT INDONESIA
                        </h4>
                        <h6 align="left">
                            <div style="text-align: justify;">
                                Jl. Raya Rembang - Tuban KM 40, Desa Bancar, Kecamatan Bancar, Kabupaten Tuban, Provinsi Jawa Timur, Indonesia
                            </div>
                            <div>
                                www.aliseafood.co.id
                            </div>
                        </h6>
                    </td>
                </tr>
            </table>
            <hr class="solid" style="width: 90%; margin-top: 0; margin-bottom: 0;">
        </header>
        <main>
            <div>
                <h3 align="center" style="margin-top: 0; margin-bottom: 0;">
                Slip Gaji Pegawai</h3>
                <h4 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                    Tanggal Pembayaran : {{$payroll->payDate}}
                </h4>
            </div>
            <table width="100%" id="invoice">
                <tr>
                    <td width="30%">
                        <span class="label" id="spanLabel"><b>Nama </b></span>
                    </td>
                    <td width="3%" style="text-align: center;">:</td>
                    <td width="67%">
                        {{$employee->name}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>NIP</b></span>
                    </td>
                    <td style="text-align: center;">:</td>
                    <td>
                        {{$employee->nip}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Bank - Rekening</b></span>
                    </td>
                    <td style="text-align: center;">:</td>
                    <td>
                        {{$employee->bankName}} - {{$employee->noRekening}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Tanggal Pencatatan</b></span>
                    </td>
                    <td style="text-align: center;">:</td>
                    <td>
                        {{$startDate}} - {{$endDate}}
                    </td>
                </tr>
            </table>
            @php
            $thp = 0;
            $total=0;
            $no=1;
            @endphp         

            @if(!empty($bulanan))
            <span class="label" id="spanLabel"><b><h3>Bulanan</h3></b></span>
            <table width="100%" id="invoice">
                <thead style="text-align: center;">
                    <tr >
                        <th width="5%">No</th>
                        <th width="15%">Tanggal</th>
                        <th width="15%">Jumlah</th>
                    </tr>
                </thead>
                <tbody style="font-size: 12px;">
                    <tr>
                        <td width="5%" style="text-align:left">1</td>
                        <td width="30%" style="text-align:center">{{$bulanan->tanggal}}</td>
                        <td width="65%" style="text-align:right;">
                            {{'Rp. '.number_format($bulanan->jumlah, 2, ',', '.')}}
                        </td>
                    </tr>
                    @php
                    $thp+=$bulanan->jumlah;
                    @endphp
                </tbody>      
            </table>
            @endif
            @if(!empty($harian))
            <span class="label" id="spanLabel"><b><h3>Presensi Harian</h3></b></span>
            <table width="100%" id="invoice">
                <thead style="text-align: center;">
                    <tr >
                        <th width="5%">No</th>
                        <th width="30%">Waktu</th>
                        <th width="30%">Jam / Honor</th>
                        <th width="15%">Total</th>
                    </tr>
                </thead>
                <tbody style="font-size: 12px;">
                    @foreach ($harian as $h)
                    <tr>
                        <td width="5%">{{$no}}
                        </td>
                        <td width="40%">
                            <div class="row form-group">
                                <span class="col-3">Masuk</span>
                                <span class="col-1">:</span>
                                <span class="col-7">{{$h->start}}</span>
                            </div>
                            <div class="row">
                                <span class="col-3">Pulang</span>
                                <span class="col-1">:</span>
                                <span class="col-7">{{$h->end}}</span>
                            </div>
                        </td>
                        <td width="35%">
                            <div class="row form-group">
                                <span class="col-md-3">Kerja</span>
                                <span class="col-md-1">:</span>
                                <span class="col-md-7">
                                    {{$h->jk.' / '.number_format($h->uh, 0, ',', '.')}}
                                </span>
                            </div>
                            <div class="row form-group">
                                <span class="col-md-3">Lembur</span>
                                <span class="col-md-1">:</span>
                                <span class="col-md-7">
                                    {{$h->jl.' / '.number_format($h->ul, 0, ',', '.')}}
                                </span>
                            </div>                            
                        </td>                        
                        <td width="20%" style="text-align: right;">
                            {{'Rp. '.number_format(($h->uh+$h->ul), 0, ',', '.')}}
                        </td>
                    </tr>
                    @php
                    $total+=$h->uh + $h->ul;
                    $no+=1;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr >
                        <td colspan="3">Total</td>
                        <td width="20%" style="text-align: right;">
                            {{'Rp. '.number_format($total, 0, ',', '.')}}
                            @php
                            $thp+=$total;
                            @endphp
                        </td>
                    </tr>
                </tfoot>        
            </table>
            @endif
            @if(!empty($borongan))
            <span class="label" id="spanLabel"><b><h3>Borongan</h3></b></span>
            <table width="100%" id="invoice">
                <thead style="text-align: center;">
                    <tr >
                        <th width="5%">No</th>
                        <th width="25%">Nama</th>
                        <th width="10%">Tanggal</th>
                        <th width="10%">Harga/Kg</th>
                        <th width="15%">Berat</th>
                        <th width="10%">Pekerja</th>
                        <th width="15%">Honor</th>
                    </tr>
                </thead>
                <tbody style="font-size: 12px;">
                    @php
                    $total=0;
                    $no=1;
                    @endphp 
                    @foreach ($borongan as $b)
                    <tr>
                        <td width="5%" style="text-align:left">{{$no}}</td>
                        <td width="25%" style="text-align:left">{{$b->name}}</td>
                        <td width="10%" style="text-align:center">{{$b->tanggalkerja}}</td>
                        <td width="10%" style="text-align:right">{{'Rp. '.number_format($b->hargaSatuan, 0, ',', '.')}}
                        </td>
                        <td width="15%" style="text-align:right">
                            {{number_format($b->netweight, 2, ',', '.').' Kg'}}
                        </td>
                        <td width="10%" style="text-align:right">{{$b->worker}} orang</td>
                        <td width="15%" style="text-align:right">
                            {{'Rp. '.number_format($b->netPayment, 2, ',', '.')}}
                        </td>
                    </tr>
                    @php
                    $total+=$b->netPayment;
                    $no+=1;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr >
                        <td colspan="6">Total</td>
                        <td width="20%" style="text-align: right;">
                            {{'Rp. '.number_format($total, 0, ',', '.')}}
                            @php
                            $thp+=$total;
                            @endphp
                        </td>
                    </tr>
                </tfoot>        
            </table>
            @endif
            @if(!empty($honorarium))
            <span class="label" id="spanLabel"><b><h3>Honorarium</h3></b></span>
            <table width="100%" id="invoice">
                <thead style="text-align: center;">
                    <tr >
                        <th width="5%">No</th>
                        <th width="15%">Tanggal</th>
                        <th width="65%">Keterangan</th>
                        <th width="15%">Jumlah</th>
                    </tr>
                </thead>
                <tbody style="font-size: 12px;">
                    @php
                    $total=0;
                    $no=1;
                    @endphp         
                    @foreach ($honorarium as $h)
                    <tr>
                        <td width="5%" style="text-align:left">{{$no}}</td>
                        <td width="15%" style="text-align:center">{{$h->tanggalKerja}}</td>
                        <td width="65%" style="text-align:left">{{$h->keterangan}}</td>
                        <td width="15%" style="text-align:right">{{'Rp. '.number_format($h->jumlah, 0, ',', '.')}}
                        </td>

                    </tr>
                    @php
                    $total+=$h->jumlah;
                    $no+=1;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr >
                        <td colspan="3">Total</td>
                        <td width="20%" style="text-align: right;">
                            {{'Rp. '.number_format($total, 0, ',', '.')}}
                            @php
                            $thp+=$total;
                            @endphp
                        </td>
                    </tr>
                </tfoot>        
            </table>
            @endif
            <table width="100%">
                <thead>
                    <tr >
                        <th width="70%" style="text-align: left;">Jumlah Total Gaji </th>
                        <th width="30%" style="text-align: right;"><h2>
                            {{'Rp. '.number_format($thp, 2, ',', '.')}}
                        </h2></th>
                    </tr>
                </thead>
            </table>
            <table width="100%">
                <tr>
                    <td width="40%" style="vertical-align: top;">
                        PT. Anugrah Laut Indonesia
                        <br>Direktur Utama
                        <br><br><span style="text-align: center;">ttd</span><br><br>
                        Aktaria Hidapratiwi
                    </td>
                </tr>       
            </table>
            Dokumen ini dicetak pada : {{Carbon\Carbon::now()}}
        </main>
    </body>
    </html>
