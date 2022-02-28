<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if ((Auth::user()->isProduction() or Auth::user()->isAdmin()) and Session::has('employeeId') and Session()->get('levelAccess') <= 3)
<script type="text/javascript"> 

    function editChecker(){
        var checker="";
        var e = document.getElementById("size");
        var size = e.options[e.selectedIndex].value;

        e = document.getElementById("grade");
        var grade = e.options[e.selectedIndex].value;

        e = document.getElementById("packing");
        var packing = e.options[e.selectedIndex].value;

        e = document.getElementById("freezing");
        var freezing = e.options[e.selectedIndex].value;

        var weightbase = document.getElementById("weightbase").value;

        checker = checker.concat(size,':',grade,':',packing,':',freezing,':',weightbase);
        document.getElementById("checker").value = checker;
    }
    $(document).ready(function() {
        $('#packing').on('change', function() {
            var e = document.getElementById("packing");
            var teks = e.options[e.selectedIndex].text;
            document.getElementById("spanAmount").textContent=teks;
            editChecker();
        });
    });
</script>
@if (session('success'))
<script type="text/javascript">
    swal.fire("Success", "Data item berhasil ditambahkan", "info");
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


<div class="container-fluid">
    <div class="row">
        <form id="formTambahItem" action="{{url('itemCreateStore')}}" method="post" name="formTambahItem" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb primary-color my-auto">
                            <li class="breadcrumb-item">
                                <a class="white-text" href="{{ url('/home') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a class="white-text" href="{{ url('itemList')}}">Items</a>
                            </li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Species*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="speciesId" name="speciesId" type="hidden" class="form-control text-md-right" value="{{$species->id}}" readonly>
                                <input id="speciesName" name="speciesName" type="text" class="form-control text-md-right" value="{{$species->name}}" readonly>
                                <input id="checker" name="checker" type="hidden" class="form-control text-md-right" value="{{old('checker')}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Size*</span>
                            </div>
                            <div class="col-md-5">
                                <select onchange="editChecker()" class="form-select w-100" id="size" name="size">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($sizes as $size)
                                    @if ( $size->id == old('size') )
                                    <option value="{{ $size->id }}" selected>{{ $size->name }}</option>
                                    @else
                                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Grade*</span>
                            </div>
                            <div class="col-md-5">
                                <select onchange="editChecker()" class="form-select w-100" id="grade" name="grade">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($grades as $grade)
                                    @if ( $grade->id == old('grade') )
                                    <option value="{{ $grade->id }}" selected>{{ $grade->name }}</option>
                                    @else
                                    <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>                      
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Packing Type*</span>
                            </div>
                            <div class="col-md-5">
                                <select class="form-select w-100" id="packing" name="packing">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($packings as $packing)
                                    @if ( $packing->id == old('packing') )
                                    <option value="{{ $packing->id }}" selected>{{ $packing->name }}</option>
                                    @else
                                    <option value="{{ $packing->id }}">{{ $packing->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Freeze Type*</span>
                            </div>
                            <div class="col-md-5">
                                <select onchange="editChecker()" class="form-select w-100" id="freezing" name="freezing">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($freezings as $freezing)
                                    @if ( $freezing->id == old('freezing') )
                                    <option value="{{ $freezing->id }}" selected>{{ $freezing->name }}</option>
                                    @else
                                    <option value="{{ $freezing->id }}">{{ $freezing->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Item Name*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="name" name="name" type="text" class="form-control text-md-right" value="{{old('name')}}" placeholder="Nama harus unik untuk species & size tersebut">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Base Price*</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text col-4">Rp</span>
                                    <input id="baseprice" name="baseprice" type="text" class="form-control text-end" value="{{old('baseprice')}}" placeholder="gunakan titik untuk pecahan">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Weight Base*</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input onchange="editChecker()" id="weightbase" name="weightbase" type="text" class="form-control text-end" value="{{old('weightbase')}}" placeholder="gunakan titik untuk pecahan">
                                    <span class="input-group-text col-4">Kg</span>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Initial Amount*</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="amount" name="amount" type="text" class="form-control text-end" value="{{old('amount')}}" placeholder="gunakan titik untuk pecahan">
                                    <span id="spanAmount" class="input-group-text col-4">-</span>
                                </div>
                            </div>
                        </div>   
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Gambar</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="imageurl" name="imageurl">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
</div>
@else
@include('partial.noAccess')
@endif

@endsection