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

    function approval(leaveId, status){
        var text = "";
        if (status == 1){
            text = "Setujui";
        } else{
            text = "Tolak";
        }
        Swal.fire({
            title: text+' cuti pegawai?',
            text: "Ubah status pengajuan cuti pegawai.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: text+' cuti pegawai.',
                    text: "Ubah status pengajuan cuti pegawai.",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    $.ajax({
                        url: '{{ url("cutiUpdate") }}',
                        type: "POST",
                        data: {
                            "_token":"{{ csrf_token() }}",
                            id : leaveId,
                            status : status
                        },
                        dataType: "json",
                        success:function(data){
                            if(data){
                                myFunction();
                            }
                            else{
                                swal.fire('warning, some Error contact administrator',data.message,'warning');
                            }
                        }
                    });
                })
            } else {
                Swal.fire(
                    'Batal!',
                    "Ubah status pengajuan cuti pegawai dibatalkan.",
                    'info'
                    );
            }
        })
    }

    function myFunction(){
        var id = {{$employee->employeeId}};
        $('#datatable').DataTable({
            ajax:'{{ url("getAllEmployeeLeaveHistory") }}'+"/"+id,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "10%", "targets":  [1], "className": "text-center"   },
                {   "width": "10%", "targets":  [2], "className": "text-center"   },
                {   "width": "5%", "targets":   [3], "className": "text-center" },
                {   "width": "25%", "targets":  [4], "className": "text-left" },
                {   "width": "25%", "targets":  [5], "className": "text-left" },
                {   "width": "10%", "targets":  [6], "className": "text-center" },
                {   "width": "10%", "targets":  [7], "className": "text-center" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'startDate', name: 'startDate'},
                {data: 'endDate', name: 'endDate'},
                {data: 'jumlahHari', name: 'jumlahHari'},
                {data: 'alasan', name: 'alasan'},
                {data: 'alamat', name: 'alamat'},
                {data: 'statusApprove', name: 'statusApprove'},
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
                        <li class="breadcrumb-item active">Arsip cuti pegawai</li>
                    </ol>
                </nav>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Awal</th>
                                <th>Akhir</th>
                                <th>Hari</th>
                                <th>Alasan</th>
                                <th>Alamat</th>
                                <th>Status</th>
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