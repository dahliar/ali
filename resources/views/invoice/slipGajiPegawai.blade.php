@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 2)
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
                                Office : Jl. Kahuripan Terrace III no. 30, Kahuripan Nirwana, Desa Sumput, Kecamatan Sidoarjo, Kabupaten Sidoarjo, Provinsi Jawa Timur, Indonesia
                            </div>
                            <div style="text-align: justify;">
                                Factory : Jl. Raya Rembang - Tuban KM 40, Desa Bancar, Kecamatan Bancar, Kabupaten Tuban, Provinsi Jawa Timur, Indonesia
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
                        <span class="label" id="spanLabel"><b>NIK</b></span>
                    </td>
                    <td style="text-align: center;">:</td>
                    <td>
                        {{$employee->nik}}
                    </td>
                </tr>
            </table>
        </main>
    </body>
    </html>
    @endif