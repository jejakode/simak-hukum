<?php

namespace App\Http\Controllers;

use App\Services\SkDocumentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class SkDocxController extends Controller
{
    public function __invoke(Request $request, SkDocumentService $documentService): BinaryFileResponse
    {
        $data = session()->get('sk_data', []);
        $tempFile = $documentService->createDocxFromData($data);

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
