<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;

class SkDocumentService
{
    private const DIKTUM_LABELS = [
        'KESATU',
        'KEDUA',
        'KETIGA',
        'KEEMPAT',
        'KELIMA',
        'KEENAM',
        'KETUJUH',
        'KEDELAPAN',
        'KESEMBILAN',
        'KESEPULUH',
    ];

    public function createDocxFromData(array $data, ?string $targetPath = null): string
    {
        $templatePath = resource_path('templates/sk.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Template not found');
        }

        $template = new TemplateProcessor($templatePath);

        $template->setValue('org1', 'BUPATI BUOL');
        $template->setValue('org2', 'PROVINSI SULAWESI TENGAH');
        $template->setValue('meta', '');
        $template->setValue('sk_number', $data['nomor_surat'] ?? '');
        $template->setValue('title', $data['sk_title'] ?? '');
        $this->cloneLetterListRows(
            $template,
            'menimbang_head',
            'menimbang_sep',
            'menimbang_label',
            'menimbang_text',
            'Menimbang',
            $data['menimbang'] ?? []
        );
        $this->cloneNumberListRows(
            $template,
            'mengingat_head',
            'mengingat_sep',
            'mengingat_label',
            'mengingat_text',
            'Mengingat',
            $data['mengingat'] ?? []
        );
        $template->setValue('menetapkan', $data['menetapkan'] ?? '');
        $this->cloneDiktumRows($template, 'diktum_label', 'diktum_text', $data['diktum'] ?? []);
        $template->setValue('ditetapkan_di', $data['ditetapkan_di'] ?? '');
        $template->setValue('pada_tanggal', '');
        $template->setValue('jabatan_penandatangan', $data['jabatan_penandatangan'] ?? '');
        $template->setValue('nama_penandatangan', $data['nama_penandatangan'] ?? '');

        $outputPath = $targetPath ?? $this->makeTempDocxPath();

        $template->saveAs($outputPath);

        return $outputPath;
    }

    private function makeTempDocxPath(): string
    {
        $base = tempnam(sys_get_temp_dir(), 'sk_docx_');
        if ($base === false) {
            abort(500, 'Failed to create temporary docx file.');
        }

        @unlink($base);

        return $base . '.docx';
    }

    private function cloneLetterListRows(
        TemplateProcessor $template,
        string $headMacro,
        string $sepMacro,
        string $labelMacro,
        string $textMacro,
        string $headText,
        array $items
    ): void
    {
        $items = $this->filterArray($items);

        if (empty($items)) {
            $template->setValue($headMacro, '');
            $template->setValue($sepMacro, '');
            $template->setValue($labelMacro, '');
            $template->setValue($textMacro, '');
            return;
        }

        foreach ($items as $index => $text) {
            $items[$index] = [
                $headMacro => $index === 0 ? $headText : '',
                $sepMacro => $index === 0 ? ':' : '',
                $labelMacro => chr(97 + $index) . '.',
                $textMacro => $text . ';',
            ];
        }

        $template->cloneRowAndSetValues($labelMacro, $items);
    }

    private function cloneNumberListRows(
        TemplateProcessor $template,
        string $headMacro,
        string $sepMacro,
        string $labelMacro,
        string $textMacro,
        string $headText,
        array $items
    ): void
    {
        $items = $this->filterArray($items);

        if (empty($items)) {
            $template->setValue($headMacro, '');
            $template->setValue($sepMacro, '');
            $template->setValue($labelMacro, '');
            $template->setValue($textMacro, '');
            return;
        }

        foreach ($items as $index => $text) {
            $items[$index] = [
                $headMacro => $index === 0 ? $headText : '',
                $sepMacro => $index === 0 ? ':' : '',
                $labelMacro => ($index + 1) . '.',
                $textMacro => $text . ';',
            ];
        }

        $template->cloneRowAndSetValues($labelMacro, $items);
    }

    private function cloneDiktumRows(TemplateProcessor $template, string $labelMacro, string $textMacro, array $items): void
    {
        $items = $this->filterArray($items);

        if (empty($items)) {
            $template->setValue($labelMacro, '');
            $template->setValue($textMacro, '');
            return;
        }

        foreach ($items as $index => $text) {
            $items[$index] = [
                $labelMacro => self::DIKTUM_LABELS[$index] ?? 'DIKTUM ' . ($index + 1),
                $textMacro => $text . ';',
            ];
        }

        $template->cloneRowAndSetValues($labelMacro, $items);
    }

    private function filterArray(array $items): array
    {
        return array_values(array_filter($items, static function ($value) {
            return is_string($value) && trim($value) !== '';
        }));
    }
}
