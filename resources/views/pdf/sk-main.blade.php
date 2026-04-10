<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 2.5cm 2.5cm 2.5cm 2.5cm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.35;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .title {
            margin: 0;
            text-transform: uppercase;
        }

        .title-space {
            margin-top: 18pt;
        }

        .section {
            margin-top: 10pt;
        }

        .konsiderans {
            width: 100%;
            border-collapse: collapse;
        }

        .konsiderans td {
            vertical-align: top;
            padding-bottom: 4pt;
        }

        .col-head {
            width: 85pt;
        }

        .col-sep {
            width: 12pt;
        }

        .col-label {
            width: 20pt;
        }

        .diktum {
            margin-top: 8pt;
            text-align: justify;
        }

        .signature {
            width: 45%;
            margin-left: auto;
            margin-top: 28pt;
        }

        .signature-space {
            height: 60pt;
        }
    </style>
</head>
<body>
    <p class="center title">BUPATI BUOL</p>
    <p class="center title">PROVINSI SULAWESI TENGAH</p>

    <p class="center title title-space">KEPUTUSAN BUPATI</p>
    <p class="center title">NOMOR : {{ $nomor_surat !== '' ? $nomor_surat : ' ' }}</p>
    <p class="center title">TENTANG</p>
    <p class="center title">{{ $sk_title !== '' ? $sk_title : '[JUDUL SURAT KEPUTUSAN]' }}</p>

    <p class="title-space">BUPATI BUOL,</p>

    <table class="konsiderans section">
        @foreach ($menimbang as $index => $item)
            <tr>
                <td class="col-head">{{ $index === 0 ? 'Menimbang' : '' }}</td>
                <td class="col-sep">{{ $index === 0 ? ':' : '' }}</td>
                <td class="col-label">{{ chr(97 + $index) }}.</td>
                <td style="text-align: justify;">{{ rtrim($item, ";\t\n\r\0\x0B") }};</td>
            </tr>
        @endforeach
    </table>

    <table class="konsiderans section">
        @foreach ($mengingat as $index => $item)
            <tr>
                <td class="col-head">{{ $index === 0 ? 'Mengingat' : '' }}</td>
                <td class="col-sep">{{ $index === 0 ? ':' : '' }}</td>
                <td class="col-label">{{ $index + 1 }}.</td>
                <td style="text-align: justify;">{{ rtrim($item, ";\t\n\r\0\x0B") }};</td>
            </tr>
        @endforeach
    </table>

    @if (!empty($memperhatikan ?? []))
        <table class="konsiderans section">
            @foreach ($memperhatikan as $index => $item)
                <tr>
                    <td class="col-head">{{ $index === 0 ? 'Memperhatikan' : '' }}</td>
                    <td class="col-sep">{{ $index === 0 ? ':' : '' }}</td>
                    <td class="col-label">{{ $index + 1 }}.</td>
                    <td style="text-align: justify;">{{ rtrim($item, ";\t\n\r\0\x0B") }};</td>
                </tr>
            @endforeach
        </table>
    @endif

    <table class="konsiderans section">
        <tr>
            <td class="col-head">Menetapkan</td>
            <td class="col-sep">:</td>
            <td colspan="2" style="text-transform: uppercase;">{{ $menetapkan !== '' ? $menetapkan : '[PENETAPAN BELUM DIISI]' }}</td>
        </tr>
    </table>

    @php
        $diktumLabels = ['KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA', 'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'];
    @endphp
    <div class="diktum">
        @foreach ($diktum as $index => $item)
            <p style="margin: 0 0 6pt 0;">
                <strong>{{ $diktumLabels[$index] ?? ('DIKTUM ' . ($index + 1)) }}:</strong>
                {{ rtrim($item, ";\t\n\r\0\x0B") }};
            </p>
        @endforeach
    </div>

    <div class="signature">
        <p style="margin: 0;">Ditetapkan di {{ $ditetapkan_di !== '' ? $ditetapkan_di : '[Tempat]' }}</p>
        <p style="margin: 10pt 0 0 0;">{{ $jabatan_penandatangan !== '' ? $jabatan_penandatangan : '[JABATAN PENANDATANGAN]' }},</p>
        <div class="signature-space"></div>
        <p style="margin: 0; text-transform: uppercase;"><strong>{{ $nama_penandatangan !== '' ? $nama_penandatangan : '[NAMA PENANDATANGAN]' }}</strong></p>
    </div>
</body>
</html>
