<x-sk-form.section-card
    number="4"
    title="Penutup"
    description="Informasi penetapan dan penandatanganan."
>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-sk-form.input
            id="ditetapkan_di"
            name="ditetapkan_di"
            label="Ditetapkan di"
            :value="$draft['ditetapkan_di'] ?? 'BUOL'"
            readonly
            class="bg-slate-100 dark:bg-slate-700/60"
        />

        <x-sk-form.input
            id="pada_tanggal"
            name="pada_tanggal"
            label="Pada Tanggal"
            type="date"
            :value="$draft['pada_tanggal'] ?? ''"
        />

        <x-sk-form.input
            id="jabatan_penandatangan"
            name="jabatan_penandatangan"
            label="Jabatan Penandatangan"
            :value="$draft['jabatan_penandatangan'] ?? 'BUPATI BUOL'"
            readonly
            class="md:col-span-2 bg-slate-100 dark:bg-slate-700/60"
        />

        <x-sk-form.input
            id="nama_penandatangan"
            name="nama_penandatangan"
            label="Nama Penandatangan"
            :value="$draft['nama_penandatangan'] ?? 'RISHARYUDI TRIWIBOWO'"
            readonly
            class="md:col-span-2 bg-slate-100 dark:bg-slate-700/60"
        />
    </div>
</x-sk-form.section-card>
