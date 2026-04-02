<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class SkDocxController extends Controller
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

    public function __invoke(Request $request): BinaryFileResponse
    {
        $data = session()->get('sk_data', []);

        $templatePath = resource_path('templates/sk_template.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Template not found');
        }

        $template = new TemplateProcessor($templatePath);

        $template->setValue('org1', 'BUPATI BUOL');
        $template->setValue('org2', 'PROVINSI SULAWESI TENGAH');
        $template->setValue('meta', '');
        $template->setValue('sk_number', $data['nomor_surat'] ?? '');
        $template->setValue('title', $data['sk_title'] ?? '');
        $template->setValue('menimbang', $this->buildLetterList($data['menimbang'] ?? []));
        $template->setValue('mengingat', $this->buildNumberList($data['mengingat'] ?? []));
        $template->setValue('memperhatikan', $this->buildNumberList($data['memperhatikan'] ?? []));
        $template->setValue('menetapkan', $data['menetapkan'] ?? '');
        $template->setValue('diktum', $this->buildDiktumList($data['diktum'] ?? []));
        $template->setValue('ditetapkan_di', $data['ditetapkan_di'] ?? '');
        $template->setValue('pada_tanggal', '');
        $template->setValue('jabatan_penandatangan', $data['jabatan_penandatangan'] ?? '');
        $template->setValue('nama_penandatangan', $data['nama_penandatangan'] ?? '');

        $tempFile = tempnam(sys_get_temp_dir(), 'sk_');
        $template->saveAs($tempFile);

        $baseFilename = 'surat_keputusan_' . now()->format('Ymd_His');
        $lampiranFiles = $this->resolveLampiranFiles($data['lampiran'] ?? []);

        if (empty($lampiranFiles)) {
            return response()->download($tempFile, $baseFilename . '.docx')->deleteFileAfterSend(true);
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'sk_paket_');
        if ($zipPath === false) {
            return response()->download($tempFile, $baseFilename . '.docx')->deleteFileAfterSend(true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::OVERWRITE) !== true) {
            @unlink($zipPath);
            return response()->download($tempFile, $baseFilename . '.docx')->deleteFileAfterSend(true);
        }

        $zip->addFile($tempFile, $baseFilename . '.docx');
        $usedNames = [];

        foreach ($lampiranFiles as $index => $lampiran) {
            $safeName = $this->uniqueLampiranName($lampiran['name'], $usedNames, $index + 1);
            $zip->addFile($lampiran['full_path'], 'lampiran/' . $safeName);
        }

        $zip->close();
        @unlink($tempFile);

        return response()
            ->download($zipPath, 'paket_sk_dan_lampiran_' . now()->format('Ymd_His') . '.zip')
            ->deleteFileAfterSend(true);
    }

    private function buildLetterList(array $items): string
    {
        $items = $this->filterArray($items);

        $lines = [];

        foreach ($items as $index => $text) {
            $label = chr(97 + $index) . '.';
            $lines[] = $label . "\t" . $text . ';';
        }

        return implode("\n", $lines);
    }

    private function buildNumberList(array $items): string
    {
        $items = $this->filterArray($items);

        $lines = [];

        foreach ($items as $index => $text) {
            $label = ($index + 1) . '.';
            $lines[] = $label . "\t" . $text . ';';
        }

        return implode("\n", $lines);
    }

    private function buildDiktumList(array $items): string
    {
        $items = $this->filterArray($items);

        $lines = [];

        foreach ($items as $index => $text) {
            $label = self::DIKTUM_LABELS[$index] ?? 'DIKTUM ' . ($index + 1);
            $lines[] = $label . "\t" . $text . ';';
        }

        return implode("\n", $lines);
    }

    private function resolveLampiranFiles(mixed $lampiran): array
    {
        if (!is_array($lampiran)) {
            return [];
        }

        $files = [];

        foreach ($lampiran as $item) {
            if (!is_array($item)) {
                continue;
            }

            $name = trim((string) ($item['name'] ?? ''));
            $path = trim((string) ($item['path'] ?? ''));
            if ($name === '' || $path === '') {
                continue;
            }

            $fullPath = storage_path('app/' . $path);
            if (!file_exists($fullPath)) {
                continue;
            }

            $files[] = [
                'name' => $name,
                'full_path' => $fullPath,
            ];
        }

        return $files;
    }

    private function filterArray(array $items): array
    {
        return array_values(array_filter($items, static function ($value) {
            return is_string($value) && trim($value) !== '';
        }));
    }

    private function uniqueLampiranName(string $originalName, array &$usedNames, int $sequence): string
    {
        $name = str_replace(['\\', '/'], '_', trim($originalName));
        if ($name === '') {
            $name = 'lampiran_' . $sequence . '.docx';
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        if ($extension === '') {
            $name .= '.docx';
            $extension = 'docx';
        }

        $base = pathinfo($name, PATHINFO_FILENAME);
        $candidate = $base . '.' . $extension;
        $counter = 2;

        while (in_array(strtolower($candidate), $usedNames, true)) {
            $candidate = $base . ' (' . $counter . ').' . $extension;
            $counter++;
        }

        $usedNames[] = strtolower($candidate);

        return $candidate;
    }
}
