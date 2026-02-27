<x-sk-form.section-card
    number="2"
    title="Dasar Hukum (Konsiderans)"
    description="Alasan dan landasan hukum penetapan keputusan."
>
    <div class="space-y-8">
        <x-sk-form.dynamic-list
            id="menimbang"
            label="Menimbang"
            buttonText="Tambah Poin"
            buttonAction="addInput('menimbang')"
            hint="Uraikan alasan perlunya penetapan keputusan ini (huruf a, b, dst)."
        />

        <hr class="border-slate-200 dark:border-slate-700">

        <x-sk-form.dynamic-list
            id="mengingat"
            label="Mengingat"
            buttonText="Tambah Poin"
            buttonAction="addInput('mengingat')"
            hint="Daftar peraturan perundang-undangan yang menjadi dasar hukum (angka 1, 2, dst)."
        />

        <hr class="border-slate-200 dark:border-slate-700">

        <x-sk-form.dynamic-list
            id="memperhatikan"
            label="Memperhatikan"
            buttonText="Tambah Poin"
            buttonAction="addInput('memperhatikan')"
            :optional="true"
        />
    </div>
</x-sk-form.section-card>
