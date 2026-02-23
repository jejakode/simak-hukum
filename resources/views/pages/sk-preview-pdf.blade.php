<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keputusan</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bookman+Old+Style&display=swap');
        .document-preview {
            font-family: 'Bookman Old Style', serif;
            line-height: 1.5;
            font-size: 12pt;
        }
        .document-preview h1, .document-preview h2, .document-preview h3 {
            font-weight: normal;
            text-align: center;
            text-transform: uppercase;
        }
        .document-preview .content-table td {
            vertical-align: top;
            padding-right: 8px;
            padding-bottom: 4px;
        }
        .document-preview .header-text {
            font-size: 12pt;
            line-height: 1.2;
        }
        .document-preview .footer-text {
            text-align: justify;
        }
        .document-preview .diktum-item {
            text-align: justify;
            margin-bottom: 5px;
        }
        .document-preview .nomor-surat {
            text-align: right;
            margin-bottom: 20px;
            font-size: 12pt;
        }
        .document-preview .kop-surat p {
            margin: 2px 0;
            line-height: 1.2;
        }
        .document-preview .judul-utama {
            font-size: 12pt;
            font-weight: normal;
            margin: 20px 0 10px 0;
        }
        .document-preview .jabatan-bupati {
            text-align: center;
        }
        .document-preview .menimbang-item {
            font-size: 12pt;
            margin: 10px 0;
        }
        .document-preview .judul-detail {
            font-size: 12pt;
            font-weight: normal;
            margin: 15px 0;
            line-height: 1.4;
        }
        .document-preview .font-bold {
            font-weight: normal;
        }
        .document-preview strong {
            font-weight: normal;
        }
        /* Margin untuk halaman pertama */
        .document-preview.first-page {
            padding-top: 5cm;
        }
        /* Margin untuk halaman selanjutnya */
        .document-preview.other-pages {
            padding-top: 2cm;
        }
    </style>
</head>
<body>
    <div class="document-preview first-page" style="width: 210mm; min-height: 297mm; margin: auto; padding-top: 5cm;">
        {{-- Kop Surat --}}
        <header class="text-center mb-8">
            <div class="kop-surat" style="position: relative; height: 150px;">
                <img src="{{ asset('garuda.png') }}" alt="Garuda Pancasila" style="width: 3cm; height: 3cm; position: absolute; left: 50%; transform: translateX(-50%); top: 0;">
                <div style="padding-top: 120px;">
                    <p class="font-bold text-sm jabatan-bupati">BUPATI BUOL</p>
                    <p class="font-bold text-sm">PROVINSI SULAWESI TENGAH</p>
                </div>
            </div>
        </header>

        {{-- Judul dan Nomor Surat --}}
        <div class="text-center mb-8">
            <h2 class="judul-utama">KEPUTUSAN BUPATI</h2>
            <div class="nomor-surat">
                <p>NOMOR : {{ $nomor_surat ?? '[NOMOR SURAT]' }}</p>
            </div>
            <p class="tentang">TENTANG</p>
            <h3 class="judul-detail">{{ $sk_title ?? '[JUDUL SURAT KEPUTUSAN]' }}</h3>
            <p class="font-bold text-left mt-6" style="text-transform: uppercase;">BUPATI BUOL,</p>
        </div>

        <main>
            {{-- Menimbang --}}
            <div class="mb-6">
                <p class="font-bold mb-3">Menimbang</p>
                <div style="white-space: pre-line;">
                    @foreach($menimbang ?? [] as $index => $item)
                        @if($item)
                        <div>{{ chr(97 + $index) }}. {{ $item }};</div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Mengingat --}}
            <div class="mb-6">
                <p class="font-bold mb-3">Mengingat</p>
                <div style="white-space: pre-line;">
                    @foreach($mengingat ?? [] as $index => $item)
                        @if($item)
                        <div>{{ $index + 1 }}. {{ $item }};</div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Memperhatikan --}}
            @if(isset($memperhatikan) && !empty(array_filter($memperhatikan)))
            <div class="mb-6">
                <p class="font-bold mb-3">Memperhatikan</p>
                <div style="white-space: pre-line;">
                    @foreach($memperhatikan ?? [] as $index => $item)
                         @if($item)
                        <div>{{ $index + 1 }}. {{ $item }};</div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Memutuskan --}}
            <div class="mb-6">
                <p class="font-bold mb-3">Memutuskan</p>
                <table class="content-table">
                    <tr>
                        <td class="w-32">Menetapkan</td>
                        <td class="w-2">:</td>
                        <td><strong>{{ $menetapkan ?? '[PENETAPAN BELUM DIISI]' }}</strong></td>
                    </tr>
                </table>
            </div>

            {{-- Diktum --}}
            @php
                $diktum_labels = ['KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA', 'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'];
            @endphp
            <div class="mb-6">
                @foreach($diktum ?? [] as $index => $item)
                    @if($item)
                    <div class="diktum-item">
                        <strong>{{ $diktum_labels[$index] ?? 'SELANJUTNYA' }}:</strong> {{ $item }};
                    </div>
                    @endif
                @endforeach
            </div>
        </main>

        {{-- Penutup --}}
        <footer class="mt-16">
            <div class="w-1/2 ml-auto text-left">
                <p>Ditetapkan di {{ $ditetapkan_di ?? '[Tempat]' }}</p>
                <p>pada tanggal {{ $pada_tanggal ? \Carbon\Carbon::createFromFormat('Y-m-d', $pada_tanggal)->isoFormat('D MMMM Y') : '[Tanggal]' }}</p>
                <p class="mt-4 font-bold">{{ $jabatan_penandatangan ?? '[JABATAN PENANDATANGAN]' }},</p>
                <div class="h-24"></div>
                <p class="font-bold underline">{{ $nama_penandatangan ?? '[NAMA PENANDATANGAN]' }}</p>
            </div>
        </footer>
    </div>
</body>
</html>
