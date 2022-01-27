@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')

@if (Auth::check() and Session::has('employeeId'))
<div class="container-fluid">

</div>
@else
@include('partial.noAccess')
@endif

@endsection