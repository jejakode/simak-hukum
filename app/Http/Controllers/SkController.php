<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

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
        $this->deleteLampiranFiles(session()->get('sk_data.lampiran', []));
        $this->deletePreviewCacheFile(session()->get('sk_preview_pdf'));
        session()->forget('sk_data');
        session()->forget('sk_preview_pdf');

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
        $data['lampiran'] = $this->storeLampiran($request, session()->get('sk_data.lampiran', []));

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
            'jabatan_penandatangan' => self::FIXED_JABATAN_PENANDATANGAN,
            'nama_penandatangan' => self::FIXED_NAMA_PENANDATANGAN,
            'lampiran' => [],
        ], $data);
    }

    private function validationRules(): array
    {
        return [
            'nomor_surat' => ['nullable', 'string', 'max:255'],
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
            'lampiran_docx' => ['nullable', 'array'],
            'lampiran_docx.*' => ['nullable', 'file', 'mimes:docx,pdf,jpg,jpeg,png', 'max:10240'],
            'remove_lampiran' => ['nullable', 'array'],
            'remove_lampiran.*' => ['nullable', 'string'],
            'ditetapkan_di' => ['required', 'string', 'max:255'],
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
            'mimes' => ':attribute harus berformat DOCX, PDF, JPG, JPEG, atau PNG.',
            'file' => ':attribute harus berupa file.',
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
            'lampiran_docx' => 'Lampiran',
            'lampiran_docx.*' => 'Lampiran',
            'remove_lampiran' => 'Hapus Lampiran',
            'remove_lampiran.*' => 'Hapus Lampiran',
            'ditetapkan_di' => 'Ditetapkan di',
            'jabatan_penandatangan' => 'Jabatan Penandatangan',
            'nama_penandatangan' => 'Nama Penandatangan',
        ];
    }

    private function storeLampiran(Request $request, mixed $existing): array
    {
        $attachments = $this->sanitizeLampiran($existing);
        $removePaths = $this->filterRemoveLampiranPaths($request->input('remove_lampiran', []));
        if (!empty($removePaths)) {
            $removeLookup = array_flip($removePaths);
            $remaining = [];

            foreach ($attachments as $attachment) {
                $path = (string) ($attachment['path'] ?? '');
                if ($path !== '' && isset($removeLookup[$path])) {
                    $fullPath = storage_path('app/' . $path);
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                    continue;
                }

                $remaining[] = $attachment;
            }

            $attachments = $remaining;
        }

        $uploadedFiles = $request->file('lampiran_docx', []);

        foreach ($uploadedFiles as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $originalName = trim((string) $file->getClientOriginalName());
            if ($originalName === '') {
                $originalName = 'lampiran';
            }

            $extension = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension()));
            if ($extension === '') {
                $extension = 'bin';
            }

            $storageName = Str::uuid()->toString() . '.' . $extension;
            $storedPath = $file->storeAs('sk-lampiran', $storageName);

            if (!$storedPath) {
                continue;
            }

            $attachments[] = [
                'name' => $originalName,
                'path' => $storedPath,
            ];
        }

        return $attachments;
    }

    private function filterRemoveLampiranPaths(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $paths = [];
        foreach ($value as $path) {
            if (!is_string($path)) {
                continue;
            }

            $normalized = trim($path);
            if ($normalized === '') {
                continue;
            }

            $paths[] = $normalized;
        }

        return array_values(array_unique($paths));
    }

    private function sanitizeLampiran(mixed $attachments): array
    {
        if (!is_array($attachments)) {
            return [];
        }

        $result = [];

        foreach ($attachments as $attachment) {
            if (!is_array($attachment)) {
                continue;
            }

            $name = trim((string) ($attachment['name'] ?? ''));
            $path = trim((string) ($attachment['path'] ?? ''));

            if ($name === '' || $path === '') {
                continue;
            }

            if (!file_exists(storage_path('app/' . $path))) {
                continue;
            }

            $result[] = [
                'name' => $name,
                'path' => $path,
            ];
        }

        return $result;
    }

    private function deleteLampiranFiles(mixed $attachments): void
    {
        $attachments = $this->sanitizeLampiran($attachments);

        foreach ($attachments as $attachment) {
            $path = storage_path('app/' . $attachment['path']);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    private function deletePreviewCacheFile(mixed $preview): void
    {
        if (!is_array($preview)) {
            return;
        }

        $path = trim((string) ($preview['path'] ?? ''));
        if ($path === '' || !file_exists($path)) {
            return;
        }

        $realPath = realpath($path);
        $realCacheDirectory = realpath(storage_path('app/sk-preview-cache'));
        if ($realPath === false || $realCacheDirectory === false) {
            return;
        }

        if (str_starts_with($realPath, $realCacheDirectory . DIRECTORY_SEPARATOR)) {
            @unlink($realPath);
        }
    }

}
