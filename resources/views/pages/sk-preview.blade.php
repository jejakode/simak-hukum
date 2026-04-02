@extends('layouts.app')

@push('styles')
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Bookman+Old+Style&display=swap');

		.document-preview {
			font-family: 'Bookman Old Style', serif;
			font-size: 12pt;
			line-height: 1.4;
		}

		.document-preview h1,
		.document-preview h2,
		.document-preview h3 {
			font-weight: normal;
			text-align: center;
			text-transform: uppercase;
		}

		.document-preview .header-label {
			display: inline-block;
			min-width: 85px;
			vertical-align: top;
		}

		.document-preview .list-row {
			margin-left: 40px;
			text-indent: -40px;
			margin-bottom: 8px;
			text-align: justify;
			line-height: 1.4;
		}

		.document-preview .diktum-item {
			text-align: justify;
			margin-bottom: 8px;
			line-height: 1.4;
			display: flex;
			gap: 8px;
		}

		.document-preview .kop-surat p {
			margin: 2px 0;
			line-height: 1.2;
		}

		.document-preview .judul-utama,
		.document-preview .tentang,
		.document-preview .judul-detail,
		.document-preview .jabatan-bupati,
		.document-preview strong {
			font-weight: normal;
		}

		.document-paper {
			width: 210mm;
			min-height: 297mm;
			margin: auto;
			padding: 2cm;
			background: #ffffff;
			color: #0f172a;
		}

		.dark .document-paper {
			background: #f8fafc;
			box-shadow: 0 20px 50px rgba(2, 6, 23, 0.55);
		}

		@media (max-width: 768px) {
			.document-paper {
				width: 100%;
				min-height: auto;
				padding: 1.25rem;
			}
		}
	</style>
@endpush

