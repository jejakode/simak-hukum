@php
	$steps = [
	    [
	        'title' => 'Pilih Menu Buat SK',
	        'description' => 'Akses fitur pembuatan Surat Keputusan melalui menu yang tersedia pada dashboard atau header aplikasi.',
	    ],
	    [
	        'title' => 'Isi Formulir',
	        'description' => 'Lengkapi seluruh data yang diperlukan, termasuk Konsiderans, Dasar Hukum, dan Diktum Keputusan sesuai ketentuan.',
	    ],
	    [
	        'title' => 'Unduh Dokumen',
	        'description' => 'Pratinjau hasil Surat Keputusan yang telah dibuat, lalu unduh dokumen dalam format PDF atau Word.',
	    ],
	    [
	        'title' => 'Cetak dan Setor Dokumen',
	        'description' => 'Cetak Surat Keputusan yang telah diunduh dan serahkan ke Sekretariat Daerah Bagian Hukum untuk proses administrasi lanjutan.',
	    ],
	];
@endphp
@extends('layouts.app')
@section('content')
	<div class="bg-white py-24 sm:py-32 dark:bg-gray-900">
		<div class="mx-auto max-w-7xl px-6 lg:px-8">
			<div class="mx-auto max-w-2xl lg:mx-0">
				<h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">Panduan Penggunaan</h2>
				<p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
					Ikuti langkah-langkah berikut untuk mulai membuat dan mengelola Surat Keputusan dengan mudah.
				</p>
			</div>
			<div class="mx-auto mt-16 max-w-4xl lg:mx-0">
				{{-- Timeline Component --}}
				<ol class="relative border-s border-gray-200 dark:border-gray-700">
					@foreach ($steps as $index => $step)
						<li class="{{ $loop->last ? 'ms-8' : 'mb-10 ms-8' }}">
							<span class="absolute -start-4 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-800 ring-8 ring-white dark:bg-indigo-900 dark:text-indigo-300 dark:ring-gray-900">
								{{ $index + 1 }}
							</span>
							<h3 class="mb-1 text-xl font-semibold text-gray-900 dark:text-white">
								{{ $step['title'] }}
							</h3>
							<p class="text-base font-normal text-gray-500 dark:text-gray-400">
								{{ $step['description'] }}
							</p>
						</li>
					@endforeach
				</ol>

				<div class="mt-16 border-t border-gray-200 pt-10 dark:border-gray-700">
					<a href="{{ url('/sk/create') }}" class="rounded-base bg-linear-to-br hover:bg-linear-to-bl inline-block from-purple-600 to-blue-500 px-4 py-2.5 text-center text-sm font-medium leading-5 text-white focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
						Mulai Buat SK Sekarang
					</a>
				</div>
			</div>
		</div>
	</div>
@endsection
