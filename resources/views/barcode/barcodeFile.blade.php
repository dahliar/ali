<!doctype html>
<html lang="en">

<style type="text/css">
    @page {
        margin:15px;
        size: 10cm 20cm portrait;
    }
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
                <td style="width:40%;" ROWSPAN="2">
                    <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($a['fullname'], 'QRCODE',3,3)}}"/>
                </td>
                <td style="width:60%;">
                    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($a['barcode'], 'C128',1,30)}}"/>
                </td>
            </tr>
            <tr>
                <td> <p style="font-size:20px"> {!! $a['barcode']!!}</p></td>
            </tr>
            <br>
            @endforeach
            <!--
                DNS1D::getBarcodeHTML(data, jenis, lebar, tinggi, warna)
            -->
        </table>
    </main>
</body>
</html>