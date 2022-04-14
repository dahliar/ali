@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
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


<script type="text/javascript">
    $(document).ready(function() {
        $('#selectAll').click(function() {
            if ($(this).prop('checked')) {
                var inputs = document.getElementsByTagName("input");
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].checked = true;
                }
            } else {
                var inputs = document.getElementsByTagName("input");
                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].checked = false;
                }
            }
        });
    });

</script>

<body">
    <div class="container-fluid">
        <form id="formPageMapping" action="{{url('pageMappingStore')}}" method="POST" name="formApplicationMapping">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row form-inline">
                        <h5 class="modal-title" id="exampleModalLabel">Daftar Aplikasi dan Pages</h5>
                    </div>
                </div>
            </div>
            <div class="modal-content">                
                <div class="modal-body">
                    <div class="row form-inline">
                        <input type="hidden" name="pageId" id="pageId" value="{{$page->id}}">
                        <div class="row form-group">
                            <div class="col-md-2 text-left">
                                <span class="label">Aplikasi</span>
                            </div>
                            <div class="col-md-7">
                                <input id="name" name="name" class="form-control" value="{{ $aplikasi }}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-2 text-left">
                                <span class="label">Page Name</span>
                            </div>
                            <div class="col-md-7">
                                <input id="name" name="name" class="form-control" value="{{ $page->name}}" readonly>
                            </div>
                        </div>                        
                        <div class="row form-group">
                            <div class="col-md-2 text-end">
                            </div>
                            <div class="col-md-7">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <input type="reset" value="Reset" class="btn btn-secondary">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-content">                
                <div class="modal-body">
                    <table style="width: 100%;" class="center table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%;text-align: center;"></th>
                                <th style="width: 5%;text-align: center;">
                                    <input id="selectAll" type="checkbox" class="form-check-input" name="selectAll">
                                </th>
                                <th colspan="3" style="text-align: left;">
                                    <label for="selectAll">Select All</label><br>
                                </th>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th style="width: 5%;text-align: center;">No</th>
                                <th style="width: 5%;text-align: center;">Pilih</th>
                                <th style="width: 25%;">Nama</th>
                                <th style="width: 40%;">Jabatan</th>
                                <th style="width: 25%;">Bagian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                            $no=1;
                            @endphp
                            @foreach($users as $user)
                            <tr>
                                <td style="text-align: center;">
                                    @php echo $no @endphp 
                                </td>
                                <td style="text-align: center;">

                                    <input id="mapping[]" type="checkbox" class="form-check-input" name="mapping[]" value="{{$user->id}}" @if ($user->upmid != null) checked @endif >
                                    <input id="mappingHidden[]" type="hidden" class="form-control" name="mappingHidden[]" value="@if ($user->upmid != null) {{$user->id}} @endif"  >
                                </td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->jabatan}}</td>
                                <td>{{$user->bagian}}</td>
                                @php $no+=1;    @endphp                                    
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-body">
                    <div class="row form-inline">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <input type="reset" value="Reset" class="btn btn-secondary">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
@endsection