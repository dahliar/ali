@include('partial.sidebar')
<!-- Page content-->
<div id="page-content-wrapper">
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
            </div>
        </div>
    </div>
</div>
@include('partial.sidebarBottom')