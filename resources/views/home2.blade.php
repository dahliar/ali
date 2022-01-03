<!DOCTYPE html><html lang="en">
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.11.3/datatables.min.css"/>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.11.3/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>

<div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-light bg-light static-top">
        <a class="navbar-brand" href="{{ url('/home') }}"><img src="{{ asset('/images/ali-logo.png') }}"  width="80" height="50"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            </ul>
            <ul class="d-flex navbar-nav mb-2">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::check() ? Auth::user()->name : '' }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ url('profileEdit', session('employeeId'))}}">Profile empid {{session('employeeId')}}
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
    </nav>
</div>
<div class="container-fluid">   
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 px-0 bg-light">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="dropdown-item" aria-current="page" href="{{ url('/home') }}">Home</a>
                    </li>

                    @if (Auth::user()->isAdmin())
                    <li class="nav-item dropend">
                        <a class="dropdown-item dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Employees
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('employeeList')}}">Employee List</a></li>
                        </ul>
                    </li>
                    @endif


                    @if (Auth::user()->isMarketing() or Auth::user()->isAdmin())
                    <li class="nav-item dropend">
                        <a class="dropdown-item dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Transactions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ url('transactionList')}}">All Transaction
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ url('companyList')}}">Companies
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->isProduction() or Auth::user()->isAdmin())
                    <li class="nav-item dropend">
                        <a class="dropdown-item dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Stock Barang
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ url('itemStockList')}}">All Item</a></li>
                        </ul>
                    </li>
                    @endif
                    @if (Auth::user()->isAdmin())
                    <li class="nav-item dropend">
                        <a class="dropdown-item dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Master Data
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ url('speciesList')}}">Species</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    Organisasi &raquo;
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ url('organizationStructureList')}}">Struktur Organisasi</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ url('structuralPositionList') }}">Jabatan</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ url('workPositionList')}}">Bagian</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    @endif
                    <hr>

                    <li class="nav-item dropend">
                        <a class="dropdown-item dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::check() ? Auth::user()->name : '' }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ url('profileEdit', session('employeeId'))}}">Profile {{session('employeeId')}}
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
            <div class="col py-3">

            </div>
        </div>
    </div>