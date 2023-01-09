<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css">


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js" type="text/javascript"></script>
<head>
    <meta charset="utf-8">
    <title>ALISeafood Online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AliSeafood Stocks Information Systems">
    <meta name="author" content="ALISeafood">
</head>
<style>
  html, body {
    font-family: 'Roboto', sans-serif;
    margin-left: 10px;
    margin-right: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
}
</style>
<div class="container-fluid">
    <div class="d-flex flex-column align-items-center">
        <div class="row">
            <img src="{{ asset('/images/ali-logo.png') }}"  width="100" height="100">
        </div>
    </div>
</div>
<div class="row p-3">
</div>

<div class="container-fluid">
    @if ($found==1)
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-8">
            <div class="d-flex flex-column">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb primary-color my-auto">
                        <li class="breadcrumb-item active"><h3>Products Information</h3></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-8">
            <div class="d-grid gap-1">
                <div class="row form-group">
                    <div class="col-md-3">
                        <span class="label">No Produk</span>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="label">:</span>
                    </div>
                    <div class="col-md-6">
                        <span class="label">{{$product->fullcode}}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3">
                        <span class="label">Spesies</span>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="label">:</span>
                    </div>
                    <div class="col-md-6">
                        <span class="label">{{$product->species}}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3">
                        <span class="label">Grade</span>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="label">:</span>
                    </div>
                    <div class="col-md-6">
                        <span class="label">{{$product->grade}}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3">
                        <span class="label">Frosting</span>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="label">:</span>
                    </div>
                    <div class="col-md-6">
                        <span class="label">{{$product->freezing}}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3">
                        <span class="label">Processing</span>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="label">:</span>
                    </div>
                    <div class="col-md-6">
                        <span class="label">{{$product->shape}}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3">
                        <span class="label">Size</span>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="label">:</span>
                    </div>
                    <div class="col-md-6">
                        <span class="label">{{$product->size}}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3">
                        <span class="label">Production Date</span>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="label">:</span>
                    </div>
                    <div class="col-md-6">
                        <span class="label">{{$product->productionDate}}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3">
                        <span class="label">Packaging Date</span>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="label">:</span>
                    </div>
                    <div class="col-md-6">
                        <span class="label">{{$product->packagingDate}}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3">
                        <span class="label">Loading Date</span>
                    </div>
                    <div class="col-md-1 text-center">
                        <span class="label">:</span>
                    </div>
                    <div class="col-md-6">
                        <span class="label">{{$product->loadingDate}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="d-flex flex-column align-items-center">
            <ol class="primary-color my-auto">
                <span><h3>Data produk dengan nomor {{$id}} tidak ditemukan</h3></span>
            </ol>
        </div>
    </div>
    @endif

    <footer class="text-center text-lg-start fixed-bottom">
        <!-- Copyright -->
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.0);">
            © 2021 Copyright:
            <a class="text-dark" href="https://aliseafood.co.id/">aliseafood.co.id</a>
        </div>
        <!-- Copyright -->
    </footer>
</div>