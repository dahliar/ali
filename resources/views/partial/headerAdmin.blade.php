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
				<i class="fas fa-users"></i> Employees
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li><a class="dropdown-item" href="{{ route('employeeList')}}"><i class="fas fa-address-card"></i> Employee List</a></li>
			</ul>
		</li>
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-file-contract"></i> Transactions
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li>
					<a class="dropdown-item" href="{{ url('transactionList')}}"><i class="fas fa-funnel-dollar"></i> Sales Transaction
					</a>
				</li>
				<li>
					<a class="dropdown-item" href="{{ url('purchaseList')}}"><i class="fas fa-shopping-cart"></i> Purchase Transaction
					</a>
				</li>
				<li>
					<a class="dropdown-item" href="{{ url('companyList')}}"><i class="fas fa-store"></i> Company List
					</a>
				</li>
			</ul>
		</li>

		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-layer-group"></i> Resources
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li>
					<a class="dropdown-item" href="#">
						<i class="fas fa-tasks"></i> Presensi &raquo;
					</a>
					<ul class="dropdown-menu dropdown-submenu">
						<li>
							<a class="dropdown-item" href="{{ url('presenceHarianList')}}"><i class="fas fa-tasks"></i> Presensi Harian
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('boronganList')}}"><i class="fas fa-tasks"></i> Presensi Borongan
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('honorariumList')}}"><i class="fas fa-tasks"></i> Presensi Honorarium
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('presenceHarianHistory')}}"><i class="fas fa-tasks"></i> Arsip Presensi Harian
							</a>
						</li>


					</ul>
				</li>
				<li>
					<a class="dropdown-item" href="#">
						<i class="fas fa-file-invoice-dollar"></i> Penggajian &raquo;
					</a>
					<ul class="dropdown-menu dropdown-submenu">
						<li>
							<a class="dropdown-item" href="{{ url('generateGaji')}}">
								<i class="fas fa-file-invoice-dollar"></i> Generate Gaji Harian/Borongan/Honorarium
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('salaryHarianList')}}">
								<i class="fas fa-file-invoice-dollar"></i> Penggajian Harian
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('salaryBoronganList')}}"><i class="fas fa-file-invoice-dollar"></i> Penggajian Borongan
							</a>
						</li>
							<!--
							<li>
							<a class="dropdown-item" href="{{ url('lemburBulananList')}}">
								<i class="fas fa-file-invoice-dollar"></i> Lembur Bulanan
							</a>
							</li>
						-->
						<li>
							<a class="dropdown-item" href="{{ url('salaryHonorariumList')}}">
								<i class="fas fa-file-invoice-dollar"></i> Honorarirum
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</li>

		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-warehouse"></i> Stok
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li><a class="dropdown-item" href="{{ url('speciesStockList')}}"><i class="fas fa-fish"></i>Stock per-Spesies</a></li>
				<li><a class="dropdown-item" href="{{ url('itemStockList')}}"><i class="fas fa-fish"></i>Stock per-Barang</a></li>
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
				<li>
					<a class="dropdown-item" href="#">
						<i class="fas fa-sitemap"></i> Organisasi &raquo;
					</a>
					<ul class="dropdown-menu dropdown-submenu">
						<li>
							<a class="dropdown-item" href="{{ url('organizationStructureList')}}"><i class="fas fa-sitemap"></i> Struktur Organisasi</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('structuralPositionList') }}"><i class="fas fa-user-tie"></i> Jabatan</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('workPositionList')}}"><i class="fas fa-building"></i> Bagian</a>
						</li>
					</ul>
				</li>
			</ul>
		</li>
	</ul>
</body>