<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SkPdfController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $data = session()->get('sk_data', []);

        return $this->downloadPdf($data);
    }

    private function downloadPdf(array $data): Response
    {
        $pdf = Pdf::loadView('pages.sk-preview-pdf', $data);

        $filename = 'surat_keputusan_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
