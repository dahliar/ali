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

<body">
    <div class="container-fluid">
        <form id="formApplicationMapping" action="{{url('applicationMappingStore')}}" method="POST" name="formApplicationMapping">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row form-inline">
                        <h5 class="modal-title" id="exampleModalLabel">Daftar Aplikasi dan Pages</h5>
                    </div>
                </div>
            </div>
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row form-inline">
                        <input type="hidden" name="uid" value="{{$user->uid}}">
                        <div class="row form-group">
                            <div class="col-md-1 text-end">
                            </div>
                            <div class="col-md-2 text-left">
                                <span class="label">Nama</span>
                            </div>
                            <div class="col-md-4">
                                <input id="name" name="name" class="form-control" value="{{ $user->name}}" readonly>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1 text-end">
                            </div>
                            <div class="col-md-2 text-left">
                                <span class="label">Username</span>
                            </div>
                            <div class="col-md-4">
                                <input id="uname" name="uname" class="form-control" value="{{ $user->uname}}" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-1 text-end">
                            </div>
                            <div class="col-md-2 text-left">
                                <span class="label">Bagian - Jabatan</span>
                            </div>
                            <div class="col-md-4">
                                <input id="jabatan" name="jabatan" class="form-control" value="{{ $user->jabatan}} - {{ $user->bagian}}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <table style="width: 50%;" class="center table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%;text-align: center;">No</th>
                                <th style="width: 5%;text-align: center;">Pilih</th>
                                <th style="width: 45%;">Aplikasi</th>
                                <th style="width: 45%;">Page</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                            $no=1;
                            @endphp
                            @foreach($pages as $page)
                            <tr>
                                <td style="width: 5%;text-align: center;">
                                    @php echo $no @endphp 
                                </td>
                                <td style="width: 5%;text-align: center;">
                                    <input id="mapping[{{$page->pageId}}]" type="checkbox" class="form-check-input" name="mapping[{{$page->pageId}}]" value="{{$page->pageId}}" @if ($page->upmPageId != null) checked @endif >
                                    <input id="mappingHidden[{{$page->pageId}}]" type="hidden" class="form-control" name="mappingHidden[{{$page->pageId}}]" value="@if ($page->upmPageId != null) {{$page->pageId}} @endif"  >
                                </td>
                                <td style="width: 45%;">{{$page->applicationName}}</td>
                                <td style="width: 45%;">{{$page->pageName}}</td>
                                @php $no+=1;    @endphp                                    
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <div class="col-md-5">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <input type="reset" value="Reset" class="btn btn-secondary">
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
@endsection