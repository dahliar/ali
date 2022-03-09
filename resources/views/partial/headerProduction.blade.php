<head>
	<meta charset="utf-8">
	<title>ALISeafood Online</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="AliSeafood Stocks Information Systems">
	<meta name="author" content="ALISeafood">
</head>


<style type="text/css">
	.dropdown-menu li {
		position: relative;
	}
	.dropdown-menu .dropdown-submenu {
		display: none;
		position: absolute;
		left: 100%;
		top: -7px;
	}
	.dropdown-menu .dropdown-submenu-left {
		right: 100%;
		left: auto;
	}
	.dropdown-menu > li:hover > .dropdown-submenu {
		display: block;
	}
</style>

<body>	
	<ul class="navbar-nav me-auto mb-2 mb-lg-0">
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-file-contract"></i> Transaksi
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li>
					<a class="dropdown-item" href="{{ url('purchaseList')}}">Transaksi Penjualan
					</a>
				</li>
				<li>
					<a class="dropdown-item" href="{{ url('companyList')}}">Perusahaan Supplier/Buyer
					</a>
				</li>
			</ul>
		</li>
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-warehouse"></i> Stok
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li><a class="dropdown-item" href="{{ url('speciesStockList')}}"><i class="fas fa-warehouse"></i>Stock per-Spesies</a></li>
				<li><a class="dropdown-item" href="{{ url('itemStockList')}}"><i class="fas fa-warehouse"></i>Stock per-Barang</a></li>
			</ul>
		</li>
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-database"></i> Master Data
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li>
					<a class="dropdown-item" href="{{ url('speciesList')}}"><i class="fas fa-fish"></i> Species</a>
				</li>
			</ul>
		</li>
	</ul>
</body>
