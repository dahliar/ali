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

    function setHonorariumIsPaid($hid, $sid){
        Swal.fire({
            title: 'Yakin menandai?',
            text: "Data ditandai sudah dibayar!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, mark it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("honorariumMarkedPaid") }}',
                    type: "GET",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        hid : $hid,
                        sid : $sid
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Marked!',
                            'Record honorarium telah ditandai dibayar.',
                            'success'
                            );
                        myFunction();
                    }
                });
            }
        })
    }

    function myFunction(){
        salary = document.getElementById("salaryId").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getSalariesHonorariumForCheck") }}'+"/"+salary,
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "15%", "targets":  [1], "className": "text-left" },
                {   "width": "10%", "targets":  [2], "className": "text-left" },
                {   "width": "10%", "targets":  [3], "className": "text-left" },
                {   "width": "10%", "targets":  [4], "className": "text-left" },
                {   "width": "15%", "targets":  [5], "className": "text-left" },
                {   "width": "5%", "targets":  [6], "className": "text-left" },
                {   "width": "10%", "targets":  [7], "className": "text-end" },
                {   "width": "5%", "targets":  [8], "className": "text-end" }
                ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'nip', name: 'nip'},
                {data: 'osname', name: 'osname'},
                {data: 'noRekening', name: 'noRekening'},
                {data: 'bankName', name: 'bankName'},
                {data: 'isPaid', name: 'isPaid'},
                {data: 'jumlah', name: 'jumlah'},
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
                <div class="col-md-9">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Slip Gaji Pegawai</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{url('printSalaryHonorariumList')}}/{{$salary->id}}" target="_blank" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak dokumen gaji">Cetak daftar honorarium
                    </a>
                </div>
            </div>

            <div class="modal-body">
                <input type="hidden" value="{{$salary->id}}"id="salaryId" name="salaryId" class="form-control" readonly>
                <div class="row form-inline">
                    <div class="col-md-2">Jenis
                    </div>
                    <div class="col-md-6"> 
                        <input type="text" value="Honorarium" class="form-control" disabled>
                    </div>
                </div>
                <div class="row form-inline">
                    <div class="col-md-2">Rentang
                    </div>
                    <div class="col-md-6"> 
                        <input type="text" value="{{$salary->startDate}} - {{$salary->endDate}}" class="form-control" disabled>
                    </div>
                </div>
                <div class="row form-inline">
                    <div class="col-md-2">Generator
                    </div>
                    <div class="col-md-6"> 
                        <input type="text" value="{{$salary->userIdGenerator}}" class="form-control" disabled>
                    </div>
                </div>
                <div class="row form-inline">
                    <div class="col-md-2">Sudah Dibayar
                    </div>
                    <div class="col-md-6"> 
                        <input type="text" value="{{$salary->isPaid}}" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>Posisi</th>
                                <th>No Rekening</th>
                                <th>Bank</th>
                                <th>Bayar</th>
                                <th>Jumlah</th>
                                <th>Action</th>
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