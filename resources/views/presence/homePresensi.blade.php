@extends('layouts.layout')
@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function presensiMasuk(){
        window.open(('{{ url("presenceHarianScanMasuk") }}'), '_self');
    }
    function presensiKeluar(){
        window.open(('{{ url("presenceHarianScanKeluar") }}'), '_self');
    }
</script>
<div class="container-fluid row">
    <div class="col-md-6 text-center">
        <div class="d-grid" style="height: 200px">
            <button onclick="presensiMasuk()" class="btn btn-primary btn-lg" type="button">Presensi Masuk</button>
        </div>
    </div>    
    <div class="col-md-6 text-center">
        <div class="d-grid" style="height: 200px">
            <button onclick="presensiKeluar()" class="btn btn-secondary btn-lg" type="button">Presensi Keluar</button>
        </div>
    </div>    
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<div class="container-fluid row">

    <div class="col-md-12">
        <form method="POST" action="{{ url('logout') }}">
            @csrf
            <a class="btn btn-info" href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}
            </a>
        </form> 
    </div>
</div>
@endsection