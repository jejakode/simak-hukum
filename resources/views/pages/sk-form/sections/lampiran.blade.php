<x-sk-form.section-card
    number="5"
    title="Lampiran"
    description="Unggah file lampiran DOCX yang akan ikut dalam paket unduhan bersama SK."
>
    <div class="space-y-4">
        <div>
            <label for="lampiran_docx" class="mb-2 block text-sm font-medium text-slate-900 dark:text-slate-100">
                Lampiran DOCX <span class="font-normal text-slate-500 dark:text-slate-400">(Opsional, bisa lebih dari satu)</span>
            </label>
            <input
                type="file"
                name="lampiran_docx[]"
                id="lampiran_docx"
                accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                multiple
                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm text-slate-900 shadow-sm outline-none transition file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:file:bg-cyan-900/30 dark:file:text-cyan-200 dark:hover:file:bg-cyan-800/40 dark:focus:border-cyan-400 dark:focus:ring-cyan-500/20"
            >
            @error('lampiran_docx')
                <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            @error('lampiran_docx.*')
                <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Maksimal 10MB per file. Saat ada lampiran, download DOCX akan menjadi paket ZIP berisi SK + lampiran.</p>
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
                <ul class="list-disc space-y-1 pl-5 text-slate-600 dark:text-slate-300">
                    @foreach ($existingLampiran as $lampiran)
                        <li>{{ $lampiran['name'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-sk-form.section-card>