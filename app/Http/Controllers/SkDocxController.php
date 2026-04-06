<?php

namespace App\Http\Controllers;

use App\Services\SkExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SkDocxController extends Controller
{
    public function __invoke(Request $request, SkExportService $exportService): BinaryFileResponse
    {
        $data = session()->get('sk_data', []);
        $tempFile = $exportService->createFinalDocxFromData($data);
        $baseFilename = 'surat_keputusan_' . now()->format('Ymd_His');
        return response()->download($tempFile, $baseFilename . '.docx')->deleteFileAfterSend(true);
    }
}
