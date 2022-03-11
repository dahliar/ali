@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 2)
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function hapusGenerateBorongan($sid){
        Swal.fire({
            title: 'Yakin menghapus?',
            text: "Menghapus record generate gaji borongan",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("hapusGenerateBorongan") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        sid : $sid
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Deleted!',
                            "Data generate gaji borongan telah dihapus",
                            'success'
                            );
                        myFunction();
                    }
                });
            }
        })
    }


    function myFunction($salaryId){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax: '{{ url("getSalariesList") }}'+"/"+$salaryId,
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "20%", "targets":  [1], "className": "text-left" },
            {   "width": "10%", "targets":  [2], "className": "text-left" },
            {   "width": "20%", "targets":  [3], "className": "text-left" },
            {   "width": "10%", "targets":  [4], "className": "text-left" },
            {   "width": "15%", "targets":  [5], "className": "text-left" },
            {   "width": "15%", "targets":  [6], "className": "text-left" },
            {   "width": "15%", "targets":  [7], "className": "text-left" },
            {   "width": "15%", "targets":  [8], "className": "text-left" },
            ], 

            columns: [
            {data: 'DT_RowIndex',   name: 'DT_RowIndex'},
            {data: 'jenisSalary',   name: 'jenisSalary'},
            {data: 'startdate',     name: 'startdate'},
            {data: 'enddate',       name: 'enddate'},
            {data: 'generator',     name: 'generator'},
            {data: 'total',         name: 'total'},
            {data: 'isPaid',        name: 'isPaid'},
            {data: 'idp',        name: 'idp'},
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

<body class="container-fluid" onload="myFunction({{$salaryId}})">
    <div class="modal-content">
        <div class="modal-header">
            <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                <ol class="breadcrumb primary-color">
                    <li class="breadcrumb-item">
                        <a class="white-text" href="{{ url('/home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Daftar Gaji</li>
                </ol>
            </nav>
        </div>
        @csrf
        <div class="modal-body">
            <div class="row form-inline">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Tanggal Awal</th>
                            <th>Tanggal Akhir</th>
                            <th>Generator</th>
                            <th>Total</th>
                            <th>Terbayar</th>
                            <th>Payroll</th>
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
@else
@include('partial.noAccess')
@endif

@endsection