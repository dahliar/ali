@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::check())
<div class="container-fluid">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb primary-color">
                <li class="breadcrumb-item active">Home</li>
            </ol>
        </nav>
    </div>
</div>
@else
@include('partial.noAccess')
@endif

@endsection

