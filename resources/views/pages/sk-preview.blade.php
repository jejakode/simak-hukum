@extends('layouts.app')

@section('content')
    @php
        $lampiranItems = array_values(array_filter($lampiran ?? [], fn($item) => is_array($item) && is_string($item['name'] ?? null) && trim($item['name']) !== ''));
    @endphp

    <div class="bg-gradient-to-b from-slate-100 via-slate-100 to-slate-200 pb-12 pt-24 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <div class="mb-8 text-center">
                    <h1 class="mb-2 text-3xl font-bold text-slate-900 dark:text-slate-100">Preview Surat Keputusan</h1>
                    <p class="text-base text-slate-600 dark:text-slate-300">Preview ini langsung dari PDF hasil konversi template DOCX agar tampilannya sama persis.</p>
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

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg dark:border-slate-800 dark:bg-slate-900">
                    <iframe
                        src="{{ route('sk.preview.pdf') }}#zoom=page-width"
                        title="Preview SK PDF"
                        class="h-[85vh] w-full"
                    ></iframe>
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
