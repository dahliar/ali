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

    function tambahHariLibur(){
        $('#cutiHolidayDateAdd').modal('show');
    }
    function hapus(id){
        Swal.fire({
            title: 'Hapus tanggal libur?',
            text: "Penghapusan tanggal libur.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("cutiHolidayDateDestroy") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        id : id
                    },
                    dataType: "json",
                    success:function(data){
                        if(data){
                            Swal.fire({
                                title: 'Data tanggal libur dihapus.',
                                text: "penghapusan tanggal libur.",
                                icon: 'info',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok dihapus.'
                            }).then((result) => {
                                myFunction();
                            })
                        }
                        else{
                            swal.fire('warning',data.message,'warning');
                        }
                    }
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Penambahan tanggal libur dibatalkan",
                    'info'
                    );
            }
        })
    };
    function simpanFunction(){
        var name = document.getElementById("namaLibur").value;
        var tanggal = document.getElementById("tanggal").value;
        Swal.fire({
            title: 'Simpan tanggal libur?',
            text: "Tambah tanggal hari libur.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("cutiHolidayDateAdddayDateStore") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        name : name,
                        dateActive: tanggal
                    },
                    dataType: "json",
                    success:function(data){
                        $('#cutiHolidayDateAdd').modal('hide');
                        if(data.isError==0){
                            Swal.fire({
                                title: 'Data tanggal libur disimpan.',
                                text: "Tambah tanggal hari libur.",
                                icon: 'info',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok disimpan.'
                            }).then((result) => {
                                myFunction();
                            })
                        }
                        else{
                            swal.fire('warning',data.message,'warning');
                        }
                    }
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Penambahan tanggal libur dibatalkan",
                    'info'
                    );
            }
        })
    };
    function myFunction(){
        $('#datatable').DataTable({
            ajax:'{{ url("getAllHolidays") }}',
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "10%",  "targets":  [0], "className": "text-center" },
                {   "width": "60%", "targets":  [1], "className": "text-left"   },
                {   "width": "20%", "targets":  [2], "className": "text-left"   },
                {   "width": "10%", "targets":  [3], "className": "text-center"   }
                ], 
            columns: [
                {data: 'SrNo',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {data: 'name', name: 'name'},
            {data: 'dateActive', name: 'dateActive'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }

    $(document).ready(function() {
        myFunction();
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
                <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/cuti') }}">Cuti</a>
                        </li>
                        <li class="breadcrumb-item active">Daftar hari libur</li>
                    </ol>
                </nav>
                <button onclick="tambahHariLibur()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah hari libur"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>

            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
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
<div class="modal fade" id="cutiHolidayDateAdd" tabindex="-1" aria-labelledby="cutiHolidayDateAdd" aria-hidden="true">
    <form id="formTambahTanggalLibur" method="POST" name="formTambahTanggalLibur">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeePresenceHarianModal">Tambah tanggal libur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Nama</span>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="namaLibur" name="namaLibur" class="form-control">
                        </div>
                    </div>                    
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Masuk</span>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="tanggal" name="tanggal" class="form-control text-end">
                        </div>
                    </div>   

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="simpanFunction()">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection