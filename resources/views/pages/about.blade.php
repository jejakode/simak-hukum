@extends('layouts.app')

@section('content')
	<div class="bg-white py-24 sm:py-32 dark:bg-gray-900">
		<div class="mx-auto max-w-7xl px-6 lg:px-8">
			<div class="mx-auto max-w-2xl lg:mx-0">
				<h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">Tentang Kami</h2>
				<p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
					SIMAK HUKUM adalah inisiatif transformasi digital untuk memodernisasi pengelolaan dokumen hukum di lingkungan pemerintahan, memastikan efisiensi, transparansi, dan akurasi.
				</p>
			</div>
			<div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
				<dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
					<div class="flex flex-col">
						<dt class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
							<div class="mb-6 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
								<svg
									class="h-6 w-6 text-white"
									fill="none"
									viewBox="0 0 24 24"
									stroke-width="1.5"
									stroke="currentColor"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"
									/>
								</svg>
							</div>
							Visi Kami
						</dt>
						<dd class="mt-1 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
							<p class="flex-auto">Menjadi platform terdepan dalam digitalisasi produk hukum yang terintegrasi dan mudah diakses.</p>
						</dd>
					</div>
					<div class="flex flex-col">
						<dt class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
							<div class="mb-6 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
								<svg
									class="h-6 w-6 text-white"
									fill="none"
									viewBox="0 0 24 24"
									stroke-width="1.5"
									stroke="currentColor"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
									/>
								</svg>
							</div>
							Tim Pengembang
						</dt>
						<dd class="mt-1 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
							<p class="flex-auto">Didukung oleh tenaga ahli hukum dan teknologi yang berdedikasi untuk inovasi pelayanan publik.</p>
						</dd>
					</div>
					<div class="flex flex-col">
						<dt class="text-base font-semibold leading-7 text-gray-900 dark:text-white">
							<div class="mb-6 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
								<svg
									class="h-6 w-6 text-white"
									fill="none"
									viewBox="0 0 24 24"
									stroke-width="1.5"
									stroke="currentColor"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										d="M9 12.75L11.25 15 15 9.75M21 12c0 5.523-4.477 10-10 10S1 17.523 1 12 5.477 2 10 2s10 4.477 10 10z"
									/>
								</svg>
							</div>
							Komitmen Mutu
						</dt>
						<dd class="mt-1 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
							<p class="flex-auto">Mengutamakan keamanan data, kemudahan penggunaan, dan kepatuhan terhadap regulasi yang berlaku.</p>
						</dd>
					</div>
				</dl>
			</div>
		</div>
	</div>
@endsection
