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

    function cetak(){
        var start = document.getElementById("startView").value;
        var end = document.getElementById("endView").value;
        var opsi = document.getElementById("opsiValue").value;
        window.open(('{{ url("cetakSalaryByDateRange") }}'+"/"+opsi+"/"+start+"/"+end), '_blank');
    }
    function openWindowWithPost(url, data) {
        var form = document.createElement("form");
        form.target = "_blank";
        form.method = "POST";
        form.action = url;
        form.style.display = "none";

        for (var key in data) {
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = key;
            input.value = data[key];
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    function myFunction(){
        var e = document.getElementById("opsi");
        var opsi = e.options[e.selectedIndex].value;       

        var start = document.getElementById("start").value;
        var end = document.getElementById("end").value;

        document.getElementById("startView").value=start;
        document.getElementById("endView").value=end;
        document.getElementById("opsiValue").value=opsi;
        document.getElementById("opsiView").value= e.options[e.selectedIndex].text;
        if (opsi == 2){
            document.getElementById("divDatatableDetil").style.display = "none";
            document.getElementById("divDatatableRekap").style.display = "block";
            document.getElementById("divCetak").style.display = "block";
            $("#divDatatableDetil tbody tr").remove(); 
            $('#datatableRekap').DataTable({
                ajax: '{{ url("getSalaryByDateRange") }}' + "/"+ opsi + "/"+ start + "/"+ end,
                type: 'get',
                serverSide: false,
                processing: true,
                deferRender: true,
                destroy:true,
                columnDefs: [
                    {   "width": "5%",  "targets":  [0], "className": "text-center" },
                    {   "width": "45%", "targets":  [1], "className": "text-left"   },
                    {   "width": "10%", "targets":  [2], "className": "text-end" },
                    {   "width": "10%", "targets":  [3], "className": "text-end" },
                    {   "width": "10%", "targets":  [4], "className": "text-end" },
                    {   "width": "10%", "targets":  [5], "className": "text-end" },
                    {   "width": "10%", "targets":  [6], "className": "text-end" }
                    ], 

                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'nama', name: 'nama'},
                    {data: 'uh', name: 'uh'},
                    {data: 'ul', name: 'ul'},
                    {data: 'borongan', name: 'borongan'},
                    {data: 'honorarium', name: 'honorarium'},
                    {data: 'total', name: 'total'}
                    ]
            });
        }else{
            document.getElementById("divDatatableDetil").style.display = "block";
            document.getElementById("divDatatableRekap").style.display = "none";
            document.getElementById("divCetak").style.display = "block";
            $("#datatableRekap tbody tr").remove(); 
            $('#datatableDetil').DataTable({
                ajax: '{{ url("getSalaryByDateRange") }}' + "/"+ opsi + "/"+ start + "/"+ end,
                type: 'get',
                serverSide: false,
                processing: true,
                deferRender: true,
                destroy:true,
                columnDefs: [
                    {   "width": "5%",  "targets":  [0], "className": "text-center" },
                    {   "width": "35%", "targets":  [1], "className": "text-left"   },
                    {   "width": "10%", "targets":  [2], "className": "text-end" },
                    {   "width": "10%", "targets":  [3], "className": "text-end" },
                    {   "width": "10%", "targets":  [4], "className": "text-end" },
                    {   "width": "10%", "targets":  [5], "className": "text-end" },
                    {   "width": "10%", "targets":  [6], "className": "text-end" },
                    {   "width": "10%", "targets":  [7], "className": "text-end" }
                    ], 

                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'nama', name: 'nama'},
                    {data: 'tanggal', name: 'tanggal'},
                    {data: 'uh', name: 'uh'},
                    {data: 'ul', name: 'ul'},
                    {data: 'borongan', name: 'borongan'},
                    {data: 'honorarium', name: 'honorarium'},
                    {data: 'total', name: 'total'}
                    ]
            });
        }
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
                            <li class="breadcrumb-item active">Daftar gaji rentang tanggal</li>
                        </ol>
                    </nav>
                </div>
            </div> 
        </div>           
        <div class="card card-header">
            <div class="row form-inline">
                <div class="col-md-2">
                    <input type="date" id="start" name="start" class="form-control text-end" value="{{ date('Y-m-d', strtotime('-1 month')) }}" > 
                </div>
                <div class="col-md-2">
                    <input type="date" id="end" name="end" class="form-control text-end" value="{{ date('Y-m-d') }}" >
                </div>                       
                <div class="col-md-2">
                    <select id="opsi" name="opsi" class="form-select" >
                        <option value="1">Detil per tanggal</option>
                        <option value="2" selected>Rekap</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" id="hitButton" class="form-control btn-primary" onclick="myFunction()">Cari</button>
                </div>
            </div>
        </div>
        <div class="card card-body">
            <div id="divCetak" style="display:none">
                <div class="row form-inline">
                    <div class="col-md-2">
                        <input type="text" id="startView" name="startView" class="form-control text-end" readonly> 
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="endView" name="endView" class="form-control text-end"  readonly> 
                    </div>                       
                    <div class="col-md-2">
                        <input type="text" id="opsiView" name="opsiView" class="form-control"  readonly> 
                    </div>
                    <input type="hidden" id="opsiValue" name="opsiValue" class="form-control"  readonly> 
                    <div class="col-md-2">
                        <button type="button" id="hitButton" class="form-control btn-primary" onclick="cetak()">Print</button>
                    </div>               
                </div>
            </div>
            <br>
            <div id="divDatatableRekap">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatableRekap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Harian</th>
                            <th>Lembur</th>
                            <th>Borongan</th>
                            <th>Honorarium</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 10px;">
                    </tbody>
                </table>
            </div>
            <div id="divDatatableDetil">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatableDetil">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Harian</th>
                            <th>Lembur</th>
                            <th>Borongan</th>
                            <th>Honorarium</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 10px;">
                    </tbody>
                </table> 
            </div>
        </div>  
        Laman ini akan menampilkan data 
        <ol>
            <li>Dalam rentang tanggal terpilih</li>
            <li>Data gaji yang dihitung <b>tidak</b> memperhatikan proses generate gaji</li>
        </ol>
    </div>
</body>
@endsection