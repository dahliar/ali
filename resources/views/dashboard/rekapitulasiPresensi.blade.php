@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function employeePresenceHarianHistory(id){
        window.open(('{{ url("employeePresenceHarianHistory") }}'+"/"+id), '_blank');
    }
    function myFunction(){
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        var opsi = document.getElementById("opsi").value;
        $('#datatable').DataTable({
            ajax: '{{ url("getRekapitulasiPresensi") }}' + "/"+ start + "/"+ end + "/"+ opsi,
            type: 'get',
            pageLength: 100,
            serverSide: false,
            processing: true,
            deferRender: true,
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "15%", "targets":  [1], "className": "text-left"   },
            {   "width": "30%", "targets":  [2], "className": "text-left" },
            {   "width": "8%", "targets":  [3], "className": "text-center" },
            {   "width": "8%", "targets":  [4], "className": "text-end" },
            {   "width": "10%", "targets":  [5], "className": "text-end" },
            {   "width": "10%", "targets":  [6], "className": "text-end" },
            {   "width": "10%", "targets":  [7], "className": "text-end" },
            {   "width": "5%", "targets":  [8], "className": "text-end" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'jabatan', name: 'jabatan'},
            {data: 'jenis', name: 'jenis'},
            {data: 'hari', name: 'hari'},
            {data: 'jamKerja', name: 'jamKerja'},
            {data: 'jamLembur', name: 'jamLembur'},
            {data: 'totalJam', name: 'totalJam'},
            {data: 'action', name: 'action'},
            ]

        });
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
                            <li class="breadcrumb-item active">Daftar Kehadiran</li>
                        </ol>
                    </nav>
                </div>
            </div>            
            <div class="modal-body">
                <div class="row form-inline">
                    <div class="col-md-2">
                        <input type="date" id="start" name="start" class="form-control text-end" value="{{ date('Y-m-d', strtotime('-1 month')) }}" > 
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="end" name="end" class="form-control text-end" value="{{ date('Y-m-d') }}" >
                    </div>
                    <div class="col-md-2">
                        <select id="opsi" name="opsi" class="form-select" >
                            <option value="-1">All Pegawai</option>
                            <option value="1">Bulanan</option>
                            <option value="2">Harian</option>
                            <option value="3">Borongan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="hitButton" class="form-control btn-primary" onclick="myFunction()">Cari</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr style="text-align: center;">
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Hari</th>
                                <th>Jam Kerja</th>
                                <th>Jam Lembur</th>
                                <th>Total</th>
                                <th>Act</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>                
                </div>
            </div>  
            Laman ini akan menampilkan data 
            <ol>
                <li>Kehadiran dalam rentang tanggal terpilih</li>
            </ol>
        </div>
    </div>
</body>
@endsection