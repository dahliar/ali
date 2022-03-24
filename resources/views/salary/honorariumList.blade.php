<!--BELUM-->
@php
$pageId = -1;
@endphp

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

    function hapusGenerateHonorarium(sid){
        Swal.fire({
            title: 'Yakin menghapus?',
            text: "Data hasil generate akan hilang dan mengembalikan status record honorarium ke belum generate!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("hapusGenerateHonorarium") }}',
                    type: "POST",
                    data: {
                        "_token":"{{ csrf_token() }}",
                        sid : sid
                    },
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Deleted!',
                            'Record borongan telah dihapus.',
                            'success'
                            );
                        myFunction();
                    }
                });
            }
        })
    }


    function setIsPaidModal(id){
        document.getElementById("modalIdIsPaid").value = id;
        $('#isPaidModal').modal('show');
    }
    function tandaiSudahDibayar(){
        var tanggalBayar = document.getElementById("modalTanggalBayar").value;
        var id = document.getElementById("modalIdIsPaid").value;

        $.ajax({
            url: '{{ url("markSalariesIsPaid") }}'+"/"+id+"/"+tanggalBayar,
            type: "POST",
            dataType: "json",
            success:function(data){
                if(data.isError==="0"){
                    swal.fire('info',data.message,'info');
                    myFunction();
                }
                else{
                    swal.fire('warning',data.message,'warning');
                }
                $('#isPaidModal').modal('hide');
            }
        });
    }

    function myFunction(){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getSalariesHonorarium") }}',
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "20%", "targets":  [1], "className": "text-left"   },
            {   "width": "10%", "targets":  [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "15%", "targets":  [4], "className": "text-left" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'generatorName', name: 'generatorName'},
            {data: 'enddate', name: 'enddate'},
            {data: 'countIsPaid', name: 'countIsPaid'},
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
                            <li class="breadcrumb-item active">Daftar Honorarium</li>
                        </ol>
                    </nav>
                </div>
                
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Generator</th>
                                <th>Tanggal Generate</th>
                                <th>Terbayar</th>
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

<div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModal" aria-hidden="true">
    <form id="modalGenerateGajiHarian" method="POST" name="modalGenerateGajiHarian">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Generate Gaji Harian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-4 text-end">
                            <span class="label">Tanggal Akhir</span>
                        </div>
                        <div class="col-md-6">
                            <input type="date" id="modalEnd" name="modalEnd" class="form-control text-end" value="{{date('Y-m-d')}}">
                        </div>
                    </div>                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="generateGajiHarian()">Generate</button>
                </div>
            </div>
        </div>
    </form>
</div>


<div class="modal fade" id="isPaidModal" tabindex="-1" aria-labelledby="isPaidModal" aria-hidden="true">
    <form id="setIsPaidForm" method="POST" name="setIsPaidForm">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tandai sudah dibayar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <input type="hidden" id="modalIdIsPaid" name="modalIdIsPaid" class="form-control" readonly>

                        <div class="col-md-2 text-end">
                            <span class="label">Tanggal Bayar</span>
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

@else
@include('partial.noAccess')
@endif

@endsection