<!DOCTYPE html><html lang="en">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css"/>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/js/bootstrap.bundle.min.js"></script>

<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@200&display=swap" rel="stylesheet">

<style>
	html, body {
		font-family: 'Raleway', sans-serif;
		margin-left: 10px;
		margin-right: 10px;
		padding-top: 40px;
		padding-bottom: 20px;
	}
</style>

<script type="text/javascript">
	$(document).ready(function() {
		$("body").tooltip({ selector: '[data-toggle=tooltip]' });
	});
</script>
<head>  
	<meta charset="UTF-8" />
	<title>{{ config('app.name', 'ALISeafood') }}</title>
</head>
<body>
	<div class="flex justify-center max-w-5xl min-h-screen pb-16 mx-auto">
		<div class="leading-none text-center text-black md:text-left">
			<h1 class="mb-2 text-5xl font-extrabold">{{ $errorCode }}</h1>
			<p class="text-xl text-gray-900">
				@isset($title)
				{{ $title }}
				@else
				Page not found.
				@endisset
				<br>
				@if($homeLink ?? false)
				@if (Auth::check())
				<a href="{{ route('/') }}" class="btn btn-dark">back to main page</a>
				@else
				<a href="{{ route('login') }}" class="btn btn-dark">back to login page</a>
				@endif
				@endif
			</p>
		</div>
	</div>
</body>
</html>

