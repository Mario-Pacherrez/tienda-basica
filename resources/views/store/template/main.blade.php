<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>@yield('title', 'Mi Tienda')</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/lumen/bootstrap.min.css" rel="stylesheet" integrity="sha384-gv0oNvwnqzF6ULI9TVsSmnULNb3zasNysvWwfT/s4l8k5I+g6oFz9dye0wg3rQ2Q" crossorigin="anonymous">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	{{-- <link href="https://fonts.googleapis.com/css?family=Poiret+One|Lobster+Two" rel="stylesheet" type="text/css"> --}}
	<link rel="stylesheet" type="text/css" href="{{ asset('src/css/main.css') }}">
</head>
<body>
	@if(\Session::has('message'))
		@include('store.template.partials.message')
	@endif

	@include('store.template.partials.nav')
	@yield('content')
	@include('store.template.partials.footer')

	<script src="{{ asset('src/jquery/jquery-3.2.1.min.js') }}"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<script src="{{ asset('src/js/pinterest_grid.js') }}"></script>
	<script src="{{ asset('src/js/main.js') }}"></script>
</body>
</html>