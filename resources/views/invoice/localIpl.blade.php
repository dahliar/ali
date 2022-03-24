@if ((Auth::user()->isMarketing() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)


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
            padding: 8px;
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
            margin: 160px 10px;
        }

        header {
            position: fixed;
            top: -200px;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -160px;
            height: 50px;
            text-align: center;
        }
    </style>
    <head>
        <meta charset="UTF-8">
        <title>Invoice - {{$transaction->transactionNum}}</title>
    </head>

    <body>
        <header>
            <table width="90%" style="margin-bottom: 0;">
                <tr>
                    <td width="30%" align="center">
                        <img src="{{ asset('/images/ali-logo.png') }}" alt="Logo" width="120" class="logo"/>
                    </td>
                    <td width="70%" style="text-align: center; vertical-align: top;">
                        <h3 align="center">
                            PT. ANUGRAH LAUT INDONESIA
                        </h3>
                        <h5 align="left">
                            <div style="text-align: justify;">
                                Office : Jl. Kahuripan Terrace III no. 30, Kahuripan Nirwana, Desa Sumput, Kecamatan Sidoarjo, Kabupaten Sidoarjo, Provinsi Jawa Timur, Indonesia
                            </div>
                            <div style="text-align: justify;">
                                Factory : Jl. Raya Rembang - Tuban KM 40, Desa Bancar, Kecamatan Bancar, Kabupaten Tuban, Provinsi Jawa Timur, Indonesia
                            </div>
                            <div>
                                www.aliseafood.co.id
                            </div>
                        </h5>
                    </td>
                </tr>
            </table>
            <hr class="solid" style="width: 90%; margin-top: 0; margin-bottom: 0;">
        </header>
        <main>
            <div>
                <h1 align="center" style="margin-top: 0; margin-bottom: 0;">
                INVOICE PENJUALAN</h1>
                <h3 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                    No : {{$transaction->transactionNum}}
                </h3>
            </div>
            <table width="100%" id="invoice">
                <tr>
                    <td width="30%">
                        <span class="label" id="spanLabel"><b>Pembeli</b></span>
                    </td>
                    <td width="3%">:</td>
                    <td width="67%">
                        {{$companyName->name}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Alamat</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        {{$transaction->companydetail}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Dikirim dari</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        {{$transaction->loadingport}} 
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Alamat pengiriman</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        {{$transaction->destinationport}}
                    </td>
                </tr>
            </table>

            @php
            $totalAmount=0;
            $totalNetWeight=0;
            $totalGrossWeight=0;
            $totalTransactionPrice=0;

            @endphp

            @if ($transaction->isundername==2)
            @php
            $totalTransactionPrice =$transaction->payment;
            @endphp
            @else
            <br>
            <table width="100%" id="invoice">
                <thead style="text-align: center;">
                    <tr >
                        <th width="38%">Barang</th>
                        <th width="12%">Jumlah</th>
                        <th width="15%">Berat</th>
                        <th width="18%">Harga/Kg ({{$valutaType}})</th>
                        <th width="17%">Total ({{$valutaType}})</th>
                    </tr>
                </thead>
                <tbody style="font-size:12px">
                    @foreach ($detailTransactions as $detail)
                    @php
                    $totalAmount            +=$detail->amount;
                    $totalNetWeight         +=$detail->netweight;
                    $totalGrossWeight       +=$detail->grossweight;
                    $totalTransactionPrice  +=$detail->totalPrice;
                    @endphp            
                    <tr >
                        <td width="38%">{{$detail->goods}}</td>
                        <td width="12%" style="text-align: right;">
                            {{$detail->quantity}}
                        </td>
                        <td width="15%" style="text-align: right;">
                            {{number_format($detail->netweight, 2, ',', '.').' Kg'}}
                        </td>
                        <td width="18%" style="text-align: right;">
                            {{number_format($detail->price, 2, ',', '.')}}
                        </td>
                        <td width="17%" style="text-align: right;">
                            {{number_format($detail->totalPrice, 2, ',', '.')}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="font-size:12px">
                    <tr >
                        <td width="38%"><b>TOTAL</b></td>
                        <td width="12%"></td>
                        <td width="15%" style="text-align: right;">
                            {{number_format($totalNetWeight, 2, ',', '.').' Kg'}}
                        </td>
                        <td width="18%" style="text-align: right;">
                        </td>
                        <td width="17%" style="text-align: right;">
                            {{number_format($totalTransactionPrice, 2, ',', '.')}}
                        </td>
                    </tr>
                </tfoot>        
            </table>
            @endif

            <br>
            <br>
            <table width="100%" id="invoice">
                @if ($transaction->isundername==1)
                <tr>
                    <td width="30%">
                        <span class="label" id="spanLabel"><b>Berat Total</b></span>
                    </td>
                    <td width="3%">:</td>
                    <td width="67%">
                        {{number_format($totalGrossWeight, 2, ',', '.').' Kg'}}
                    </td>
                </tr>
                @endif
                <tr>
                    <td width="30%">
                        <span class="label" id="spanLabel"><b>Jumlah Total</b></span>
                    </td>
                    <td width="3%">:</td>
                    <td width="67%">
                        {{$valutaType.' '.number_format($totalTransactionPrice, 2, ',', '.')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Uang Muka</b></span>
                    </td>
                    <td>:</td>
                    <td>                        
                        {{$valutaType.' '.number_format($transaction->advance, 2, ',', '.')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Balance</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        {{$valutaType.' '.number_format(($totalTransactionPrice - $transaction->advance), 2, ',', '.')}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Detil Bank</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        Bank : {{$rekening->bank}}<br>
                        Account Name : {{$rekening->owner}}<br>
                        Account Number : {{$rekening->rekening}}  
                    </td>
                </tr>
            </table>
            <br>
            <br>
            <table width="100%">
                <tr>
                    <td width="40%" style="text-align: left;vertical-align: top;">
                        Tuban, {{$transaction->transactionDate}}
                        <br><br>ttd<br><br>
                        {{$payerName}}
                    </td>
                </tr>       
            </table>
            Document generated at : {{Carbon\Carbon::now()}}
        </main>
    </body>
    </html>
    @endif