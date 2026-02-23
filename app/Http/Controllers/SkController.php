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
    const DIKTUM_LABELS = [
        'KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA',
        'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH',
    ];

    public function create(): Response
    {
        return response()->view('pages.sk-form');
    }

    public function handle(Request $request): RedirectResponse
    {
        $action = $request->input('action', 'preview');
        $data = $this->normalizePayload($request);

        $data['requested_output'] = $action;

        // Simpan data ke session untuk digunakan di PDF dan DOCX
        session()->put('sk_data', $data);

        // Debug: Pastikan data tersimpan
        if (empty($data['menimbang']) && empty($data['mengingat'])) {
            // Jika data kosong, tambah testing data
            $data['menimbang'] = ['bahwa dalam rangka meningkatkan kesehatan masyarakat perlu dibentuk tim pelaksana kegiatan vaksinasi'];
            $data['mengingat'] = ['Undang-Undang Nomor 36 Tahun 2009 tentang Kesehatan'];
             session()->put('sk_data', $data);
        }

        // Redirect tanpa data di URL, data akan diambil dari session
        return redirect()->route('sk.preview');
    }

    public function preview(Request $request): Response
    {
        // Ambil data dari session
        $data = session()->get('sk_data', []);
        
        // Jika session kosong, coba dari request
        if (empty($data)) {
            $data = $request->all();
        }
        
        // Pastikan semua key ada dengan default array kosong
        $data = array_merge([
            'menimbang' => [],
            'mengingat' => [],
            'memperhatikan' => [],
            'diktum' => [],
            'sk_title' => '',
            'nomor_surat' => '',
            'menetapkan' => '',
            'ditetapkan_di' => '',
            'pada_tanggal' => '',
            'jabatan_penandatangan' => '',
            'nama_penandatangan' => '',
            'nip_penandatangan' => ''
        ], $data);

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

    private function normalizePayload(Request $request): array
    {
        return [
            'nomor_surat' => $request->string('nomor_surat')->toString(),
            'sk_title' => $request->string('sk_title')->toString(),
            'menimbang' => $this->filterArray($request->input('menimbang', [])),
            'mengingat' => $this->filterArray($request->input('mengingat', [])),
            'memperhatikan' => $this->filterArray($request->input('memperhatikan', [])),
            'menetapkan' => $request->string('menetapkan')->toString(),
            'diktum' => $this->filterArray($request->input('diktum', [])),
            'ditetapkan_di' => $request->string('ditetapkan_di')->toString(),
            'pada_tanggal' => $request->string('pada_tanggal')->toString(),
            'jabatan_penandatangan' => $request->string('jabatan_penandatangan')->toString(),
            'nama_penandatangan' => $request->string('nama_penandatangan')->toString(),
            'nip_penandatangan' => $request->string('nip_penandatangan')->toString(),
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

    private function downloadPdf(array $data): Response
    {
        $pdf = Pdf::loadView('pages.sk-preview-pdf', $data);

        $filename = 'surat_keputusan_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
