<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
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
        $data = session()->get('sk_data', $request->all());
        
        if (empty($data['menimbang']) && empty($data['mengingat'])) {
            $data['menimbang'] = ['bahwa dalam rangka meningkatkan kesehatan masyarakat perlu dibentuk tim pelaksana kegiatan vaksinasi'];
            $data['mengingat'] = ['Undang-Undang Nomor 36 Tahun 2009 tentang Kesehatan'];
        }

        try {
            $templatePath = resource_path('templates/sk_template.docx');

            if (!file_exists($templatePath)) {
                return response()->json(['error' => 'Template not found'], 404);
            }
            
            $template = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

            $template->setValue('${org1}', 'BUPATI BUOL');
            $template->setValue('${org2}', 'PROVINSI SULAWESI TENGAH');
            $template->setValue('${meta}', '');
            $template->setValue('${sk_number}', $data['nomor_surat'] ?? '');
            $template->setValue('${title}', $data['sk_title'] ?? '');
            $template->setValue('${menimbang}', $this->buildLetterList($data['menimbang'] ?? []));
            $template->setValue('${mengingat}', $this->buildNumberList($data['mengingat'] ?? []));
            $template->setValue('${memperhatikan}', $this->buildNumberList($data['memperhatikan'] ?? []));
            $template->setValue('${menetapkan}', $data['menetapkan'] ?? '');
            $template->setValue('${diktum}', $this->buildDiktumList($data['diktum'] ?? []));
            $template->setValue('${ditetapkan_di}', $data['ditetapkan_di'] ?? '');
            $template->setValue('${pada_tanggal}', $data['pada_tanggal'] ? \Carbon\Carbon::createFromFormat('Y-m-d', $data['pada_tanggal'])->isoFormat('D MMMM Y') : '');
            $template->setValue('${jabatan_penandatangan}', $data['jabatan_penandatangan'] ?? '');
            $template->setValue('${nama_penandatangan}', $data['nama_penandatangan'] ?? '');

            $tempFile = tempnam(sys_get_temp_dir(), 'sk_');
            $template->saveAs($tempFile);

            $filename = 'surat_keputusan_' . now()->format('Ymd_His') . '.docx';

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error generating DOCX: ' . $e->getMessage()], 500);
        }
    }

    private function buildLetterList(array $items): string
    {
        $items = $this->filterArray($items);

        $lines = [];

        foreach ($items as $index => $text) {
            $label = chr(97 + $index) . '.';
            // Format dengan tab indentasi untuk alignment yang benar
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
            // Format dengan tab indentasi untuk alignment yang benar
            $lines[] = $label . "\t" . $text . ';';
        }

        return implode("\n", $lines);
    }

    private function buildDiktumList(array $items): string
    {
        $items = $this->filterArray($items);

        $lines = [];

        foreach ($items as $index => $text) {
            $label = self::DIKTUM_LABELS[$index] ?? 'SELANJUTNYA';
            // Format dengan tab indentasi untuk alignment yang benar
            $lines[] = $label . "\t" . $text . ';';
        }

        return implode("\n", $lines);
    }

    private function filterArray(array $items): array
    {
        return array_values(array_filter($items, static function ($value) {
            if ($value === null) {
                return false;
            }

            if (is_string($value)) {
                return !empty(trim($value));
            }

            return true;
        }));
    }
}
