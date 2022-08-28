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

    function getFileDownload(filepath){
        window.open(('{{ url("getFileDownload") }}'+"/"+filepath), '_blank');
    };

    function tambahPI(id){
        Swal.fire({
            title: 'Generate dokumen PI baru?',
            text: "Akan membuat dokumen proforma invoice baru!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: ('{{ url("transaction/pi") }}'+"/"+id),
                    type: "GET",
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Created!',
                            'Dokumen PI baru telah dibuat.',
                            'success'
                            );
                        myFunction(id);
                    }
                });
            }
        })
    }
    function tambahIPL(id){
        Swal.fire({
            title: 'Generate dokumen Invoice baru?',
            text: "Akan membuat dokumen Invoice baru!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: ('{{ url("transaction/ipl") }}'+"/"+id),
                    type: "GET",
                    dataType: "json",
                    success:function(data){
                        Swal.fire(
                            'Created!',
                            'Invoice baru telah dibuat.',
                            'success'
                            );
                        myFunction(id);
                    }
                });
            }
        })
    }

    function myFunction(transactionId){
        $('#datatable').DataTable({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            ajax:{
                url: '{{ url("getAllTransactionDocuments") }}',
                data: function (d){
                    d.transactionId = transactionId
                }
            },
            dataType: 'json',            
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%", "targets":  [0], "className": "text-left"   },
            {   "width": "15%", "targets": [1], "className": "text-left" },
            {   "width": "15%", "targets": [2], "className": "text-left" },
            {   "width": "30%", "targets":  [3], "className": "text-left" },
            {   "width": "20%", "targets":  [4], "className": "text-left" },
            {   "width": "15%", "targets":   [5], "className": "text-left" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'documentNo', name: 'documentNo'},
            {data: 'jenis', name: 'jenis'},
            {data: 'name', name: 'name'},
            {data: 'tanggal', name: 'tanggal'},
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
            @if (session('listBarang'))
            <ol>
                @foreach(session('listBarang') as $barang)
                <li>
                    {{$barang}}
                </li>
                @endforeach
            </ol>
            @endif
        </div>
        <div class="col-md-1 text-center">
            <span class="label"><strong >x</strong></span>
        </div>
    </div>
</div>
@endif
<body onload="myFunction({{$transaction->id}})">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Transactions</li>
                    </ol>
                </nav>
                <div>
                    <button onclick="tambahPI({{$transaction->id}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak PI Baru">
                        <i class="fa fa-plus"></i> Cetak PI Baru
                    </button>

                    @if ($transaction->status==2)
                    <button onclick="tambahIPL({{$transaction->id}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Cetak Invoice Baru">
                        <i class="fa fa-plus"></i> Cetak Invoice Baru
                    </button>
                    @else
                    <button class="btn btn-primary" disabled>
                        <i class="fa fa-plus"></i> Cetak Invoice Baru
                    </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="card card-body">
            <div class="row form-inline">
                <table class="table table-striped table-hover table-bordered data-table"  id="datatable">
                    <thead>
                        <tr style="font-size: 12px;">
                            <th>No</th>
                            <th>Nomor Dokumen</th>
                            <th>Jenis</th>
                            <th>Pembuat</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:14px">
                    </tbody>
                </table>                
            </div>
        </div>
    </div>    
</div>
</div>
</div>
</body>
@endsection