<x-sk-form.section-card
    number="1"
    title="Judul Surat Keputusan"
    description="Informasi utama mengenai keputusan yang akan ditetapkan."
>
    <div class="space-y-6">
        <x-sk-form.input
            id="nomor_surat"
            name="nomor_surat"
            label="Nomor Surat"
            :value="$draft['nomor_surat'] ?? ''"
            placeholder="Contoh: 800 / BKPSDM / 2024"
            help="Format nomor surat keputusan."
        />

        <x-sk-form.textarea
            id="sk_title"
            name="sk_title"
            label="Judul Lengkap"
            :value="$draft['sk_title'] ?? ''"
            rows="3"
            placeholder="Contoh: KEPUTUSAN KEPALA DINAS KESEHATAN TENTANG PEMBENTUKAN TIM PELAKSANA KEGIATAN VAKSINASI TAHUN 2024"
            help="Gunakan huruf kapital untuk judul resmi."
        />
    </div>
</x-sk-form.section-card>
