<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
<script type="text/javascript"> 
    function editChecker(){
        var e = document.getElementById("size");
        var $size = e.options[e.selectedIndex].value;

        e = document.getElementById("shape");
        $shape = e.options[e.selectedIndex].value;

        e = document.getElementById("grade");
        var $grade = e.options[e.selectedIndex].value;

        e = document.getElementById("packing");
        var $packing = e.options[e.selectedIndex].value;

        e = document.getElementById("freezing");
        var $freezing = e.options[e.selectedIndex].value;

        var $weightbase = document.getElementById("weightbase").value;
        //if (  ($size!=-1) && ($shape!=-1) && ($grade!=-1) && ($packing!=-1) && ($freezing!=-1) && ($weightbase)  && ($name) ) {

        if (  ($size!=-1) && ($shape!=-1) && ($grade!=-1) && ($packing!=-1) && ($freezing!=-1) && ($weightbase) ) {
            $.ajax({
                url: '{{ url("getIsItemAlreadyExist") }}',
                type: "POST",
                data: {
                    "_token":"{{ csrf_token() }}",
                    shape: $shape,
                    //name: $name,
                    size: $size,
                    grade: $grade,
                    packing: $packing,
                    freezing: $freezing,
                    weightbase: $weightbase
                },
                dataType: "json",
                success:function(data){
                    var myspan = document.getElementById('info');
                    if (data==1)
                    {
                        document.getElementById("buttonSubmit").disabled=true;
                        myspan.innerText = "Item barang sudah digunakan";
                    } else {
                        document.getElementById("buttonSubmit").disabled=false;
                        myspan.innerText = "";
                    }
                    
                }
            });
        }

        return false;
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
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Species*</span>
                            </div>
                            <div class="col-md-5">
                                <input id="speciesId" name="speciesId" type="hidden" class="form-control" value="{{$species->id}}" readonly>
                                <input id="speciesName" name="speciesName" type="text" class="form-control" value="{{$species->nameBahasa}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
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
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
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
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Packing Type*</span>
                            </div>
                            <div class="col-md-5">
                                <select onchange="editChecker()" class="form-select w-100" id="packing" name="packing">
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
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Bentuk Olahan*</span>
                            </div>
                            <div class="col-md-5">
                                <select onchange="editChecker()" class="form-select w-100" id="shape" name="shape">
                                    <option value="-1">--Choose One--</option>
                                    @foreach ($shapes as $shape)
                                    @if ( $shape->id == old('shape') )
                                    <option value="{{ $shape->id }}" selected>{{ $shape->name }}</option>
                                    @else
                                    <option value="{{ $shape->id }}">{{ $shape->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
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
                            <!--
                            <div class="row form-group">
                                <div class="col-md-1"></div>
                                <div class="col-md-3 text-md-end">
                                    <span class="label">Item Name*</span>
                                </div>
                                <div class="col-md-5">
                                    <input onchange="editChecker()" id="name" name="name" type="text" class="form-control text-md-end" value="{{old('name')}}" placeholder="Nama harus unik untuk species & size tersebut">
                                </div>
                            </div>
                        -->
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
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
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Weight Base*</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input onchange="editChecker()" id="weightbase" name="weightbase" type="text" class="form-control text-end" value="{{old('weightbase')}}" placeholder="gunakan titik untuk pecahan">
                                    <span class="input-group-text col-4">Kg</span>
                                </div>
                            </div>
                            <div class="col-md-3 text-md-left">
                                <span class="label">berat dalam 1 packing/mc</span>
                            </div>

                        </div>

                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Initial Amount*</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input id="amount" name="amount" type="text" class="form-control text-end" value="{{old('amount')}}" placeholder="gunakan titik untuk pecahan">
                                    <span id="spanAmount" class="input-group-text col-4">-</span>
                                </div>
                            </div>
                            <div class="col-md-3 text-md-left">
                                <span class="label">Jumlah berat barang awal</span>
                            </div>

                        </div>   
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label">Gambar</span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input class="form-control" type="file" id="imageurl" name="imageurl">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1"></div>
                            <div class="col-md-3 text-md-end">
                                <span class="label"></span>
                            </div>
                            <div class="col-md-5">
                                <span id="info" style="color:red" class="col-4"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button id="buttonSubmit" type="submit" class="btn btn-primary">Save</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection