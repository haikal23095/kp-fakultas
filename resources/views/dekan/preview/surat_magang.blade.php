<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview Surat Pengantar Magang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        @page { size: A4; margin: 2cm 2.2cm 1.8cm 2.5cm; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.45; background:#f5f5f5; margin:0; }
        .toolbar { position:fixed; top:0; left:0; right:0; background:#1f2937; color:#fff; padding:14px 28px; display:flex; justify-content:space-between; align-items:center; font-family: Arial, sans-serif; z-index:1000; }
        .toolbar h5 { margin:0; font-size:14px; font-weight:600; display:flex; align-items:center; gap:8px; }
        .toolbar button { border:1px solid #374151; background:#fff; color:#1f2937; padding:6px 16px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:500; }
        .toolbar button.print { background:#2563eb; color:#fff; border-color:#2563eb; }
        .container { max-width:21cm; margin:100px auto 40px; background:#fff; box-shadow:0 8px 32px rgba(0,0,0,0.12); padding:1.6cm 1.8cm; border-radius:8px; }
        .kop { display:flex; align-items:flex-start; gap:18px; border-bottom:3px solid #000; padding-bottom:14px; margin-bottom:22px; }
        .kop img { width:80px; height:auto; }
        .kop-title { flex:1; text-align:center; }
        .kop-title h2 { margin:0; font-size:12pt; font-weight:bold; text-transform:uppercase; line-height:1.25; }
        .kop-title h2 + h2 { margin-top:2px; }
        .kop-title p { margin:8px 0 0 0; font-size:9pt; }
        .title-block { text-align:center; margin:10px 0 20px; }
        .title-block h3 { margin:0 0 6px; font-size:14pt; font-weight:bold; text-decoration:underline; }
        .title-block p { margin:0; font-size:11pt; }
        table.meta { width:100%; border-collapse:collapse; margin-bottom:20px; }
        table.meta td { padding:4px 2px; vertical-align:top; }
        table.meta td:first-child { width:28%; }
        table.meta td:nth-child(2) { width:2%; }
        .qr-box { text-align:center; margin:22px 0 8px; }
        .qr-box img { width:100px; height:100px; border:2px solid #333; padding:6px; background:#fff; }
        .section { margin-top:22px; }
        .ttd-wrapper { margin-top:40px; display:flex; justify-content:space-between; }
        .ttd-pane { width:48%; text-align:center; }
        .footer-note { margin-top:28px; font-size:8.5pt; color:#555; border-top:1px solid #ccc; padding-top:10px; }
        @media print { body { background:#fff; } .toolbar { display:none !important; } .container { margin:0; box-shadow:none; border-radius:0; } }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <h5>Pratinjau Surat Pengantar Magang</h5>
        <div style="display:flex; gap:10px;">
            <button class="print" onclick="window.print()">Cetak / Simpan PDF</button>
            <button onclick="window.close()">Tutup</button>
        </div>
    </div>

    @php
        $suratMagang = $surat->suratMagang; // Child model
        $fakultasName = $mahasiswa && $mahasiswa->prodi && $mahasiswa->prodi->fakultas ? $mahasiswa->prodi->fakultas->Nama_Fakultas : 'Fakultas Teknik';
        $dataMahasiswa = is_array($suratMagang?->Data_Mahasiswa) ? $suratMagang->Data_Mahasiswa : json_decode($suratMagang?->Data_Mahasiswa ?? '[]', true);
        $dosenPembimbing = is_array($suratMagang?->Data_Dosen_pembiming) ? $suratMagang->Data_Dosen_pembiming : json_decode($suratMagang?->Data_Dosen_pembiming ?? '[]', true);
    @endphp

    <div class="container">
        <div class="kop">
            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo Universitas" />
            <div class="kop-title">
                <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
                <h2>UNIVERSITAS TRUNOJOYO MADURA</h2>
                <h2>{{ strtoupper($fakultasName) }}</h2>
                <p>Jl. Raya Telang, Perumahan Telang Indah, Telang, Kamal - Bangkalan 69162</p>
            </div>
        </div>

        <div class="title-block">
            <h3>SURAT PENGANTAR MAGANG / KP</h3>
            <p>Nomor: {{ $surat->Nomor_Surat ?? '[Belum Diberi Nomor]' }}</p>
        </div>

        <table class="meta">
            <tr>
                <td>Nama Mahasiswa</td><td>:</td>
                <td>
                    @foreach($dataMahasiswa as $i => $m)
                        {{ $i+1 }}. {{ $m['nama'] ?? '-' }} ({{ $m['nim'] ?? '-' }})<br>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>Program Studi</td><td>:</td><td>{{ $mahasiswa->prodi->Nama_Prodi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Dosen Pembimbing</td><td>:</td><td>{{ $dosenPembimbing['dosen_pembimbing_1'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Instansi Tujuan</td><td>:</td><td><strong>{{ $suratMagang->Nama_Instansi ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Periode Magang</td><td>:</td>
                <td>
                    @if($suratMagang?->Tanggal_Mulai && $suratMagang?->Tanggal_Selesai)
                        {{ \Carbon\Carbon::parse($suratMagang->Tanggal_Mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($suratMagang->Tanggal_Selesai)->format('d M Y') }}
                    @else - @endif
                </td>
            </tr>
        </table>

        <div class="section">
            <p>Yang bertanda tangan di bawah ini menyatakan bahwa mahasiswa tersebut benar terdaftar dan diperkenankan untuk melaksanakan kegiatan Kerja Praktek / Magang pada instansi yang dituju sesuai periode yang tercantum.</p>
            @if($suratMagang?->Deskripsi)
                <p>Keperluan / Fokus kegiatan: <strong>{{ $suratMagang->Deskripsi }}</strong></p>
            @endif
            <p>Demikian surat pengantar ini dibuat agar dapat digunakan sebagaimana mestinya.</p>
        </div>

        <div class="ttd-wrapper">
            <div class="ttd-pane">
                <p>Menyetujui,<br>Koordinator KP/Magang</p>
                @if($suratMagang?->Qr_code)
                    <div class="qr-box">
                        <img src="{{ asset('storage/' . $suratMagang->Qr_code) }}" alt="QR Koordinator" />
                    </div>
                @else
                    <div style="height:100px; display:flex; align-items:center; justify-content:center; color:#777; border:1px dashed #aaa; margin:20px 0;">Menunggu Persetujuan</div>
                @endif
                <p class="fw-bold" style="font-weight:bold; margin:0;">{{ $suratMagang->koordinator->Nama_Dosen ?? '[Nama Koordinator]' }}</p>
                <p style="margin:4px 0 0;">NIP. {{ $suratMagang->koordinator->NIP ?? '...' }}</p>
            </div>
            <div class="ttd-pane">
                <p>Bangkalan, {{ \Carbon\Carbon::now()->format('d M Y') }}<br>Pemohon</p>
                @if($suratMagang?->Foto_ttd)
                    <div class="qr-box" style="margin-top:0;">
                        <img src="{{ asset('storage/' . $suratMagang->Foto_ttd) }}" alt="TTD Pemohon" style="width:100px; height:60px; border:none; padding:0;" />
                    </div>
                @else
                    <div style="height:60px;">&nbsp;</div>
                @endif
                <p class="fw-bold" style="font-weight:bold; margin:0;">{{ $mahasiswa->Nama_Mahasiswa ?? '[Nama Mahasiswa]' }}</p>
                <p style="margin:4px 0 0;">NIM. {{ $mahasiswa->NIM ?? '-' }}</p>
            </div>
        </div>

        @if($verification && $verification->qr_path)
            @php
                $parsed = parse_url($verification->qr_path);
                $relative = ltrim($parsed['path'] ?? '', '/');
                $abs = public_path($relative);
                $qrSignedSrc = file_exists($abs) ? 'data:image/png;base64,' . base64_encode(file_get_contents($abs)) : $verification->qr_path;
            @endphp
            <div class="qr-box" style="margin-top:30px;">
                <img src="{{ $qrSignedSrc }}" alt="QR Tanda Tangan Dekan" />
                <div style="font-size:8pt; color:#666; margin-top:4px;">QR Verifikasi Tanda Tangan Dekan</div>
            </div>
        @else
            <div style="text-align:center; margin-top:30px; font-size:9pt; color:#666;">(Menunggu Tanda Tangan Digital Dekan)</div>
        @endif

        @if($verification && $verification->signed_at)
            <div class="footer-note">
                Dokumen ditandatangani pada {{ \Carbon\Carbon::parse($verification->signed_at)->locale('id')->isoFormat('D MMMM YYYY') }}.
                @if($verification->token) Verifikasi: pindai QR atau akses sistem. @endif
            </div>
        @endif
    </div>
</body>
</html>
