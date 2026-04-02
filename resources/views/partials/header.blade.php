@php
	$menus = [['title' => 'Beranda', 'url' => '/', 'active' => url()->current() == url('/')], ['title' => 'Panduan', 'url' => '/guide', 'active' => url()->current() == url('/guide')], ['title' => 'Tentang', 'url' => '/about', 'active' => url()->current() == url('/about')], ['title' => 'Kontak', 'url' => '/contact', 'active' => url()->current() == url('/contact')]];
@endphp

<nav class="bg-neutral-primary border-default fixed top-0 z-20 w-full border-b">
	<div class="relative mx-auto flex max-w-7xl flex-wrap items-center justify-between p-4">
		<a href="{{ url('/') }}" class="flex items-center gap-2 rtl:space-x-reverse">
			<img
				src="{{ asset('assets/logo-buol.png') }}"
				class="h-12 w-12 rounded-full"
				alt="SIMAK Logo"
			>
			<div class="flex flex-col">
				<span class="text-heading whitespace-nowrap text-lg font-bold"><span class="text-indigo-600 dark:text-indigo-400">SIMAK</span>HUKUM</span>
				<span class="text-sm dark:text-gray-300">Bagian Hukum Kabupaten Buol</span>
			</div>
		</a>

		<div class="flex space-x-3 md:order-2 md:space-x-0 rtl:space-x-reverse">
			<button
				id="theme-toggle"
				type="button"
				class="text-body bg-neutral-primary-soft border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-neutral-tertiary-soft shadow-xs me-3 rounded-base border p-2 text-sm font-medium leading-5 focus:outline-none focus:ring-4 cursor-pointer"
			>
				<svg
					id="theme-toggle-dark-icon"
					class="hidden h-5 w-5"
					fill="currentColor"
					viewBox="0 0 20 20"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
				</svg>
				<svg
					id="theme-toggle-light-icon"
					class="hidden h-5 w-5"
					fill="currentColor"
					viewBox="0 0 20 20"
					xmlns="http://www.w3.org/2000/svg"
				>
					<path
						d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
						fill-rule="evenodd"
						clip-rule="evenodd"
					></path>
				</svg>
				<span class="sr-only">Toggle Theme</span>
			</button>
			<a href="{{ url('/sk/create') }}" class="inline-flex items-center gap-2 rounded-base bg-linear-to-br hover:bg-linear-to-bl from-purple-600 to-blue-500 px-4 py-2.5 text-center text-sm font-medium leading-5 text-white focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
				<x-heroicon-o-document-text class="h-4 w-4" />
				<span>Buat SK</span>
			</a>

			<button
				data-collapse-toggle="navbar-sticky"
				type="button"
				class="text-body bg-neutral-primary-soft border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-neutral-tertiary-soft shadow-xs me-3 rounded-base border p-2 text-sm font-medium leading-5 focus:outline-none focus:ring-4 cursor-pointer md:hidden"
				aria-controls="navbar-sticky"
				aria-expanded="false"
			>
				<span class="sr-only">Open main menu</span>
				<svg
					class="h-5 w-5"
					aria-hidden="true"
					xmlns="http://www.w3.org/2000/svg"
					width="24"
					height="24"
					fill="none"
					viewBox="0 0 24 24"
				>
					<path
						stroke="currentColor"
						stroke-linecap="round"
						stroke-width="2"
						d="M5 7h14M5 12h14M5 17h14"
					/>
				</svg>
			</button>
		</div>
		<div class="hidden w-full items-center justify-between md:absolute md:left-1/2 md:top-1/2 md:flex md:w-auto md:-translate-x-1/2 md:-translate-y-1/2" id="navbar-sticky">
			<ul class="border-default rounded-base bg-neutral-secondary-soft md:bg-neutral-primary mt-4 flex flex-col border p-4 font-medium md:mt-0 md:flex-row md:space-x-8 md:border-0 md:p-0 rtl:space-x-reverse">
				@foreach ($menus as $menu)
					<li>
						<a
							href="{{ $menu['url'] }}"
							class="{{ $menu['active'] ? 'md:text-purple-600 dark:md:text-purple-400' : 'text-heading hover:bg-neutral-tertiary md:hover:text-purple' }} block rounded-sm px-3 py-2 md:border-0 md:p-0 md:hover:bg-transparent md:dark:hover:bg-transparent"
							aria-current="{{ $menu['active'] ? 'page' : false }}"
						>{{ $menu['title'] }}</a>
					</li>
				@endforeach
			</ul>
		</div>
	</div>
</nav>

@pushOnce('scripts')
	<script>
		if (
			localStorage.getItem('color-theme') === 'dark' ||
			(!('color-theme' in localStorage) &&
				window.matchMedia('(prefers-color-scheme: dark)').matches)
		) {
			document.documentElement.classList.add('dark');
		} else {
			document.documentElement.classList.remove('dark');
		}

		document.addEventListener('DOMContentLoaded', function() {
			const themeToggleBtn = document.getElementById('theme-toggle');
			const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
			const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

			if (themeToggleBtn) {
				if (document.documentElement.classList.contains('dark')) {
					themeToggleLightIcon.classList.remove('hidden');
				} else {
					themeToggleDarkIcon.classList.remove('hidden');
				}

				themeToggleBtn.addEventListener('click', function() {
					themeToggleDarkIcon.classList.toggle('hidden');
					themeToggleLightIcon.classList.toggle('hidden');

					if (document.documentElement.classList.contains('dark')) {
						document.documentElement.classList.remove('dark');
						localStorage.setItem('color-theme', 'light');
					} else {
						document.documentElement.classList.add('dark');
						localStorage.setItem('color-theme', 'dark');
					}
				});
			}
		});
	</script>
@endpushonce
