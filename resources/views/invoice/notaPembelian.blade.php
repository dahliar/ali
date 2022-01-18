@if (Auth::check() and (Auth::user()->isMarketing() or Auth::user()->isAdmin()))
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
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: center;
            background-color: #040aaa;
            color: white;
        }
        
        body {
            margin-left: 1cm;
            margin-right: 1cm;
        }
        @page {
            margin: 225px 25px;
        }

        header {
            position: fixed;
            top: -200px;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -200px;
            height: 50px;
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
                <h2 align="center" style="margin-top: 0; margin-bottom: 0;">
                PURCHASING INVOICE</h2>
                <h3 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                    No : {{$purchase->purchasingNum}}
                </h3>
            </div>
            <table width="100%" id="invoice">
                <tr>
                    <td width="30%">
                        <span class="label" id="spanLabel"><b>Nama Perusahaan</b></span>
                    </td>
                    <td width="3%">:</td>
                    <td width="67%">
                        {{$company->name}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Alamat</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        {{$company->address}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Tanggal Kedatangan</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        {{$purchase->arrivaldate}}
                    </td>
                </tr>
            </table>

            @php
            $totalAmount=0;
            $totalPrice=0;

            @endphp
            <table width="100%" id="invoice">
                <thead style="text-align: center;">
                    <tr >
                        <th width="40%">Goods Description</th>
                        <th width="15%">Quantity (Kg)</th>
                        <th width="15%">Unit Price ({{$valutaType}})</th>
                        <th width="15%">Amount ({{$valutaType}})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseDetails as $detail)
                    @php
                    $totalAmount    +=$detail->amount;
                    $totalPrice     +=($detail->amount*$detail->price);
                    @endphp            
                    <tr >
                        <td width="40%">{{$detail->goods}}</td>
                        <td width="15%" style="text-align: right;">
                            @php
                            echo number_format($detail->amount, 2, ',', '.');
                            @endphp
                        </td>
                        <td width="15%" style="text-align: right;">
                            @php
                            echo number_format($detail->price, 2, ',', '.');
                            @endphp
                        </td>
                        <td width="15%" style="text-align: right;">
                            @php
                            echo number_format(($detail->amount*$detail->price), 2, ',', '.');
                            @endphp
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr >
                        <td width="40%"><b>TOTAL</b></td>
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

            <br>
            <br>
            <table width="100%" id="invoice">
                <tr>
                    <td width="30%">
                        <span class="label" id="spanLabel"><b>Total Amount</b></span>
                    </td>
                    <td width="3%">:</td>
                    <td width="67%">
                        @php
                        echo $valutaType.' '.number_format($totalPrice, 2, ',', '.');
                        @endphp
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Tax Percentage</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        @php
                        echo number_format($purchase->taxPercentage, 2, ',', '.').' %';
                        @endphp
                    </td>
                </tr>                
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Tax</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        @php
                        $tax = $totalPrice * $purchase->taxPercentage /100;
                        echo $valutaType.' '.number_format($tax, 2, ',', '.');
                        @endphp
                    </td>
                </tr>
                @php
                $finalAmount = 0;
                $taxIncluded = "";

                if ($company->taxIncluded == 0){
                    $finalAmount = $totalPrice;
                    $taxIncluded = "NO";
                }
                else{
                    $finalAmount = $totalPrice - $tax;                
                    $taxIncluded = "YES";
                }
                @endphp             
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Tax Included?</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        @php echo $taxIncluded;@endphp
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label" id="spanLabel"><b>Grand Amount</b></span>
                    </td>
                    <td>:</td>
                    <td>
                        @php
                        echo $valutaType.' '.number_format($finalAmount, 2, ',', '.');
                        @endphp
                    </td>
                </tr>                
            </table>
            <br>
            <br>
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
                        <br><br><br><br><br><br>
                        PT. Anugrah Laut Indonesia
                    </td>
                    <td width="20%"></td>
                    <td width="40%" style="text-align: left;vertical-align: top;">
                        Supplier
                        <br><br><br><br><br><br>
                        {{$company->name}}
                    </td>
                </tr>       
            </table>
        </main>
    </body>
    </html>
    @endif