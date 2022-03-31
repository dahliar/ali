<style>
	body{
		background-color: white;
		color: black;
	}

	h1 {
		color: red;
	}

	h6{
		color: red;
		text-decoration: underline;
	}

</style>
<!DOCTYPE html>
<html>
<head>
	<title>Access Denied</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
	<div class="w3-display-middle">
		<h1 class="w3-jumbo w3-animate-top w3-center"><code>Access Denied</code></h1>
		<hr class="w3-border-white w3-animate-left" style="margin:auto;width:100%">
		<h4 class="w3-center w3-animate-right">Anda tidak memiliki hak akses ke halaman tersebut{{session('message')}}, kontak Administrator.</h4>
		<hr class="w3-border-white w3-animate-left" style="margin:auto;width:50%">
		<h3 class="w3-center w3-animate-right"><a href="home">Klik ini untuk kembali ke halaman utama.</a></h3>
	</div>
</body>
</html>
</html>