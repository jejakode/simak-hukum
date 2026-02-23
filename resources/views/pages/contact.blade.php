@extends('layouts.app')

@section('content')
	<div class="bg-white py-24 sm:py-32 dark:bg-gray-900">
		<div class="mx-auto max-w-7xl px-6 lg:px-8">
			<div class="mx-auto max-w-2xl lg:mx-0">
				<h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">Hubungi Kami</h2>
				<p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
					Kami siap membantu Anda. Silakan hubungi kami melalui kontak di bawah ini atau kunjungi kantor kami untuk konsultasi lebih lanjut.
				</p>
			</div>

			<div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
				{{-- Alamat --}}
				<div class="flex flex-col rounded-2xl bg-gray-50 p-8 dark:bg-gray-800">
					<div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
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
								d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
							/>
							<path
								stroke-linecap="round"
								stroke-linejoin="round"
								d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
							/>
						</svg>
					</div>
					<h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white">Alamat Kantor</h3>
					<address class="mt-2 not-italic text-gray-600 dark:text-gray-300">
						<p>Gedung Pemerintahan Lt. 2</p>
						<p>Jl. Jenderal Sudirman No. 1</p>
						<p>Jakarta Pusat, 10110</p>
					</address>
				</div>

				{{-- Email --}}
				<div class="flex flex-col rounded-2xl bg-gray-50 p-8 dark:bg-gray-800">
					<div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
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
								d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"
							/>
						</svg>
					</div>
					<h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white">Email</h3>
					<div class="mt-2 text-gray-600 dark:text-gray-300">
						<p>Pertanyaan Umum:</p>
						<a href="mailto:info@simakhukum.go.id" class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">info@simakhukum.go.id</a>
					</div>
				</div>

				{{-- Telepon --}}
				<div class="flex flex-col rounded-2xl bg-gray-50 p-8 dark:bg-gray-800">
					<div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 dark:bg-indigo-500">
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
								d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"
							/>
						</svg>
					</div>
					<h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white">Telepon</h3>
					<div class="mt-2 text-gray-600 dark:text-gray-300">
						<p>Senin - Jumat (08:00 - 16:00)</p>
						<a href="tel:+622112345678" class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">(021) 1234-5678</a>
					</div>
				</div>
			</div>

			{{-- Maps --}}
			<div class="mt-16 overflow-hidden rounded-2xl bg-gray-50 shadow-lg dark:bg-gray-800 dark:ring-1 dark:ring-white/10">
				<iframe
					src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.666467336754!2d106.82496477499003!3d-6.175387060508173!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764536d%3A0x6da469cb2e0f9ea8!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1715678901234!5m2!1sid!2sid"
					width="100%"
					height="450"
					style="border:0;"
					allowfullscreen=""
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					class="grayscale transition-all duration-500 hover:grayscale-0"
				></iframe>
			</div>
		</div>
	</div>
@endsection
