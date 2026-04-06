<?php

namespace App\Http\Controllers;

use App\Services\SkDocumentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Process;

class SkPdfController extends Controller
{
    public function __invoke(Request $request, SkDocumentService $documentService): BinaryFileResponse
    {
        $data = session()->get('sk_data', []);
        $pdfPath = $this->buildPdfFromData($data, $documentService);
        $filename = 'surat_keputusan_' . now()->format('Ymd_His') . '.pdf';

        return response()->download($pdfPath, $filename)->deleteFileAfterSend(true);
    }

    public function preview(Request $request, SkDocumentService $documentService): BinaryFileResponse
    {
        $data = session()->get('sk_data', []);
        $pdfPath = $this->buildPdfFromData($data, $documentService);

        return response()->file($pdfPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
    }

    private function buildPdfFromData(array $data, SkDocumentService $documentService): string
    {
        $docxPath = $documentService->createDocxFromData($data);
        $tempPdfBasePath = tempnam(sys_get_temp_dir(), 'sk_pdf_');
        if ($tempPdfBasePath === false) {
            @unlink($docxPath);
            abort(500, 'Gagal menyiapkan file PDF sementara.');
        }

        @unlink($tempPdfBasePath);
        $finalPdfPath = $tempPdfBasePath . '.pdf';
        if ($finalPdfPath === false) {
            abort(500, 'Gagal menyiapkan file PDF sementara.');
        }

        $binary = $this->resolveSofficeBinary();
        if ($binary !== null) {
            $outDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sk_pdf_' . uniqid();
            if (!@mkdir($outDir) && !is_dir($outDir)) {
                @unlink($docxPath);
                abort(500, 'Failed to create temporary pdf directory.');
            }

            $process = new Process([
                $binary,
                '--headless',
                '--nologo',
                '--nofirststartwizard',
                '--convert-to',
                'pdf:writer_pdf_Export',
                '--outdir',
                $outDir,
                $docxPath,
            ]);
            $process->setTimeout(120);
            $process->run();
            @unlink($docxPath);

            if (!$process->isSuccessful()) {
                $this->cleanupDirectory($outDir);
                abort(
                    500,
                    'Konversi DOCX ke PDF gagal. Output: ' . trim($process->getErrorOutput() . ' ' . $process->getOutput())
                );
            }

            $pdfPath = $outDir . DIRECTORY_SEPARATOR . pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';
            if (!file_exists($pdfPath)) {
                $this->cleanupDirectory($outDir);
                abort(500, 'PDF tidak ditemukan setelah proses konversi.');
            }

            if (!@rename($pdfPath, $finalPdfPath)) {
                if (!@copy($pdfPath, $finalPdfPath)) {
                    @unlink($finalPdfPath);
                    $this->cleanupDirectory($outDir);
                    abort(500, 'Gagal menyimpan hasil konversi PDF.');
                }
                @unlink($pdfPath);
            }

            $this->cleanupDirectory($outDir);
        } else {
            $this->convertWithWordCom($docxPath, $finalPdfPath);
            @unlink($docxPath);
        }

        if (!file_exists($finalPdfPath)) {
            abort(500, 'Konversi PDF gagal. File hasil tidak ditemukan.');
        }

        return $finalPdfPath;
    }

    private function resolveSofficeBinary(): ?string
    {
        $configured = trim((string) env('SK_OFFICE_BINARY', ''));
        if ($configured !== '') {
            return $configured;
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            $candidates = [
                'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
                'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            ];

            foreach ($candidates as $candidate) {
                if (file_exists($candidate)) {
                    return $candidate;
                }
            }

            return null;
        }

        return 'soffice';
    }

    private function convertWithWordCom(string $docxPath, string $pdfPath): void
    {
        if (DIRECTORY_SEPARATOR !== '\\') {
            abort(500, 'Konversi PDF membutuhkan LibreOffice/soffice pada server Linux/macOS.');
        }

        $docxEscaped = str_replace("'", "''", $docxPath);
        $pdfEscaped = str_replace("'", "''", $pdfPath);

        $script = <<<'PS'
$ErrorActionPreference = 'Stop'
$docxPath = '__DOCX__'
$pdfPath = '__PDF__'
$word = $null
$doc = $null
try {
    $word = New-Object -ComObject Word.Application
    $word.Visible = $false
    $word.DisplayAlerts = 0
    $doc = $word.Documents.Open($docxPath, $false, $true)
    $doc.SaveAs([ref]$pdfPath, [ref]17)
    $doc.Close()
    $word.Quit()
} catch {
    if ($doc -ne $null) { $doc.Close() }
    if ($word -ne $null) { $word.Quit() }
    throw
}
PS;

        $script = str_replace(['__DOCX__', '__PDF__'], [$docxEscaped, $pdfEscaped], $script);

        $process = new Process([
            'powershell',
            '-NoProfile',
            '-NonInteractive',
            '-ExecutionPolicy',
            'Bypass',
            '-Command',
            $script,
        ]);
        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($pdfPath)) {
            abort(
                500,
                'Konversi DOCX ke PDF gagal (Word COM). Output: ' . trim($process->getErrorOutput() . ' ' . $process->getOutput())
            );
        }
    }

    private function cleanupDirectory(string $directory): void
    {
        $files = @scandir($directory);
        if (!is_array($files)) {
            return;
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            @unlink($directory . DIRECTORY_SEPARATOR . $file);
        }

        @rmdir($directory);
    }
}
