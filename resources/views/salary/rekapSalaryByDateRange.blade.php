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
    <title>Rekap Gaji {{$start}} - {{$end}}</title>
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
            Daftar gaji</h3>
            <h3 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                {{$start}} - {{$end}}
            </h3>
        </div>
        <table width="100%" id="invoice">
            <thead style="text-align: center;font-size:12px">
                <tr>
                    @if($opsi == 1)
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 13%;">Harian</th>
                    <th style="width: 13%;">Lembur</th>
                    <th style="width: 13%;">Borongan</th>
                    <th style="width: 13%;">Honorarium</th>
                    <th style="width: 15%;">Total</th>
                    @else
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama</th>
                    <th style="width: 13%;">Harian</th>
                    <th style="width: 13%;">Lembur</th>
                    <th style="width: 13%;">Borongan</th>
                    <th style="width: 13%;">Honorarium</th>
                    <th style="width: 15%;">Total</th>
                    @endif
                </tr>
            </thead>
            @if($opsi == 1)
            <tbody style="font-size:10px">
                @php 
                $no=1; 
                $total=0;
                @endphp

                @foreach($third as $data)
                <tr>
                    <td style="text-align: center;">{{$no}}</td>
                    <td style="text-align: left;">{{$data->nama}}</td>
                    <td style="text-align: right;">{{$data->tanggal}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->uh, 2, ',', '.')}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->ul, 2, ',', '.')}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->borongan, 2, ',', '.')}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->honorarium, 2, ',', '.')}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->total, 2, ',', '.')}}</td>
                </tr>
                @php 
                $no=$no+1;
                $total=$total+$data->total;
                @endphp
                @endforeach
            </tbody>
            <tfoot style="font-size:10px">
                <tr>
                    <td colspan="7" style="text-align: left;"></td>
                    <td style="text-align: right;">Rp. {{number_format($total, 2, ',', '.')}}</td>
                </tr>
            </tfoot>
            @else

            <tbody style="font-size:10px">
                @php 
                $no=1; 
                $total=0;
                @endphp

                @foreach($third as $data)
                <tr>
                    <td style="text-align: center;">{{$no}}</td>
                    <td style="text-align: left;">{{$data->nama}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->uh, 2, ',', '.')}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->ul, 2, ',', '.')}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->borongan, 2, ',', '.')}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->honorarium, 2, ',', '.')}}</td>
                    <td style="text-align: right;">Rp. {{number_format($data->total, 2, ',', '.')}}</td>
                </tr>
                @php 
                $no=$no+1;
                $total=$total+$data->total;
                @endphp
                @endforeach
            </tbody>
            <tfoot style="font-size:10px">
                <tr>
                    <td colspan="6" style="text-align: left;"></td>
                    <td style="text-align: right;">Rp. {{number_format($total, 2, ',', '.')}}</td>
                </tr>
            </tfoot>
            @endif
        </table>          
    </main>
</body>
</html>