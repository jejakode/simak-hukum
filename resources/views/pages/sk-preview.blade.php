@extends('layouts.app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Bookman+Old+Style&display=swap');
    .document-preview {
        font-family: 'Bookman Old Style', serif;
        font-size: 12pt;
        line-height: 1.4;
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
    .document-preview .nomor-surat {
        text-align: center;
        margin-bottom: 20px;
        font-size: 12pt;
    }
    .document-preview .jabatan-bupati {
        text-align: center;
        font-weight: normal;
        text-transform: uppercase;
        font-size: 12pt;
    }
    .document-preview .menimbang-item {
        margin-left: 40px;
        text-indent: -40px;
        margin-bottom: 8px;
        text-align: justify;
        line-height: 1.4;
    }
    .document-preview .mengingat-item {
        margin-left: 40px;
        text-indent: -40px;
        margin-bottom: 8px;
        text-align: justify;
        line-height: 1.4;
    }
    .document-preview .header-label {
        display: inline-block;
        min-width: 85px;
        vertical-align: top;
        font-weight: normal;
    }
    .document-preview .list-item {
        text-align: justify;
        margin-bottom: 8px;
        line-height: 1.4;
    }
    .document-preview div[style*="pre-line"] div {
        text-align: justify;
    }
    .document-preview .content-table td {
        text-align: justify;
    }
    .document-preview .footer-text {
        text-align: justify;
    }
    .document-preview .diktum-item {
        text-align: justify;
        margin-bottom: 8px;
        line-height: 1.4;
        padding-left: 0;
        text-indent: 0;
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
    .document-preview .tentang {
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
@endpush

@section('content')
<div class="pt-26 py-12 bg-gradient-to-b from-slate-900/5 via-slate-100 to-slate-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-5xl mx-auto">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">
                    Preview Surat Keputusan
                </h1>
                <p class="text-lg text-gray-600 mb-8">Ini adalah pratinjau dokumen. Periksa kembali isinya sebelum mengunduh.</p>
            </div>

            {{-- Action Buttons --}}
            <div class="mb-8 rounded-2xl bg-gradient-to-r from-purple-600 via-indigo-500 to-blue-500 p-1 shadow-xl">
                <div class="flex flex-col gap-4 rounded-2xl bg-white/95 px-5 py-4 backdrop-blur-sm sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-center text-xs font-medium text-gray-600 sm:text-left">
                        <span class="inline-flex items-center gap-2 rounded-full bg-purple-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-purple-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-purple-500"></span>
                            Pratinjau aktif
                        </span>
                        <span class="ml-2 hidden text-gray-500 sm:inline">
                            Periksa isi dokumen lalu pilih jenis unduhan.
                        </span>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <a
                            href="{{ route('sk.create') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-400 hover:bg-slate-50 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 sm:w-auto"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                            <span>Kembali ke Form</span>
                        </a>
                        <a
                            href="{{ route('sk.pdf', request()->query()) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-red-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-red-600 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 sm:w-auto"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            <span>Download PDF</span>
                        </a>
                        <a
                            href="{{ route('sk.docx', request()->query()) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-emerald-600 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 sm:w-auto"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            <span>Download DOCX</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Document Preview --}}
            <div class="bg-white p-16 shadow-lg document-preview first-page" style="width: 210mm; min-height: 297mm; margin: auto; padding: 2cm;">
                {{-- Kop Surat --}}
                <header class="text-center mb-8">
                    <div class="kop-surat" style="position: relative; height: 180px; display: flex; flex-direction: column; align-items: center;">
                        <img src="{{ asset('garuda.png') }}" alt="Garuda Pancasila" style="width: 3.5cm; height: 3.5cm; margin-bottom: 10px;">
                        <div style="text-align: center;">
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
                    <p class="font-bold text-center mt-6" style="text-transform: uppercase;">BUPATI BUOL,</p>
                </div>

                <main>
                    {{-- Menimbang --}}
                    <div class="mb-6">
                        <div style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                            <span class="header-label" style="vertical-align: top;">Menimbang:</span>
                            @foreach(($menimbang ?? []) as $index => $item)
                                @if(is_string($item) && !empty(trim($item)))
                                @if($index === 0)
                                <span style="min-width: 30px; margin-left: 8px; vertical-align: top;">{{ chr(97 + $index) }}.</span>
                                <span style="margin-left: 8px; vertical-align: top;">{{ $item }};</span>
                                @endif
                                @endif
                            @endforeach
                        </div>
                        @foreach(($menimbang ?? []) as $index => $item)
                            @if(is_string($item) && !empty(trim($item)))
                            @if($index > 0)
                            <div class="menimbang-item">{{ chr(97 + $index) }}. {{ $item }};</div>
                            @endif
                            @endif
                        @endforeach
                    </div>

                    {{-- Mengingat --}}
                    <div class="mb-6">
                        <div style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                            <span class="header-label" style="vertical-align: top;">Mengingat:</span>
                            @foreach(($mengingat ?? []) as $index => $item)
                                @if(is_string($item) && !empty(trim($item)))
                                @if($index === 0)
                                <span style="min-width: 30px; margin-left: 8px; vertical-align: top;">{{ $index + 1 }}.</span>
                                <span style="margin-left: 8px; vertical-align: top;">{{ $item }};</span>
                                @endif
                                @endif
                            @endforeach
                        </div>
                        @foreach(($mengingat ?? []) as $index => $item)
                            @if(is_string($item) && !empty(trim($item)))
                            @if($index > 0)
                            <div class="mengingat-item">{{ $index + 1 }}. {{ $item }};</div>
                            @endif
                            @endif
                        @endforeach
                    </div>

                    {{-- Memperhatikan --}}
                    @if(isset($memperhatikan) && !empty(array_filter($memperhatikan))))
                    <div class="mb-6">
                        <p class="font-bold mb-3">Memperhatikan</p>
                        <div style="white-space: pre-line;">
                            @foreach($memperhatikan ?? [] as $index => $item)
                                 @if($item)
                                <div class="list-item">{{ $index + 1 }}. {{ $item }};</div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Memutuskan --}}
                    <div class="mb-6">
                        <p class="font-bold text-center mb-3" style="text-transform: uppercase;">MEMUTUSKAN</p>
                        <table class="content-table">
                            <tr>
                                <td class="w-32" style="min-width: 85px; vertical-align: top;">Menetapkan</td>
                                <td class="w-2" style="vertical-align: top;">:</td>
                                <td style="vertical-align: top;"><p>Menetapkan : {{ $menetapkan ?? '[MENETAPKAN]' }}</p></td>
                            </tr>
                        </table>
                    </div>

                    {{-- Diktum --}}
                    @php
                        $diktum_labels = ['KESATU', 'KEDUA', 'KETIGA', 'KEEMPAT', 'KELIMA', 'KEENAM', 'KETUJUH', 'KEDELAPAN', 'KESEMBILAN', 'KESEPULUH'];
                    @endphp
                    <div class="mb-6">
                            @foreach(($diktum ?? []) as $index => $item)
                            @if(is_string($item) && !empty(trim($item)))
                            <div class="diktum-item" style="display: flex;">
                                <span style="min-width: 80px;"><strong>{{ $diktum_labels[$index] ?? 'SELANJUTNYA' }}:</strong></span>
                                <span style="flex: 1; margin-left: 8px;">{{ $item }};</span>
                            </div>
                            @endif
                    @endforeach
                </main>

                {{-- Penutup --}}
                <footer class="mt-16">
                    <div class="w-1/2 ml-auto text-left footer-text">
                        <p>Ditetapkan di {{ $ditetapkan_di ?? '[Tempat]' }}</p>
                        <p>pada tanggal {{ $pada_tanggal ? \Carbon\Carbon::parse($pada_tanggal)->isoFormat('D MMMM Y') : '[Tanggal]' }}</p>
                        <p class="mt-4 font-bold">{{ $jabatan_penandatangan ?? '[JABATAN PENANDATANGAN]' }},</p>
                        <div class="h-24"></div>
                        <p class="font-bold underline">{{ $nama_penandatangan ?? '[NAMA PENANDATANGAN]' }}</p>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</div>
@endsection
