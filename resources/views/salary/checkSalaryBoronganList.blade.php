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
    function setIsPaidModal($sid, $empid){
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
                    url: '{{ url("markBoronganIsPaid") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        sid : $sid,
                        empid : $empid
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Marked!',
                            'Record gaji harian telah ditandai dibayar.',
                            'success'
                            );
                        myFunction();
                    }
                });
            }
        })
    }

    function myFunction(){
        boronganId = document.getElementById("boronganId").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getBoronganSalariesForPrint") }}'+"/"+boronganId,
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
            {   "width": "10%", "targets":  [4], "className": "text-left" },
            {   "width": "10%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-end" },
            {   "width": "10%", "targets":  [7], "className": "text-left" },
            {   "width": "5%", "targets":  [8], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nip', name: 'nip'},
            {data: 'osname', name: 'osname'},
            {data: 'noRekening', name: 'noRekening'},
            {data: 'bankName', name: 'bankName'},
            {data: 'netPayment', name: 'netPayment'},
            {data: 'isPaid', name: 'isPaid'},
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
<body onload="myFunction()">
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-9">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Daftar Penggajian Borongan</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{url('printSalaryBoronganList')}}/{{$borongan->id}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak Daftar Gaji" target="_blank"><i class="fas fa-money-check-alt"></i>
                    </a>
                </div>
            </div>
            <input type="hidden" value="{{$borongan->id}}" id="boronganId" name="boronganId" class="form-control" readonly>
            <div class="modal-body">
                <input type="hidden" value="{{$salary->id}}"id="salaryId" name="salaryId" class="form-control" readonly>
                <div class="row form-inline">
                    <div class="col-md-2">Jenis
                    </div>
                    <div class="col-md-6"> 
                        <input type="text" value="Borongan" class="form-control" disabled>
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
                        <input type="text" value="{{$generatorName}}" class="form-control" disabled>
                    </div>
                </div>
                <div class="row form-inline">
                    <div class="col-md-2">Sudah Dibayar
                    </div>
                    <div class="col-md-6"> 
                        <input type="text" value="@if ($salary->isPaid == null)BELUM @else SUDAH @endif" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>NIP</th>
                                <th>Posisi</th>
                                <th>Rekening</th>
                                <th>Bank</th>
                                <th>Gaji</th>
                                <th>Bayar</th>
                                <th>Act</th>
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
@else
@include('partial.noAccess')
@endif

@endsection