<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('content')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript"> 
    function myFunction(){
        Swal.fire({
            title: 'Edit data perusahaan?',
            text: "Simpan data perusahaan",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan saja.'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Data perusahaan disimpan',
                    text: "Simpan data perusahaan",
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok disimpan.'
                }).then((result) => {
                    document.getElementById("companyForm").submit();
                })
            } else {
                Swal.fire(
                    'Batal disimpan!',
                    "Penyimpanan data perusahaan dibatalkan",
                    'info'
                    );
            }
        })
    };
    $(document).ready(function() {
        var i=1;
        $('#add').click(function(){
            i++;  
            $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td class="col-md-12"><div class="row form-group"><div class="col-md-4"><input id="tableid[]" name="tableid[]" value="-1"  type="hidden"><input id="contactName[]" placeholder="Nama Kontak" name="contactName[]" class="form-control"></div><div class="col-md-3"><input id="phone[]" name="phone[]" class="form-control" placeholder="No Telepon"></div><div class="col-md-4"><input id="email[]" name="email[]" class="form-control" placeholder="Email" type="email"></div><div class="col-md-1"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove"><i class="fa fa-trash"></i></button></div></div></td></tr>'); 
        });
        $(document).on('click', '.btn_remove', function(){  
            var button_id = $(this).attr("id");   
            $('#row'+button_id+'').remove();  
        });
    });
</script>
@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data perusahaan berhasil ditambahkan", "info");
</script>
@endif

@if ($errors->any())
<div class="alert alert-success">
    <div class="row form-inline" onclick='$(this).parent().remove();'>
        <div class="col-11">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color">
                        <li class="breadcrumb-item">
                            <a class="white-text" href="{{ url('/home') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a class="white-text" href="{{ url('companyList')}}">Perusahaan</a>
                        </li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card card-body">
            <div class="col-12">
                <form id="companyForm" action="{{route('companyUpdate')}}"  method="post" name="companyForm" enctype="multipart/form-data">
                    @csrf
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label" id="spanBank">Nama</span>
                            </div>
                            <div class="col-md-4">
                                <input id="name" name="name" class="form-control" value="{{$company->name}}" readonly>
                                <input id="companyId" name="companyId" class="form-control" value="{{$company->id}}" type="hidden" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label" id="spanBank">Alamat</span>
                            </div>
                            <div class="col-md-7">
                                <textarea id="address" name="address" rows="4"  class="form-control">{{ old('address', $company->address) }}</textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label" id="spanBank">Negara</span>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select w-100" id="countryId" name="countryId">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($countries as $country)
                                    @if ( $country->id == old('countryId', $company->nation) )
                                    <option value="{{ $country->id }}" selected>{{ $country->name }}</option>
                                    @else
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label" id="npwp">KTP</span>
                            </div>
                            <div class="col-md-4">
                                <input id="ktp" name="ktp" class="form-control" value="{{ old('ktp', $company->npwp) }}" placeholder="KTP">
                            </div>
                            @if($company->ktpFile)
                            <div class="col-md-4">
                                <div class="input-group">
                                    <a href="{{ url('getFileDownload').'/'.$company->ktpFile }}" target="_blank">{{$company->ktpFile}}</a>
                                </div>
                            </div>
                            @endif  
                        </div>

                        <div class="row form-group">
                            <div class="col-md-2 text-md-end">
                                <span class="label">Upload File KTP Baru</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="ktpFile" name="ktpFile" accept="image/jpeg,image/jpg,image/png,application/pdf">
                                </div>
                                <span style="font-size:9px" class="label">File dalam bentuk image dengan ukuran maksimal 1MB</span>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label" id="npwp">NPWP</span>
                            </div>
                            <div class="col-md-4">
                                <input id="npwpnum" name="npwpnum" class="form-control" value="{{ old('npwpnum', $company->npwp) }}" placeholder="NPWP Number">
                            </div>
                            @if($company->npwpFile)
                            <div class="col-md-4">
                                <div class="input-group">
                                    <a href="{{ url('getFileDownload').'/'.$company->npwpFile }}" target="_blank">{{$company->npwpFile}}</a>
                                </div>
                            </div>       
                            @endif  
                        </div>

                        <div class="row form-group">
                            <div class="col-md-2 text-md-end">
                                <span class="label">Upload File NPWP Baru</span>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="npwpFile" name="npwpFile" accept="image/jpeg,image/jpg,image/png,application/pdf">
                                </div>
                                <span style="font-size:9px" class="label">File dalam bentuk image dengan ukuran maksimal 1MB</span>
                            </div>
                        </div>  
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label" id="spanBank">Pajak terhitung</span>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select w-100" id="taxIncluded" name="taxIncluded">
                                    <option value="-1" @if(old('taxIncluded', $company->taxIncluded) == -1) selected @endif>--Pilih dahulu--</option>
                                    <option value="0" @if(old('taxIncluded', $company->taxIncluded) == 0) selected @endif>NO</option>
                                    <option value="1" @if(old('taxIncluded', $company->taxIncluded) == 1) selected @endif>YES</option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                                <span class="label" id="spanBank">Kontak person</span>
                            </div>
                            <div class="col-md-2">
                                <button type="button" name="add" id="add" class="btn btn-primary"><i class="fa fa-plus"></i> Kontak</button>
                            </div>
                        </div>


                        <div class="row form-group">
                            <div class="col-md-2 text-end"></div>
                            <div class="col-md-10">
                                <div class="table-responsive">  
                                    <table class="table table-striped table-hover table-bordered" id="dynamic_field">
                                    </table> 
                                </div>  
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary" id="btn-submit" name="btn-submit" onclick="myFunction()">Save</button>
                                <input type="reset" value="Reset" class="btn btn-secondary">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

