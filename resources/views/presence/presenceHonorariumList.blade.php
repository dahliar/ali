@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if ((Auth::user()->isHumanResources() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function presenceForTodayModal(id, name){
        document.getElementById("empidModal").value = id;
        document.getElementById("modalName").value = name;
        $('#employeePresenceHonorariumModal').modal('show');
    }
    function presenceForTodayStore(id){
        var empid = document.getElementById("empidModal").value;
        var tanggalKerja = document.getElementById("modalTanggalKerja").value;
        var jumlah = document.getElementById("modalJumlah").value;
        var keterangan = document.getElementById("modalKeterangan").value;

        $.ajax({
            url: '{{ url("storePresenceHonorariumEmployee") }}',
            type: "POST",
            data: {
                "_token":"{{ csrf_token() }}",
                keterangan : keterangan,
                empid : empid,
                tanggalKerja: tanggalKerja,
                jumlah: jumlah
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
                $('#employeePresenceHonorariumModal').modal('hide');
            }
        });
    }

    function myFunction(){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:'{{ url("getPresenceHonorariumEmployees") }}',
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
                            <li class="breadcrumb-item active">Presensi Honorarium</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{url('presenceHonorariumHistory')}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Arsip Honorarium"><i class="fa fa-history" style="font-size:20px"></i>
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

<div class="modal fade" id="employeePresenceHonorariumModal" tabindex="-1" aria-labelledby="employeePresenceHonorariumModal" aria-hidden="true">
    <form id="presenceTunggalHarian" method="POST" name="presenceTunggalHarian">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="employeePresenceHonorariumModal">Presensi Honorarium</h5>
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
                            <input type="text" id="modalName" name="modalName" class="form-control" readonly>
                        </div>
                    </div>                    
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Tanggal Kerja</span>
                        </div>
                        <div class="col-md-8">
                            <input type="date" id="modalTanggalKerja" name="modalTanggalKerja" class="form-control text-end" value="{{date('Y-m-d')}}">
                        </div>
                    </div>                    
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Jumlah</span>
                        </div>
                        <div class="col-md-8">
                            <input type="number" id="modalJumlah" name="modalJumlah" class="form-control text-end" value="0">
                        </div>
                    </div>                    
                    <div class="row form-group">
                        <div class="col-md-2 text-end">
                            <span class="label">Keterangan</span>
                        </div>
                        <div class="col-md-8">
                            <textarea id="modalKeterangan" name="modalKeterangan" rows="4"  class="form-control" style="min-width: 100%">Keterangan honorarium</textarea>
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