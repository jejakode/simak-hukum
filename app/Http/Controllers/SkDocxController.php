<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $template->setValue('pada_tanggal', $this->formatDate($data['pada_tanggal'] ?? ''));
        $template->setValue('jabatan_penandatangan', $data['jabatan_penandatangan'] ?? '');
        $template->setValue('nama_penandatangan', $data['nama_penandatangan'] ?? '');

        $tempFile = tempnam(sys_get_temp_dir(), 'sk_');
        $template->saveAs($tempFile);

        $filename = 'surat_keputusan_' . now()->format('Ymd_His') . '.docx';

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
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

    private function formatDate(string $date): string
    {
        if (trim($date) === '') {
            return '';
        }

        try {
            return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->isoFormat('D MMMM Y');
        } catch (\Throwable $exception) {
            return $date;
        }
    }

    private function filterArray(array $items): array
    {
        return array_values(array_filter($items, static function ($value) {
            return is_string($value) && trim($value) !== '';
        }));
    }
}
