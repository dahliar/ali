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
    <div class="row">

    </div>
</div>
@else
@include('partial.noAccess')
@endif

@endsection