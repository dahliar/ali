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
        <title>Rekap Gaji Bulanan - {{$monthYear}}</title>
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
                        </h7>
                    </td>
                </tr>
            </table>
            <hr class="solid" style="width: 90%; margin-top: 0; margin-bottom: 0;">
        </header>
        <main>
            <div>
                <h3 align="center" style="margin-top: 0; margin-bottom: 0;">
                Rekapitulasi Gaji Bulan</h3>
                <h4 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                    {{$monthYear}}
                </h4>
            </div>
            <table width="100%" id="invoice">
                <thead style="text-align: center;">
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 20%;">Nama</th>
                        <th style="width: 15%;">No Slip</th>
                        <th style="width: 14%;">Bulanan</th>
                        <th style="width: 14%;">Harian</th>
                        <th style="width: 14%;">Borongan</th>
                        <th style="width: 14%;">Honorarium</th>
                        <th style="width: 20%;">Total</th>
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
                        <td style="text-align: center;">
                            {{$paymonth->slipid}}{{$tahun}}{{$bulan}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->bulanan, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->harian, 2, ',', '.')}}
                        </td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->borongan, 2, ',', '.')}}</td>
                        <td style="text-align: right;">Rp. {{number_format($paymonth->honorarium, 2, ',', '.')}}</td>
                        <td style="text-align: right;">Rp. {{number_format($totalBulan, 2, ',', '.')}}</td>
                        @php $no+=1;    @endphp                                    
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size:12px">
                    <tr>
                        <td style="text-align: center;"></td>
                        <td style="text-align: center;"></td>
                        <td style="text-align: center;"></td>
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
                </tfoot>
            </table>

            <table width="100%">
                <tr>
                    <td width="40%">
                        Tuban, {{ Carbon\Carbon::now()->toDateString()}}
                    </td>
                    <td width="20%"></td>
                    <td width="40%">
                        _________,__________________
                    </td>
                </tr>
                <tr>
                    <td width="40%" style="text-align: left;vertical-align: top;">
                        Direktur Utama
                        <br><br>ttd<br><br>
                        Aktaria Hidapratiwi<br>
                        PT. Anugrah Laut Indonesia
                    </td>
                    <td width="20%"></td>
                </tr>
            </table>
            Dokumen ini dicetak pada : {{Carbon\Carbon::now()}}                
        </main>
    </body>
    </html>