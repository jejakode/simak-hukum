<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SkPdfController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $data = $this->normalizePayload($request);

        return $this->downloadPdf($data);
    }

    private function normalizePayload(Request $request): array
    {
        return [
            'sk_title' => $request->get('sk_title', ''),
            'nomor_surat' => $request->get('nomor_surat', ''),
            'menimbang' => array_filter($request->get('menimbang', [])),
            'mengingat' => array_filter($request->get('mengingat', [])),
            'memperhatikan' => array_filter($request->get('memperhatikan', [])),
            'menetapkan' => $request->get('menetapkan', ''),
            'diktum' => array_filter($request->get('diktum', [])),
            'ditetapkan_di' => $request->get('ditetapkan_di', ''),
            'pada_tanggal' => $request->get('pada_tanggal', ''),
            'jabatan_penandatangan' => $request->get('jabatan_penandatangan', ''),
            'nama_penandatangan' => $request->get('nama_penandatangan', ''),
        ];
    }

    private function downloadPdf(array $data): Response
    {
        $pdf = Pdf::loadView('pages.sk-preview-pdf', $data);

        $filename = 'surat_keputusan_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
