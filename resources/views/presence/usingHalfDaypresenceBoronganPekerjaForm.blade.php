<meta name="csrf-token" content="{{ csrf_token() }}" />
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
</script>


<script type="text/javascript">


    function workerAddedChange($a, $b){
        if ($a){
            document.getElementById("workerAdded").value++;
            document.getElementById("boronganType["+$b+"]").checked = true;         
        } else{
            document.getElementById("workerAdded").value--;
            document.getElementById("boronganType["+$b+"]").checked = false;         
        }
    }
    function boronganTypeChange($b, $index){
        $a = document.getElementById("boronganWorker["+$index+"]").checked;
        if ($b){
            if(!document.getElementById("boronganWorker["+$index+"]").checked){
                document.getElementById("boronganWorker["+$index+"]").checked = true;
                document.getElementById("workerAdded").value++;
            }
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
            <form id="formTambahPekerjaBorongan" action="{{url('storePekerjaBorongan')}}/{{$borongan->id}}" method="POST" name="formTambahPekerjaBorongan">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="row form-inline">
                            <h5 class="modal-title" id="exampleModalLabel">Daftar Pekerja Borongan</h5>
                        </div>
                    </div>
                    <div class="modal-body">
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
                </div>
                <div class="modal-content">
                    <div class="modal-body">
                        <table style="width: 100%" class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="width: 5%; text-align:center">Masuk Kerja</th>
                                    <th style="width: 5%; text-align:center">Full day</th>
                                    <th style="width: 10%;">NIP</th>
                                    <th style="width: 20%;">Nama</th>
                                    <th style="width: 10%;">Jenis</th>
                                    <th class="table-dark"></th>
                                    <th>No</th>
                                    <th style="width: 5%; text-align:center">Masuk Kerja</th>
                                    <th style="width: 5%; text-align:center">Full day</th>
                                    <th style="width: 10%;">NIP</th>
                                    <th style="width: 20%;">Nama</th>
                                    <th style="width: 10%;">Jenis</th>
                                    <th class="table-dark"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                $no=0; 
                                $noLeft=1;
                                $noRight=2;

                                @endphp
                                @foreach($employees->split($employees->count()/2) as $row)
                                <tr>
                                    @foreach($row as $employee)
                                    <td>
                                        @if(($no % 2)==0)
                                        {{$noLeft}}
                                        @php $noLeft+=2;    @endphp
                                        @else
                                        {{$noRight}}
                                        @php $noRight+=2;    @endphp
                                        @endif
                                    </td>
                                    <td style="width: 5%; text-align:center">
                                        <input id="boronganWorker[{{$no}}]" type="checkbox" class="form-check-input" onchange="workerAddedChange(this.checked, {{$no}})" name="boronganWorker[{{$no}}]" value="{{$employee->empid}}">
                                    </td>
                                    <td style="width: 5%; text-align:center">
                                        <input id="boronganType[{{$no}}]" type="checkbox" class="form-check-input" name="boronganType[{{$no}}]" value="{{$employee->empid}}" onchange="boronganTypeChange(this.checked, {{$no}})" >
                                    </td>
                                    <td style="width: 10%;">{{$employee->nip}}</td>
                                    <td style="width: 20%;">{{$employee->nama}}</td>
                                    <td style="width: 10%;">{{$employee->employmentStatus}}</td> 
                                    <td class="table-dark"></td>
                                    @php $no+=1;    @endphp
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row form-group text-center">
                    <div class="col-md-5">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <input type="reset" value="Reset" class="btn btn-secondary">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@else
@include('partial.noAccess')
@endif

@endsection