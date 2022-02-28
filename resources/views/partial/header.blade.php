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

				@if (Auth::user()->isAdmin() and (Session()->get('levelAccess') <= 3))
				@include('partial.headerAdmin')
				@endif

				@if (Auth::user()->isProduction() and (Session()->get('levelAccess') <= 3))
				@include('partial.headerProduction')
				@endif

				@if (Auth::user()->isHumanResources() and (Session()->get('levelAccess') <= 3))
				@include('partial.headerHumanResources')
				@endif

				@if (Auth::user()->isMarketing() and (Session()->get('levelAccess') <= 3))
				@include('partial.headerMarketing')
				@endif

				<ul class="d-flex navbar-nav mb-s">
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="fas fa-user"></i> {{ Auth::check() ? Auth::user()->username : '' }}
						</a>
						<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
							<li>
								<a class="dropdown-item" href="{{ url('profileEdit', session('employeeId'))}}"><i class="fas fa-edit"></i> {{ Auth::check() ? Auth::user()->name : '' }}
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
</body>