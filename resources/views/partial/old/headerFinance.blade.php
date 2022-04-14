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
	<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
		<div class="container-fluid">
			<a class="navbar-brand" href="{{ url('/home') }}"><img src="{{ asset('/images/ali-logo.png') }}"  width="80" height="50"></a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">


				@if (Auth::check() and Session::has('employeeId'))
				<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
					<div class="container-fluid">
						<a class="navbar-brand" href="{{ url('/home') }}"><img src="{{ asset('/images/ali-logo.png') }}"  width="80" height="50"></a>
						<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
							<span class="navbar-toggler-icon"></span>
						</button>
						<div class="collapse navbar-collapse" id="navbarSupportedContent">
							<ul class="navbar-nav me-auto mb-2 mb-lg-0">
								<li class="nav-item">
									<a class="nav-link" aria-current="page" href="{{ url('/home') }}">Home</a>
								</li>

								@if (Auth::user()->isAdmin() or Auth::user()->isHumanResources())
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="fas fa-users"></i> Employees
									</a>
									<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
										<li><a class="dropdown-item" href="{{ route('employeeList')}}"><i class="fas fa-address-card"></i> Employee List</a></li>
									</ul>
								</li>
								@endif

								@if (Auth::user()->isMarketing() or Auth::user()->isAdmin() or Auth::user()->isProduction())
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="fas fa-file-contract"></i> Transactions
									</a>
									<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
										@if (Auth::user()->isMarketing() or Auth::user()->isAdmin())
										<li>
											<a class="dropdown-item" href="{{ url('transactionList')}}"><i class="fas fa-funnel-dollar"></i> Sales Transaction
											</a>
										</li>
										@endif
										@if (Auth::user()->isProduction() or Auth::user()->isAdmin())
										<li>
											<a class="dropdown-item" href="{{ url('purchaseList')}}"><i class="fas fa-shopping-cart"></i> Purchase Transaction
											</a>
										</li>
										@endif
										<li>
											<a class="dropdown-item" href="{{ url('companyList')}}"><i class="fas fa-store"></i> Company List
											</a>
										</li>
									</ul>
								</li>
								@endif

								@if (Auth::user()->isHumanResources() or Auth::user()->isAdmin())
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
										Resources
									</a>
									<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
										<li>
											<a class="dropdown-item" href="#">
												<i class="fas fa-tasks"></i> Presensi &raquo;
											</a>
											<ul class="dropdown-menu dropdown-submenu">
												<li>
													<a class="dropdown-item" href="{{ url('presenceHarianList')}}">
														<i class="fas fa-tasks"></i> Presensi Harian
													</a>
												</li>
												<li>
													<a class="dropdown-item" href="{{ url('boronganList')}}">
														<i class="fas fa-tasks"></i> Presensi Borongan
													</a>
												</li>
												<li>
													<a class="dropdown-item" href="{{ url('honorariumList')}}">
														<i class="fas fa-tasks"></i> Presensi Honorarium
													</a>
												</li>
												<li>
													<a class="dropdown-item" href="{{ url('presenceHarianHistory')}}">
														<i class="fas fa-tasks"></i> Arsip Presensi Harian
													</a>
												</li>


											</ul>
										</li>
										@if (Session()->get('levelAccess') <= 2)
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
													<a class="dropdown-item" href="{{ url('salaryBoronganList')}}">
														<i class="fas fa-file-invoice-dollar"></i> Penggajian Borongan
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
												<li>
													<a class="dropdown-item" href="{{ url('payrollList')}}">
														<i class="fas fa-file-invoice-dollar"></i> Daftar Gaji
													</a>
												</li>
											</ul>
										</li>
										@endif
									</ul>
								</li>
								@endif

								@if (Auth::user()->isProduction() or Auth::user()->isAdmin())
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="fas fa-warehouse"></i> Stok
									</a>
									<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
										<li><a class="dropdown-item" href="{{ url('speciesStockList')}}"><i class="fas fa-warehouse"></i>Stock per-Spesies</a></li>
										<li><a class="dropdown-item" href="{{ url('itemStockList')}}"><i class="fas fa-warehouse"></i>Stock per-Barang</a></li>
									</ul>
								</li>
								@endif

								@if (Auth::user()->isProduction() or Auth::user()->isAdmin()  or Auth::user()->isHumanResources())
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
										Master Data
									</a>
									<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
										@if (Auth::user()->isProduction() or Auth::user()->isAdmin())
										<li>
											<a class="dropdown-item" href="{{ url('speciesList')}}"><i class="fas fa-fish"></i> Species</a>
										</li>
										@endif
										@if (Auth::user()->isAdmin()  or Auth::user()->isHumanResources())
										<li>
											<a class="dropdown-item" href="#">
												<i class="fas fa-sitemap"></i> Organisasi &raquo;
											</a>
											<ul class="dropdown-menu dropdown-submenu">
												<li>
													<a class="dropdown-item" href="{{ url('organizationStructureList')}}">
														<i class="fas fa-sitemap"></i> Struktur Organisasi</a>
													</li>
													<li>
														<a class="dropdown-item" href="{{ url('structuralPositionList') }}"><i class="fas fa-user-tie"></i> Jabatan</a>
													</li>
													<li>
														<a class="dropdown-item" href="{{ url('workPositionList')}}"><i class="fas fa-building"></i> Bagian</a>
													</li>
												</ul>
											</li>
											@endif
										</ul>
									</li>
									@endif
								</ul>
								<ul class="d-flex navbar-nav mb-2">
									<li class="nav-item dropdown">
										<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
											{{ Auth::check() ? Auth::user()->name : '' }}
										</a>
										<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
											<li>
												<a class="dropdown-item" href="{{ url('profileEdit', session('employeeId'))}}">Edit Profile
												</a>								
											</li>
											<li>
												<form method="POST" action="{{ url('logout') }}">
													@csrf
													<a class="dropdown-item" href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}
													</a>
												</form>	
											</li>
										</ul>
									</li>
								</ul>
							</div>
						</div>
					</nav>
					@endif

				</div>
			</div>
		</nav>
	</body>