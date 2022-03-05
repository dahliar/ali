@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection


@section('content')
@if (Auth::check() and Session::has('employeeId') and (session()->get('levelAccess') <= 3))
<body>
	<div class="container-fluid">
		<div class="container-fluid">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-md-9">
						<nav aria-label="breadcrumb" class="navbar navbar-expand-lg navbar-light">
							<ol class="breadcrumb primary-color">
								<li class="breadcrumb-item">
									<a class="white-text" href="{{ url('/home') }}">Dashboard</a>
								</li>
							</ol>
						</nav>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-2">
							<h3>Penggajian</h3>
						</div>
						<div class="col-md-9">
							<table class="table table-striped table-hover table-bordered data-table"  id="datatable">
								<thead>
									<tr>
										<th>No</th>
										<th>Keterangan</th>
										<th>Harian</th>
										<th>Borongan</th>
										<th>Lembur</th>
										<th>Honorarium</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>Belum Generate</td>
										<td>{{$ungenerate[1]}} karyawan</td>
										<td>{{$ungenerate[2]}} karyawan</td>
										<td>{{$ungenerate[0]}} karyawan</td>
										<td>{{$ungenerate[3]}} karyawan</td>
									</tr>
									<tr>
										<td>2</td>
										<td>Belum Terbayar</td>
										<td>{{$unpaid[1]}} karyawan</td>
										<td>{{$unpaid[2]}} karyawan</td>
										<td>{{$unpaid[0]}} karyawan</td>
										<td>{{$unpaid[3]}} karyawan</td>
									</tr>
								</tbody>
							</table> 
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<h3>Stock</h3>
						</div>
						<div class="col-md-9">
							<table class="table table-striped table-hover table-bordered data-table"  id="datatable">
								<thead>
									<tr>
										
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
@else
@include('partial.noAccess')
@endif

@endsection