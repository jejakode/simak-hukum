@extends('layouts.app')

@section('content')
    @php
        $lampiranItems = array_values(array_filter($lampiran ?? [], fn($item) => is_array($item) && is_string($item['name'] ?? null) && trim($item['name']) !== ''));
    @endphp

    <div class="bg-gradient-to-b from-slate-100 via-slate-100 to-slate-200 pb-12 pt-28 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8 text-center">
                    <h1 class="mb-2 text-3xl font-bold text-slate-900 dark:text-slate-100">Preview Surat Keputusan</h1>
                    <p class="text-base text-slate-600 dark:text-slate-300">Preview ini di-render dari DOCX ke PDF agar tampilan mendekati Word dan konsisten di berbagai browser.</p>
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
                            <a href="{{ route('sk.docx') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50/80 px-4 py-2.5 text-sm font-semibold text-emerald-800 shadow-sm transition hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200 dark:hover:bg-emerald-900/60 sm:w-auto">
                                <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                                Download
                            </a>
                            <button
                                type="button"
                                id="refresh-preview-btn"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-blue-300 bg-blue-50/80 px-4 py-2.5 text-sm font-semibold text-blue-800 shadow-sm transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/60 dark:text-blue-200 dark:hover:bg-blue-900/60 sm:w-auto"
                            >
                                <x-heroicon-o-arrow-path class="h-4 w-4" />
                                Refresh Preview
                            </button>
                        </div>

                        <div class="space-y-2 text-xs text-slate-500 dark:text-slate-300">
                            <p class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-800/60">
                                Jika tidak terjadi perubahan, tekan <span class="font-semibold">Ctrl + Shift + R</span> atau klik tombol <span class="font-semibold">Refresh Preview</span>.
                            </p>
                            @if (!empty($lampiranItems))
                                <div class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-sky-900 dark:border-sky-900/60 dark:bg-sky-950/40 dark:text-sky-100">
                                    <p class="font-semibold">Lampiran akan disisipkan setelah halaman terakhir dokumen:</p>
                                    <ul class="mt-1 list-disc pl-5">
                                        @foreach ($lampiranItems as $item)
                                            <li>{{ $item['name'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-lg dark:border-slate-800 dark:bg-slate-900">
                    <div id="pdf-preview-loading" class="py-4 text-center text-sm text-slate-500 dark:text-slate-300">Menyiapkan preview dokumen...</div>
                    <div id="pdf-preview-pages" class="space-y-4"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const newSkTrigger = document.getElementById('new-sk-trigger');
            const refreshPreviewButton = document.getElementById('refresh-preview-btn');
            if (newSkTrigger) {
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
            }

            if (refreshPreviewButton) {
                refreshPreviewButton.addEventListener('click', async () => {
                    await renderPdfPreview(true);
                });
            }

            document.addEventListener('keydown', async event => {
                const key = String(event.key || '').toLowerCase();
                if ((event.ctrlKey || event.metaKey) && event.shiftKey && key === 'r') {
                    event.preventDefault();
                    await renderPdfPreview(true);
                }
            });

            renderPdfPreview();
        });

        async function renderPdfPreview(forceRefresh = false) {
            if (typeof window.pdfjsLib === 'undefined') {
                setPreviewError('Library preview PDF tidak tersedia di browser ini.');
                return;
            }

            const container = document.getElementById('pdf-preview-pages');
            const loading = document.getElementById('pdf-preview-loading');
            if (!container || !loading) {
                return;
            }

            try {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                container.innerHTML = '';
                loading.textContent = forceRefresh ? 'Merefresh preview dokumen...' : 'Menyiapkan preview dokumen...';
                loading.classList.remove('hidden', 'text-red-600', 'dark:text-red-300');
                loading.classList.add('text-slate-500', 'dark:text-slate-300');

                const pdfUrl = new URL(@json(route('sk.preview.pdf')), window.location.origin);
                pdfUrl.searchParams.set('_ts', String(Date.now()));
                if (forceRefresh) {
                    pdfUrl.searchParams.set('refresh', '1');
                }

                const pdf = await window.pdfjsLib.getDocument({ url: pdfUrl.toString(), withCredentials: true }).promise;

                container.innerHTML = '';
                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    const page = await pdf.getPage(pageNum);
                    const baseViewport = page.getViewport({ scale: 1 });
                    const maxWidth = Math.min(container.clientWidth || 900, 900);
                    const scale = maxWidth / baseViewport.width;
                    const viewport = page.getViewport({ scale });

                    const wrap = document.createElement('div');
                    wrap.className = 'mx-auto w-fit bg-white shadow';

                    const canvas = document.createElement('canvas');
                    canvas.width = Math.floor(viewport.width);
                    canvas.height = Math.floor(viewport.height);
                    canvas.className = 'block h-auto w-full max-w-full';

                    wrap.appendChild(canvas);
                    container.appendChild(wrap);

                    const context = canvas.getContext('2d');
                    await page.render({ canvasContext: context, viewport }).promise;
                }

                loading.classList.add('hidden');
            } catch (error) {
                setPreviewError('Preview belum bisa ditampilkan. Silakan klik Refresh Preview atau gunakan tombol Download');
            }
        }

        function setPreviewError(message) {
            const loading = document.getElementById('pdf-preview-loading');
            if (!loading) {
                return;
            }

            loading.textContent = message;
            loading.classList.remove('text-slate-500', 'dark:text-slate-300');
            loading.classList.add('text-red-600', 'dark:text-red-300');
        }
    </script>
@endpush
