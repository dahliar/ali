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
    <title>Slip Pendapatan Pegawai - {{$employee->nip}} - {{$employee->name}}</title>
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
            Slip pendapatan Pegawai</h3>
            <h4 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                Periode : {{$bulanName}} - {{$tahun}}
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

        </table>
        @php
        $thp = 0;
        $total=0;
        $no=1;
        @endphp         
        <br><br>
        <table width="80%" id="invoice">
            <thead style="text-align: center;">
                <tr >
                    <th width="10%">No</th>
                    <th width="40%">Jenis</th>
                    <th width="50%">Jumlah</th>
                </tr>
            </thead>
            <tbody style="font-size: 12px;">
                <tr>
                    <td style="text-align:center">1</td>
                    <td style="text-align:left">Bulanan</td>
                    <td style="text-align:right;">{{'Rp. '.number_format($payroll->bulanan, 2, ',', '.')}}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center">2</td>
                    <td style="text-align:left">Harian</td>
                    <td style="text-align:right;">{{'Rp. '.number_format($payroll->harian, 2, ',', '.')}}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center">3</td>
                    <td style="text-align:left">Borongan</td>
                    <td style="text-align:right;">{{'Rp. '.number_format($payroll->borongan, 2, ',', '.')}}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center">4</td>
                    <td style="text-align:left">Honorarium</td>
                    <td style="text-align:right;">{{'Rp. '.number_format($payroll->honorarium, 2, ',', '.')}}
                    </td>
                </tr>
            </tbody>     
        </table>
        @php
        $thp=$payroll->bulanan+$payroll->harian+$payroll->borongan+$payroll->honorarium;
        @endphp
        <table width="100%">
            <thead>
                <tr >
                    <th width="70%" style="text-align: left;">Jumlah Total pendapatan </th>
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
