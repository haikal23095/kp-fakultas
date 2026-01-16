@extends('layouts.mahasiswa')

@section('title', 'Preview Surat Peminjaman Mobil Dinas')

@push('styles')
<style>
    .surat-container {
        background: white;
        padding: 2rem;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        max-width: 900px;
        margin: 2rem auto;
    }
    
    .kop-surat {
        display: flex;
        align-items: center;
        border-bottom: 3px solid #000;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    
    .kop-surat img {
        height: 70px;
        margin-right: 15px;
    }
    
    .text-kop {
        flex: 1;
        text-align: center;
    }
    
    .text-kop h2 {
        margin: 2px 0;
        font-size: 18px;
        font-weight: bold;
        color: #000;
    }
    
    .text-kop h3 {
        margin: 2px 0;
        font-size: 16px;
        font-weight: bold;
        color: #000;
    }
    
    .text-kop p {
        margin: 1px 0;
        font-size: 12px;
        color: #000;
    }
    
    .nomor-surat {
        text-align: center;
        margin: 20px 0;
    }
    
    .nomor-surat h4 {
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .isi-surat {
        text-align: justify;
        line-height: 1.8;
    }
    
    .detail-table {
        width: 100%;
        margin: 15px 0;
    }
    
    .detail-table td {
        padding: 5px 0;
        vertical-align: top;
    }
    
    .detail-table td:first-child {
        width: 200px;
        font-weight: 500;
    }
    
    .detail-table td:nth-child(2) {
        width: 20px;
        text-align: center;
    }
    
    .ttd-section {
        margin-top: 40px;
        margin-left: 50%;
        text-align: center;
    }
    
    .ttd-section p {
        margin: 5px 0;
    }
    
    .qr-code {
        margin: 10px 0;
    }
    
    .qr-code img {
        width: 120px;
        height: 120px;
    }
    
    .footer-info {
        margin-top: 30px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
        text-align: center;
        font-size: 11px;
        color: #666;
    }
    
    @media print {
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .no-print {
            display: none !important;
        }
        body {
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }
        .surat-container {
            box-shadow: none;
            padding: 15px 20px;
            margin: 0;
            max-width: 100%;
        }
        .kop-surat {
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .kop-surat img {
            height: 55px !important;
            margin-right: 10px;
        }
        .text-kop h2 {
            font-size: 13px !important;
            margin: 1px 0 !important;
        }
        .text-kop h3 {
            font-size: 11px !important;
            margin: 1px 0 !important;
        }
        .text-kop p {
            font-size: 8px !important;
            margin: 0.5px 0 !important;
        }
        .nomor-surat {
            margin: 10px 0;
        }
        .nomor-surat h4 {
            font-size: 12px !important;
            margin: 3px 0 !important;
        }
        .nomor-surat p {
            font-size: 10px !important;
        }
        .isi-surat {
            font-size: 10pt !important;
            line-height: 1.4 !important;
        }
        .isi-surat p {
            margin: 5px 0 !important;
        }
        .detail-table {
            font-size: 10pt !important;
            margin: 8px 0 !important;
        }
        .detail-table td {
            padding: 2px 0 !important;
        }
        .ttd-section {
            margin-top: 20px !important;
            page-break-inside: avoid;
        }
        .ttd-section p {
            margin: 2px 0 !important;
            font-size: 10pt !important;
        }
        .qr-code {
            margin: 5px 0 !important;
        }
        .qr-code img {
            width: 70px !important;
            height: 70px !important;
        }
        .footer-info {
            margin-top: 15px !important;
            padding-top: 10px !important;
            font-size: 8pt !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Preview Surat Peminjaman Mobil Dinas</h1>
            <p class="text-muted mb-0">Surat yang telah ditandatangani secara elektronik</p>
        </div>
        <div>
            <a href="{{ route('mahasiswa.riwayat.mobil_dinas') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button onclick="window.print()" class="btn btn-info me-2">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <a href="{{ route('mahasiswa.peminjaman.mobil.download', $peminjaman->id) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
        </div>
    </div>

    {{-- Surat Content --}}
    <div class="surat-container">
        {{-- Kop Surat --}}
        @php
            $fakultasNama = 'Fakultas Teknik';
            $fakultasAlamat = 'Jl. Raya Telang, PO. Box 2 Kamal, Bangkalan, Madura';
            
            if ($peminjaman->user->dosen && $peminjaman->user->dosen->jurusan) {
                $fakultasNama = $peminjaman->user->dosen->jurusan->fakultas->Nama_Fakultas ?? 'Fakultas Teknik';
            } elseif ($peminjaman->user->mahasiswa && $peminjaman->user->mahasiswa->prodi) {
                $fakultasNama = $peminjaman->user->mahasiswa->prodi->jurusan->fakultas->Nama_Fakultas ?? 'Fakultas Teknik';
            } elseif ($peminjaman->user->pegawaiFakultas && $peminjaman->user->pegawaiFakultas->fakultas) {
                $fakultasNama = $peminjaman->user->pegawaiFakultas->fakultas->Nama_Fakultas ?? 'Fakultas Teknik';
            }

            $wadek2User = \App\Models\User::where('Id_Role', 10)->first();
        @endphp
        
        <div class="kop-surat">
            <img src="{{ public_path('images/logo_unijoyo.png') }}" alt="Logo UTM" 
                 onerror="this.src='{{ asset('images/logo_unijoyo.png') }}'">
            <div class="text-kop">
                <h2>UNIVERSITAS TRUNOJOYO MADURA</h2>
                <h3>{{ strtoupper($fakultasNama) }}</h3>
                <p>{{ $fakultasAlamat }}</p>
                <p>Telp: (031) 3011146 | Email: info@trunojoyo.ac.id | Website: www.trunojoyo.ac.id</p>
            </div>
        </div>

        {{-- Nomor Surat --}}
        <div class="nomor-surat">
            <h4>SURAT PEMINJAMAN MOBIL DINAS</h4>
            <p><strong>Nomor: {{ $peminjaman->nomor_surat ?? '-' }}</strong></p>
        </div>

        {{-- Isi Surat --}}
        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini:</p>
            
            <table class="detail-table">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><strong>{{ $wadek2User->Name_User ?? '[Nama Wakil Dekan II]' }}</strong></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>Wakil Dekan II</td>
                </tr>
            </table>

            <p>Dengan ini memberikan izin peminjaman mobil dinas fakultas kepada:</p>

            <table class="detail-table">
                <tr>
                    <td>Nama Peminjam</td>
                    <td>:</td>
                    <td><strong>{{ $peminjaman->user->Name_User ?? '' }}</strong></td>
                </tr>
                <tr>
                    <td>NIM/NIP</td>
                    <td>:</td>
                    <td>{{ $peminjaman->user->mahasiswa->NIM ?? $peminjaman->user->Id_User }}</td>
                </tr>
                <tr>
                    <td>Program Studi/Unit</td>
                    <td>:</td>
                    <td>{{ $peminjaman->user->mahasiswa->prodi->Nama_Prodi ?? 'Fakultas' }}</td>
                </tr>
            </table>

            <p><strong>Dengan rincian sebagai berikut:</strong></p>

            <table class="detail-table">
                <tr>
                    <td>Kendaraan</td>
                    <td>:</td>
                    <td><strong>{{ $peminjaman->kendaraan->nama_kendaraan ?? '' }} ({{ $peminjaman->kendaraan->plat_nomor ?? '' }})</strong></td>
                </tr>
                <tr>
                    <td>Kapasitas</td>
                    <td>:</td>
                    <td>{{ $peminjaman->kendaraan->kapasitas ?? '' }} orang</td>
                </tr>
                <tr>
                    <td>Tujuan</td>
                    <td>:</td>
                    <td><strong>{{ $peminjaman->tujuan }}</strong></td>
                </tr>
                <tr>
                    <td>Keperluan</td>
                    <td>:</td>
                    <td>{{ $peminjaman->keperluan }}</td>
                </tr>
                <tr>
                    <td>Tanggal Pemakaian</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pemakaian_mulai)->format('d F Y, H:i') }} WIB</td>
                </tr>
                <tr>
                    <td>Sampai</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pemakaian_selesai)->format('d F Y, H:i') }} WIB</td>
                </tr>
                <tr>
                    <td>Jumlah Penumpang</td>
                    <td>:</td>
                    <td>{{ $peminjaman->jumlah_penumpang }} orang</td>
                </tr>
            </table>

            @if($peminjaman->rekomendasi_admin)
            <div class="mt-3">
                <p><strong>Catatan Admin:</strong><br>{{ $peminjaman->rekomendasi_admin }}</p>
            </div>
            @endif

            @if($peminjaman->catatan_wadek2)
            <div class="mt-3">
                <p><strong>Catatan Wakil Dekan II:</strong><br>{{ $peminjaman->catatan_wadek2 }}</p>
            </div>
            @endif

            <p class="mt-3">Demikian surat peminjaman mobil dinas ini dibuat untuk digunakan sebagaimana mestinya.</p>
        </div>

        {{-- Tanda Tangan --}}
        <div class="ttd-section">
            <p>{{ \Carbon\Carbon::parse($peminjaman->updated_at)->locale('id')->isoFormat('D MMMM Y') }}</p>
            <p><strong>Wakil Dekan II</strong></p>
            
            @php
                // Cek QR code dari verification atau fallback ke qr_code_path
                $qrPath = null;
                if($peminjaman->verification && $peminjaman->verification->qr_path) {
                    $qrPath = $peminjaman->verification->qr_path;
                } elseif($peminjaman->qr_code_path) {
                    $qrPath = $peminjaman->qr_code_path;
                }
            @endphp
            
            @if($qrPath)
            <div class="qr-code">
                <img src="{{ asset('storage/' . $qrPath) }}" alt="QR Code TTE">
            </div>
            @else
            <div style="height: 80px;"></div>
            @endif
            
            <p><strong><u>{{ $wadek2User->Name_User ?? '[Nama Wakil Dekan II]' }}</u></strong></p>
            <p>NIP. {{ $wadek2User->dosen->NIP ?? $wadek2User->pegawaiFakultas->NIP ?? '-' }}</p>
        </div>

        {{-- Footer --}}
        <div class="footer-info">
            <p><strong>Surat ini sah dan ditandatangani secara elektronik</strong></p>
            @if(($peminjaman->verification && $peminjaman->verification->qr_path) || $peminjaman->qr_code_path)
            <p>Scan QR Code untuk verifikasi keaslian surat</p>
            @endif
        </div>
    </div>
</div>
@endsection
