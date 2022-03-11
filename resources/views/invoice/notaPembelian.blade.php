@if ((Auth::user()->isProduction() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
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
        <title>Invoice - {{$purchase->purchasingNum}}</title>
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
                NOTA PEMBELIAN</h3>
                <h4 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                    No : {{$purchase->purchasingNum}}
                </h4>
            </div>
            <table width="100%" id="invoice">
                <tr>
                    <td width="30%">
                        <span class="label" id="spanLabel"><b>Nama Perusahaan</b></span>
                    </td>
                    <td width="3%" style="text-align: center;">:</td>
                    <td width="67%">
                        {{$company->name}} - {{$company->address}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Tanggal Kedatangan</b></span>
                    </td>
                    <td style="text-align: center;">:</td>
                    <td>
                        {{$purchase->arrivaldate}}
                    </td>
                </tr>
            </table>

            @php
            $totalAmount=0;
            $totalPrice=0;
            $rowCount=0;
            @endphp
            <h4>
                <table width="100%" id="invoice">
                    <thead style="text-align: center;">
                        <tr>
                            <th width="5%">No</th>
                            <th width="52%">Barang</th>
                            <th width="10%">Jumlah (Kg)</th>
                            <th width="18%">Harga/Kg ({{$valutaType}})</th>
                            <th width="18%">Total ({{$valutaType}})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchaseDetails as $detail)
                        @php
                        $rowCount       +=1;
                        $totalAmount    +=$detail->amount;
                        $totalPrice     +=($detail->amount*$detail->price);
                        @endphp            
                        <tr >
                            <td width="5%" style="text-align: right;font-size:14px">{{$rowCount}}</td>
                            <td width="52%" style="font-size:14px">{{$detail->goodsBahasa}}</td>
                            <td width="10%" style="text-align: right;font-size:14px">
                                @php
                                echo number_format($detail->amount, 2, ',', '.');
                                @endphp
                            </td>
                            <td width="18%" style="text-align: right;font-size:14px">
                                @php
                                echo number_format($detail->price, 2, ',', '.');
                                @endphp
                            </td>
                            <td width="18%" style="text-align: right;font-size:14px">
                                @php
                                echo number_format(($detail->amount*$detail->price), 2, ',', '.');
                                @endphp
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr >
                            <td colspan="2" width="40%"><b>TOTAL</b></td>
                            <td width="15%" style="text-align: right;">
                                @php
                                echo number_format($totalAmount, 2, ',', '.');
                                @endphp

                            </td>
                            <td width="15%" style="text-align: right;">
                            </td>
                            <td width="15%" style="text-align: right;">
                                @php
                                echo number_format($totalPrice, 2, ',', '.');
                                @endphp
                            </td>
                        </tr>
                    </tfoot>        
                </table>
            </h4>
            <br>
            <table width="100%" id="invoice">
                <tr>
                    <td width="30%">
                        <span class="label" id="spanLabel"><b>Jumlah</b></span>
                    </td>
                    <td width="3%" style="text-align: center;">:</td>
                    <td width="67%">
                        @php
                        echo $valutaType.' '.number_format($totalPrice, 2, ',', '.');
                        @endphp
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Persen Pajak</b></span>
                    </td>
                    <td width="3%" style="text-align: center;">:</td>
                    <td>
                        @php
                        echo number_format($purchase->taxPercentage, 2, ',', '.').' %';
                        @endphp
                    </td>
                </tr>
                @php
                $finalAmount = 0;
                $taxIncluded = "";
                $tax = $totalPrice * $purchase->taxPercentage / 100;

                if ($company->taxIncluded == 0){
                    $finalAmount = $totalPrice;
                    $taxIncluded = "Pajak tidak termasuk";
                }
                else{
                    $finalAmount = $totalPrice - $tax;                
                    $taxIncluded = "Pajak termasuk";
                }
                @endphp                
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>@php echo $taxIncluded;@endphp</b></span>
                    </td>
                    <td width="3%" style="text-align: center;">:</td>
                    <td>
                        @php
                        echo $valutaType.' '.number_format($tax, 2, ',', '.');
                        @endphp
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Total Jumlah</b></span>
                    </td>
                    <td width="3%" style="text-align: center;">:</td>
                    <td>
                        @php
                        echo $valutaType.' '.number_format($finalAmount, 2, ',', '.');
                        @endphp
                    </td>
                </tr>                
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
                        Authorized Signature
                        <br><br><br><br>
                        PT. Anugrah Laut Indonesia
                    </td>
                    <td width="20%"></td>
                    <td width="40%" style="text-align: left;vertical-align: top;">
                        Supplier
                        <br><br><br><br>
                        {{$company->name}}
                    </td>
                </tr>       
            </table>
        </main>
    </body>
    </html>
    @endif