@if ($errors->any())
@if (!empty(old('contactName')) or !empty(old('phone')) or !empty(old('email')))
<script type="text/javascript">
    $("#dynamic_field tr").remove(); 
    var $i=1;
    var $a0 = @json(old('tableid'));
    var $a1 = @json(old('contactName'));
    var $a2 = @json(old('phone'));
    var $a3 = @json(old('email'));


    var $count = Math.max($a1.length, $a2.length, $a3.length);
    for ($i=0; $i<$count;$i++){
        $val1=$val2=$val3="";
        if ($a0[$i]) {
            $val0 = $a0[$i];
        }
        if ($a1[$i]) {
            $val1 = $a1[$i];
        }
        if ($a2[$i]) {
            $val2 = $a2[$i];
        }
        if ($a3[$i]) {
            $val3 = $a3[$i];
        }

        $('#dynamic_field').append('<tr id="row'+$i+'" class="dynamic-added"><td class="col-md-12"><div class="row form-group"><div class="col-md-4"><input id="tableid[]" name="tableid[]" value="'+$val0+'" type="hidden"><input id="contactName[]" placeholder="Nama Kontak" name="contactName[]" class="form-control" value="'+$val1+'"></div><div class="col-md-3"><input id="phone[]" name="phone[]" class="form-control" placeholder="No Telepon" value="'+$val2+'"></div><div class="col-md-4"><input id="email[]" name="email[]" class="form-control" placeholder="Email" type="email" value="'+$val3+'"></div><div class="col-md-1"><button type="button" name="remove" id="'+$i+'" class="btn btn-danger btn_remove"><i class="fa fa-trash"></i></button></div></div></td></tr>'); 
    }
</script>
@endif
@else
@if (!empty($contacts))
<script type="text/javascript">
    $("#dynamic_field tr").remove(); 
    var $i=1;
</script>

@foreach ($contacts as $contact)
<script type="text/javascript"> 
    $i++;

    $('#dynamic_field').append('<tr id="row'+$i+'" class="dynamic-added"><td class="col-md-12"><div class="row form-group"><div class="col-md-4"><input id="tableid[]" name="tableid[]"  type="hidden" value="'+{!! json_encode($contact->id) !!}+'"><input id="contactName[]" placeholder="Nama Kontak" name="contactName[]" class="form-control" value="'+{!! json_encode($contact->name) !!}+'"></div><div class="col-md-3"><input id="phone[]" name="phone[]" class="form-control" placeholder="No Telepon" value="'+{!! json_encode($contact->phone) !!}+'"></div><div class="col-md-4"><input id="email[]" name="email[]" class="form-control" placeholder="Email" type="email" value="'+{!! json_encode($contact->email) !!}+'"></div><div class="col-md-1"><button type="button" name="remove" id="'+$i+'" class="btn btn-danger btn_remove"><i class="fa fa-trash"></i></button></div></div></td></tr>');  
</script>
@endforeach
@endif
@endif

@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('header')
@include('partial.header')
@endsection







