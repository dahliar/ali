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

    function tambahPresensiBatchInput(){
        window.open(('{{ url("presenceAddForm") }}'), '_self');
    }

    function presenceForTodayModal(id, name){
        document.getElementById("empidModal").value = id;
        document.getElementById("nameModal").value = name;
        $('#employeePresenceHarianModal').modal('show');
    }
    function presenceForTodayStore(id){
        var empidModal = document.getElementById("empidModal").value;
        var start = document.getElementById("modalStart").value;
        var end = document.getElementById("modalEnd").value;

        $.ajax({
            url: '{{ url("storePresenceHarianEmployee") }}',
            type: "POST",
            data: {
                "_token":"{{ csrf_token() }}",
                empidModal : empidModal,
                start: start,
                end: end
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
                $('#employeePresenceHarianModal').modal('hide');
            }
        });
    }

    function employeePresenceHarianHistory(id){
        window.open(('{{ url("employeePresenceHarianHistory") }}'+"/"+id), '_blank');
    }


    function myFunction(){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getPresenceHarianEmployees") }}',
            dataType: "JSON",
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "25%", "targets":  [1], "className": "text-left"   },
            {   "width": "15%", "targets":  [2], "className": "text-left" },
            {   "width": "10%", "targets":  [3], "className": "text-left" },
            {   "width": "15%", "targets":  [4], "className": "text-left" },
            {   "width": "10%", "targets":  [5], "className": "text-left" },
            {   "width": "10%", "targets":  [6], "className": "text-left" },
            {   "width": "10%", "targets":  [7], "className": "text-left" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'nik', name: 'nik'},
            {data: 'jenisPenggajian', name: 'jenisPenggajian'},
            {data: 'orgStructure', name: 'orgStructure'},
            {data: 'jabatan', name: 'jabatan'},
            {data: 'bagian', name: 'bagian'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    }

    $(document).ready(function() {
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
                            <li class="breadcrumb-item active">Presensi Harian</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{url('presenceHarianImport')}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Import Presensi Pegawai Harian/Bulanan"><i class="fa fa-upload" style="font-size:20px"></i>
                    </a>
                    <a href="{{url('presenceHarianHistory')}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Arsip Presensi Harian"><i class="fa fa-history" style="font-size:20px"></i>
                    </a>
                </div>
            </div>
            <div class="modal-body">
                <div class="row form-inline">
                    <table class="table cell-border stripe hover row-border data-table"  id="datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Jenis Karyawan</th>
                                <th>Posisi</th>
                                <th>Jabatan</th>
                                <th>Bagian</th>
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

<div class="modal fade" id="employeePresenceHarianModal" tabindex="-1" aria-labelledby="employeePresenceHarianModal" aria-hidden="true">
    <form id="presenceTunggalHarian" method="POST" name="presenceTunggalHarian">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeePresenceHarianModal">Presensi Hari ini</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="empidModal" name="empidModal" class="form-control" readonly>
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Nama</span>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="nameModal" name="nameModal" class="form-control" readonly>
                        </div>
                    </div>                    
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Start</span>
                        </div>
                        <div class="col-md-8">
                            <input type="datetime-local" id="modalStart" name="modalStart" class="form-control text-end" value="{{date('Y-m-d\Th:m:s')}}">
                        </div>
                    </div>                    
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">End</span>
                        </div>
                        <div class="col-md-8">
                            <input type="datetime-local" id="modalEnd" name="modalEnd" class="form-control text-end" value="{{date('Y-m-d\Th:m:s')}}">
                        </div>
                    </div>                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="presenceForTodayStore()">Save changes</button>
                </div>
            </div>
        </div>
    </form>
</div>

@else
@include('partial.noAccess')
@endif

@endsection