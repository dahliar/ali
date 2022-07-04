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

    function tambahDetail(id){
        window.open(('{{ url("detailundernameAdd") }}'+"/"+id), '_self');
    }
    function deleteItem(undernameId){
        Swal.fire({
            title: "Hapus item?",
            text: "Yakin hendak menghapus item?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus aja!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("itemDetailUndernameDelete") }}'+"/"+undernameId,
                    type: "GET",
                    data : {"_token":"{{ csrf_token() }}"},
                    dataType: "text",
                    success:function(data){
                        swal.fire("Deleted!", "Detail undername berhasil dihapus", "success");
                        myFunction({{ $undernameId }});
                    }
                });
            }
        })
    };

    function myFunction($id){
        $('#datatable').DataTable({
            ajax:'{{ url("getUndernameDetails") }}' + "/"+ $id,
            serverSide: false,
            processing: true,
            deferRender: true,
            type: 'GET',
            destroy:true,
            columnDefs: [
            {   "width": "5%",  "targets":  [0], "className": "text-center" },
            {   "width": "40%", "targets":  [1], "className": "text-left" },
            {   "width": "15%", "targets":  [2], "className": "text-end" },
            {   "width": "15%", "targets":  [3], "className": "text-end" },
            {   "width": "15%", "targets":  [4], "className": "text-end" },
            {   "width": "10%", "targets":  [5], "className": "text-center" }
            ], 

            columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'item', name: 'item'},
            {data: 'price', name: 'price'},
            {data: 'amount', name: 'amount'},
            {data: 'total', name: 'total'},
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

<body onload="myFunction({{ $undernameId }})">
    {{ csrf_field() }}
    <div class="container-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/undernameList') }}">Undername Ekspor</a>
                        </li>
                        <li class="breadcrumb-item active">Detil Undername</li>
                    </ol>
                </nav>

                @if ($undernameStatus == 1)
                <button onclick="tambahDetail({{$undernameId}})" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body" title="Tambah barang"><i class="fa fa-plus" style="font-size:20px"></i>
                </button>
                @endif
            </div>
        </div>
        <div class="card card-body">
            <table class="table table-striped table-hover table-bordered data-table" id="datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Berat</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                </tbody>
            </table>                
        </div>
    </div>
</body>
@endsection