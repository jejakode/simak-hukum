<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class SkExportService
{
    private const PREVIEW_CACHE_DIR = 'app/sk-preview-cache';
    private const WORK_TEMP_DIR = 'app/sk-temp';

    public function __construct(private readonly SkDocumentService $documentService)
    {
    }

    public function getOrCreatePreviewPdf(
        array $data,
        ?array $currentPreview = null,
        bool $forceRebuild = false
    ): array
    {
        $hash = $this->buildPreviewHash($data);
        $cachedPath = $this->previewCachePath($hash);

        if (!$forceRebuild && is_array($currentPreview)) {
            $currentHash = (string) ($currentPreview['hash'] ?? '');
            $currentPath = (string) ($currentPreview['path'] ?? '');

            if ($currentHash === $hash && $currentPath !== '' && file_exists($currentPath)) {
                return [
                    'hash' => $hash,
                    'path' => $currentPath,
                ];
            }
        }

        if (!$forceRebuild && file_exists($cachedPath)) {
            $this->cleanupPreviousPreviewPath($currentPreview, $cachedPath);
            $this->maybePrunePreviewCache();

            return [
                'hash' => $hash,
                'path' => $cachedPath,
            ];
        }

        if ($forceRebuild && file_exists($cachedPath)) {
            @unlink($cachedPath);
        }

        $tempPath = $this->createFinalPdfFromData($data);

        try {
            $this->ensurePreviewCacheDirectory();

            if (!@rename($tempPath, $cachedPath)) {
                if (!@copy($tempPath, $cachedPath)) {
                    abort(500, 'Gagal menyimpan cache preview PDF.');
                }
                @unlink($tempPath);
            }
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }

        $this->cleanupPreviousPreviewPath($currentPreview, $cachedPath);
        $this->maybePrunePreviewCache();

        return [
            'hash' => $hash,
            'path' => $cachedPath,
        ];
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
        $lampiranFiles = $this->resolveLampiranFiles($data['lampiran'] ?? []);

        if (DIRECTORY_SEPARATOR === '\\') {
            $docxPath = $this->createFinalDocxFromData($data);

            try {
                return $this->convertDocxToPdf($docxPath);
            } finally {
                @unlink($docxPath);
            }
        }

        $docxPath = $this->createFinalDocxFromData($data);
        $this->stabilizeDocxTableLayoutForLinux($docxPath);
        $tempPdfs = [];
        $finalPdfPath = null;

        try {
            $mainPdfPath = $this->convertDocxToPdf($docxPath);
            $tempPdfs[] = $mainPdfPath;

            if (empty($lampiranFiles)) {
                $finalPdfPath = $mainPdfPath;
                return $mainPdfPath;
            }

            $sofficeBinary = $this->resolveSofficeBinary();
            foreach ($lampiranFiles as $lampiran) {
                $lampiranPdfPath = $this->convertLampiranToPdf($lampiran['full_path'], $sofficeBinary);
                $tempPdfs[] = $lampiranPdfPath;
            }

            $finalPdfPath = $this->mergePdfFiles($tempPdfs);
            return $finalPdfPath;
        } finally {
            @unlink($docxPath);

            foreach ($tempPdfs as $tempPdfPath) {
                if ($finalPdfPath !== null && $tempPdfPath === $finalPdfPath) {
                    continue;
                }

                if (is_string($tempPdfPath) && file_exists($tempPdfPath)) {
                    @unlink($tempPdfPath);
                }
            }
        }
    }

    private function convertDocxToPdf(string $docxPath): string
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            try {
                return $this->convertWithWordCom($docxPath);
            } catch (\Throwable $exception) {
                $sofficeBinary = $this->resolveSofficeBinary();
                if ($sofficeBinary === null) {
                    throw $exception;
                }

                Log::warning('Word COM gagal saat konversi PDF, fallback ke LibreOffice.', [
                    'message' => $exception->getMessage(),
                ]);

                return $this->convertWithSoffice($docxPath, $sofficeBinary);
            }
        }

        $sofficeBinary = $this->resolveSofficeBinary();
        if ($sofficeBinary !== null) {
            return $this->convertWithSoffice($docxPath, $sofficeBinary);
        }

        return $this->convertWithWordCom($docxPath);
    }

    private function convertWithSoffice(string $docxPath, string $binary): string
    {
        $outDir = $this->workTempDirectory() . DIRECTORY_SEPARATOR . 'sk_pdf_' . uniqid();
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

    private function convertLampiranToPdf(string $inputPath, ?string $sofficeBinary): string
    {
        $extension = strtolower((string) pathinfo($inputPath, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            $copyPath = $this->makeTempPath('pdf');
            if (!@copy($inputPath, $copyPath)) {
                abort(500, 'Gagal menyalin lampiran PDF untuk proses penggabungan.');
            }

            return $copyPath;
        }

        if ($sofficeBinary === null) {
            abort(500, 'Lampiran DOCX/JPG/PNG membutuhkan LibreOffice (soffice) di server Linux.');
        }

        return $this->convertWithSoffice($inputPath, $sofficeBinary);
    }

    private function mergePdfFiles(array $pdfPaths): string
    {
        if (count($pdfPaths) === 1) {
            $singlePath = (string) $pdfPaths[0];
            $finalPath = $this->makeTempPath('pdf');
            if (!@copy($singlePath, $finalPath)) {
                abort(500, 'Gagal menyiapkan file PDF akhir.');
            }
            return $finalPath;
        }

        $outputPath = $this->makeTempPath('pdf');
        $args = ['qpdf', '--empty', '--pages'];
        foreach ($pdfPaths as $pdfPath) {
            $args[] = (string) $pdfPath;
            $args[] = '1-z';
        }
        $args[] = '--';
        $args[] = $outputPath;

        $process = new Process($args);
        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($outputPath)) {
            abort(
                500,
                'Gagal menggabungkan PDF utama dan lampiran. Pastikan qpdf terpasang. Output: ' .
                trim($process->getErrorOutput() . ' ' . $process->getOutput())
            );
        }

        return $outputPath;
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
$pdfDoc = $null
try {
    $wdCollapseEnd = 0
    $wdPageBreak = 7
    $wdFormatXMLDocument = 16

    $word = New-Object -ComObject Word.Application
    $word.Visible = $false
    $word.DisplayAlerts = 0

    if (-not (Test-Path -LiteralPath $mainDocxPath)) {
        throw "Dokumen utama tidak ditemukan: $mainDocxPath"
    }

    $doc = $word.Documents.Open($mainDocxPath, $false, $false)
    if ($doc -eq $null) {
        throw "Word gagal membuka dokumen utama: $mainDocxPath"
    }

    foreach ($attachmentPath in $attachmentPaths) {
        if ([string]::IsNullOrWhiteSpace($attachmentPath) -or -not (Test-Path -LiteralPath $attachmentPath)) {
            continue
        }

        $insertRange = $doc.Range($doc.Content.End - 1, $doc.Content.End - 1)
        $insertRange.Collapse($wdCollapseEnd)
        $insertRange.InsertBreak($wdPageBreak)
        $insertRange = $doc.Range($doc.Content.End - 1, $doc.Content.End - 1)
        $insertRange.Collapse($wdCollapseEnd)

        $extension = [System.IO.Path]::GetExtension($attachmentPath).ToLowerInvariant()
        if ($extension -eq '.docx') {
            $insertRange.InsertFile($attachmentPath)
        } elseif ($extension -eq '.pdf') {
            $pdfDoc = $word.Documents.Open($attachmentPath, $false, $true)
            $insertRange.FormattedText = $pdfDoc.Content.FormattedText
            $pdfDoc.Close()
            $pdfDoc = $null
        } elseif ($extension -in @('.jpg', '.jpeg', '.png')) {
            $shape = $insertRange.InlineShapes.AddPicture($attachmentPath, $false, $true)
            $maxWidth = 430
            if ($shape.Width -gt $maxWidth) {
                $ratio = $maxWidth / $shape.Width
                $shape.Width = $maxWidth
                $shape.Height = [int]($shape.Height * $ratio)
            }
            $insertRange.InsertParagraphAfter()
        }
    }

    $doc.SaveAs([ref]$outputDocxPath, [ref]$wdFormatXMLDocument)
    $doc.Close()
    $word.Quit()
} catch {
    if ($pdfDoc -ne $null) { $pdfDoc.Close() }
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
        $base = tempnam($this->workTempDirectory(), 'sk_' . $extension . '_');
        if ($base === false) {
            abort(500, 'Gagal menyiapkan file sementara.');
        }

        @unlink($base);

        return $base . '.' . $extension;
    }

    private function workTempDirectory(): string
    {
        $directory = storage_path(self::WORK_TEMP_DIR);
        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            abort(500, 'Gagal menyiapkan direktori temp dokumen.');
        }

        return $directory;
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

    private function filterTextArray(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, static function ($item) {
            return is_string($item) && trim($item) !== '';
        }));
    }

    private function stabilizeDocxTableLayoutForLinux(string $docxPath): void
    {
        if (DIRECTORY_SEPARATOR === '\\' || !is_file($docxPath)) {
            return;
        }

        if (!class_exists(\ZipArchive::class)) {
            Log::warning('Ekstensi ZipArchive tidak tersedia. Lewati stabilisasi tabel DOCX untuk Linux.');
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            Log::warning('Gagal membuka DOCX untuk stabilisasi layout tabel Linux.', ['docx' => $docxPath]);
            return;
        }

        try {
            $documentXml = $zip->getFromName('word/document.xml');
            if (!is_string($documentXml) || $documentXml === '') {
                return;
            }

            $updatedXml = preg_replace_callback(
                '/<w:tblPr>(.*?)<\/w:tblPr>/s',
                static function (array $matches): string {
                    $tblPr = $matches[0];
                    if (str_contains($tblPr, '<w:tblLayout')) {
                        $tblPr = preg_replace(
                            '/<w:tblLayout[^>]*\/>/',
                            '<w:tblLayout w:type="fixed"/>',
                            $tblPr
                        );

                        return is_string($tblPr) ? $tblPr : $matches[0];
                    }

                    return str_replace(
                        '</w:tblPr>',
                        '<w:tblLayout w:type="fixed"/></w:tblPr>',
                        $tblPr
                    );
                },
                $documentXml
            );

            if (!is_string($updatedXml) || $updatedXml === $documentXml) {
                return;
            }

            $zip->addFromString('word/document.xml', $updatedXml);
        } finally {
            $zip->close();
        }
    }

    private function buildPreviewHash(array $data): string
    {
        $lampiranSignatures = [];
        foreach ($this->resolveLampiranFiles($data['lampiran'] ?? []) as $lampiran) {
            $path = (string) ($lampiran['full_path'] ?? '');
            $lampiranSignatures[] = [
                'name' => (string) ($lampiran['name'] ?? ''),
                'path' => $path,
                'size' => is_file($path) ? filesize($path) : null,
                'mtime' => is_file($path) ? filemtime($path) : null,
            ];
        }

        $payload = [
            'pdf_engine' => 'docx_soffice_v1',
            'nomor_surat' => trim((string) ($data['nomor_surat'] ?? '')),
            'sk_title' => trim((string) ($data['sk_title'] ?? '')),
            'menimbang' => $this->filterTextArray($data['menimbang'] ?? []),
            'mengingat' => $this->filterTextArray($data['mengingat'] ?? []),
            'memperhatikan' => $this->filterTextArray($data['memperhatikan'] ?? []),
            'menetapkan' => trim((string) ($data['menetapkan'] ?? '')),
            'diktum' => $this->filterTextArray($data['diktum'] ?? []),
            'ditetapkan_di' => trim((string) ($data['ditetapkan_di'] ?? '')),
            'jabatan_penandatangan' => trim((string) ($data['jabatan_penandatangan'] ?? '')),
            'nama_penandatangan' => trim((string) ($data['nama_penandatangan'] ?? '')),
            'lampiran' => $lampiranSignatures,
        ];

        return hash('sha256', (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function previewCachePath(string $hash): string
    {
        return storage_path(self::PREVIEW_CACHE_DIR . '/' . $hash . '.pdf');
    }

    private function previewCacheDirectoryPath(): string
    {
        return storage_path(self::PREVIEW_CACHE_DIR);
    }

    private function ensurePreviewCacheDirectory(): void
    {
        $directory = $this->previewCacheDirectoryPath();
        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            abort(500, 'Gagal menyiapkan direktori cache preview PDF.');
        }
    }

    private function cleanupPreviousPreviewPath(?array $currentPreview, string $activePath): void
    {
        if (!is_array($currentPreview)) {
            return;
        }

        $oldPath = (string) ($currentPreview['path'] ?? '');
        if ($oldPath === '' || $oldPath === $activePath || !file_exists($oldPath)) {
            return;
        }

        if ($this->isPreviewCachePath($oldPath)) {
            @unlink($oldPath);
        }
    }

    private function isPreviewCachePath(string $path): bool
    {
        $realPath = realpath($path);
        $realCacheDirectory = realpath($this->previewCacheDirectoryPath());

        if ($realPath === false || $realCacheDirectory === false) {
            return false;
        }

        return str_starts_with($realPath, $realCacheDirectory . DIRECTORY_SEPARATOR);
    }

    private function maybePrunePreviewCache(): void
    {
        if (random_int(1, 20) !== 1) {
            return;
        }

        $directory = $this->previewCacheDirectoryPath();
        if (!is_dir($directory)) {
            return;
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . '*.pdf');
        if (!is_array($files)) {
            return;
        }

        $cutoff = time() - (2 * 24 * 60 * 60);
        foreach ($files as $file) {
            if (!is_string($file)) {
                continue;
            }

            $mtime = @filemtime($file);
            if ($mtime !== false && $mtime < $cutoff && $this->isPreviewCachePath($file)) {
                @unlink($file);
            }
        }
    }
}
