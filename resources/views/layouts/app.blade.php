<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>SIMAK HUKUM - Sistem Informasi Manajemen Surat Keputusan</title>

		<!-- Fonts -->
		<link rel="preconnect" href="https://fonts.bunny.net">
		<link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700&display=swap" rel="stylesheet" />

		<!-- Scripts -->
		@vite(['resources/css/app.css', 'resources/js/app.js'])

		@stack('styles')

		<style>
			body {
				font-family: 'Nunito', sans-serif;
			}
		</style>
	</head>

	<body>
		<div class="flex min-h-screen flex-col">
			@include('partials.header')

			<main class="grow">
				@yield('content')
			</main>

			@include('partials.footer')
		</div>

		@stack('scripts')
	</body>

</html>
