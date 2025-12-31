@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js" type="text/javascript" ></script>


<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function cetakSlipPersonal(id, tahun, bulan){
        window.open(('{{ url("slipGaji/slipGajiBulanan") }}'+"/"+id+"/"+tahun+"/"+bulan), '_blank');
    };

    function cetak(){
        $bulanTahun = document.getElementById("bulanTahun").value;
        
        if ($bulanTahun=="") {
            Swal.fire(
                'Pilihan kosong!',
                "Pilih data dulu",
                'warning'
                );
        } else {
            openWindowWithPost('{{ url("cetakRekapGajiBulanan") }}', {
                '_token': "{{ csrf_token() }}" ,
                bulanTahun: $bulanTahun
            });
        }
    }


    function openWindowWithPost(url, data) {
        var form = document.createElement("form");
        form.target = "_blank";
        form.method = "POST";
        form.action = url;
        form.style.display = "none";

        for (var key in data) {
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = key;
            input.value = data[key];
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }


    function myFunction(){
        var bulanTahun = document.getElementById("bulanTahun").value;
        if (bulanTahun===""){
            Swal.fire(
                'Pilihan kosong!',
                "Pilih data dulu",
                'warning'
                );  
        }
        else
        {
            $('#datatable').DataTable({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                ajax:{
                    url: '{{ url("getDataRekapitulasiGajiPerBulan") }}',
                    data: function (d){
                        d.bulanTahun = bulanTahun
                    }
                },
                dataType: 'json',            
                serverSide: false,
                processing: true,
                deferRender: true,
                type: 'GET',
                destroy:true,
                columnDefs: [
                    {   "width": "2%", "targets": [0], "className": "text-left"   },
                    {   "width": "18%", "targets": [1], "className": "text-left" },
                    {   "width": "10%", "targets": [2], "className": "text-left" },
                    {   "width": "10%", "targets": [3], "className": "text-left" },
                    {   "width": "10%", "targets": [4], "className": "text-end"   },
                    {   "width": "10%", "targets": [5], "className": "text-end" },
                    {   "width": "10%", "targets": [6], "className": "text-end" },
                    {   "width": "10%", "targets": [7], "className": "text-end" },
                    {   "width": "10%", "targets": [8], "className": "text-end"   },
                    {   "width": "10%", "targets": [9], "className": "text-center"   }
                ], 
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'nik', name: 'nik'},
                    {data: 'slipid', name: 'slipid', orderable: false, searchable: false},
                    {data: 'bulanan', name: 'bulanan'},
                    {data: 'harian', name: 'harian'},
                    {data: 'borongan', name: 'borongan'},
                    {data: 'honorarium', name: 'honorarium', orderable: false, searchable: false},
                    {data: 'total', name: 'total'},
                    {data: 'action', name: 'action'},
                ]
            });
        }
    }

</script>
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

<body>
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Informasi penggajian per bulan</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card card-header">
            <div class="row form-group">
                <div class="col-md-2">
                    <input type="month" name="bulanTahun" id="bulanTahun" class="form-control" max="{{date('Y-m')}}">
                </div>
                <div class="col-md-8">
                    <button onclick="myFunction()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Filter"><i class="fas fa-search">  Cari</i>
                    </button>
                    <button onclick="cetak()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Filter"><i class="fas fa-print">  Cetak</i>
                    </button>
                </div>               
            </div>
        </div>
        <div class="card card-body">
            <div class="row form-group">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr style="font-size: 12px;">
                            <th style="width: 2%;text-align: center;">No</th>
                            <th style="width: 18%;text-align: center">Nama</th>
                            <th style="width: 10%;text-align: center">NIK</th>
                            <th style="width: 10%;text-align: center">No Slip</th>
                            <th style="width: 10%;text-align: center">Bulanan</th>
                            <th style="width: 10%;text-align: center">Harian</th>
                            <th style="width: 10%;text-align: center">Borongan</th>
                            <th style="width: 10%;text-align: center">Honorarium</th>
                            <th style="width: 10%;text-align: center">Total</th>
                            <th style="width: 10%;text-align: center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:14px">
                    </tbody>
                </table>     
            </div>
        </div>
        Laman ini akan menampilkan data 
        <ol>
            <li>Dalam rentang bulan terpilih</li>
            <li>Data yang dihitung adalah data antara tanggal 1-akhir bulan</li>
        </ol>
    </div>
</body>
@endsection