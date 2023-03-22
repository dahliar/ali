@extends('layouts.layout')

@section('header')
@include('partial.header')
@endsection

@section('footer')
@include('partial.footer')
@endsection

@section('content')

<style>
/* Googlefont Poppins CDN Link */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
*{
	margin: 0;
	padding: 0;
	box-sizing: border-box;
	font-family: 'Poppins', sans-serif;
}
.home-section{
	position: relative;
	/*background: #f5f5f5;*/
	min-height: 50vh;
	left: 0px;
	transition: all 0.5s ease;
}
.home-section .home-content{
	position: relative;
	padding-top: 0px;
}
.home-content .overview-boxes{
	display: flex;
	align-items: center;
	justify-content: space-between;
	flex-wrap: wrap;
	padding: 0 20px;
	margin-bottom: 6px;
}
.overview-boxes .box{
	height: 60px;
	display: flex;
	align-items: center;
	justify-content: left;
	width: calc(100% / 4 - 15px);
	background: #fff;
	padding: 15px 14px;
	border-radius: 12px;
	box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}
.overview-boxes .box-topic{
	font-size: 14px;
	font-weight: 500;
}
.home-content .box .number{
	display: inline-block;
	font-size: 12px;
	margin-top: -6px;
	font-weight: 500;
}
.right-side{
	margin-left:5%;
}
</style>
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

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<section class="home-section">
		<div class="home-content">
			<div class="overview-boxes">
				<div class="box">
					<div class="right-side">
						<div class="box-topic">Stock</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('itemStockList')}}'" target="_blank">
						<i class="fas fa-box"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Packed</div>
						<div class="number">{{number_format($datas['jumlahPacked'], 2, ',', '.').' Kg'}}</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-secondary" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('itemStockList')}}'" target="_blank">
						<i class="fas fa-box-open"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Unpacked</div>
						<div class="number">{{number_format($datas['jumlahUnpacked'], 2, ',', '.').' Kg'}}</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-success" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('itemStockList')}}'" target="_blank">
						<i class="fab fa-docker"></i>
					</button>

					<div class="right-side">
						<div class="box-topic">Sailing</div>
						<div class="number">{{number_format($datas['totalSailing'], 2, ',', '.').' Kg'}}</div>
					</div>
				</div>
			</div>

			<div class="overview-boxes">
				<div class="box">
					<div class="right-side">
						<div class="box-topic">Transaction - Export</div>
					</div>
				</div>
				
				<div class="box">
					<button class="btn btn-warning" data-toggle="tooltip" data-placement="top" data-container="body" onclick="window.location='{{ url('transactionList')}}'" target="_blank">
						<i class="fas fa-file-alt"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Offering</div>
						<div class="number">{{$datas['offeringExport']}} transaction</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('transactionList')}}'" target="_blank">
						<i class="fas fa-file-signature"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Sailing</div>
						<div class="number">{{$datas['sailingExport']}} transaction</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-success" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('transactionList')}}'" target="_blank">
						<i class="fas fa-tasks"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Total {{$datas['currentYear']}}</div>
						<div class="number">{{$datas['allExport']}}  transaction</div>
					</div>
				</div>
			</div>
			<div class="overview-boxes">
				<div class="box">
					<div class="right-side">
						<div class="box-topic">Transaction - Lokal</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-warning" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('localTransactionList')}}'" target="_blank">
						<i class="fas fa-file-alt"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Offering</div>
						<div class="number">{{$datas['offeringLocal']}} transaction</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('localTransactionList')}}'" target="_blank">
						<i class="fas fa-file-signature"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Sailing</div>
						<div class="number">{{$datas['sailingLocal']}} transaction</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-success" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('localTransactionList')}}'" target="_blank">
						<i class="fas fa-tasks"></i>
					</button>

					<div class="right-side">
						<div class="box-topic">Total  {{$datas['currentYear']}}</div>
						<div class="number">{{$datas['allLocal']}}  transaction</div>
					</div>
				</div>
			</div>
			<div class="overview-boxes">
				<div class="box">
					<div class="right-side">
						<div class="box-topic">Purchase</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-success" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('purchaseList')}}'" target="_blank">
						<i class="fas fa-shopping-cart"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Unfinish</div>
						<div class="number">{{$datas['pembelian']}} transaction</div>
					</div>
				</div>
				<div class="box">
				</div>
				<div class="box">
					<button class="btn btn-success" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('purchaseList')}}'" target="_blank">
						<i class="fas fa-shopping-cart"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Total {{$datas['currentYear']}}</div>
						<div class="number">{{$datas['allPembelian']}} transaction</div>
					</div>
				</div>
			</div>
			<div class="overview-boxes">
				<div class="box">
					<div class="right-side">
						<div class="box-topic">Stock</div>
					</div>
				</div>				
				<div class="box">
					<button class="btn btn-info" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('itemStockApprovalPenambahan')}}'" target="_blank">
						<i class="fas fa-plus-circle"></i>
					</button>

					<div class="right-side">
						<div class="box-topic">Penambahan Stock</div>
						<div class="number">{{$tambah}} belum approve</div>
					</div>
				</div>
				<div class="box">
					<button class="btn btn-warning" data-toggle="tooltip" data-placement="top" data-container="body"onclick="window.location='{{ url('itemStockApprovalPengurangan')}}'" target="_blank">
						<i class="fas fa-minus-circle"></i>
					</button>
					<div class="right-side">
						<div class="box-topic">Pengurangan Stock</div>
						<div class="number">{{$kurang}} belum approve</div>
					</div>
				</div>
				<div class="box">
				</div>

			</div>
		</section>
	</body>
	</html>


	<body>
		<div class="container-fluid">
			<div class="card card-header">
				<form action="{{url('home')}}" method="get">
					{{ csrf_field() }}
					<div class="row form-group">
						<div class="col-md-3">
							<select id="tahun" name="tahun" class="form-select" >
								@if(empty($tahun))
								<option value="-1" selected>--Pilih tahun--</option>
								<option value="2022">2022</option>
								<option value="2023">2023</option>
								@else
								<option value="-1"   @if(old('tahun', $tahun) == -1) selected @endif>--Pilih tahun--</option>
								<option value="2022" @if(old('tahun', $tahun) == 2022) selected @endif>2022</option>
								<option value="2023" @if(old('tahun', $tahun) == 2023) selected @endif>2023</option>
								@endif
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" id="hitButton" class="form-control btn-primary"><i class="fa fa-search"></i>Show Data</button>
						</div>
					</div>
				</form>
			</div>


			@isset ($tahun)
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
				var employees= @json($employees);
				var transactions = @json($transactions);
				var stocks = @json($stocks);
				var employeesGender = @json($employeesGender);
				var employeesGenderByTypes = @json($employeesGenderByTypes);
				var transactionRupiah = @json($transactionRupiah);
				var transactionUSD = @json($transactionUSD);
				var purchases = @json($purchases);
				var transactionUSDLine = @json($transactionUSDLine);	
				var transactionRupiahLine = @json($transactionRupiahLine);	
				var purchaseRupiahLine = @json($purchaseRupiahLine);	


				window.onload = function() {
					google.charts.load('current', {packages: ['corechart'], 'language': 'id'});

					google.charts.setOnLoadCallback(drawEmployees);
					google.charts.setOnLoadCallback(drawEmployeesGender);
					google.charts.setOnLoadCallback(drawGenderByEmployeeTypes);

					google.charts.setOnLoadCallback(drawTransactions);
					google.charts.setOnLoadCallback(drawPurchases);
					google.charts.setOnLoadCallback(drawStocks);
					google.charts.setOnLoadCallback(drawTransactionsRupiah);
					google.charts.setOnLoadCallback(drawTransactionsUSD);

					google.charts.load('current', {'packages':['line'], 'language': 'id'});
					google.charts.setOnLoadCallback(drawTransactionUSDLine);		
					google.charts.setOnLoadCallback(drawTransactionRupiahLine);	
					google.charts.setOnLoadCallback(drawPurchaseRupiahLine);	



				};


				function drawEmployees() {
					var data = [];
					var header=["Pegawai", "Jumlah"];
					data.push(header);
					for (var i = 0; i < employees.length; i++) {
						var temp=[];
						temp.push(employees[i].empStatus+" "+parseInt(employees[i].status));
						temp.push(parseInt(employees[i].status));
						data.push(temp);
					}
					var chartdata = new google.visualization.arrayToDataTable(data);
					var view = new google.visualization.DataView(chartdata);

					var options = {
						pieHole: 0.4,
						title: 'Status Kepegawaian',
					};

					var chart = new google.visualization.PieChart(document.getElementById('chartPegawaiAktif'));
					chart.draw(view, options);
				}
				function drawEmployeesGender() {
					var data = [];
					var header=["Pegawai", "Jumlah"];
					data.push(header);
					for (var i = 0; i < employeesGender.length; i++) {
						var temp=[];
						temp.push(employeesGender[i].gender);
						temp.push(parseInt(employeesGender[i].jumlahGender));
						data.push(temp);
					}
					var chartdata = new google.visualization.arrayToDataTable(data);
					var view = new google.visualization.DataView(chartdata);

					var options = {
						pieHole: 0.4,
						title: 'Jenis kelamin pegawai',
					};

					var chart = new google.visualization.PieChart(document.getElementById('chartGender'));
					chart.draw(view, options);
				}

				function drawGenderByEmployeeTypes() {
					var data = [];
					var header=["Jenis Kepegawaian", "Perempuan", "Laki-laki"];
					data.push(header);
					for (var i = 0; i < employeesGenderByTypes.length; i++) {
						var temp=[];
						temp.push(employeesGenderByTypes[i].empStatus);
						temp.push(parseInt(employeesGenderByTypes[i].jumlahGenderPerempuan));
						temp.push(parseInt(employeesGenderByTypes[i].jumlahGenderLaki));
						data.push(temp);
					}

					var chartdata = new google.visualization.arrayToDataTable(data);
					var view = new google.visualization.DataView(chartdata);
					var options = {
						title: 'Jenis kelamin per jenis karyawan',
						chartArea: {width: '50%'},
						hAxis: {
							title: 'Jenis Kelamin',
							minValue: 0,
							textStyle: {
								bold: true,
								fontSize: 12,
								color: '#4d4d4d'
							},
							titleTextStyle: {
								bold: true,
								fontSize: 18,
								color: '#4d4d4d'
							}
						},
						vAxis: {
							title: 'Jumlah',
							textStyle: {
								fontSize: 14,
								bold: true,
								color: '#848484'
							},
							titleTextStyle: {
								fontSize: 14,
								bold: true,
								color: '#848484'
							}
						}
					};
					var chart = new google.visualization.ColumnChart(document.getElementById('chartGenderByEmployeeTypes'));
					chart.draw(view, options);
				}




				function drawTransactions() {
					var data = [];
					var header=["Transaksi", "Jumlah"];
					data.push(header);
					for (var i = 0; i < transactions.length; i++) {
						var temp=[];
						temp.push(transactions[i].jenis);
						temp.push(parseInt(transactions[i].jumlahJenis));
						data.push(temp);
					}
					var chartdata = new google.visualization.arrayToDataTable(data);
					var view = new google.visualization.DataView(chartdata);
					var options = {
						pieHole: 0.4,
						title: 'Transaksi jual tahun 2002',
					};

					var chart = new google.visualization.PieChart(document.getElementById('chartTransaksi'));
					chart.draw(view, options);
				}
				function drawTransactionsRupiah() {
					var data = [];
					var header=["Transaksi", "Jumlah"];
					data.push(header);
					for (var i = 0; i < transactionRupiah.length; i++) {
						var temp=[];
						temp.push(transactionRupiah[i].name);
						temp.push(parseInt(transactionRupiah[i].amount));
						data.push(temp);
					}
					var chartdata = new google.visualization.arrayToDataTable(data);
					var view = new google.visualization.DataView(chartdata);
					var options = {
						pieHole: 0.4,
						title: 'Transaksi Jual dalam Rupiah',
					};

					var chart = new google.visualization.PieChart(document.getElementById('chartTransaksiRupiah'));
					chart.draw(view, options);
				}
				function drawPurchases() {
					var data = [];
					var header=["Pembelian", "Jumlah"];
					data.push(header);
					for (var i = 0; i < purchases.length; i++) {
						var temp=[];
						temp.push(purchases[i].name);
						temp.push(parseInt(purchases[i].amount));
						data.push(temp);
					}
					var chartdata = new google.visualization.arrayToDataTable(data);
					var view = new google.visualization.DataView(chartdata);
					var options = {
						pieHole: 0.4,
						title: 'Transaksi beli dalam Rupiah',
					};

					var chart = new google.visualization.PieChart(document.getElementById('chartPurchases'));
					chart.draw(view, options);
				}
				function drawTransactionsUSD() {
					var data = [];
					var header=["Transaksi", "Jumlah"];
					data.push(header);
					for (var i = 0; i < transactionUSD.length; i++) {
						var temp=[];
						temp.push(transactionUSD[i].name);
						temp.push(parseInt(transactionUSD[i].amount));
						data.push(temp);
					}
					var chartdata = new google.visualization.arrayToDataTable(data);
					var view = new google.visualization.DataView(chartdata);
					var options = {
						pieHole: 0.4,
						title: 'Transaksi Jual dalam USD',
					};

					var chart = new google.visualization.PieChart(document.getElementById('chartTransaksiUSD'));
					chart.draw(view, options);
				}
				function drawStocks() {
					var data = [];
					var header=["Barang", "Jumlah", { role: "style" } ];
					data.push(header);
					for (var i = 0; i < stocks.length; i++) {
						var temp=[];
						temp.push(stocks[i].name);
						temp.push(parseInt(stocks[i].jumlahSpecies));
						temp.push(stocks[i].kedua);
						data.push(temp);
					}
					var chartdata = new google.visualization.arrayToDataTable(data);
					var view = new google.visualization.DataView(chartdata);
					view.setColumns([0, 1,
						{ calc: "stringify",
						sourceColumn: 1,
						type: "string",
						role: "annotation" }]);

					var options = {
						title: 'Stock barang (Kg)',
						height: 500,
						bar: {groupWidth: "100%"},
						legend: { position: "none" },
					};

					var chart = new google.visualization.ColumnChart(document.getElementById('chartStock'));
					chart.draw(view, options);
				}
				function drawTransactionUSDLine() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Bulan');
					data.addColumn('number', 'USD');
					var arrValue = [ 
						["Januari",0],
						["Februari",0],
						["Maret",0],
						["April",0],
						["Mei",0],
						["Juni",0],
						["Juli",0],
						["Agustus",0],
						["September",0],
						["Oktober",0],
						["November",0],
						["Desember",0],
						];

					for (var i = 0; i < transactionUSDLine.length; i++) {
						arrValue[transactionUSDLine[i].bulan-1][1] = Number(transactionUSDLine[i].amount);
					}
					for (var i = 0; i < 12; i++) {
						data.addRows(new Array(arrValue[i]));
					}
					var options = {
						chart: {
							title: 'Transaksi Penjualan tahun 2022',
							subtitle: 'transaksi satuan USD'
						},
						height: 500,
						axes: {
							x: {
								0: {side: 'top'}
							}
						}
					};

					var chart = new google.charts.Line(document.getElementById('chartTransactionUSDLine'));
					chart.draw(data, google.charts.Line.convertOptions(options));
				}
				function drawTransactionRupiahLine() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Bulan');
					data.addColumn('number', 'Rupiah');
					var arrValue = [ 
						["Januari",0],
						["Februari",0],
						["Maret",0],
						["April",0],
						["Mei",0],
						["Juni",0],
						["Juli",0],
						["Agustus",0],
						["September",0],
						["Oktober",0],
						["November",0],
						["Desember",0],
						];

					for (var i = 0; i < transactionRupiahLine.length; i++) {
						arrValue[transactionRupiahLine[i].bulan-1][1] = Number(transactionRupiahLine[i].amount);
					}
					for (var i = 0; i < 12; i++) {
						data.addRows(new Array(arrValue[i]));
					}
					var options = {
						chart: {
							title: 'Transaksi Penjualan tahun 2022',
							subtitle: 'transaksi satuan Rupiah'
						},
						height: 500,
						axes: {
							x: {
								0: {side: 'top'}
							}
						}
					};

					var chart = new google.charts.Line(document.getElementById('chartTransactionRupiahLine'));
					chart.draw(data, google.charts.Line.convertOptions(options));
				}
				function drawPurchaseRupiahLine() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Bulan');
					data.addColumn('number', 'Rupiah');
					var arrValue = [ 
						["Januari",0],
						["Februari",0],
						["Maret",0],
						["April",0],
						["Mei",0],
						["Juni",0],
						["Juli",0],
						["Agustus",0],
						["September",0],
						["Oktober",0],
						["November",0],
						["Desember",0],
						];

					for (var i = 0; i < purchaseRupiahLine.length; i++) {
						arrValue[purchaseRupiahLine[i].bulan-1][1] = Number(purchaseRupiahLine[i].amount);
					}
					for (var i = 0; i < 12; i++) {
						data.addRows(new Array(arrValue[i]));
					}
					var options = {
						chart: {
							title: 'Transaksi Pembelian tahun 2022',
							subtitle: 'transaksi satuan Rupiah'
						},
						height: 500,
						axes: {
							x: {
								0: {side: 'top'}
							}
						}
					};

					var chart = new google.charts.Line(document.getElementById('drawPurchaseRupiahLine'));
					chart.draw(data, google.charts.Line.convertOptions(options));
				}
			</script>
			<div class="card card-body">
				<div class="row">
					<span class="white-text"><h4>Stok Barang</h4></span>
					<div class="col-md-12">
						<div id="chartStock"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<br>
						<br>
						<h5><b>Jumlah minimal barang produksi</b></h5>
						<table class="table table-striped table-hover table-bordered data-table" id="datatable">
							<thead>
								<tr>
									<th>No</th>
									<th>Nama Barang</th>
									<th>Jumlah</th>
									<th>Minimal</th>
								</tr>
							</thead>
							@php
							$no=1;
							@endphp
							<tbody style="font-size: 14px;">
								@foreach($goods as $good)
								<tr>
									<td>{{$no}}</td>
									<td>{{$good->name}}</td>
									<td>{{$good->amount}}</td>
									<td>{{$good->minimal}}</td>
								</tr>
								@php
								$no++;
								@endphp
								@endforeach
							</tbody>
						</table> 

					</div>
				</div>
			</div>
			<div class="card card-body">
				<span class="white-text"><h2>Transaksi Penjualan tahun 2022</h2></span>
				<div class="row">
					<div class="col-md-6">
						<div id="chartTransactionUSDLine"></div>
					</div>
					<div class="col-md-6">
						<div id="chartTransactionRupiahLine"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div id="chartTransaksi" class="chart"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div id="chartTransaksiRupiah" class="chart"></div>
					</div>
					<div class="col-md-4">
						<div id="chartTransaksiUSD" class="chart"></div>
					</div>
				</div>
			</div>
			<div class="card card-body">
				<span class="white-text"><h2>Transaksi Pembelian tahun 2022</h2></span>
				<div class="row">
					<div class="col-md-6">
						<div id="drawPurchaseRupiahLine"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div id="chartPurchases" class="chart"></div>
					</div>
					<div class="col-md-6">
						<h4><b>Top 10 Supplier</b></h4>
						<table class="table table-striped table-hover table-bordered data-table" id="datatable">
							<thead>
								<tr>
									<th style="width: 10%;text-align: center;">No</th>
									<th style="width: 30%;text-align: center;">Nama Supplier</th>
									<th style="width: 30%;text-align: center;">Jumlah</th>
								</tr>
							</thead>
							@php
							$no=1;
							@endphp
							<tbody style="font-size: 14px;">
								@foreach($purchases as $good)

								<tr>
									<td style="text-align: center;">{{$no}}</td>
									<td>{{$good->name}}</td>
									<td style="width: 30%;text-align: right;">Rp. {{number_format($good->amount, 2, ',', '.')}}</td>
								</tr>
								@php
								$no++;
								@endphp
								@if ($no>10)
								@break
								@endif
								@endforeach
							</tbody>
						</table> 

					</div>
				</div>
			</div>
			<div class="card card-body">
				<span class="white-text"><h2>Kepegawaian</h2></span>
				<div class="row">
					<div class="col-md-6">
						<div id="chartPegawaiAktif" class="chart"></div>
					</div>
					<div class="col-md-6">
						<div id="chartGenderByEmployeeTypes" class="chart"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div id="chartGender" class="chart"></div>
					</div>
					<div class="col-md-6">
						<div id="" class="chart"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<h4><b>Ulang tahun bulan {{$month }}</b></h4>
						<table class="table table-striped table-hover table-bordered data-table" id="datatable">
							<thead>
								<tr>
									<th style="width: 10%;text-align: center;">No</th>
									<th style="width: 30%;text-align: center;">Nama</th>
									<th style="width: 30%;text-align: center;">Tanggal lahir</th>
									<th style="width: 30%;text-align: center;">Usia</th>
								</tr>
							</thead>
							@php
							$no=1;
							@endphp
							<tbody style="font-size: 14px;">
								@foreach($birthday as $good)
								<tr>
									<td style="text-align: center;">{{$no}}</td>
									<td>{{$good->name}}</td>
									<td>{{$good->birthdate}}</td>
									<td>{{$good->usia}}</td>
								</tr>
								@php
								$no++;
								@endphp
								@endforeach
							</tbody>
						</table> 

					</div>
				</div>
			</div>
			@endisset
		</div>
	</body>
	@endsection