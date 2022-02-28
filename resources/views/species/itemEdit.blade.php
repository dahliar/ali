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
    @if (session('status'))
    <script type="text/javascript">
        swal.fire("Success",  "{{session('status')}}" , "info");
    </script>
    @endif
    <div class="row">
        <form id="formTambahItem" action="{{url('itemEditStore')}}" method="get" name="formTambahItem">
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
                            <li class="breadcrumb-item active">Ubah</li>
                        </ol>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-1">
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Item Name</span>
                            </div>
                            <div class="col-md-5">
                                <input id="name" name="name" type="text" class="form-control text-md-right" value="{{$item->itemName}}" readonly>
                                <input id="itemId" name="itemId" type="text" class="form-control text-md-right" value="{{$item->id}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Species</span>
                            </div>
                            <div class="col-md-5">
                                <input id="checker" name="checker" type="text" class="form-control text-md-right" value="{{$item->speciesName}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Size</span>
                            </div>
                            <div class="col-md-5">
                                <input id="size" name="size" type="text" class="form-control text-md-right" value="{{$item->sizeName}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Grade</span>
                            </div>
                            <div class="col-md-5">
                                <input id="grade" name="grade" type="text" class="form-control text-md-right" value="{{$item->gradeName}}" readonly>

                            </div>
                        </div>                      
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Packing Type</span>
                            </div>
                            <div class="col-md-5">
                                <input id="packing" name="packing" type="text" class="form-control text-md-right" value="{{$item->packingName}}" readonly>

                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Freeze Type</span>
                            </div>
                            <div class="col-md-5">
                                <input id="freezing" name="freezing" type="text" class="form-control text-md-right" value="{{$item->freezingName}}" readonly>

                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Base Price</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text col-2 text-end">Rp</span>
                                    <input id="baseprice" name="baseprice" type="text" class="form-control" value="{{$item->baseprice}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Weight Base</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input onchange="editChecker()" id="weightbase" name="weightbase" type="text" class="form-control text-end" value="{{$item->weightbase}}" readonly>
                                    <span class="input-group-text col-4">Kg</span>
                                </div>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Current Amount</span>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input id="amount" name="amount" type="text" class="form-control text-end" value="{{$item->amount}}" readonly>
                                    <span id="spanAmount" class="input-group-text col-4">Kg</span>
                                </div>
                            </div>
                        </div> 
                        <div class="row form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-3 text-md-right">
                                <span class="label">Status</span>
                            </div>
                            <div class="col-md-3">
                                <select id="isActive" name="isActive" class="form-select" >
                                    <option value="1" @if($item->isActive == 1) selected @endif>Aktif</option>
                                    <option value="0" @if($item->isActive == 0) selected @endif>Non-Aktif</option>
                                </select>
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