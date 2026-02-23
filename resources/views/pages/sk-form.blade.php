@extends('layouts.app')
@section('content')
	<div class="min-h-screen bg-slate-950 font-sans text-gray-100">
		<main class="pt-26 py-10">
			<div class="container mx-auto px-4 sm:px-6 lg:px-8">
				<div class="mb-8 text-center">
					<span class="ring-brand-subtle text-fg-brand-strong bg-brand-softer inline-flex items-center rounded px-2 py-1 text-sm font-medium ring-1 ring-inset mb-2">DRAFT</span>
					<h1 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">
						Form Surat Keputusan
					</h1>
					<p class="text-lg text-gray-600">Buat draf dokumen resmi sesuai standar tata naskah dinas.</p>
				</div>
				<form
					action="{{ route('sk.handle') }}"
					method="POST"
					class="mx-auto max-w-5xl space-y-8"
				>
					@csrf
					{{-- Section 1: Judul --}}
					<section class="overflow-hidden rounded-lg border border-slate-800 bg-slate-900 shadow-lg">
						<div class="border-b border-slate-800 bg-slate-800 px-6 py-4">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">1. Judul Surat Keputusan</h2>
							<p class="text-sm text-gray-400">Informasi utama mengenai keputusan yang akan ditetapkan.</p>
						</div>
						<div class="space-y-6 p-6">
							<div>
								<label for="nomor_surat" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nomor Surat</label>
								<input
									type="text"
									name="nomor_surat"
									id="nomor_surat"
									class="border-default-medium text-heading rounded-base shadow-xs placeholder:text-body focus:ring-none block w-full border bg-gray-50 p-3.5 text-sm focus:outline-2 focus:outline-purple-600 dark:bg-gray-700 dark:focus:outline-purple-400"
									placeholder="Contoh: 800 / BKPSDM / 2024"
								>
								<p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
									Format nomor surat keputusan.
								</p>
							</div>
							<div>
								<label for="sk_title" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Judul Lengkap</label>
								<textarea
								 name="sk_title"
								 id="sk_title"
								 rows="3"
								 class="border-default-medium text-heading rounded-base shadow-xs placeholder:text-body focus:ring-none block w-full border bg-gray-50 p-3.5 text-sm focus:outline-2 focus:outline-purple-600 dark:bg-gray-700 dark:focus:outline-purple-400"
								 placeholder="Contoh: KEPUTUSAN KEPALA DINAS KESEHATAN TENTANG PEMBENTUKAN TIM PELAKSANA KEGIATAN VAKSINASI TAHUN 2024"
								></textarea>
								<p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
									Gunakan huruf kapital untuk judul resmi.
								</p>
							</div>
						</div>
					</section>

					{{-- Section 2: Konsiderans --}}
					<section class="overflow-hidden rounded-lg border border-slate-800 bg-slate-900 shadow-lg">
						<div class="border-b border-slate-800 bg-slate-800 px-6 py-4">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">2. Dasar Hukum (Konsiderans)</h2>
							<p class="text-sm text-gray-400">Alasan dan landasan hukum penetapan keputusan.</p>
						</div>
						<div class="space-y-8 p-6">
							{{-- Menimbang --}}
							<div>
								<div class="mb-3 flex items-center justify-between">
									<label class="block text-sm font-medium text-gray-900 dark:text-white">Menimbang</label>
									<button
										type="button"
										onclick="addInput('menimbang')"
										class="inline-flex cursor-pointer items-center text-sm font-medium text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-500"
									>
										<svg
											xmlns="http://www.w3.org/2000/svg"
											viewBox="0 0 20 20"
											fill="currentColor"
											class="h-4 w-4"
										>
											<path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
										</svg>
										Tambah Poin
									</button>
								</div>
								<div id="menimbang-container" class="space-y-3">
									{{-- Dynamic inputs --}}
								</div>
								<p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Uraikan alasan-alasan perlunya penetapan keputusan ini (huruf a, b, dst).</p>
							</div>

							<hr class="border-gray-200 dark:border-gray-700">

							{{-- Mengingat --}}
							<div>
								<div class="mb-3 flex items-center justify-between">
									<label class="block text-sm font-medium text-gray-900 dark:text-white">Mengingat</label>
									<button
										type="button"
										onclick="addInput('mengingat')"
										class="inline-flex cursor-pointer items-center text-sm font-medium text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-500"
									>
										<svg
											xmlns="http://www.w3.org/2000/svg"
											viewBox="0 0 20 20"
											fill="currentColor"
											class="h-4 w-4"
										>
											<path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
										</svg>
										Tambah Poin
									</button>
								</div>
								<div id="mengingat-container" class="space-y-3">
									{{-- Dynamic inputs --}}
								</div>
								<p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Daftar peraturan perundang-undangan yang menjadi dasar hukum (angka 1, 2, dst).</p>
							</div>

							<hr class="border-gray-200 dark:border-gray-700">

							{{-- Memperhatikan --}}
							<div>
								<div class="mb-3 flex items-center justify-between">
									<label class="block text-sm font-medium text-gray-900 dark:text-white">Memperhatikan <span class="font-normal text-gray-400">(Opsional)</span></label>
									<button
										type="button"
										onclick="addInput('memperhatikan')"
										class="inline-flex cursor-pointer items-center text-sm font-medium text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-500"
									>
										<svg
											xmlns="http://www.w3.org/2000/svg"
											viewBox="0 0 20 20"
											fill="currentColor"
											class="h-4 w-4"
										>
											<path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
										</svg>
										Tambah Poin
									</button>
								</div>
								<div id="memperhatikan-container" class="space-y-3">
									{{-- Dynamic inputs --}}
								</div>
							</div>
						</div>
					</section>

					{{-- Section 3: Diktum --}}
					<section class="overflow-hidden rounded-lg border border-slate-800 bg-slate-900 shadow-lg">
						<div class="border-b border-slate-800 bg-slate-800 px-6 py-4">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">3. Diktum Keputusan</h2>
							<p class="text-sm text-gray-400">Isi keputusan yang ditetapkan.</p>
						</div>
						<div class="space-y-6 p-6">
							<div>
								<label for="menetapkan" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Menetapkan</label>
								<textarea
								 name="menetapkan"
								 id="menetapkan"
								 rows="2"
								 class="border-default-medium text-heading rounded-base shadow-xs placeholder:text-body focus:ring-none block w-full border bg-gray-50 p-3.5 text-sm focus:outline-2 focus:outline-purple-600 dark:bg-gray-700 dark:focus:outline-purple-400"
								 placeholder="Contoh: KEPUTUSAN KEPALA DINAS TENTANG..."
								></textarea>
							</div>

							<div>
								<div class="mb-3 flex items-center justify-between">
									<label class="block text-sm font-medium text-gray-900 dark:text-white">Amar Putusan (Diktum)</label>
									<button
										type="button"
										onclick="addDiktum()"
										class="inline-flex cursor-pointer items-center text-sm font-medium text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-500"
									>
										<svg
											xmlns="http://www.w3.org/2000/svg"
											viewBox="0 0 20 20"
											fill="currentColor"
											class="h-4 w-4"
										>
											<path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
										</svg>
										Tambah Diktum
									</button>
								</div>
								<div id="diktum-container" class="space-y-4">
									{{-- Dynamic inputs --}}
								</div>
							</div>
						</div>
					</section>

					{{-- Section 4: Penutup --}}
					<section class="overflow-hidden rounded-lg border border-slate-800 bg-slate-900 shadow-lg">
						<div class="border-b border-slate-800 bg-slate-800 px-6 py-4">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">4. Penutup</h2>
							<p class="text-sm text-gray-400">Informasi penetapan dan penandatanganan.</p>
						</div>
						<div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
							<div>
								<label for="ditetapkan_di" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Ditetapkan di</label>
								<input
									type="text"
									name="ditetapkan_di"
									id="ditetapkan_di"
									class="border-default-medium text-heading rounded-base shadow-xs placeholder:text-body focus:ring-none block w-full border bg-gray-50 p-3.5 text-sm focus:outline-2 focus:outline-purple-600 dark:bg-gray-700 dark:focus:outline-purple-400"
									placeholder="Contoh: Jakarta"
								>
							</div>
							<div>
								<label for="pada_tanggal" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Pada Tanggal</label>
								<input
									type="date"
									name="pada_tanggal"
									id="pada_tanggal"
									class="border-default-medium text-heading rounded-base shadow-xs placeholder:text-body focus:ring-none block w-full border bg-gray-50 p-3.5 text-sm focus:outline-2 focus:outline-purple-600 dark:bg-gray-700 dark:focus:outline-purple-400"
								>
							</div>
							<div class="md:col-span-2">
								<label for="jabatan_penandatangan" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Jabatan Penandatangan</label>
								<input
									type="text"
									name="jabatan_penandatangan"
									id="jabatan_penandatangan"
									value="BUPATI BUOL"
									class="border-default-medium text-heading rounded-base shadow-xs placeholder:text-body focus:ring-none block w-full border bg-gray-50 p-3.5 text-sm focus:outline-2 focus:outline-purple-600 dark:bg-gray-700 dark:focus:outline-purple-400"
								>
							</div>
							<div class="md:col-span-2">
								<label for="nama_penandatangan" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Nama Penandatangan</label>
								<input
									type="text"
									name="nama_penandatangan"
									id="nama_penandatangan"
									value="RISHARYUDI TRIWIBOWO"
									class="border-default-medium text-heading rounded-base shadow-xs placeholder:text-body focus:ring-none block w-full border bg-gray-50 p-3.5 text-sm focus:outline-2 focus:outline-purple-600 dark:bg-gray-700 dark:focus:outline-purple-400"
								>
							</div>
						</div>
					</section>

					{{-- Action Bar --}}
					<div class="flex flex-wrap items-center justify-end gap-3">
						<button type="reset" class="text-gray-200 bg-slate-800 border border-slate-600 hover:bg-slate-700 hover:text-white focus:ring-slate-500 shadow-xs rounded-base box-border cursor-pointer px-4 py-2.5 text-sm font-medium leading-5 focus:outline-none focus:ring-4">
							Batal
						</button>

						<button
							type="submit"
							name="action"
							value="preview"
							class="rounded-base bg-linear-to-r from-purple-600 to-blue-500 hover:bg-linear-to-br cursor-pointer px-4 py-2.5 text-center text-sm font-medium leading-5 text-white focus:outline-none focus:ring-4 focus:ring-purple-500"
						>
							Preview Surat Keputusan
						</button>

											</div>
				</form>
			</div>
		</main>
	</div>
