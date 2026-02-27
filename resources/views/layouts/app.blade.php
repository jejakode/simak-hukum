<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="@yield('meta_description', 'SIMAK HUKUM - Sistem Informasi Manajemen Surat Keputusan untuk penyusunan, pratinjau, dan ekspor dokumen SK.')">
		<meta name="robots" content="@yield('meta_robots', 'index,follow')">
		<meta name="author" content="SIMAK HUKUM">

		<meta property="og:type" content="website">
		<meta property="og:site_name" content="SIMAK HUKUM">
		<meta property="og:title" content="@yield('meta_title', 'SIMAK HUKUM - Sistem Informasi Manajemen Surat Keputusan')">
		<meta property="og:description" content="@yield('meta_description', 'SIMAK HUKUM - Sistem Informasi Manajemen Surat Keputusan untuk penyusunan, pratinjau, dan ekspor dokumen SK.')">
		<meta property="og:url" content="{{ url()->current() }}">
		<meta property="og:image" content="@yield('meta_image', asset('assets/hero.png'))">

		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:title" content="@yield('meta_title', 'SIMAK HUKUM - Sistem Informasi Manajemen Surat Keputusan')">
		<meta name="twitter:description" content="@yield('meta_description', 'SIMAK HUKUM - Sistem Informasi Manajemen Surat Keputusan untuk penyusunan, pratinjau, dan ekspor dokumen SK.')">
		<meta name="twitter:image" content="@yield('meta_image', asset('assets/hero.png'))">
		<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
		<link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

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
