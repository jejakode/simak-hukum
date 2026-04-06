<x-sk-form.section-card
    number="5"
    title="Lampiran"
    description="Unggah lampiran (DOCX, PDF, JPG, PNG). Lampiran akan langsung disisipkan setelah halaman terakhir SK."
>
    <div class="space-y-4">
        <div>
            <label for="lampiran_docx" class="mb-2 block text-sm font-medium text-slate-900 dark:text-slate-100">
                Lampiran <span class="font-normal text-slate-500 dark:text-slate-400">(Opsional, bisa lebih dari satu)</span>
            </label>
            <input
                type="file"
                name="lampiran_docx[]"
                id="lampiran_docx"
                accept=".docx,.pdf,.jpg,.jpeg,.png,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,image/jpeg,image/png"
                multiple
                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm text-slate-900 shadow-sm outline-none transition file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:file:bg-cyan-900/30 dark:file:text-cyan-200 dark:hover:file:bg-cyan-800/40 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
            >
            @error('lampiran_docx')
                <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            @error('lampiran_docx.*')
                <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Maksimal 10MB per file. Lampiran otomatis disisipkan setelah halaman terakhir dokumen utama.</p>
        </div>

        @php
            $existingLampiran = array_values(array_filter($draft['lampiran'] ?? [], function ($item) {
                return is_array($item)
                    && is_string($item['name'] ?? null)
                    && trim($item['name']) !== '';
            }));
        @endphp

        @if (!empty($existingLampiran))
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-800/70">
                <p class="mb-2 font-semibold text-slate-800 dark:text-slate-100">Lampiran tersimpan di draft:</p>
                <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">Klik tombol X untuk menghapus lampiran dari draft.</p>
                <div id="remove-lampiran-inputs"></div>
                <ul id="existing-lampiran-list" class="space-y-2 text-slate-600 dark:text-slate-300">
                    @foreach ($existingLampiran as $lampiran)
                        <li class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900/50">
                            <span class="truncate">{{ $lampiran['name'] }}</span>
                            <button
                                type="button"
                                data-remove-lampiran="true"
                                data-lampiran-path="{{ $lampiran['path'] }}"
                                class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100 dark:border-red-900/70 dark:bg-red-950/30 dark:text-red-300 dark:hover:bg-red-900/40"
                                aria-label="Hapus lampiran {{ $lampiran['name'] }}"
                                title="Hapus lampiran"
                            >
                                <span class="text-sm font-bold leading-none">X</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
                @error('remove_lampiran')
                    <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('remove_lampiran.*')
                    <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif
    </div>
</x-sk-form.section-card>
