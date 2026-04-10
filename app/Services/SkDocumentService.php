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
        $this->cloneNumberListRows(
            $template,
            'mmprhtkn_head',
            'mmprhtkn_sep',
            'mmprhtkn_label',
            'mmprhtkn_text',
            'Memperhatikan',
            $data['memperhatikan'] ?? []
        );
        $template->setValue('menetapkan', $this->withFinalPeriod((string) ($data['menetapkan'] ?? '')));
        $this->cloneDiktumRows($template, 'diktum_label', 'diktum_text', $data['diktum'] ?? []);
        $template->setValue('ditetapkan_di', $data['ditetapkan_di'] ?? '');
        $template->setValue('pada_tanggal', '');
        $template->setValue('jabatan_penandatangan', $data['jabatan_penandatangan'] ?? '');
        $template->setValue('nama_penandatangan', $data['nama_penandatangan'] ?? '');

        $outputPath = $targetPath ?? $this->makeTempDocxPath();

        $template->saveAs($outputPath);
        $this->normalizeKonsideransLayout($outputPath);

        return $outputPath;
    }

    private function makeTempDocxPath(): string
    {
        $tempDirectory = storage_path('app/sk-temp');
        if (!is_dir($tempDirectory) && !@mkdir($tempDirectory, 0775, true) && !is_dir($tempDirectory)) {
            abort(500, 'Failed to prepare temporary docx directory.');
        }

        $base = tempnam($tempDirectory, 'sk_docx_');
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
            $normalizedText = $this->normalizeListItemText($text);
            $items[$index] = [
                $headMacro => $index === 0 ? $headText : '',
                $sepMacro => $index === 0 ? ':' : '',
                $labelMacro => chr(97 + $index) . '.',
                $textMacro => $normalizedText . ';',
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
            $normalizedText = $this->normalizeListItemText($text);
            $items[$index] = [
                $headMacro => $index === 0 ? $headText : '',
                $sepMacro => $index === 0 ? ':' : '',
                $labelMacro => ($index + 1) . '.',
                $textMacro => $normalizedText . ';',
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
                $textMacro => $this->withFinalPeriod($text),
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

    private function withFinalPeriod(string $text): string
    {
        $normalized = rtrim(trim($text), " \t\n\r\0\x0B;:.!");
        if ($normalized === '') {
            return '';
        }

        return $normalized . '.';
    }

    private function normalizeListItemText(string $text): string
    {
        $text = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim((string) $text);
    }

    private function normalizeKonsideransLayout(string $docxPath): void
    {
        if (!is_file($docxPath) || !class_exists(\ZipArchive::class)) {
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        try {
            $documentXml = $zip->getFromName('word/document.xml');
            if (!is_string($documentXml) || $documentXml === '') {
                return;
            }

            $updatedXml = preg_replace_callback(
                '/<w:tbl\b[^>]*>.*?<\/w:tbl>/s',
                static function (array $matches): string {
                    $tableXml = $matches[0];
                    $isKonsideransTable = str_contains($tableXml, '<w:t>Menimbang</w:t>')
                        || str_contains($tableXml, '<w:t>Mengingat</w:t>')
                        || str_contains($tableXml, '<w:t>Memperhatikan</w:t>');

                    if (!$isKonsideransTable) {
                        return $tableXml;
                    }

                    $gridXml = '<w:tblGrid>'
                        . '<w:gridCol w:w="1699"/>'
                        . '<w:gridCol w:w="426"/>'
                        . '<w:gridCol w:w="442"/>'
                        . '<w:gridCol w:w="6523"/>'
                        . '</w:tblGrid>';

                    $tableXml = (string) preg_replace('/<w:tblGrid>.*?<\/w:tblGrid>/s', $gridXml, $tableXml, 1);

                    $widths = ['1699', '426', '442', '6523'];
                    $tableXml = (string) preg_replace_callback(
                        '/<w:tr\b[^>]*>.*?<\/w:tr>/s',
                        static function (array $rowMatches) use ($widths): string {
                            $rowXml = $rowMatches[0];
                            $cellIndex = 0;

                            $rowXml = (string) preg_replace_callback(
                                '/<w:tcW\b[^>]*\/>/',
                                static function () use (&$cellIndex, $widths): string {
                                    $width = $widths[min($cellIndex, 3)];
                                    $cellIndex++;
                                    return '<w:tcW w:w="' . $width . '" w:type="dxa"/>';
                                },
                                $rowXml
                            );

                            return $rowXml;
                        },
                        $tableXml
                    );

                    return $tableXml;
                },
                $documentXml
            );

            if (!is_string($updatedXml) || $updatedXml === $documentXml) {
                return;
            }

            $zip->addFromString('word/document.xml', $updatedXml);
        } finally {
            $zip->close();
        }
    }

}
