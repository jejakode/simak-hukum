@extends('layouts.app')

@section('content')
	@php
		$legalPdfUrl = asset('legal/' . rawurlencode('DASAR HUKUM FORMAT PENYUSUNAN SURAT KEPUTUSAN BUPATI.pdf'));
	@endphp
	<div class="bg-gray-50 dark:bg-gray-900">
		{{-- Hero Section --}}
		<div class="relative isolate min-h-screen px-6 lg:px-8">
			<div class="mx-auto max-w-2xl py-28 sm:py-48 lg:py-52">
				<div class="text-center">
					<img
						src="{{ asset('assets/hero.png') }}"
						alt="Ilustrasi"
						class="mx-auto mb-6 h-20 sm:h-24"
					>
					<h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white">
						Manajemen <span class="text-indigo-600 dark:text-indigo-400">Surat Keputusan Bupati</span>
					</h1>
					<p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
						Sistem yang dibuat untuk memanajemeni pembuatan Surat Keputusan Bupati secara efisien dan terstandarisasi di Lingkungan Pemerintah Kabupaten Buol.
					</p>
					<div class="mt-10 flex flex-col items-center gap-6">
						<div class="flex w-full flex-col items-center justify-center gap-3 sm:w-auto sm:flex-row sm:gap-x-6">
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
						<button type="button" id="legal-basis-trigger" class="text-body bg-neutral-primary-soft border-default hover:bg-neutral-secondary-medium hover:text-heading focus:ring-neutral-tertiary-soft shadow-xs inline-flex items-center gap-2 rounded-base border px-4 py-2.5 text-sm font-medium leading-5 focus:outline-none focus:ring-4">
							<x-heroicon-o-document-text class="h-4 w-4" />
							Dasar Hukum Format SK Bupati
						</button>
					</div>
				</div>
			</div>
		</div>

		{{-- Features Section --}}
		<div class="bg-gray-50 py-24 sm:py-32 dark:bg-gray-900">
			<div class="mx-auto max-w-7xl px-6 lg:px-8">
				<div class="mx-auto max-w-2xl text-center">
					<h2 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">Manfaat Utama</h2>
					<p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">Sistem Cerdas Hukum</p>
					<p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
						"Dirancang untuk mendigitalkan layanan fasilitasi pembuatan Surat Keputusan Bupati agar sesuai format dan ketentuan Peraturan Perundang-undangan"
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
								<p class="flex-auto">Menjamin setiap Surat Keputusan Bupati yang diterbitkan memiliki format dan struktur yang seragam sesuai dengan peraturan yang berlaku.</p>
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

@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const legalBasisTrigger = document.getElementById('legal-basis-trigger');
			const legalPdfUrl = @json($legalPdfUrl);

			if (!legalBasisTrigger) {
				return;
			}

			legalBasisTrigger.addEventListener('click', async () => {
				if (typeof window.Swal === 'undefined') {
					window.open(legalPdfUrl, '_blank', 'noopener');
					return;
				}

				const isMobile = window.matchMedia('(max-width: 640px)').matches;

				await window.Swal.fire({
					title: 'Dasar Hukum Format SK Bupati',
					width: isMobile ? '100vw' : '92vw',
					padding: isMobile ? '0.5rem' : '1rem',
					showConfirmButton: isMobile,
					confirmButtonText: 'Tutup',
					showCloseButton: true,
					customClass: {
						popup: isMobile ? 'rounded-lg' : 'rounded-2xl',
						confirmButton: 'rounded-lg px-4 py-2',
					},
					html: `
						<div style="height:${isMobile ? '72vh' : 'min(78vh, 900px)'};">
							<iframe
								src="${legalPdfUrl}"
								title="Dasar Hukum Format SK Bupati"
								style="width:100%; height:100%; border:1px solid #e5e7eb; border-radius:${isMobile ? '8px' : '10px'};"
							></iframe>
						</div>
						<div style="margin-top:10px; text-align:${isMobile ? 'left' : 'right'};">
							<a href="${legalPdfUrl}" target="_blank" rel="noopener" style="font-size:13px; color:#2563eb; text-decoration:underline;">
								Buka di tab baru
							</a>
						</div>
					`,
				});
			});
		});
	</script>
@endpush
