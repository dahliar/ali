@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::user()->isAdmin())
<div class="container-fluid">
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb primary-color">
                <li class="breadcrumb-item">
                    <a class="white-text" href="{{ url('/home') }}">Home</a>
                </li>
                <li class="breadcrumb-item active">
                    <a class="white-text" href="">Species</a>
                </li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>
@else
@include('partial.noAccess')
@endif

@endsection