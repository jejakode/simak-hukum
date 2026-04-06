<?php

namespace App\Http\Controllers;

use App\Services\SkExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SkPdfController extends Controller
{
    public function __invoke(Request $request, SkExportService $exportService): BinaryFileResponse
    {
        $data = session()->get('sk_data', []);
        $pdfPath = $exportService->createFinalPdfFromData($data);
        $filename = 'surat_keputusan_' . now()->format('Ymd_His') . '.pdf';

        return response()->download($pdfPath, $filename)->deleteFileAfterSend(true);
    }

    public function preview(Request $request, SkExportService $exportService): BinaryFileResponse
    {
        $data = session()->get('sk_data', []);
        $pdfPath = $exportService->createFinalPdfFromData($data);

        return response()->file($pdfPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
    }
}
