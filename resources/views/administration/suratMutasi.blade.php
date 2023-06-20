<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<!doctype html>
<html lang="en">
<style type="text/css">
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
    td {
        vertical-align: top;
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
                <td width="30%" align="center" style="vertical-align:middle">
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
    <b>
        <table width="100%" style="margin-bottom: 0;">
            <tr>
                <td width="100%" align="center">SURAT KEPUTUSAN</td>
            </tr>
            <tr>
                <td width="100%" align="center">DIREKTUR PT. ANUGRAH LAUT INDONESIA</td>
            </tr>
            <tr>
                <td width="100%" align="center">No : {{$paperworkNum}}</td>
            </tr>
        </table>
        <br>
        <table width="100%" style="margin-bottom: 0;">
            <tr>
                <td width="100%" align="center">TENTANG</td>
            </tr>
            <tr>
                <td width="100%" align="center">MUTASI PEGAWAI</td>
            </tr>
        </table>
    </b>
    <br>
    <table width="100%" style="margin-bottom: 0;">
        <tr>
            <td width="25%" align="left"><b>Menimbang</b></td>
            <td width="5%" align="center"><b>:</b></td>
            <td width="75%" align="left" style="text-align:justify;">
                Bahwa berdasarkan kebutuhan kepegawaian yang ada pada PT. Anugrah Laut Indonesia,
            </td>
        </tr>
    </table>
    <br>
    <b>
        <table width="100%" style="margin-bottom: 0;">
            <tr>
                <td width="100%" align="center">MEMUTUSKAN</td>
            </tr>
        </table>
    </b>
    <br>

    @php
    $dateBerlaku = Carbon\Carbon::parse($data['tanggalBerlaku'])->locale('id');
    $dateBerlaku->settings(['formatFunction' => 'translatedFormat']);
    $tanggalBerlaku = $dateBerlaku->format('j F Y');

    $dateDitetapkan = Carbon\Carbon::now()->locale('id');
    $dateDitetapkan->settings(['formatFunction' => 'translatedFormat']);
    $tanggalDitetapkan = $dateDitetapkan->format('j F Y');
    @endphp


    <table width="100%" style="margin-bottom: 0;">
        <tr>
            <td width="25%" align="left"><b>Menetapkan</b></td>
            <td width="5%" align="center"><b>:</b></td>
            <td width="75%" align="left">
                <b>SURAT KEPUTUSAN DIREKTUR PT. ANUGRAH LAUT INDONESIA
                TENTANG MUTASI PEGAWAI</b>

            </td>
        </tr>
        <br>
        <tr>
            <td width="25%" align="left">KESATU</td>
            <td width="5%" align="center">:</td>
            <td width="75%" align="justif" style="text-align:justify;">
                Memutasikan Sdr. {{$data['nama']}}/NIP. {{$data['nip']}} dari posisinya sebagai {{$data['oldLevel']}} {{$data['oldJabatan']}} {{$data['oldBagian']}} menjadi {{$data['newLevel']}} {{$data['newJabatan']}} {{$data['newBagian']}}.
            </td>
        </tr>
        <tr>
            <td width="25%" align="left">KEDUA</td>
            <td width="5%" align="center">:</td>
            <td width="75%" align="left" style="text-align:justify;">
                Memberikan penghasilan yang berkaitan dengan posisi tersebut sesuai dengan ketentuan
                yang berlaku.
            </td>
        </tr>
        <tr>
            <td width="25%" align="left">KETIGA</td>
            <td width="5%" align="center">:</td>
            <td width="75%" align="left" style="text-align:justify;">
                Apabila terdapat kekeliruan pada keputusan ini maka akan dilakukan perubahan
                sebagaimana mestinya.
            </td>
        </tr>
        <tr>
            <td width="25%" align="left">KEEMPAT</td>
            <td width="5%" align="center">:</td>
            <td width="75%" align="left">
                Surat Keputusan ini berlaku sejak {{$tanggalBerlaku}}.
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table width="100%">
        <tr>
            <td width="60%" style="text-align: left;vertical-align: top;"></td>
            <td width="40%" style="text-align: left;vertical-align: top;">
                Ditetapkan di : Tuban
            </td>
        </tr>       
        <tr>
            <td width="60%" style="text-align: left;vertical-align: top;"></td>
            <td width="40%" style="text-align: left;vertical-align: top;">
                Pada Tanggal : {{ $tanggalDitetapkan }}
            </td>
        </tr>       
        <tr>
            <td width="60%" style="text-align: left;vertical-align: top;"></td>
            <td width="40%" style="text-align: left;vertical-align: top;">
                a.n PT. Anugrah Laut Indonesia
                <br><br>ttd<br><br>
                Aktaria Hidapratiwi
                <br>
                Direktur Utama
            </td>
        </tr>       
    </table>
    <br>
    <br>
    Document generated at : {{Carbon\Carbon::now()}}
</body>
</html>