<x-sk-form.section-card
    number="3"
    title="Diktum Keputusan"
    description="Isi keputusan yang ditetapkan."
>
    <div class="space-y-6">
        <x-sk-form.textarea
            id="menetapkan"
            name="menetapkan"
            label="Menetapkan"
            :value="$draft['menetapkan'] ?? ''"
            rows="2"
            placeholder="Contoh: KEPUTUSAN BUPATI TENTANG..."
        />

        <x-sk-form.dynamic-list
            id="diktum"
            label="Amar Putusan (Diktum)"
            buttonText="Tambah Diktum"
            buttonAction="addDiktum()"
            containerClass="space-y-4"
        />
    </div>
</x-sk-form.section-card>
