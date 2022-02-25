@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if (Auth::user()->isAdmin())
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function setSalaryIsPaid($salaryId, $empid){
        document.getElementById("modalSalaryId").value = $salaryId;
        document.getElementById("modalEmpid").value = $empid;
        $('#StatusModal').modal('show');
    }
    function tandaiSudahDibayar(){
        var tanggalBayar = document.getElementById("modalTanggalBayar").value;
        var empid = document.getElementById("modalEmpid").value;
        var salaryId = document.getElementById("modalSalaryId").value;

        $.ajax({
            url: '{{ url("markLemburIsPaid") }}',
            type: "POST",
            data: {
                "_token":"{{ csrf_token() }}",
                salaryId:salaryId,
                empid:empid,
                tanggalBayar: tanggalBayar
            },
            dataType: "json",
            success:function(data){
                if(data.isError==="0"){
                    swal.fire('info',data.message,'info');
                    myFunction();
                }
                else{
                    swal.fire('warning',data.message,'warning');
                }
                $('#StatusModal').modal('hide');
            }
        });
    }
    function myFunction(){
        salary = document.getElementById("salaryId").value;
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getLemburPegawaiBulanan") }}'+"/"+salary,
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "20%", "targets":  [1], "className": "text-left" },
            {   "width": "15%", "targets":  [2], "className": "text-left" },
            {   "width": "15%", "targets":  [3], "className": "text-left" },
            {   "width": "10%", "targets":  [4], "className": "text-left" },
            {   "width": "10%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-end" },
            {   "width": "10%", "targets":  [7], "className": "text-left" },
            {   "width": "5%", "targets":  [8], "className": "text-end" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nip', name: 'nip'},
            {data: 'osname', name: 'osname'},
            {data: 'noRekening', name: 'noRekening'},
            {data: 'bank', name: 'bank'},
            {data: 'ul', name: 'ul'},
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
                            <li class="breadcrumb-item active">Daftar lembur pegawai bulanan</li>
                        </ol>
                    </nav>
                </div>                
            </div>

            <input type="hidden" value="{{$salary->id}}"id="salaryId" name="salaryId" class="form-control" readonly>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table cell-border stripe hover row-border data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>NIP</th>
                                <th>Posisi</th>
                                <th>Rekening</th>
                                <th>Bank</th>
                                <th>Lembur</th>
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
    <div class="modal fade" id="StatusModal" tabindex="-1" aria-labelledby="StatusModal" aria-hidden="true">
        <form id="setStatusForm" method="POST" name="setStatusForm">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tandai sudah dibayar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row form-group">
                            <input type="hidden" id="modalSalaryId" name="modalSalaryId" class="form-control" readonly>
                            <input type="hidden" id="modalEmpid" name="modalEmpid" class="form-control" readonly>

                            <div class="col-md-2 text-end">
                                <span class="label">Dibayar Tanggal</span>
                            </div>
                            <div class="col-md-8">
                                <input type="date" id="modalTanggalBayar" name="modalTanggalBayar" class="form-control text-end" value="{{date('Y-m-d')}}">
                            </div>
                        </div>                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="tandaiSudahDibayar()">Save changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
@else
@include('partial.noAccess')
@endif

@endsection