<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
        <title>Invoice - {{$transaction->pinum}}</title>
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
                                Jl. Raya Rembang - Tuban KM 40, Desa Bancar, Kecamatan Bancar, Kabupaten Tuban, Provinsi Jawa Timur, Indonesia
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
    </div>
    <div class="row form-group">
        <div>
            <h1 align="center" style="margin-top: 0; margin-bottom: 0;">
            PROFORMA INVOICE</h1>
            <h3 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                No : {{$transaction->pinum}}
            </h3>
        </div>
    </div>
    <table width="100%" id="invoice">
        <tr>
            <td>
                <span class="label" id="spanLabel"><b>Shipper</b></span>
            </td>
            <td>:</td>
            <td>
                {{$transaction->shipper}}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label" id="spanLabel"><b>Shipper Address</b></span>
            </td>
            <td>:</td>
            <td>
                {{$transaction->shipperAddress}}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label" id="spanLabel"><b>Exported Code</b></span>
            </td>
            <td>:</td>
            <td>
                {{$registration}}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label" id="spanLabel"><b>Bank Details</b></span>
            </td>
            <td>:</td>
            <td>
                Bank : {{$rekening->bank}}<br>
                Bank Address: {{$rekening->alamatbank}}<br>
                Account Name : {{$rekening->owner}}<br>
                Account Number : {{$rekening->rekening}}<br>
                Swiftcode : {{$rekening->swiftcode}}
            </td>
        </tr>
        <tr>
            <td width="30%">
                <span class="label" id="spanLabel"><b>Consignee</b></span>
            </td>
            <td width="3%">:</td>
            <td width="67%">
                {{$companyName->name}}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label" id="spanLabel"><b>Consignee Address</b></span>
            </td>
            <td>:</td>
            <td>
                {{$transaction->companydetail}}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label" id="spanLabel"><b>Shipped From</b></span>
            </td>
            <td>:</td>
            <td>
                {{$transaction->loadingport}} 
            </td>
        </tr>
        <tr>
            <td>
                <span class="label" id="spanLabel"><b>Shipped To</b></span>
            </td>
            <td>:</td>
            <td>
                {{$transaction->destinationport}}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label" id="spanLabel"><b>Payment</b></span>
            </td>
            <td>:</td>
            <td>
                {{$transaction->paymentPlan}} 
            </td>
        </tr>
    </table>
    <br>
    <br>

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
    <table width="100%" id="invoice">
        <thead style="text-align: center;">
            <tr >
                <th width="40%">Goods Description</th>
                <th width="12%">Quantity</th>
                <th width="15%">Net Weight (Kg)</th>
                <th width="16%">Unit Price ({{$valutaType}})</th>
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
                <td width="40%">{{$detail->goods}}</td>
                <td width="12%" style="text-align: right;">{{$detail->quantity}}</td>
                <td width="15%" style="text-align: right;">
                    {{number_format($detail->netweight, 2, ',', '.')}}
                </td>
                <td width="16%" style="text-align: right;">
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
                <td width="40%"><b>TOTAL</b></td>
                <td width="12%"></td>
                <td width="15%" style="text-align: right;">
                    {{number_format($totalNetWeight, 2, ',', '.')}}
                </td>
                <td width="16%" style="text-align: right;">
                </td>
                <td width="17%" style="text-align: right;">
                    {{number_format($totalTransactionPrice, 2, ',', '.')}}
                </td>
            </tr>
        </tfoot>        
    </table>
    <br>
    @endif

    <table width="100%" id="invoice">
        @if ($transaction->isundername==1)
        <tr>
            <td width="30%">
                <span class="label" id="spanLabel"><b>Gross Weight</b></span>
            </td>
            <td width="3%">:</td>
            <td width="67%">
                {{number_format($totalGrossWeight, 2, ',', '.').' Kg'}}
            </td>
        </tr>
        @endif
        <tr>
            <td width="30%">
                <span class="label" id="spanLabel"><b>Total Amount</b></span>
            </td>
            <td width="3%">:</td>
            <td width="67%">
                {{$valutaType.' '.number_format($totalTransactionPrice, 2, ',', '.')}}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label" id="spanLabel"><b>Advance</b></span>
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
    </table>
    <br>

    <!-- 
        PI Transaction NOTES
        PI Transaction NOTES
        PI Transaction NOTES
        PI Transaction NOTES
        PI Transaction NOTES
    -->

    @if ( $notes->count() > 0)
    <table width="100%" id="invoice">
        <thead style="text-align: center;">
            <tr >
                <th width="5%">No</th>
                <th width="95%">Notes</th>
            </tr>
        </thead>
        <tbody>
            @php
            $a=0;
            @endphp
            @foreach ($notes as $note)
            @php
            $a=$a+1;
            @endphp

            <tr >
                <td width="5%" align="center">{{$a}}</td>
                <td width="95%">{{$note->note}}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
        </tfoot>        
    </table>
    <br>
    <br>
    @endif
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
                Authorized Signature
                <br><br><br><br><br><br>
                {{$transaction->packer}}
            </td>
            <td width="20%"></td>
            <td width="40%" style="text-align: left;vertical-align: top;">
                Buyer
                <br><br><br><br><br><br>
                {{$companyName->name}}
            </td>
        </tr>       
    </table>
    Document generated at : {{Carbon\Carbon::now()}}
</body>
</html>