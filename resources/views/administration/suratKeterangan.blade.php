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
            Surat Keterangan Bekerja</h1>
            <h3 align="center"  style="margin-top: 0; margin-bottom: 10px;">
                No : 
            </h3>
        </div>
    </div>
    <table width="100%">
        <tr>
            <td>
                Kami yang bertanda tangan dibawah ini, menerangkan bahwa :
            </td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td style="width:5%"></td>
            <td style="width:25%"><b>Nama</b></td>
            <td style="width:5%">:</td>
            <td style="width:60%">{{$employee->name}}</td>
            <td style="width:5%"></td>
        </tr>
        <tr>
            <td></td>
            <td><b>NIK</b></td>
            <td>:</td>
            <td>{{$employee->nik}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b>NIP</b></td>
            <td>:</td>
            <td>{{$employee->nip}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b>Level</b></td>
            <td>:</td>
            <td>{{$employee->structuralPosition}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b>Posisi</b></td>
            <td>:</td>
            <td>{{$employee->orgstructure}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b>Bagian</b></td>
            <td>:</td>
            <td>{{$employee->workPosition}}</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b>Alamat</b></td>
            <td>:</td>
            <td>{{$employee->address}}</td>
            <td></td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td style="word-break: break-all;">
                adalah benar merupakan karyawan dari PT. Anugrah Laut Indonesia, yang telah bekerja mulai dari tanggal {{$employee->startdate}} sampai dengan sekarang. Selama bekerja, yang bersangkutan memiliki kinerja yang baik dan bertanggung jawab, serta selalu menjaga nama baik dari PT. Anugrah Laut Indonesia.
                <br>
                <br>
                Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table width="100%">
        <tr>
            <td width="40%" style="text-align: left;vertical-align: top;">
                Tuban, {{ Carbon\Carbon::now()->toDateString()}}
                <br><br>ttd<br><br>
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