<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="AliSeafood Stocks Information Systems">
    <meta name="author" content="ALISeafood">
    <title>ALISeafood Online</title>
        <!-- Favicon
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    -->
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{ asset('/images/ali-logo.png') }}"  width="90" height="50"></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Welcome {{ Auth::check() ? Auth::user()->username : '' }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar-->
        <div class="border-end bg-white" id="sidebar-wrapper">
            <div class="list-group list-group-flush">
                <button class="btn btn-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Dashboard</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Shortcuts</a>                
                <!-- EMPLOYEE-->
                <a href="#" class="list-group-item list-group-item-action list-group-item-light p-3" data-bs-toggle="collapse" data-bs-target="#sumberDaya-collapse" aria-expanded="false">Karyawan</a>
                <div class="collapse" id="sumberDaya-collapse">
                    <a  href="{{ route('employeeList')}}" class="list-group-item list-group-item-action list-group-item-light p-3 dropdown-submenu">Daftar Karyawan</a>
                </div>
                <!-- SUMBER DAYA START-->
                <a href="#" class="list-group-item list-group-item-action list-group-item-light p-3" data-bs-toggle="collapse" data-bs-target="#sumberDaya-collapse" aria-expanded="false">Sumber Daya</a>
                <div class="collapse" id="sumberDaya-collapse">
                    <a href="#" class="list-group-item list-group-item-action list-group-item-light p-3" data-bs-toggle="collapse" data-bs-target="#presensi-collapse" aria-expanded="false">Presensi</a>

                    <div class="collapse" id="presensi-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="#" class="list-group-item list-group-item-action list-group-item-light p-3">Harian</a></li>
                            <li><a href="#" class="list-group-item list-group-item-action list-group-item-light p-3">Borongan</a></li>
                            <li><a href="#" class="list-group-item list-group-item-action list-group-item-light p-3">Honorarium</a></li>
                        </ul>
                    </div>
                    <a href="#" class="list-group-item list-group-item-action list-group-item-light p-3" data-bs-toggle="collapse" data-bs-target="#penggajian-collapse" aria-expanded="false">Penggajian</a>
                    <div class="collapse" id="penggajian-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="#" class="list-group-item list-group-item-action list-group-item-light p-3">Generate Gaji Bulanan</a></li>
                            <li><a href="#" class="list-group-item list-group-item-action list-group-item-light p-3">Generate Gaji Harian/Borongan/Honorarium</a></li>
                            <li><a href="#" class="list-group-item list-group-item-action list-group-item-light p-3">Daftar Gaji</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

