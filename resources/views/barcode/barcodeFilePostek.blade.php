<!doctype html>
<html lang="en">


<style type="text/css">
    @page {
        margin:15px;
        size: 10cm 5cm landscape;
    }
</style>

<style type="text/css">
    body { 
        margin: 0px; 
    }
    td {
        border: 0px solid;
        text-align: left;
        vertical-align: middle;
    }
    table.center {
        margin-left: auto; 
        margin-right: auto;
    }
</style>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <main>
        <table width="100%" >
            @foreach ($arrData as $a)
            <tr>
                <td style="width:40%;">
                    <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($a['fullname'], 'QRCODE',2.5,2.5)}}"/>
                </td>
                <td style="width:60%;">
                    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($a['barcode'], 'C128',1,30)}}"/>
                    <p style="font-size:12px"> {!! $a['barcode']!!}</p>
                    <p style="font-size:12px"> {!! $name !!}</p>
                </td>
            </tr>
            @endforeach
            <!--
                DNS1D::getBarcodeHTML(data, jenis, lebar, tinggi, warna)
            -->
        </table>
    </main>
</body>
</html>