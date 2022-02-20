<meta name="csrf-token" content="{{ csrf_token() }}" />
@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if (Auth::user()->isMarketing() or Auth::user()->isAdmin())
<script type="text/javascript"> 
</script>


<script type="text/javascript">


    function workerAddedChange($a){
        if ($a){
            document.getElementById("workerAdded").value++;
            
        } else{
            document.getElementById("workerAdded").value--;
        }
    }
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
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb primary-color">
                <li class="breadcrumb-item">
                    <a class="white-text" href="{{ url('/home') }}">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="white-text" href="{{ url('/boronganList') }}">Borongan</a>
                </li>
                <li class="breadcrumb-item active">Tambah Pekerja Borongan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid">
    <div class="row form-group">
        <div class="d-grid gap">
            <form id="formTambahPekerjaBorongan" action="{{url('storePekerjaBorongan')}}" method="POST" name="formTambahPekerjaBorongan">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="row form-inline">
                            <h5 class="modal-title" id="exampleModalLabel">Daftar Pekerja Borongan</h5>
                        </div>
                    </div>
                    <div class="modal-header">
                        <div class="row form-inline">
                            <input type="hidden" value="{{$borongan->id}}"id="boronganId" name="boronganId" class="form-control" readonly>
                            <div class="row form-group">
                                <div class="col-md-3 text-end">
                                    <label class="form-label">Upah bersih</label>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text text-end">Rp.</span>
                                        <input type="text" value="{{$borongan->netPrice}}"id="netPayment" name="netPayment" class="form-control text-end" readonly>
                                        <span class="input-group-text text-end">per Pekerja</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-3 text-end">
                                    <label class="form-label">Jumlah Pekerja Terpilih</label>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text"> Terpilih </span>
                                        <input type="text" value="0" id="workerAdded" name="workerAdded" class="form-control text-end" readonly>
                                        <span class="input-group-text"> orang dari </span>
                                        <input type="text" value="{{$borongan->worker}}"id="worker" name="worker" class="form-control text-end" readonly>
                                        <span class="input-group-text">Orang</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row form-group">
                            @foreach ($employees as $employee)
                            <div class="col-md-3">
                                <input id="boronganWorker[]" type="checkbox" class="form-check-input" onclick="workerAddedChange(this.checked)" name="boronganWorker[]" value="{{$employee->empid}}"> {{$employee->nama}}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <input type="reset" value="Reset" class="btn btn-secondary">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@else
@include('partial.noAccess')
@endif

@endsection