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

    function myFunction(){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllEmployees") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "20%", "targets":  [1], "className": "text-left"   },
            {   "width": "15%", "targets":  [2], "className": "text-left"   },
            {   "width": "5%", "targets":   [3], "className": "text-center" },
            {   "width": "15%", "targets":  [4], "className": "text-left" },
            {   "width": "10%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-left" },
            {   "width": "5%",  "targets":  [7], "className": "text-center" },
            {   "width": "15%", "targets":  [8], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'username', name: 'username'},
            {data: 'gender', name: 'gender'},
            {data: 'phone', name: 'phone'},
            {data: 'jenisPenggajian', name: 'jenisPenggajian'},
            {data: 'accessLevel', name: 'accessLevel'},
            {data: 'statusKepegawaian', name: 'statusKepegawaian'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }
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



//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN
//HISTORY STATUS HARIAN BORONGAN BULANAN BELUM DISIMPAN

<body onload="myFunction()">
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Pegawai</li>
                    </ol>
                </nav>
                <button onclick="tambahTransaksi()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Pegawai"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Bagian</th>
                                <th>Penempatan</th>
                                <th>Gaji Pokok</th>
                                <th>Uang Harian</th>
                                <th>Uang Lembur</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>                
                </div>
            </div>    
        </div>
    </div>
</body>
@endsection