@section('content')
	@php
		$menimbangItems = array_values(array_filter($menimbang ?? [], fn($item) => is_string($item) && trim($item) !== ''));
		$mengingatItems = array_values(array_filter($mengingat ?? [], fn($item) => is_string($item) && trim($item) !== ''));
		$memperhatikanItems = array_values(array_filter($memperhatikan ?? [], fn($item) => is_string($item) && trim($item) !== ''));
		$diktumItems = array_values(array_filter($diktum ?? [], fn($item) => is_string($item) && trim($item) !== ''));
		$lampiranItems = array_values(array_filter($lampiran ?? [], fn($item) => is_array($item) && is_string($item['name'] ?? null) && trim($item['name']) !== ''));
		$diktumLabels = ['KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA', 'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'];
	@endphp

	<div class="pt-26 bg-gradient-to-b from-slate-100 via-slate-100 to-slate-200 py-12 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
		<div class="container mx-auto px-4 sm:px-6 lg:px-8">
			<div class="mx-auto max-w-5xl">
				<div class="mb-8 text-center">
					<h1 class="mb-2 text-3xl font-bold text-slate-900 dark:text-slate-100">Preview Surat Keputusan</h1>
					<p class="text-base text-slate-600 dark:text-slate-300">Ini adalah pratinjau dokumen. Periksa kembali isinya sebelum mengunduh.</p>
				</div>

				<div class="mb-8 rounded-2xl border border-slate-200 bg-white/90 p-1 shadow-lg backdrop-blur-sm dark:border-slate-800 dark:bg-slate-900/70">
					<div class="flex flex-col gap-4 rounded-2xl bg-white px-4 py-4 dark:bg-slate-900 sm:px-5">
						<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:flex items-center md:justify-center">
							<a href="{{ route('sk.create', ['edit' => 1]) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100 sm:w-auto dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
								<x-heroicon-o-pencil-square class="h-4 w-4" />
								Edit SK
							</a>
							<a
								href="{{ route('sk.new') }}"
								id="new-sk-trigger"
								class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-amber-300 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-800 shadow-sm transition hover:bg-amber-100 sm:w-auto dark:border-amber-800 dark:bg-amber-950/60 dark:text-amber-200 dark:hover:bg-amber-900/60"
							>
								<x-heroicon-o-plus-circle class="h-4 w-4" />
								Buat Baru
							</a>
                            <a href="{{ route('sk.pdf') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-300 bg-red-50/80 px-4 py-2.5 text-sm font-semibold text-red-800 shadow-sm transition hover:bg-red-100 dark:border-red-800 dark:bg-red-950/60 dark:text-red-200 dark:hover:bg-red-900/60 sm:w-auto">
								<x-heroicon-o-arrow-down-tray class="h-4 w-4" />
								Download PDF
							</a>
							<a href="{{ route('sk.docx') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50/80 px-4 py-2.5 text-sm font-semibold text-emerald-800 shadow-sm transition hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200 dark:hover:bg-emerald-900/60 sm:w-auto">
								<x-heroicon-o-arrow-down-tray class="h-4 w-4" />
								{{ empty($lampiranItems) ? 'Download DOCX' : 'Download Paket DOCX' }}
							</a>
						</div>
					</div>
				</div>

				@if (!empty($lampiranItems))
					<div class="mb-6 rounded-xl border border-sky-200 bg-sky-50/80 px-4 py-3 text-sm text-sky-900 dark:border-sky-900/60 dark:bg-sky-950/40 dark:text-sky-100">
						<p class="font-semibold">Lampiran DOCX akan ikut dalam paket unduhan:</p>
						<ul class="mt-1 list-disc pl-5">
							@foreach ($lampiranItems as $item)
								<li>{{ $item['name'] }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<div class="document-paper document-preview shadow-lg">
					<header class="mb-8 text-center">
						<div class="kop-surat" style="position: relative; height: 180px; display: flex; flex-direction: column; align-items: center;">
							<img
								src="{{ asset('garuda.png') }}"
								alt="Garuda Pancasila"
								style="width: 3.5cm; height: 3.5cm; margin-bottom: 10px;"
							>
							<div style="text-align: center;">
								<p class="jabatan-bupati">BUPATI BUOL</p>
								<p>PROVINSI SULAWESI TENGAH</p>
							</div>
						</div>
					</header>

					<div class="mb-8 text-center">
						<h2 class="judul-utama">KEPUTUSAN BUPATI</h2>
						@if (trim((string) $nomor_surat) !== '')
							<p>NOMOR : {{ $nomor_surat }}</p>
						@endif
						<p class="tentang">TENTANG</p>
						<h3 class="judul-detail">{{ $sk_title ?: '[JUDUL SURAT KEPUTUSAN]' }}</h3>
						<p class="mt-6" style="text-transform: uppercase;">BUPATI BUOL,</p>
					</div>

					<main>
						<div class="mb-6">
							<span class="header-label">Menimbang:</span>
							@foreach ($menimbangItems as $index => $item)
								<div class="list-row">{{ chr(97 + $index) }}. {{ $item }};</div>
							@endforeach
						</div>

						<div class="mb-6">
							<span class="header-label">Mengingat:</span>
							@foreach ($mengingatItems as $index => $item)
								<div class="list-row">{{ $index + 1 }}. {{ $item }};</div>
							@endforeach
						</div>

						@if (!empty($memperhatikanItems))
							<div class="mb-6">
								<span class="header-label">Memperhatikan:</span>
								@foreach ($memperhatikanItems as $index => $item)
									<div class="list-row">{{ $index + 1 }}. {{ $item }};</div>
								@endforeach
							</div>
						@endif

						<div class="mb-6">
							<p class="mb-3 text-center" style="text-transform: uppercase;">MEMUTUSKAN</p>
							<div>Menetapkan: {{ $menetapkan ?: '[MENETAPKAN]' }}</div>
						</div>

						<div class="mb-6">
							@foreach ($diktumItems as $index => $item)
								<div class="diktum-item">
									<span style="min-width: 90px;">{{ $diktumLabels[$index] ?? 'DIKTUM ' . ($index + 1) }}:</span>
									<span>{{ $item }};</span>
								</div>
							@endforeach
						</div>
					</main>

					<footer class="mt-16">
						<div class="ml-auto w-1/2 text-left">
							<p>Ditetapkan di {{ $ditetapkan_di ?: '[Tempat]' }}</p>
							<p class="mt-4">{{ $jabatan_penandatangan ?: '[JABATAN PENANDATANGAN]' }},</p>
							<div class="h-24"></div>
							<p class="underline">{{ $nama_penandatangan ?: '[NAMA PENANDATANGAN]' }}</p>
						</div>
					</footer>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const newSkTrigger = document.getElementById('new-sk-trigger');
			if (!newSkTrigger) {
				return;
			}

			newSkTrigger.addEventListener('click', async event => {
				event.preventDefault();

				const href = newSkTrigger.getAttribute('href');
				if (!href) {
					return;
				}

				if (typeof window.Swal === 'undefined') {
					return;
				}

				const result = await window.Swal.fire({
					title: 'Buat SK baru?',
					text: 'Draft SK yang sedang dikerjakan akan dihapus.',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Ya, buat baru',
					cancelButtonText: 'Batal',
					reverseButtons: true,
					customClass: {
						popup: 'rounded-2xl',
						confirmButton: 'rounded-lg',
						cancelButton: 'rounded-lg',
					},
				});

				if (result.isConfirmed) {
					window.location.href = href;
				}
			});
		});
	</script>
@endpush
