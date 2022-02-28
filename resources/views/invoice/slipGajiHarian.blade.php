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
        <title>Slip Gaji Harian - {{$salary->endDate}}</title>
    </head>

    <body>
        <header>
            <table width="90%" style="margin-bottom: 0;">
                <tr>
                    <td width="30%" align="center">
                        <img src="{{ asset('/images/ali-logo.png') }}" alt="Logo" width="120" class="logo"/>
                    </td>
                    <td width="70%" style="text-align: center; vertical-align: top;">
                        <h3 align="left">
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
            <table width="90%" style="margin-bottom: 0;">
                <tr>
                    <td width="30%" align="center">
                        <img src="{{ asset('/images/ali-logo.png') }}" alt="Logo" width="120" class="logo"/>
                    </td>
                    <td width="70%" style="text-align: left; vertical-align: top;">
                        <h3 align="left">
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
            <div>
                <h1 align="center" style="margin-top: 0; margin-bottom: 0;">
                Slip Gaji Harian </h1>
                <h3 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                    Tanggal : {{$salary->endDate}}
                </h3>
            </div>
            <table width="40%">
                <tr>
                    <td><span class="label"><b>Jenis Karyawan : </b></span></td>
                    <td>: Harian</td>
                </tr>
                <tr>
                    <td><span class="label"><b>Tanggal Akhir Generate : </b></span></td>
                    <td>: {{$salary->endDate}}</td>
                </tr>
                <tr>
                    <td><span class="label"><b>Tanggal Bayar : </b></span></td>
                    <td>: {{$salary->tanggalBayar}}</td>
                </tr>
            </table>
            <br>
            <br>

            <table width="100%" id="invoice">
                <thead style="text-align: center;">
                    <tr >
                        <th width="5%">No</th>
                        <th width="25%">Nama Pegawai</th>
                        <th width="10%">NIP</th>
                        <th width="15%">Bagian</th>
                        <th width="15%">Gaji</th>
                        <th width="15%">Lembur</th>
                        <th width="15%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $total=0;
                    $no=1;
                    @endphp
                    @foreach ($dailysalaries as $daily)
                    <tr >
                        <td width="5%">{{$no}}</td>
                        <td width="25%">{{$daily->name}}</td>
                        <td width="15%">{{$daily->nip}}</td>
                        <td width="15%">{{$daily->osname}}</td>
                        <td width="15%" style="text-align: right;">{{$daily->uh}}</td>
                        <td width="15%" style="text-align: right;">{{$daily->ul}}</td>
                        <td width="15%" style="text-align: right;">{{$daily->total}}</td>
                    </tr>
                    @php
                    $total+=$daily->total;
                    $no+=1;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr >
                        <td colspan="6">Total</td>
                        <td width="15%" style="text-align: right;">{{$total}}</td>
                    </tr>
                </tfoot>        
            </table>

            <table width="100%">
                <tr>
                    <td width="40%" style="text-align: center;vertical-align: top;">Tuban, {{ date('Y-m-d') }}
                    </td>
                </tr>       
                <tr>
                    <td width="40%" style="text-align: center;vertical-align: top;">Pembuat Daftar
                        <br><br><br><br><br><br>
                        {{$generatorName->name}}
                    </td>
                    <td width="40%" style="text-align: center;vertical-align: top;">Pembayar
                        <br><br><br><br><br><br>
                        {{$payerName->name}}
                    </td>
                </tr>       
            </table>
        </main>
    </body>
    </html>
    @endif