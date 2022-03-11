@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function tambahDetailPekerjaBorongan(id){
        window.open(('{{ url("tambahDetailPekerjaBorongan") }}'+"/"+id), '_self');
    }
    function detailPekerjaBorongan(id){
        window.open(('{{ url("boronganWorkerList") }}'+"/"+id), '_blank');
    }

    function storeBorongan(){
        var modalNama = document.getElementById("modalNama").value;
        var modalTanggal = document.getElementById("modalTanggal").value;
        var modalKg = document.getElementById("modalKg").value;
        var modalNetweight = document.getElementById("modalNetweight").value;
        var modalWorker = document.getElementById("modalWorker").value;

        $.ajax({
            url: '{{ url("storeBorongan") }}',
            type: "POST",
            data: {
                "_token":"{{ csrf_token() }}",
                name : modalNama,
                tanggalKerja: modalTanggal,
                hargaSatuan: modalKg,
                netweight: modalNetweight,
                worker: modalWorker
            },
            dataType: "json",
            success:function(data){
                if(data.isError==="0"){
                    swal.fire('info',data.message,'info');
                    myFunction();
                }
                else{
                    swal.fire('warning',data.message,'warning');
                }
                $('#exampleModal').modal('hide');
            }
        });        
    }

    function presenceHistory(id){
        //window.open(('{{ url("presenceHistory") }}'+"/"+id), '_blank');
    }

    $(document).ready(function() {
    });
</script>

@if (session('status'))
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            {{ session('status') }}
        </div>
        <div class="col-md-1 text-center">
            <span class="label"><strong >x</strong></span>
        </div>
    </div>
</div>
@endif
<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-9">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Borongan - {{$borongan->name}} / {{$borongan->tanggalKerja}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="col-md-6">
                        <table style="width:100%" class="table table-striped table-hover table-bordered data-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Nama</th>
                                    <th width="15%">NIP</th>
                                    <th width="15%">Bagian</th>
                                    <th width="10%">Honor (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $a=1 @endphp
                                @foreach($query as $worker)
                                <tr>
                                    <td>@php echo $a @endphp</td>
                                    <td>{{$worker->nama}}</td>
                                    <td>{{$worker->nip}}</td>
                                    <td>{{$worker->osname}}</td>
                                    <td style="text-align: right;">{{$worker->netPayment}}</td>
                                </tr>
                                @php $a++ @endphp
                                @endforeach
                            </tbody>
                        </table>                
                    </div>
                </div>
            </div>       
        </div>
    </div>
</body>
@else
@include('partial.noAccess')
@endif

@endsection