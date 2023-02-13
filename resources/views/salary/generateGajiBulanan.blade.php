@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function validateProcess(){
        $date = null;
        $month = null;
        $year = null;
        $.get("{{ url("getServerDate") }}", function(data){
            $date = new Date(data);
            $month = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
            $monthPresent   = $date.getMonth();
            $monthBefore   = $monthPresent-1;
            
            $yearPresent = $date.getFullYear();
            $yearBefore = $date.getFullYear();
            if ($monthBefore==-1){
                $monthBefore==11;
                $yearBefore = $yearBefore-1;
            }

            $monthPresentName = $month[$monthPresent];
            $monthBeforeName = $month[$monthBefore];

            Swal.fire({
                title: 'Generate gaji bulan '+$monthPresentName+' '+$yearPresent+'?',
                text: "Perhitungan antara 21 "+$monthBeforeName+" "+$yearBefore+" hingga 20 "+$monthPresentName+" "+$yearPresent,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, generate saja!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Yakin generate gaji bulan '+$monthPresentName+' '+$yearPresent+'?',
                        text: "Yakin",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, generate saja!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ url("generateGajiBulananStore") }}',
                                type: "POST",
                                data: {
                                    "_token":"{{ csrf_token() }}",
                                    monthBefore   : $monthBefore+1,
                                    yearBefore    : $yearBefore,
                                    monthPresent   : $monthPresent+1,
                                    yearPresent    : $yearPresent
                                },
                                dataType: "json",
                                success:function(data){
                                    Swal.fire(
                                        'Berhasil digenerate!',
                                        data[0]+', '+data[1]+', '+data[2],
                                        'success'
                                        );
                                }
                            });
                        } else {
                            Swal.fire(
                                'Batal generate!',
                                "Generate gaji dibatalkan",
                                'info'
                                );
                        }
                    })

                } else {
                    Swal.fire(
                        'Batal generate!',
                        "Generate gaji dibatalkan",
                        'info'
                        );
                }
            })
        });
        return false;
    }
</script>
@if (Session::has('val'))
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            {{ Session::get('val')[0]}}<br>{{ Session::get('val')[1]}}<br>
        </div>
        <div class="col-md-1 text-end">
            <span class="label"><strong >x</strong></span>
        </div>
    </div>
</div>
@endif
@if ($errors->any())
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-1 text-center">
            <span class="label"><strong >x</strong></span>
        </div>
    </div>
</div>
@endif

<body class="container-fluid">
    <div class="modal-content">
        <div class="modal-header">
            <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Generate Gaji Bulanan</li>
                </ol>
            </nav>
        </div>
        <form method="POST" action="{{url('generateGajiBulananStore')}}" onsubmit="return validateProcess()">
            @csrf                
            <div class="modal-body" align="center">
                <button type="submit" class="btn btn-primary">Generate</button>
            </div>
        </form>
    </div>


    <ol>
        <li>Laman ini digunakan untuk melakukan proses generate gaji pegawai bulanan</li>
        <li>Tanggal batas akhir: Honorarium dan Lembur adalah hingga presensi tanggal 20 pada setiap bulannya</li>
        <li>Aplikasi akan melihat data presensi atau honorarium yang belum digenerate di tanggal-tanggal sebelumnya</li>
        <li>Pastikan data telah diinput ketika dilakukan Generate</li>
    </ol>
</body>
@endsection