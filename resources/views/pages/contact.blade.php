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
						<p>Bagian Hukum-Sekretariat Daerah Lt.1, Jalan Batalipu No. 01, Kabupaten Buol.</p>
					</address>
				</div>

				{{-- Jam Buka --}}
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
								d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"
							/>
						</svg>
					</div>
					<h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white">Jam Pelayanan</h3>
					<div class="mt-2 text-gray-600 dark:text-gray-300">
						<p>Senin - Jumat</p>
						<p>(08:00 - 16:00 WITA)</p>
					</div>
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
						<a href="mailto:bagianhukumsetdabuol@gmail.com" class="font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">bagianhukumsetdabuol@gmail.com</a>
					</div>
				</div>
			</div>

			{{-- Maps --}}
			<div class="mt-16 overflow-hidden rounded-2xl bg-gray-50 shadow-lg dark:bg-gray-800 dark:ring-1 dark:ring-white/10">
				<iframe
					src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d251.90878743546097!2d121.40968351450176!3d1.1684915052010523!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3270ba8316591fed%3A0xfb0598f2d86b4f42!2sKantor%20Bupati%20Buol!5e1!3m2!1sen!2sid!4v1772224039395!5m2!1sen!2sid"
					width="100%"
					height="450"
					style="border:0;"
					allowfullscreen=""
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
				></iframe>
			</div>
		</div>
	</div>
@endsection
