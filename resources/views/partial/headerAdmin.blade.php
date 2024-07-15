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
		position: absolute;
		left: 100%;
		top: -7px;
	}
	.dropdown-menu .dropdown-submenu-left {
		right: 100%;
		left: auto;
	}
	i {
		min-width:20px;
	}

</style>

<body>
	<ul class="navbar-nav me-auto mb-2 mb-lg-0">
		@if (Session::get('accessLevel') <= 40)
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
				<i class="fas fa-file-contract"></i> Transaksi
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="multilevelDropdownMenu1" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-file-invoice-dollar"></i> Penjualan</a>
					<ul class="dropdown-menu" aria-labelledby="multilevelDropdownMenu1">
						<li>
							<a class="dropdown-item" href="{{ url('transactionList')}}"><i class="fas fa-ship"></i> Export</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('localTransactionList')}}"><i class="fas fa-truck"></i> Lokal</a>
						</li>
					</ul>
				</li>
				<li>
					<a class="dropdown-item" href="{{ url('purchaseList')}}"><i class="fas fa-shopping-cart"></i> Pembelian</a>
				</li>
				<li>
					<a class="dropdown-item" href="{{ url('undernameList')}}"><i class="fas fa-file-alt"></i> Undername</a>
				</li>
				<li>
					<a class="dropdown-item" href="{{ url('companyList')}}"><i class="fas fa-store"></i> Perusahaan Supplier/Buyer</a>
				</li>
			</ul>
		</li>
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
				<i class="fas fa-layer-group"></i> Human Resources
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarHr">
				@if (Session::get('accessLevel') <= 30)
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="navbarKaryawan" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-users" onclick=""></i> Karyawan
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarKaryawan">
						<li><a class="dropdown-item" href="{{ url('employeeList')}}"><i class="fas fa-address-card"></i> Daftar Karyawan</a></li>
						<li><a class="dropdown-item" href="{{ url('employeeList2')}}"><i class="fas fa-address-card"></i> Daftar Karyawan - New Menu</a></li>
					</ul>
				</li>
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="navbarAdministrasi" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-mail-bulk"></i> Surat Menyurat
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarAdministrasi">
						<li>
							<a class="dropdown-item" href="{{ url('administrasi')}}">
								<i class="fas fa-file-signature"></i> Buat Surat
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('administrasiAllSurat')}}"><i class="fas fa-folder-open"></i> Arsip
							</a>
						</li>
					</ul>
				</li>
				@endif
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="navbarAdministrasi" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-plane-departure"></i></i> Cuti
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarAdministrasi">
						<li>
							<a class="dropdown-item" href="{{ url('cuti')}}"><i class="fas fa-file-signature"></i> Daftar Cuti
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('cutiHolidayList')}}"><i class="fas fa-folder-open"></i> Daftar hari libur
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</li>
		@if (Session::get('accessLevel') <= 30)
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
				<i class="fas fa-layer-group"></i> Payroll
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarHr">
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="navbarPresensi" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-tasks" onclick=""></i> Presensi
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarPresensi">
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
							<a class="dropdown-item" href="{{ url('presenceHarianScanMasuk')}}"><i class="fas fa-barcode"></i> Scan Presensi Harian Masuk
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('presenceHarianScanKeluar')}}"><i class="fas fa-barcode"></i> Scan Presensi Harian Pulang
							</a>
						</li>
						<li><hr class="dropdown-divider"></li>
						<li>
							<a class="dropdown-item" href="{{ url('presenceHarianHistory')}}"><i class="fas fa-tasks"></i> Arsip Presensi Harian
							</a>
							<a class="dropdown-item" href="{{ url('presenceBoronganHistoryPerorangan')}}"><i class="fas fa-tasks"></i> Arsip Presensi Borongan Pegawai
							</a>
						</li>
					</ul>
				</li>

				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="navbarPenggajian" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-file-invoice-dollar"></i> Penggajian
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarPenggajian">
						<li>
							<a class="dropdown-item" href="{{ url('generateGajiBulanan')}}">
								<i class="fas fa-file-invoice-dollar"></i> Generate Gaji Bulanan
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('generateGaji')}}">
								<i class="fas fa-file-invoice-dollar"></i> Generate Gaji Harian/Borongan/Honorarium
							</a>
						</li>
						<hr class="dropdown-divider">
						<li>
							<a class="dropdown-item" href="{{ url('payrollList')}}">
								<i class="fas fa-file-invoice-dollar"></i> Daftar Gaji Pegawai Harian/Borongan
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('payrollListBulanan')}}">
								<i class="fas fa-file-invoice-dollar"></i> Daftar Gaji Pegawai Bulanan
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</li>
		@endif
		@endif

		@if (Session::get('accessLevel') == 60)
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarHr" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
				<i class="fas fa-layer-group"></i> Presensi
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarHr">
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="navbarPresensi" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-tasks" onclick=""></i> Presensi
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarPresensi">
						<li>
							<a class="dropdown-item" href="{{ url('presenceHarianScanMasuk')}}"><i class="fas fa-barcode"></i> Scan Presensi Harian Masuk
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('presenceHarianScanKeluar')}}"><i class="fas fa-barcode"></i> Scan Presensi Harian Pulang
							</a>
						</li>
						<li><hr class="dropdown-divider"></li>
					</ul>
				</li>
			</ul>
		</li>
		@endif
		@if (Session::get('accessLevel') <= 40)
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarBarang" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
				<i class="fas fa-warehouse"></i> Inventory
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarBarang">
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="navbarStok" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-fish"></i> Produk
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarStok">
						<li>
							<a class="dropdown-item" href="{{ url('speciesStockList')}}"><i class="fas fa-fish"></i> Stock per-Spesies</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('itemStockList')}}"><i class="fas fa-fish"></i> Stock per-Barang</a>
						</li>
						@if (Session::get('accessLevel') <= 30)
						<li>
							<a class="dropdown-item" href="{{ url('itemStockApprovalPenambahan')}}"><i class="fas fa-box"></i> Approval Penambahan</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('itemStockApprovalPengurangan')}}"><i class="fas fa-box"></i> Approval Pengurangan</a>
						</li>
						@endif
					</ul>
				</li>
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="menuQR" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-fish"></i> Bar/QR Code
					</a>
					<ul class="dropdown-menu" aria-labelledby="menuQR">
						<li>
							<a class="dropdown-item" href="{{ url('barcodeList')}}"><i class="fas fa-qrcode"></i>Generate Bar/QR Code </a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('scanList')}}"><i class="fas fa-barcode"></i> Daftar Bar/QR Code</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('scanMasuk')}}"><i class="fas fa-satellite-dish"></i> Scan barang - Masuk</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('scanTransactionList')}}"><i class="fas fa-satellite-dish"></i> Scan barang - Keluar</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('scanRekapMasuk')}}"><i class="fas fa-clipboard-list"></i> Rekapitulasi Scan Masuk</a>
						</li>
						<li>
							<a class="dropdown-item" href="{{ url('scanRekapKeluar')}}"><i class="fas fa-clipboard-list"></i> Rekapitulasi Scan Keluar</a>
						</li>
					</ul>
				</li>
				<li>
					<a class="dropdown-item" href="{{ url('goodList')}}"><i class="fas fa-box"></i> Barang Pendukung Produksi</a>
				</li>

				@if (Session::get('accessLevel') <= 30)
				<li>
					<a class="dropdown-item" href="{{ url('opname')}}"><i class="fas fa-dolly-flatbed"></i> Opname </a>
				</li>
				@endif
				<li>
					<a class="dropdown-item" href="{{ url('historyPerubahanStock')}}"><i class="fas fa-history"></i> History Perubahan stock </a>
				</li>

			</ul>
		</li>				
		@if (Session::get('accessLevel') <= 30)
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-file-contract"></i> Dashboard
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li>
					<a class="dropdown-item" href="{{ url('priceList')}}"><i class="fas fa-store"></i> Informasi Harga
					</a>
					<a class="dropdown-item" href="{{ url('hppList')}}"><i class="fas fa-store"></i> Harga Pokok Produksi
					</a>
					<a class="dropdown-item" href="{{ url('rekapitulasiGaji')}}"><i class="fas fa-store"></i> Rekapitulasi gaji per tahun
					</a>
					<a class="dropdown-item" href="{{ url('rekapitulasiGajiPerBulan')}}"><i class="fas fa-store"></i> Rekapitulasi gaji berdasar payroll
					</a>
					<a class="dropdown-item" href="{{ url('salaryByDateRange')}}"><i class="fas fa-store"></i> Rekapitulasi gaji berdasar tanggal
					</a>
					<a class="dropdown-item" href="{{ url('rekapitulasiPembelianPerBulan')}}"><i class="fas fa-store"></i> Rekapitulasi Pembelian Per-bulan
					</a>
					<a class="dropdown-item" href="{{ url('rekapitulasiPresensi')}}"><i class="fas fa-store"></i> Rekapitulasi Kehadiran
					</a>
					<a class="dropdown-item" href="{{ url('historyDetailPenjualan')}}"><i class="fas fa-history"></i> History Detail Penjualan Barang
					</a>

				</li>
			</ul>
		</li>
		@endif
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarMasterData" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
				<i class="fas fa-layer-group"></i> Master Data
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarMasterData">
				<li class="dropdown dropend">
					<a class="dropdown-item" href="{{ url('speciesList')}}"><i class="fas fa-fish"></i> Species</a>
				</li>
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="navbarPenggajian" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-wallet"></i> Penggajian
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarPenggajian">
						<li>
							<a class="dropdown-item" href="{{ url('standarBorongan')}}"><i class="fas fa-folder-open"></i> Standar Borongan
							</a>
							<a class="dropdown-item" href="{{ url('standarBoronganApproval')}}"><i class="fas fa-folder-open"></i> Approval Standar Borongan
							</a>


						</li>
					</ul>
				</li>
				@if (Session::get('accessLevel') <= 30)
				<li class="dropdown dropend">
					<a class="dropdown-item dropdown-toggle" href="#" id="navbarPenggajian" data-bs-auto-close="true" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-sitemap"></i> Organisasi
					</a>
					<ul class="dropdown-menu" aria-labelledby="navbarPenggajian">
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
				@endif
			</ul>
		</li>
		@if (Session::get('accessLevel') <= 1)
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-database"></i> Admin Area
			</a>
			<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
				<li>
					<a class="dropdown-item" href="{{ url('applicationList')}}"><i class="fas fa-users"></i> Daftar Aplikasi</a>
				</li>
				<li>
					<a class="dropdown-item" href="{{ url('userMappingList')}}"><i class="fas fa-users"></i> User Mapping</a>
				</li>
				<li>
					<a class="dropdown-item" href="{{ url('infophp')}}"><i class="fas fa-users"></i> Info</a>
				</li>
			</ul>
		</li>
		@endif
		@endif
	</ul>
</body>