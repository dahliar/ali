@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 2)
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function myFunction($payrollId){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getEmployeeDetailSalaries") }}'+"/"+$payrollId,
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
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "10%", "targets":  [4], "className": "text-end" },
            {   "width": "10%", "targets":  [5], "className": "text-end" },
            {   "width": "10%", "targets":  [6], "className": "text-end" },
            {   "width": "10%", "targets":  [7], "className": "text-end" },
            {   "width": "10%", "targets":  [8], "className": "text-end" },
            {   "width": "10%", "targets":  [9], "className": "text-end" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nip', name: 'nip'},
            {data: 'bank', name: 'bank'},
            {data: 'detilHarian', name: 'detilHarian'},
            {data: 'detilBorongan', name: 'detilBorongan'},
            {data: 'harian', name: 'harian'},
            {data: 'borongan', name: 'borongan'},
            {data: 'honorarium', name: 'honorarium'},
            {data: 'total', name: 'total'}
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
<body onload="myFunction({{$payrollId}})">
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-9">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Daftar Take Home Pay Pegawai</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead style="font-size:12px">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>Rekening</th>
                                <th>Detil Harian</th>
                                <th>Detil Borongan</th>
                                <th>Harian</th>
                                <th>Borongan</th>
                                <th>Honorarium</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody style="font-size:12px">
                        </tbody>
                    </table>                
                </div>
            </div>    
        </div>
    </div>
</body>
@else
@include('partial.noAccess')
@endif

@endsection