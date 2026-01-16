<!DOCTYPE html>
<html>
<head>
    <title>Surat Peminjaman Mobil Dinas</title>
    <style>
        @page {
            margin: 1.5cm 2cm 1.5cm 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .kop-surat img {
            height: 70px;
            margin-right: 15px;
        }
        .text-kop {
            flex: 1;
            text-align: center;
        }
        .kop-surat h2 {
            margin: 2px 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .kop-surat h3 {
            margin: 2px 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .kop-surat p {
            margin: 1px 0;
            font-size: 9pt;
        }
        .nomor-surat {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
        }
        .isi-surat {
            text-align: justify;
            margin: 10px 0;
        }
        .isi-surat p {
            margin: 8px 0;
        }
        table.detail-peminjaman {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }
        table.detail-peminjaman td {
            padding: 3px;
            vertical-align: top;
        }
        table.detail-peminjaman td:first-child {
            width: 35%;
        }
        table.detail-peminjaman td:nth-child(2) {
            width: 5%;
        }
        .ttd-section {
            margin-top: 30px;
            margin-left: 50%;
            text-align: center;
        }
        .ttd-section p {
            margin: 3px 0;
        }
        .qr-code-ttd {
            width: 80px;
            height: 80px;
            margin: 5px auto;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    {{-- Kop Surat --}}
    @php
        // Ambil fakultas dari user yang login atau peminjam
        $fakultasNama = 'Fakultas Teknik'; // Default
        $fakultasAlamat = 'Jl. Raya Telang, PO. Box 2 Kamal, Bangkalan, Madura';
        
        if ($peminjaman->user->dosen && $peminjaman->user->dosen->jurusan) {
            $fakultasNama = $peminjaman->user->dosen->jurusan->fakultas->Nama_Fakultas ?? 'Fakultas Teknik';
        } elseif ($peminjaman->user->mahasiswa && $peminjaman->user->mahasiswa->prodi) {
            $fakultasNama = $peminjaman->user->mahasiswa->prodi->jurusan->fakultas->Nama_Fakultas ?? 'Fakultas Teknik';
        } elseif ($peminjaman->user->pegawaiFakultas && $peminjaman->user->pegawaiFakultas->fakultas) {
            $fakultasNama = $peminjaman->user->pegawaiFakultas->fakultas->Nama_Fakultas ?? 'Fakultas Teknik';
        }
    @endphp
    <div class="kop-surat">
        <img src="{{ public_path('images/logo_unijoyo.png') }}" alt="Logo UTM">
        <div class="text-kop">
            <h2>UNIVERSITAS TRUNOJOYO MADURA</h2>
            <h3>{{ strtoupper($fakultasNama) }}</h3>
            <p>{{ $fakultasAlamat }}</p>
            <p>Telp: (031) 3011146 | Email: info@trunojoyo.ac.id | Website: www.trunojoyo.ac.id</p>
        </div>
    </div>

    {{-- Nomor Surat --}}
    <div class="nomor-surat">
        <p>SURAT PEMINJAMAN MOBIL DINAS</p>
        <p>Nomor: <strong>{{ $peminjaman->nomor_surat ?? $peminjaman->tugasSurat->Nomor_Surat ?? '-' }}</strong></p>
    </div>

    {{-- Isi Surat --}}
    <div class="isi-surat">
        @php
            // Ambil data Wadek2 untuk bagian penandatangan
            $wadek2User = \App\Models\User::where('Id_Role', 10)->first();
        @endphp
        
        <p>Yang bertanda tangan di bawah ini:</p>
        
        <table class="detail-peminjaman">
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

        <table class="detail-peminjaman">
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

        <table class="detail-peminjaman">
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
        <p><strong>Catatan Admin:</strong><br>{{ $peminjaman->rekomendasi_admin }}</p>
        @endif

        <p>Demikian surat peminjaman mobil dinas ini dibuat untuk digunakan sebagaimana mestinya.</p>
    </div>

    {{-- Tanda Tangan --}}
    <div class="ttd-section">
        @php
            // Ambil data Wadek2 dari user yang login (untuk preview) atau dari database
            $wadek2User = null;
            
            // Coba ambil Wadek2 dari role 10
            $wadek2User = \App\Models\User::where('Id_Role', 10)->first();
            
            // Jika tidak ada, gunakan dari Id_Pejabat di peminjaman (ketika sudah disetujui)
            if ($peminjaman->Id_Pejabat && !$wadek2User) {
                $pejabat = \App\Models\Pejabat::find($peminjaman->Id_Pejabat);
                if ($pejabat) {
                    $wadek2User = $pejabat->user ?? null;
                }
            }
            
            // Ambil QR code path
            $qrPath = null;
            if($peminjaman->verification && $peminjaman->verification->qr_path) {
                $qrPath = storage_path('app/public/' . $peminjaman->verification->qr_path);
            } elseif($peminjaman->qr_code_path) {
                $qrPath = storage_path('app/public/' . $peminjaman->qr_code_path);
            }
        @endphp
        <p>{{ \Carbon\Carbon::parse($peminjaman->tugasSurat->Tanggal_Diselesaikan ?? now())->locale('id')->isoFormat('D MMMM Y') }}</p>
        <p><strong>Wakil Dekan II</strong></p>
        
        @if($qrPath && file_exists($qrPath))
            <img src="{{ $qrPath }}" class="qr-code-ttd" alt="QR Code">
        @else
            <div style="height: 80px;"></div>
        @endif
        
        @if($wadek2User)
            <p><strong><u>{{ $wadek2User->Name_User }}</u></strong></p>
            <p>NIP. {{ $wadek2User->dosen->NIP ?? $wadek2User->pegawaiFakultas->NIP ?? '-' }}</p>
        @else
            <p><strong><u>[Nama Wakil Dekan II]</u></strong></p>
            <p>NIP. [NIP]</p>
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Surat ini sah dan ditandatangani secara elektronik</p>
    </div>
</body>
</html>
