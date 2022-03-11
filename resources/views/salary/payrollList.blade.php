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

    function printPayrollList($payrollId){
        window.open(('{{ url("printPayrollList") }}' + "/"+ $payrollId), '_blank');
    }

    function showPayrolList(){
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax: '{{ url("getPayrollList") }}'+"/"+start+"/"+end,
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
            {   "width": "15%", "targets":  [4], "className": "text-left" },
            ], 

            columns: [
            {data: 'DT_RowIndex',   name: 'DT_RowIndex'},
            {data: 'payDate',       name: 'payDate'},
            {data: 'generator',     name: 'generator'},
            {data: 'total',         name: 'total'},
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
                    <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 week')))}}" > 
                </div>
                <div class="col-md-3">
                    <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}" >
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
                            <th>Tanggal Payroll</th>
                            <th>Generator</th>
                            <th>Total Payroll (Rp)</th>
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
        <li></li>
        <li>Laman ini digunakan untuk melihat daftar gaji yang harus dibayarkan</li>
        <li>Ketika dilakukan </li>
    </ol>
</body>
@else
@include('partial.noAccess')
@endif

@endsection