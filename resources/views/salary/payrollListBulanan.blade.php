@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js" type="text/javascript" ></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function printPayrollList($payrollId){
        window.open(('{{ url("printPayrollListBulanan") }}' + "/"+ $payrollId), '_blank');
    }
    function deletePayrollList($payrollId){
        Swal.fire({
            title: 'Hapus catatan penggajian?',
            text: "Tindakan ini akan menghapus data penggajian, dan tidak bsa dikembalikan lagi",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Yakin hapus?',
                    text: "Data tidak bisa dikembalikan",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus saja.'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Penghapusan akan dilakukan',
                            text: "Penghapusan gaji bulanan",
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok dihapus.'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: '{{ url("ubahStatusPenggajianHarian") }}',
                                    type: "POST",
                                    data: {
                                        "_token":"{{ csrf_token() }}",
                                        payrollId : $payrollId
                                    },
                                    dataType: "json",
                                    success:function(data){
                                        Swal.fire({
                                            title: 'Penggajian harian/borongan/honorarium telah dilakukan',
                                            text: "Data tidak bisa dikembalikan",
                                            icon: 'warning',
                                            showCancelButton: false,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Ok'
                                        });
                                        showPayrolList();
                                    }
                                });

                                
                            }
                        })
                    } else {
                        Swal.fire(
                            'Batal dihapus!',
                            "Penghapusan gaji harian/borongan/honorarium dibatalkan",
                            'info'
                            );
                    }
                })
            } else {
                Swal.fire(
                    'Batal dihapus!',
                    "Penghapusan gaji harian/borongan/honorarium dibatalkan",
                    'info'
                    );
            }
        })
    }

    function showPayrolList(){
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax: '{{ url("getPayrollListBulanan") }}'+"/"+start+"/"+end,
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "10%", "targets":  [1], "className": "text-left" },
                {   "width": "10%", "targets":  [2], "className": "text-left" },
                {   "width": "10%", "targets":  [3], "className": "text-left" },
                {   "width": "15%", "targets":  [4], "className": "text-end" },
                {   "width": "15%", "targets":  [5], "className": "text-end" },
                {   "width": "15%", "targets":  [6], "className": "text-end" },
                {   "width": "15%", "targets":  [7], "className": "text-end" },
                {   "width": "5%", "targets":  [8], "className": "text-left" }
                ], 

            columns: [
                {data: 'DT_RowIndex',   name: 'DT_RowIndex'},
                {data: 'payDate',       name: 'payDate'},
                {data: 'generator',     name: 'generator'},
                {data: 'totalPegawai',  name: 'totalPegawai'},
                {data: 'harian',        name: 'harian'},
                {data: 'bulanan',      name: 'bulanan'},
                {data: 'honorarium',    name: 'honorarium'},
                {data: 'totalBayar',    name: 'totalBayar'},
                {data: 'action',        name: 'action', orderable: false, searchable: false}
                ]
        });
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

<body class="container-fluid">
    <div class="modal-content">
        <div class="modal-header">
            <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Payroll List</li>
                </ol>
            </nav>
        </div>
        @csrf
        <div class="modal-body">
            <div class="row form-group">
                <div class="col-md-2">Batas Tanggal
                </div>
                <div class="col-md-3">
                    <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', $start)}}" > 
                </div>
                <div class="col-md-3">
                    <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', $end)}}" >
                </div>
                <div class="col-md-2">
                    <button type="button" onclick="showPayrolList()" class="btn btn-primary">Tampilkan</button>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="row form-inline">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Generator</th>
                            <th>Jumlah</th>
                            <th>Harian</th>
                            <th>Bulanan</th>
                            <th>Honorarium</th>
                            <th>Total</th>
                            <th>Act</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>                
            </div>
        </div>  
    </div>


    <ol>
        <li>Laman ini digunakan untuk melihat daftar gaji yang harus dibayarkan</li>
        <li>Ketika dilakukan </li>
    </ol>
</body>
@endsection