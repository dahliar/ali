@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')
@if (Auth::check() and Session::has('employeeId') and (session()->get('accessLevel') <= 40))
<!--
<script src="https://www.google.com/jsapi"></script>
-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<style>
	.chart {
		width: 600px;
		height: 400px;
		margin: 0 auto;
	}
	.text-center{
		text-align: center;
	}
</style>
<script type="text/javascript">
	var employeeStatus = @json($arrEmployeeStatus);
	var transaction = @json($arrTransaction);
	var stocks = @json($stocks);

	window.onload = function() {
		google.load("visualization", "current", {
			packages: ["corechart"],
			callback: 'drawEmployees'
		});
		google.load("visualization", "current", {
			packages: ["corechart"],
			callback: 'drawTransactions'
		});
		google.charts.load('current', {packages: ['corechart', 'bar']});
		google.charts.setOnLoadCallback(drawStocks);
	};
	function drawEmployees() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Pegawai');
		data.addColumn('number', 'Jumlah');
		data.addRows(employeeStatus);

		var options = {
			pieHole: 0.4,
			title: 'Daftar pegawai status aktif',
		};

		var chart = new google.visualization.PieChart(document.getElementById('chartPegawaiAktif'));
		chart.draw(data, options);
	}
	function drawTransactions() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Transaksi');
		data.addColumn('number', 'Jumlah');
		data.addRows(transaction);

		var options = {
			pieHole: 0.4,
			title: 'Jumlah transaksi tahun 2002',
		};

		var chart = new google.visualization.PieChart(document.getElementById('chartTransaksi'));
		chart.draw(data, options);
	}
	function drawStocks() {
		var data = [];
		var header=["Barang", "Jumlah", { role: "style" } ];
		data.push(header);
		for (var i = 0; i < stocks.length; i++) {
			var temp=[];
			temp.push(stocks[i].name);
			temp.push(stocks[i].jumlahSpecies);
			temp.push(stocks[i].kedua);
			data.push(temp);
		}
		var chartdata = new google.visualization.arrayToDataTable(data);
		var view = new google.visualization.DataView(chartdata);
		view.setColumns([0, 1,
			{ calc: "stringify",
			sourceColumn: 1,
			type: "string",
			role: "annotation" },
			2]);

		var options = {
			title: 'Data informasi stock barang (Kg)',
			height: 500,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		};

		var chart = new google.visualization.ColumnChart(document.getElementById('chartStock'));
		chart.draw(view, options);
	}
</script>
<body>
	<div class="container-fluid">
		<div class="modal-content">
			<div class="modal-header">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb primary-color">
						<li class="breadcrumb-item">
							<a class="white-text" href="{{ url('/home') }}">Dashboard</a>
						</li>
					</ol>
				</nav>
			</div>
		</div>

		<div class="modal-content">
			<div class="modal-body">
				<div class="row">
					<div id="chartStock"></div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div id="chartPegawaiAktif" class="chart"></div>
					</div>
					<div class="col-md-6">
						<div id="chartTransaksi" class="chart"></div>
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