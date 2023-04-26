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

<meta name="csrf-token" content="{{ csrf_token() }}" />

<script type="text/javascript">
    function approveStore(id){
        Swal.fire({
            title: 'Setuju perubahan standar gaji',
            text: "Approve?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, approve saja!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("approveStandarChange") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        bshId         : id,
                        approveStore    : "1"
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Done!',
                            'Pengajuan perubahan diterima',
                            'success'
                            );
                        myFunction();
                    }
                });
            } else {
                Swal.fire(
                    'Batal!',
                    "Approval dibatalkan",
                    'warning'
                    );
            }
        })
    }
    function tolakStore(id){
        Swal.fire({
            title: 'Penolakan perubahan standar gaji',
            text: "Tolak?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, tolak saja!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("approveStandarChange") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        bshId   : id,
                        approveStore    : "2"
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Ditolak!',
                            'Pengajuan perubahan ditolak',
                            'success'
                            );
                        myFunction();
                    }
                });
            } else {
                Swal.fire(
                    'Batal!',
                    "Approval dibatalkan",
                    'warning'
                    );
            }
        })
    }
    function myFunction(){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax: '{{ url("getStandarBoronganApproval") }}',
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets": [0], "className": "text-center" },
                {   "width": "20%", "targets":  [1], "className": "text-left" },
                {   "width": "10%", "targets":  [2], "className": "text-center" },
                {   "width": "10%", "targets":  [3], "className": "text-center" },
                {   "width": "10%", "targets":  [4], "className": "text-center" },
                {   "width": "15%", "targets":  [5], "className": "text-center" },
                {   "width": "15%", "targets":  [6], "className": "text-left" },
                {   "width": "15%", "targets":  [7], "className": "text-center" }
                ], 

            columns: [
                {data: 'id', name: 'id'},
                {data: 'nama', name: 'nama'},
                {data: 'harga', name: 'harga'},
                {data: 'status', name: 'status'},
                {data: 'jenisRecord', name: 'jenisRecord'},
                {data: 'oleh', name: 'oleh'},
                {data: 'pada', name: 'pada'},
                {data: 'action', name: 'action'},
                ]
        });

    }
    $(document).ready(function () {
        myFunction();
    });
</script>
@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data berhasil ditambahkan", "info");
</script>
@endif

<body class="container-fluid">
    <div class="modal-content">
        <div class="modal-header">
            <div class="col-md-6 text-end">
                <nav aria-label="breadcrumb" class="navbar navbar-expand-xs navbar-light">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Pengaturan Standar Honor Borongan</li>
                    </ol>
                </nav>
            </div>
        </div>
        @csrf
        <div class="modal-body">
            <div class="row form-inline">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Harga/Kg</th>
                            <th>Status</th>
                            <th>Approval</th>
                            <th>Oleh</th>
                            <th>Tanggal</th>
                            <th>Act</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>                
            </div>
        </div>  
    </div>
</body>
@endsection