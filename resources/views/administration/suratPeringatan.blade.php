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
        top: -150px;
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
    <title>Surat Keterangan Bekerja</title>
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
    <div class="row form-group">
        <div>
            <h1 align="center" style="margin-top: 0; margin-bottom: 0;">
                @if($num == 1)
                Surat Peringatan Pertama
                @elseif($num == 2)
                Surat Peringatan Kedua
                @else
                Surat Pemberhentian Kerja
                @endif

            </h1>
            <h3 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                No : {{$paperworkNum}}
            </h3>
        </div>
    </div>

    @if($num <= 2)
    <table width="100%">
        <tr>
            <td>
                Surat peringatan ini dibuat oleh perusahaan ditujukan kepada :
            </td>
        </tr>
    </table>
    @else
    <table width="100%">
        <tr>
            <td>
                Surat pemberhentian ini dibuat oleh perusahaan ditujukan kepada :    
            </td>
        </tr>
    </table>
    @endif
    <br>
    <table width="100%">
        <tr>
            <td style="width:5%"></td>
            <td style="width:25%"><b>Nama</b></td>
            <td style="width:5%">:</td>
            <td style="width:60%">{{$name}}</td>
            <td style="width:5%"></td>
        </tr>
        <tr>
            <td></td>
            <td><b>NIP</b></td>
            <td>:</td>
            <td>{{$nip}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b>Level</b></td>
            <td>:</td>
            <td>{{$jabatan}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b>Posisi</b></td>
            <td>:</td>
            <td>{{$orgStructure}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b>Bagian</b></td>
            <td>:</td>
            <td>{{$workPosition}}</td>
            <td></td>
        </tr>
    </table>
    <br>
    @if($num == 1)
    <table width="100%">
        <tr>
            <td style="word-break: break-all;">
                Surat peringatan ini dibuat berdasarkan atas tindakan yang telah saudara perbuat, yaitu
                <br>
                <br>
                {{$reason}}
                <br>
                <br>
                Oleh karena itu perusahaan memberikan surat peringatan/teguran ini, dengan tujuan agar saudara tidak mengulangi kesalahan yang sama yang dapat merugikan perusahaan. Jika saudara masih mengulangi kesalahan yang sama, maka perusahaan berhak mengambil keputusan terkait dengan status kepegawaian saudara.
                <br>
                <br>
                Demikian surat peringatan ini dibuat dengan sebenarnya.
            </td>
        </tr>
    </table>
    @elseif($num == 2)
    <table width="100%">
        <tr>
            <td style="word-break: break-all;">
                Surat peringatan ini dibuat berdasarkan atas tindakan yang telah saudara perbuat, yaitu
                <br>
                <br>
                {{$reason}}
                <br>
                <br>
                Skorsing hingga tanggal {{$skorsingTanggal}}
                <br>
                <br>
                Denda berupa {{$skorsingDenda}}
                <br>
                <br>
                Oleh karena itu perusahaan memberikan surat peringatan/teguran ini, dengan tujuan agar saudara tidak mengulangi kesalahan yang sama yang dapat merugikan perusahaan. Jika saudara masih mengulangi kesalahan yang sama, maka perusahaan berhak mengambil keputusan terkait dengan status kepegawaian saudara.
                <br>
                <br>
                Demikian surat peringatan ini dibuat dengan sebenarnya.
            </td>
        </tr>
    </table>
    @else
    <table width="100%">
        <tr>
            <td style="width:10%"></td>
            <td style="word-break: break-all;">
                Surat peringatan ketiga atau Pemutusan Hubungan Kerja (PHK) ini diterbitkan karena perusahaan tidak dapat menerima alasan Saudara terkait dengan masalah
                <br>
                <br>
                {{$reason}}
                <br>
                <br>
                Demikian Surat Pemberhentian Hubungan Kerja ini dibuat. PT. Anugrah Laut Indonesia mengucapkan terimakasih atas sumbangsih yang telah diberikan kepada saudara selama bergabung dengan kami.
                <br>
                <br>
                Demikian surat pemberhentian hubungan kerja ini dibuat dengan sebenarnya.
            </td>
            <td style="width:10%"></td>
        </tr>
    </table>
    @endif
    <br>
    <br>
    <table width="100%">
        <tr>
            <td width="40%" style="text-align: left;vertical-align: top;">
                Tuban, {{ Carbon\Carbon::now()->toDateString()}}
                <br>
                Pembuat,
                <br><br>ttd<br><br>
                <br>
                Bagian Sumber Daya Manusia
            </td>
            <td width="40%" style="text-align: left;vertical-align: top;">
                <br>
                Mengetahui,
                <br><br>ttd<br><br><br>
                Aktaria Hidapratiwi
                <br>
                Direktur Utama PT. Anugrah Laut Indonesia
            </td>
        </tr>       
    </table>
    <br>
    <br>
    Document generated at : {{Carbon\Carbon::now()}}
</body>
</html>