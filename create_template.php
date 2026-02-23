<?php
require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\IOFactory;

// Sample data for testing
$sk_number = '800 / BKPSDM / 2024';
$title = 'KEPUTUSAN BUPATI BUOL TENTANG PEMBENTUKAN TIM PELAKSANA KEGIATAN VAKSINASI TAHUN 2024';
$menimbang = "a. Bahwa berdasarkan pertimbangan dari segi kesehatan dan pertimbangan dari segi ketenagakerjaan perlu dibentuk tim pelaksana kegiatan vaksinasi tahun 2024;" . PHP_EOL . "b. Bahwa berdasarkan pertimbangan dari segi kesehatan dan pertimbangan dari segi ketenagakerjaan perlu dibentuk tim pelaksana kegiatan vaksinasi tahun 2024;";
$mengingat = "1. Undang-Undangang Nomor 1 Tahun 1974 tentang Kesehatan (Lembaran Negara Republik Indonesia Nomor 1 Tahun 1974);\n2. Undang-Undangang Nomor 36 Tahun 2009 tentang Kesehatan (Lembaran Negara Republik Indonesia Nomor 36 Tahun 2009);\n3. Peraturan Pemerintah Nomor 70 Tahun 2014 tentang Pelayanan Kesehatan (Lembaran Negara Republik Indonesia Nomor 70 Tahun 2014);";
$memperhatikan = "1. Surat Edaran Bupati Buol Nomor 800/215/BKPSDM/2024 tanggal 1 Agustus 2024 perihal permohonan tenaga medis dan perawat;";
$menetapkan = 'Pembentukan Tim Pelaksana Kegiatan Vaksinasi Tahun 2024';
$diktum = "KESATU: Membentuk Tim Pelaksana Kegiatan Vaksinasi.\nKEDUA: Menugaskan ketua tim dan anggota tim.\nKETIGA: Menetapkan tugas dan tanggung jawab tim.";
$ditetapkan_di = 'Buol';
$pada_tanggal = '1 Agustus 2024';
$jabatan_penandatangan = 'BUPATI BUOL';
$nama_penandatangan = 'RISHARYUDI TRIWIBOWO';

// Create new PhpWord object
$phpWord = new PhpWord();

// Set default font
$phpWord->setDefaultFontName('Bookman Old Style');
$phpWord->setDefaultFontSize(12);

// Add title section
$section = $phpWord->addSection();
$section->addText('KEPUTUSAN BUPATI', ['bold' => false, 'size' => 14, 'alignment' => 'center']);
$section->addTextBreak();
$section->addText('NOMOR : ' . $sk_number, ['alignment' => 'right']);
$section->addTextBreak();
$section->addText('TENTANG', ['alignment' => 'center', 'size' => 14]);
$section->addTextBreak();
$section->addText($title, ['bold' => false, 'alignment' => 'center', 'size' => 12]);
$section->addTextBreak();
$section->addText('BUPATI BUOL,', ['alignment' => 'center', 'allCaps' => true]);

// Add content sections
$section->addTextBreak(2);

// Menimbang
$section->addText('Menimbang', ['bold' => false]);
$section->addTextBreak();
$menimbangLines = explode("\n", $menimbang);
foreach ($menimbangLines as $line) {
    $section->addText($line, ['alignment' => 'both']);
}

$section->addTextBreak();

// Mengingat
$section->addText('Mengingat', ['bold' => false]);
$section->addTextBreak();
$mengingatLines = explode("\n", $mengingat);
foreach ($mengingatLines as $line) {
    $section->addText($line, ['alignment' => 'both']);
}

$section->addTextBreak();

// Memperhatikan
if (!empty($memperhatikan)) {
    $section->addText('Memperhatikan', ['bold' => false]);
    $section->addTextBreak();
    $memperhatikanLines = explode("\n", $memperhatikan);
    foreach ($memperhatikanLines as $line) {
        $section->addText($line, ['alignment' => 'both']);
    }
    $section->addTextBreak();
}

// Memutuskan
$section->addText('Memutuskan', ['bold' => false]);
$section->addTextBreak();
$table = $section->addTable(1, 3);
$table->addRow();
$table->addCell(3000)->addText('Menetapkan');
$table->addCell(500)->addText(':');
$table->addCell(15000)->addText($menetapkan, ['bold' => false]);
$table->addRow();
$section->addTextBreak();

// Diktum
$section->addText('Diktum', ['bold' => false]);
$section->addTextBreak();
$diktumLines = explode("\n", $diktum);
foreach ($diktumLines as $line) {
    $section->addText($line, ['alignment' => 'both']);
}

// Footer
$section->addTextBreak(2);
$section->addText('Ditetapkan di ' . $ditetapkan_di, ['alignment' => 'left']);
$section->addText('pada tanggal ' . $pada_tanggal, ['alignment' => 'left']);
$section->addTextBreak(2);
$section->addText($jabatan_penandatangan . ',', ['alignment' => 'right']);
$section->addTextBreak(3);
$section->addText($nama_penandatangan, ['bold' => false, 'alignment' => 'right']);

// Save as DOCX
$objWriter = IOFactory::createWriter($phpWord);
$objWriter->save('sk_template_new.docx');

echo "Template DOCX baru telah dibuat: sk_template_new.docx\n";
?>
