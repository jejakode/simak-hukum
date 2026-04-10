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
        $preview = $exportService->getOrCreatePreviewPdf($data, session()->get('sk_preview_pdf'));
        session()->put('sk_preview_pdf', $preview);
        $filename = 'surat_keputusan_' . now()->format('Ymd_His') . '.pdf';

        return response()->download((string) $preview['path'], $filename);
    }

    public function preview(Request $request, SkExportService $exportService): BinaryFileResponse
    {
        $data = session()->get('sk_data', []);
        $forceRebuild = (bool) session()->pull('sk_preview_force_refresh', false) || $request->boolean('refresh');
        $preview = $exportService->getOrCreatePreviewPdf(
            $data,
            session()->get('sk_preview_pdf'),
            $forceRebuild
        );
        session()->put('sk_preview_pdf', $preview);

        return response()->file((string) $preview['path'], [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
