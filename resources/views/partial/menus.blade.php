<main>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{ asset('/images/ali-logo.png') }}" width="80" height="50"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="d-flex navbar-nav mb-s ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i>Welcome {{ Auth::check() ? Auth::user()->name : '' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ url('profileEdit', session('employeeId'))}}"><i class="fas fa-edit"></i>Edit Profile
                                </a>                                
                            </li>
                            <li>
                                <form method="POST" action="{{ url('logout') }}">
                                    @csrf
                                    <a class="dropdown-item" href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fas fa-sign-out-alt"></i> {{ __('Log Out') }}
                                    </a>
                                </form> 
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="flex-shrink-0 p-1 bg-white" style="width: 280px;">
        <ul class="list-unstyled ps-0">
            <li class="mb-1">
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#home-collapse" aria-expanded="false">
                    Orders
                </button>
                <div class="collapse" id="home-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1">
                        <li>
                            <a class="link-dark rounded collapsed btn-toggle" data-bs-toggle="collapse" data-bs-target="#penjualan-collapse" aria-expanded="false">
                                Penjualan
                            </a>
                            <div class="collapse innermost" id="penjualan-collapse">
                                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                    <li><a href="#" class="link-dark rounded">Export</a></li>
                                    <li><a href="#" class="link-dark rounded">Lokal</a></li>
                                </ul>
                            </div>
                        </li>
                        <li><a href="#" class="link-dark rounded innermost">Pembelian</a></li>
                    </ul>
                </div>
            </li>
            <li class="mb-1">
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#hr-collapse" aria-expanded="false">
                    Human Resources
                </button>
                <div class="collapse" id="hr-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1">
                        <li>
                            <a href="{{ url('employeeList2')}}" class="link-dark rounded innermost">Karyawan</a>
                        </li>
                        <li>
                            <a class="link-dark rounded collapsed btn-toggle" data-bs-toggle="collapse" data-bs-target="#presensi-collapse" aria-expanded="false">
                                Presensi
                            </a>
                            <div class="collapse innermost" id="presensi-collapse">
                                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                    <li><a href="#" class="link-dark rounded">Harian</a></li>
                                    <li><a href="#" class="link-dark rounded">Borongan</a></li>
                                    <li><a href="#" class="link-dark rounded">Honorarium</a></li>
                                    <li><hr class="innermost dropdown-divider"></li>
                                    <li><a href="#" class="link-dark rounded">Arsip Presensi</a></li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a class="link-dark rounded collapsed btn-toggle" data-bs-toggle="collapse" data-bs-target="#gaji-collapse" aria-expanded="false">
                                Penggajian
                            </a>
                            <div class="collapse innermost" id="gaji-collapse">
                                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                    <li>
                                        <a class="link-dark rounded collapsed btn-toggle" data-bs-toggle="collapse" data-bs-target="#generate-collapse" aria-expanded="false">
                                            Generate
                                        </a>
                                        <div class="collapse innermost" id="generate-collapse">
                                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                                <li><a href="#" class="link-dark rounded">Generate Bulanan</a></li>
                                                <li><a href="#" class="link-dark rounded">Generate Harian Borongan Honorarium</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="link-dark rounded collapsed btn-toggle" data-bs-toggle="collapse" data-bs-target="#pr-collapse" aria-expanded="false">
                                            Payroll
                                        </a>
                                        <div class="collapse innermost" id="pr-collapse">
                                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                                <li><a href="#" class="link-dark rounded">Payroll Bulanan</a></li>
                                                <li><a href="#" class="link-dark rounded">Payroll Harian Borongan Honorarium</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="mb-1">
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#stok-collapse" aria-expanded="false">
                    Stok
                </button>
                <div class="collapse" id="stok-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1">
                        <li><a href="#" class="link-dark rounded innermost">Produk-Spesies</a></li>
                        <li><a href="#" class="link-dark rounded innermost">Produk-Barang</a></li>
                        <li><a href="#" class="link-dark rounded innermost">Alat produksi</a></li>
                    </ul>
                </div>
            </li>
            <li class="mb-1">
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false">
                    Dashboard
                </button>
                <div class="collapse" id="dashboard-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li>
                            <a class="link-dark rounded" href="{{ url('priceList')}}"> Informasi Harga
                            </a>
                        </li>
                        <li>
                            <a class="link-dark rounded" href="{{ url('hppList')}}"> Harga Pokok Produksi
                            </a>
                        </li>
                        <li>
                            <a class="link-dark rounded" href="{{ url('rekapitulasiGaji')}}"> Rekapitulasi gaji per tahun
                            </a>
                        </li>
                        <li>
                            <a class="link-dark rounded" href="{{ url('rekapitulasiGajiPerBulan')}}"> Rekapitulasi gaji berdasar payroll
                            </a>
                        </li>
                        <li>
                            <a class="link-dark rounded" href="{{ url('checkPayrollByDateRange')}}"> Rekapitulasi gaji berdasar tanggal
                            </a>
                        </li>
                        <li>
                            <a class="link-dark rounded" href="{{ url('rekapitulasiPembelianPerBulan')}}"> Rekapitulasi Pembelian Per-bulan
                            </a>
                        </li>
                        <li>
                            <a class="link-dark rounded" href="{{ url('rekapitulasiPresensi')}}"> Rekapitulasi Kehadiran
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="mb-1">
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#master-collapse" aria-expanded="false">
                    Master Data
                </button>
                <div class="collapse" id="master-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1">
                        <li>
                            <a class="link-dark rounded collapsed btn-toggle" data-bs-toggle="collapse" data-bs-target="#organization-collapse" aria-expanded="false">
                                Organisasi
                            </a>
                            <div class="collapse innermost" id="organization-collapse">
                                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                    <li><a href="#" class="link-dark rounded">Struktur Organisasi</a></li>
                                    <li><a href="#" class="link-dark rounded">Jabatan</a></li>
                                    <li><a href="#" class="link-dark rounded">Bagian</a></li>
                                </ul>
                            </div>
                        </li>
                        <li><a href="" class="link-dark rounded">Spesies</a></li>
                        <li><a href="#" class="link-dark rounded">Perusahaan</a></li>
                    </ul>

                </div>
            </li> 
            @if (auth()->user()->accessLevel <= 1)
            <li class="mb-1">
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#admin-collapse" aria-expanded="false">
                    Admin Area
                </button>
                <div class="collapse" id="admin-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1">
                        <li><a href="#" class="link-dark rounded innermost">Daftar Aplikasi</a></li>
                        <li><a href="#" class="link-dark rounded innermost">Pemetaan User-Aplikasi</a></li>
                    </ul>
                </div>
            </li>    
            @endif           
            <li class="border-top my-3"></li>
            <li class="mb-1">
                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#account-collapse" aria-expanded="false">
                    Welcome {{ Auth::check() ? Auth::user()->name : '' }}
                </button>
                <div class="collapse" id="account-collapse">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li>
                            <a href="{{ url('profileEdit', session('employeeId'))}}" class="link-dark rounded">Edit Profile
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ url('logout') }}">
                                <a href="route('logout')"  onclick="event.preventDefault(); this.closest('form').submit();" class="link-dark rounded">{{ __('Sign Out') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</main>