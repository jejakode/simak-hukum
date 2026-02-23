@extends('layouts.app')

@section('content')
	<div class="bg-gray-50 dark:bg-gray-900">
		{{-- Hero Section --}}
		<div class="relative isolate min-h-screen px-6 lg:px-8">
			<div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
				<div class="aspect-1155/678 bg-linear-to-tr w-144.5 rotate-30 sm:w-288.75 relative left-[calc(50%-11rem)] -translate-x-1/2 from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
			</div>
			<div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
				<div class="text-center">
					<img
						src="{{ asset('assets/hero.png') }}"
						alt="Ilustrasi"
						class="h-22 mx-auto mb-6"
					>
					<h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl dark:text-white">
						Manajemen Surat Keputusan <span class="text-indigo-600 dark:text-indigo-400">Modern</span>
					</h1>
					<p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
						Sistem terintegrasi untuk manajemen dan pembuatan draf Surat Keputusan di lingkungan pemerintahan secara efisien, akurat, dan terstandarisasi.
					</p>
					<div class="mt-10 flex items-center justify-center gap-x-6">
						<a href="{{ url('/sk/create') }}" class="rounded-base bg-linear-to-br from-purple-600 to-blue-500 px-4 py-2.5 text-center text-sm font-medium leading-5 text-white hover:bg-linear-to-bl focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
							Buat Surat Keputusan
						</a>
						<a href="{{ url('/guide') }}" class="text-body bg-neutral-primary-soft border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-neutral-tertiary-soft shadow-xs inline-flex items-center rounded-base border px-4 py-2.5 text-sm font-medium leading-5 focus:outline-none focus:ring-4">
							Baca Panduan
							<svg
								class="-me-0.5 ms-1.5 h-4 w-4"
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
									stroke-linejoin="round"
									stroke-width="2"
									d="M19 12H5m14 0-4 4m4-4-4-4"
								/>
							</svg>
						</a>
					</div>
				</div>
			</div>
			<div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
				<div class="aspect-1155/678 w-144.5 bg-linear-to-tr sm:w-288.75 relative left-[calc(50%+3rem)] -translate-x-1/2 from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
			</div>
		</div>

		{{-- Features Section --}}
		<div class="bg-gray-50 py-24 sm:py-32 dark:bg-gray-900">
			<div class="mx-auto max-w-7xl px-6 lg:px-8">
				<div class="mx-auto max-w-2xl text-center">
					<h2 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">Manfaat Utama</h2>
					<p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">Sistem Cerdas Hukum</p>
					<p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
						Dirancang untuk merevolusi cara Anda bekerja dengan dokumen hukum.
					</p>
				</div>
				<div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
					<dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
						<div class="flex flex-col">
							<dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
								<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
									<svg
										class="h-6 w-6 text-white"
										fill="none"
										viewBox="0 0 24 24"
										stroke="currentColor"
									>
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
										/>
									</svg>
								</div>
								Standardisasi
							</dt>
							<dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
								<p class="flex-auto">Menjamin setiap Surat Keputusan yang diterbitkan memiliki format dan struktur yang seragam sesuai dengan peraturan yang berlaku.</p>
							</dd>
						</div>
						<div class="flex flex-col">
							<dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
								<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
									<svg
										class="h-6 w-6 text-white"
										fill="none"
										viewBox="0 0 24 24"
										stroke="currentColor"
									>
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M13 10V3L4 14h7v7l9-11h-7z"
										/>
									</svg>
								</div>
								Efisiensi
							</dt>
							<dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
								<p class="flex-auto">Mempercepat proses penyusunan draf SK melalui antarmuka yang terstruktur dan helper text yang informatif.</p>
							</dd>
						</div>
						<div class="flex flex-col">
							<dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-white">
								<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
									<svg
										class="h-6 w-6 text-white"
										fill="none"
										viewBox="0 0 24 24"
										stroke="currentColor"
									>
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
										/>
									</svg>
								</div>
								Akurasi
							</dt>
							<dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
								<p class="flex-auto">Mengurangi risiko kesalahan manusia (human error) dalam pengetikan dan pemformatan dokumen hukum.</p>
							</dd>
						</div>
					</dl>
				</div>
			</div>
		</div>
	</div>
@endsection