@endsection
@push('scripts')
	<script>
		const DIKTUM_LABELS = [
			'KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA',
			'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'
		];

		document.addEventListener('DOMContentLoaded', () => {
			addInput('menimbang', 'bahwa dalam rangka...');
			addInput('mengingat', 'Undang-Undang Nomor ...');
			addDiktum();
		});

		function updateRemoveButtons(container) {
			const groups = container.querySelectorAll('.group');
			groups.forEach(group => {
				const btn = group.querySelector('button');
				if (btn) btn.style.display = groups.length > 1 ? '' : 'none';
			});
		}

		function createInputGroup(type, placeholder) {
			const container = document.getElementById(`${type}-container`);
			if (!container) return;

			const wrapper = document.createElement('div');
			wrapper.className = 'relative group';

			const textarea = document.createElement('textarea');
			textarea.name = `${type}[]`;
			textarea.rows = 2;
			textarea.placeholder = placeholder;
			textarea.className = `
				w-full rounded-base border border-default-medium
				bg-gray-50 dark:bg-gray-700
				text-sm text-heading
				p-3.5 pr-12
				shadow-xs
				placeholder:text-body
				focus:outline-2 focus:outline-purple-600
				dark:focus:outline-purple-400
			`;

			const removeBtn = document.createElement('button');
			removeBtn.type = 'button';
			removeBtn.className = `
				absolute top-2 right-2 z-10
				p-1.5 rounded-md
				bg-white/80 dark:bg-gray-800/80
				text-gray-400
				hover:text-red-600 hover:bg-red-50
				dark:text-gray-500
				dark:hover:text-red-500 dark:hover:bg-red-900/20
				opacity-0 group-hover:opacity-100 focus:opacity-100
				cursor-pointer
			`;
			removeBtn.innerHTML = `
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
					fill="currentColor" class="w-4 h-4">
					<path fill-rule="evenodd"
						d="M8.75 1A2.75 2.75 0 006 3.75v.443
						c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482
						l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807
						a2.75 2.75 0 002.742-2.53l.841-10.52.149.023
						a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75
						A2.75 2.75 0 0011.25 1h-2.5z" clip-rule="evenodd"/>
				</svg>
			`;

			removeBtn.onclick = () => {
				wrapper.remove();
				updateRemoveButtons(container);
			};

			wrapper.appendChild(textarea);
			wrapper.appendChild(removeBtn);
			container.appendChild(wrapper);

			updateRemoveButtons(container);
		}

		function addInput(type, placeholder = '') {
			createInputGroup(type, placeholder || `Isi poin ${type}...`);
		}

		function addDiktum() {
			const container = document.getElementById('diktum-container');
			const count = container.children.length;
			const label = DIKTUM_LABELS[count] || `Diktum Ke-${count + 1}`;
			createInputGroup('diktum', `${label}: ...`);
		}
	</script>
@endpush
