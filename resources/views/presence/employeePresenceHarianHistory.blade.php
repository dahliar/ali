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

    function editPresence($presence){
        window.open(('{{ url("presenceHarianEdit") }}/'+$presence), '_blank');
    }
    
    function myFunction(){
        var employeeId = document.getElementById("employeeId").value;
        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getEmployeePresenceHarianHistory") }}'+"/"+employeeId+"/"+start+"/"+end,
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "20%", "targets":  [1], "className": "text-left"   },
            {   "width": "5%", "targets":  [2], "className": "text-left" },
            {   "width": "20%", "targets":  [3], "className": "text-left" },
            {   "width": "15%", "targets":  [4], "className": "text-left" },
            {   "width": "20%", "targets":  [5], "className": "text-left" },
            {   "width": "5%", "targets":  [6], "className": "text-left" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'posisi', name: 'posisi'},
            {data: 'shift', name: 'shift'},
            {data: 'tanggal', name: 'tanggal'},
            {data: 'jam', name: 'jam'},
            {data: 'salary', name: 'salary'},
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
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{url('presenceHarianList')}}">Presensi</a>
                            </li>
                            <li class="breadcrumb-item active">Arsip Presensi Pegawai {{$employeeName}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <input type="hidden" id="employeeId" name="employeeId" value="{{$employeeId}}">
            
            <div class="modal-body">
                <div class="row">
                    <div class="card card-body">
                        <div class="row form-group">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span name="spanAm" id="spanAm" class="input-group-text">Start</span>
                                    <input type="date" id="start" name="start" class="form-control text-end" value="{{ old('start', date('Y-m-d', strtotime('-1 month')))}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span name="spanAm" id="spanAm" class="input-group-text">End</span>
                                    <input type="date" id="end" name="end" class="form-control text-end" value="{{ old('end', date('Y-m-d'))}}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button onclick="myFunction()" class="btn btn-primary" style="display: block;width: 50%;">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Posisi</th>
                                <th>Shift</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
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