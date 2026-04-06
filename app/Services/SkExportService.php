<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class SkExportService
{
    public function __construct(private readonly SkDocumentService $documentService)
    {
    }

    public function createFinalDocxFromData(array $data): string
    {
        $mainDocxPath = $this->documentService->createDocxFromData($data);
        $lampiranFiles = $this->resolveLampiranFiles($data['lampiran'] ?? []);

        if (empty($lampiranFiles)) {
            return $mainDocxPath;
        }

        if (DIRECTORY_SEPARATOR !== '\\') {
            Log::warning('Lampiran DOCX inline dilewati di Linux karena membutuhkan Word COM.', [
                'lampiran_count' => count($lampiranFiles),
            ]);
            return $mainDocxPath;
        }

        $mergedDocxPath = $this->appendLampiranWithWordCom($mainDocxPath, $lampiranFiles);
        @unlink($mainDocxPath);

        return $mergedDocxPath;
    }

    public function createFinalPdfFromData(array $data): string
    {
        $docxPath = $this->createFinalDocxFromData($data);

        try {
            return $this->convertDocxToPdf($docxPath);
        } finally {
            @unlink($docxPath);
        }
    }

    private function convertDocxToPdf(string $docxPath): string
    {
        $sofficeBinary = $this->resolveSofficeBinary();
        if ($sofficeBinary !== null) {
            return $this->convertWithSoffice($docxPath, $sofficeBinary);
        }

        return $this->convertWithWordCom($docxPath);
    }

    private function convertWithSoffice(string $docxPath, string $binary): string
    {
        $outDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sk_pdf_' . uniqid();
        if (!@mkdir($outDir) && !is_dir($outDir)) {
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
        $process->setTimeout(600);
        $process->run();

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

        $finalPdfPath = $this->makeTempPath('pdf');
        if (!@rename($pdfPath, $finalPdfPath)) {
            if (!@copy($pdfPath, $finalPdfPath)) {
                @unlink($finalPdfPath);
                $this->cleanupDirectory($outDir);
                abort(500, 'Gagal menyimpan hasil konversi PDF.');
            }
            @unlink($pdfPath);
        }

        $this->cleanupDirectory($outDir);

        return $finalPdfPath;
    }

    private function convertWithWordCom(string $docxPath): string
    {
        if (DIRECTORY_SEPARATOR !== '\\') {
            abort(500, 'Konversi PDF membutuhkan LibreOffice/soffice pada server Linux/macOS.');
        }

        $pdfPath = $this->makeTempPath('pdf');
        $docxEscaped = $this->escapePowerShellSingleQuoted($docxPath);
        $pdfEscaped = $this->escapePowerShellSingleQuoted($pdfPath);

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

        return $pdfPath;
    }

    private function appendLampiranWithWordCom(string $mainDocxPath, array $lampiranFiles): string
    {
        if (DIRECTORY_SEPARATOR !== '\\') {
            abort(500, 'Penggabungan lampiran saat ini membutuhkan Microsoft Word di Windows.');
        }

        $outputDocxPath = $this->makeTempPath('docx');
        $mainDocxEscaped = $this->escapePowerShellSingleQuoted($mainDocxPath);
        $outputDocxEscaped = $this->escapePowerShellSingleQuoted($outputDocxPath);

        $attachmentLines = [];
        foreach ($lampiranFiles as $lampiran) {
            $attachmentLines[] = "'" . $this->escapePowerShellSingleQuoted($lampiran['full_path']) . "'";
        }

        $attachmentArrayLiteral = implode(",\n    ", $attachmentLines);
        if ($attachmentArrayLiteral === '') {
            $attachmentArrayLiteral = "''";
        }

        $script = <<<'PS'
$ErrorActionPreference = 'Stop'
$mainDocxPath = '__MAIN_DOCX__'
$outputDocxPath = '__OUTPUT_DOCX__'
$attachmentPaths = @(
    __ATTACHMENTS__
)

$word = $null
$doc = $null
try {
    $wdStory = 6
    $wdPageBreak = 7
    $wdFormatXMLDocument = 16

    $word = New-Object -ComObject Word.Application
    $word.Visible = $false
    $word.DisplayAlerts = 0
    $doc = $word.Documents.Open($mainDocxPath, $false, $false)
    $selection = $word.Selection
    $selection.EndKey($wdStory) | Out-Null

    foreach ($attachmentPath in $attachmentPaths) {
        if ([string]::IsNullOrWhiteSpace($attachmentPath) -or -not (Test-Path -LiteralPath $attachmentPath)) {
            continue
        }

        $selection.InsertBreak($wdPageBreak)
        $selection.EndKey($wdStory) | Out-Null

        $extension = [System.IO.Path]::GetExtension($attachmentPath).ToLowerInvariant()
        if ($extension -eq '.docx') {
            $selection.InsertFile($attachmentPath)
        } elseif ($extension -eq '.pdf') {
            $pdfDoc = $word.Documents.Open($attachmentPath, $false, $true)
            $pdfDoc.Content.Copy()
            $selection.Paste()
            $pdfDoc.Close()
        } elseif ($extension -in @('.jpg', '.jpeg', '.png')) {
            $shape = $selection.InlineShapes.AddPicture($attachmentPath, $false, $true)
            $maxWidth = 430
            if ($shape.Width -gt $maxWidth) {
                $ratio = $maxWidth / $shape.Width
                $shape.Width = $maxWidth
                $shape.Height = [int]($shape.Height * $ratio)
            }
            $selection.TypeParagraph()
        }

        $selection.EndKey($wdStory) | Out-Null
    }

    $doc.SaveAs([ref]$outputDocxPath, [ref]$wdFormatXMLDocument)
    $doc.Close()
    $word.Quit()
} catch {
    if ($doc -ne $null) { $doc.Close() }
    if ($word -ne $null) { $word.Quit() }
    throw
}
PS;

        $script = str_replace(
            ['__MAIN_DOCX__', '__OUTPUT_DOCX__', '__ATTACHMENTS__'],
            [$mainDocxEscaped, $outputDocxEscaped, $attachmentArrayLiteral],
            $script
        );

        $process = new Process([
            'powershell',
            '-NoProfile',
            '-NonInteractive',
            '-ExecutionPolicy',
            'Bypass',
            '-Command',
            $script,
        ]);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($outputDocxPath)) {
            abort(
                500,
                'Gagal menggabungkan lampiran ke dokumen. Output: ' . trim($process->getErrorOutput() . ' ' . $process->getOutput())
            );
        }

        return $outputDocxPath;
    }

    private function resolveLampiranFiles(mixed $lampiran): array
    {
        if (!is_array($lampiran)) {
            return [];
        }

        $allowedExtensions = ['docx', 'pdf', 'jpg', 'jpeg', 'png'];
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

            $extension = strtolower((string) pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedExtensions, true)) {
                $extension = strtolower((string) pathinfo($fullPath, PATHINFO_EXTENSION));
            }

            if (!in_array($extension, $allowedExtensions, true)) {
                continue;
            }

            $files[] = [
                'name' => $name,
                'full_path' => $fullPath,
            ];
        }

        return $files;
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

            $where = new Process(['where', 'soffice']);
            $where->setTimeout(5);
            $where->run();
            if ($where->isSuccessful()) {
                $first = trim((string) strtok(str_replace("\r", '', $where->getOutput()), "\n"));
                if ($first !== '') {
                    return $first;
                }
            }

            return null;
        }

        return 'soffice';
    }

    private function makeTempPath(string $extension): string
    {
        $base = tempnam(sys_get_temp_dir(), 'sk_' . $extension . '_');
        if ($base === false) {
            abort(500, 'Gagal menyiapkan file sementara.');
        }

        @unlink($base);

        return $base . '.' . $extension;
    }

    private function escapePowerShellSingleQuoted(string $value): string
    {
        return str_replace("'", "''", $value);
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
