@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
<script src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js" type="text/javascript" ></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function employeePresenceHistory(id){
        window.open(('{{ url("employeePresenceHarianHistory") }}'+"/"+id), '_blank');
    }
    function slipGajiPegawai(id){
        alert("belum");
        var mapForm = document.createElement("form");
        mapForm.target = "_blank";    
        mapForm.method = "POST";
        mapForm.action = "{{url("slipGajiKaryawan")}}";

        var mapInput = document.createElement("input");
        mapInput.type = "text";
        mapInput.name = "empid";
        mapInput.value = id;

        mapForm.appendChild(mapInput);

        document.body.appendChild(mapForm);
        mapForm.submit();
    }
    function employeePaperList(id){
        window.open(('{{ url("employeePaperList") }}'+"/"+id), '_self');
    }
    function editEmployee(id){
        window.open(('{{ url("employeeEdit") }}'+"/"+id), '_self');
    }
    function editPassword(id){
        window.open(('{{ url("passedit") }}'+"/"+id), '_self');
    }
    function editPemetaan(id){
        window.open(('{{ url("employeeMappingEdit") }}'+"/"+id), '_self');
    }
    function historyPemetaan(id){
        window.open(('{{ url("employeeMappingHistory") }}'+"/"+id), '_self');
    }

    function tambahTransaksi(){
        window.open(('{{ url("employeeAdd") }}'), '_self');
    }

    function barcodeList(){
        window.open(('{{ url("employeeBarcodeList") }}'), '_self');
    }

    function myFunction(){
        var isChecked = document.querySelector('input[name="showData"]:checked').value;
        var e = document.getElementById("empType");
        var empType = e.value;
        $('#datatable').DataTable({
            ajax:'{{ url("getAllEmployees") }}'+"/"+isChecked+"/"+empType,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
                {   "width": "5%",  "targets":  [0], "className": "text-center" },
                {   "width": "20%", "targets":  [1], "className": "text-left"   },
                {   "width": "15%", "targets":  [2], "className": "text-left"   },
                {   "width": "5%", "targets":   [3], "className": "text-center" },
                {   "width": "15%", "targets":  [4], "className": "text-left" },
                {   "width": "5%", "targets":  [5], "className": "text-center" },
                {   "width": "10%", "targets":  [6], "className": "text-center" },
                {   "width": "20%", "targets":  [7], "className": "text-left" }
            ], 

            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'username', name: 'username'},
                {data: 'gender', name: 'gender'},
                {data: 'jenisPenggajian', name: 'jenisPenggajian'},
                {data: 'statusKepegawaian', name: 'statusKepegawaian'},
                {data: 'lamaKerja', name: 'lamaKerja'},
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
                <div class="col-md-6">
                    <nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
                        <ol class="breadcrumb primary-color">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">Pegawai</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-end">
                    <button onclick="barcodeList()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Employee Barcode List"><i class="fas fa-barcode" style="font-size:20px"></i>
                    </button>
                    <button onclick="tambahTransaksi()" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah Pegawai"><i class="fa fa-plus" style="font-size:20px"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row align-items-center p-1">
                    <div class="col-2">
                        <label class="form-check-label" for="inlineRadio2">Status Kepegawaian</label>
                    </div>
                    <div class="col-9">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="showData" id="r2" value="1" checked>
                            <label class="form-check-label" for="inlineRadio2">Yang Aktif saja</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="showData" id="r3" value="0">
                            <label class="form-check-label" for="inlineRadio3">Yang Non Aktif saja</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="showData" id="r1" value="all">
                            <label class="form-check-label" for="inlineRadio1">Semua</label>
                        </div>
                    </div>
                </div>
                <div class="row  align-items-center p-1">
                    <div class="col-2">
                        <label class="form-check-label" for="inlineRadio2">Jenis Kepegawaian</label>
                    </div>
                    <div class="col-3">
                        <div class="d-grid d-md-flex">
                            <select class="form-select" id="empType" aria-label="Default select example">
                                <option value="0" selected>Semua</option>
                                <option value="2">Harian</option>
                                <option value="3">Borongan</option>
                                <option value="1">Bulanan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div>
                    <button id="buttonShow" type="submit" class="btn btn-primary" onclick="myFunction()">Tampilkan Data</button>
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
                            <th>Username</th>
                            <th>JK</th>
                            <th>Karyawan</th>
                            <th>Aktif</th>
                            <th>Masa Kerja</th>
                            <th>Aksi</th>
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