<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SkController extends Controller
{
    private const FIXED_DITETAPKAN_DI = 'BUOL';
    private const FIXED_JABATAN_PENANDATANGAN = 'BUPATI BUOL';
    private const FIXED_NAMA_PENANDATANGAN = 'RISHARYUDI TRIWIBOWO';

    const DIKTUM_LABELS = [
        'KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA',
        'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH',
    ];

    public function create(Request $request): Response|RedirectResponse
    {
        $hasSessionDraft = !empty(session()->get('sk_data', []));
        $isEditMode = $request->boolean('edit');
        $isFreshMode = $request->boolean('fresh');
        $hasOldInput = $request->session()->hasOldInput();

        if ($hasSessionDraft && !$isEditMode && !$isFreshMode && !$hasOldInput) {
            return redirect()->route('sk.preview');
        }

        $draft = $isFreshMode
            ? $this->defaultData()
            : $this->defaultData(session()->get('sk_data', []));

        return response()->view('pages.sk-form', [
            'draft' => $draft,
            'fresh' => $isFreshMode,
            'hasServerDraft' => $hasSessionDraft,
        ]);
    }

    public function newDraft(): RedirectResponse
    {
        session()->forget('sk_data');

        return redirect()->route('sk.create', ['fresh' => 1]);
    }

    public function handle(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $action = $request->input('action', 'preview');
        $data = $this->normalizePayload($validated);

        $data['requested_output'] = $action;

        session()->put('sk_data', $data);

        return redirect()->route('sk.preview');
    }

    public function preview(Request $request): Response
    {
        $data = session()->get('sk_data', []);
        $data = $this->defaultData($data);

        return response()->view('pages.sk-preview', $data);
    }

    public function pdf(Request $request): Response
    {
        $data = Session::get('sk_data');

        return $this->downloadPdf($data);
    }

    public function docx(Request $request): BinaryFileResponse
    {
        $data = Session::get('sk_data');

        return $this->downloadDocx($data);
    }

    private function normalizePayload(array $validated): array
    {
        return [
            'nomor_surat' => trim((string) ($validated['nomor_surat'] ?? '')),
            'sk_title' => trim((string) ($validated['sk_title'] ?? '')),
            'menimbang' => $this->filterArray($validated['menimbang'] ?? []),
            'mengingat' => $this->filterArray($validated['mengingat'] ?? []),
            'memperhatikan' => $this->filterArray($validated['memperhatikan'] ?? []),
            'menetapkan' => trim((string) ($validated['menetapkan'] ?? '')),
            'diktum' => $this->filterArray($validated['diktum'] ?? []),
            'ditetapkan_di' => self::FIXED_DITETAPKAN_DI,
            'pada_tanggal' => trim((string) ($validated['pada_tanggal'] ?? '')),
            'jabatan_penandatangan' => self::FIXED_JABATAN_PENANDATANGAN,
            'nama_penandatangan' => self::FIXED_NAMA_PENANDATANGAN,
        ];
    }

    private function filterArray(array $items): array
    {
        return array_values(array_filter($items, static function ($value) {
            if ($value === null) {
                return false;
            }

            if (is_string($value)) {
                return trim($value) !== '';
            }

            return (bool) $value;
        }));
    }

    private function defaultData(array $data = []): array
    {
        return array_merge([
            'menimbang' => [],
            'mengingat' => [],
            'memperhatikan' => [],
            'diktum' => [],
            'sk_title' => '',
            'nomor_surat' => '',
            'menetapkan' => '',
            'ditetapkan_di' => self::FIXED_DITETAPKAN_DI,
            'pada_tanggal' => '',
            'jabatan_penandatangan' => self::FIXED_JABATAN_PENANDATANGAN,
            'nama_penandatangan' => self::FIXED_NAMA_PENANDATANGAN,
        ], $data);
    }

    private function validationRules(): array
    {
        return [
            'nomor_surat' => ['required', 'string', 'max:255'],
            'sk_title' => ['required', 'string', 'max:2000'],
            'menimbang' => ['required', 'array', 'min:1'],
            'menimbang.*' => ['required', 'string', 'max:4000'],
            'mengingat' => ['required', 'array', 'min:1'],
            'mengingat.*' => ['required', 'string', 'max:4000'],
            'memperhatikan' => ['nullable', 'array'],
            'memperhatikan.*' => ['nullable', 'string', 'max:4000'],
            'menetapkan' => ['required', 'string', 'max:4000'],
            'diktum' => ['required', 'array', 'min:1'],
            'diktum.*' => ['required', 'string', 'max:4000'],
            'ditetapkan_di' => ['required', 'string', 'max:255'],
            'pada_tanggal' => ['required', 'date_format:Y-m-d'],
            'jabatan_penandatangan' => ['required', 'string', 'max:255'],
            'nama_penandatangan' => ['required', 'string', 'max:255'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'array' => ':attribute harus berupa daftar.',
            'min' => ':attribute minimal :min poin.',
            'max' => ':attribute melebihi batas karakter.',
            'date_format' => ':attribute harus menggunakan format tanggal yang valid.',
        ];
    }

    private function validationAttributes(): array
    {
        return [
            'nomor_surat' => 'Nomor Surat',
            'sk_title' => 'Judul Lengkap',
            'menimbang' => 'Menimbang',
            'menimbang.*' => 'Poin Menimbang',
            'mengingat' => 'Mengingat',
            'mengingat.*' => 'Poin Mengingat',
            'memperhatikan' => 'Memperhatikan',
            'memperhatikan.*' => 'Poin Memperhatikan',
            'menetapkan' => 'Menetapkan',
            'diktum' => 'Diktum',
            'diktum.*' => 'Poin Diktum',
            'ditetapkan_di' => 'Ditetapkan di',
            'pada_tanggal' => 'Pada Tanggal',
            'jabatan_penandatangan' => 'Jabatan Penandatangan',
            'nama_penandatangan' => 'Nama Penandatangan',
        ];
    }

    private function downloadPdf(array $data): Response
    {
        $pdf = Pdf::loadView('pages.sk-preview-pdf', $data);

        $filename = 'surat_keputusan_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
