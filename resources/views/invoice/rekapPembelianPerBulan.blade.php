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
        <title>Rekap Pembelian Bulanan - {{$monthYear}}</title>
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
                                Office : Jl. Kahuripan Terrace III no. 30, Kahuripan Nirwana, Desa Sumput, Kecamatan Sidoarjo, Kabupaten Sidoarjo, Provinsi Jawa Timur, Indonesia
                            </div>
                            <div style="text-align: justify;">
                                Factory : Jl. Raya Rembang - Tuban KM 40, Desa Bancar, Kecamatan Bancar, Kabupaten Tuban, Provinsi Jawa Timur, Indonesia
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
                Rekapitulasi Pembelian per-Bulan</h3>
                <h4 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                    {{$monthYear}}
                </h4>
            </div>
            <table width="100%" id="invoice">
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
                        @if($opsi==1)
                        <td colspan="5" style="text-align: center;"></td>
                        @else
                        <td colspan="3" style="text-align: center;"></td>
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

            <table width="100%">
                <tr>
                    <td width="40%">
                        _________,___ {{ date(' F Y', strtotime(today())); }}
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