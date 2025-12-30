<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=850, user-scalable=yes">
    <title>Form Surat Pengantar Magang</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .print-toolbar {
                display: none !important;
            }
        }
        
        .print-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #2c3e50;
            padding: 12px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #34495e;
        }
        
        .print-toolbar h5 {
            margin: 0;
            color: white;
            font-size: 14pt;
            font-weight: 600;
            font-family: Arial, sans-serif;
        }
        
        .print-toolbar button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-family: Arial, sans-serif;
            background: #27ae60;
            color: white;
        }
        
        .print-toolbar button:hover {
            background: #229954;
        }
        
        .content-wrapper {
            margin-top: 80px;
            width: 21cm;
            margin-left: auto;
            margin-right: auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .preview-header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
        }
        
        .preview-header img {
            width: 120px;
            float: left;
            margin-top: -12px;
        }
        
        .preview-header strong {
            display: block;
            text-transform: uppercase;
        }
        
        .preview-header .line-1 { font-size: 13pt; }
        .preview-header .line-2 { font-size: 15pt; }
        .preview-header .line-3 { font-size: 15pt; }
        
        .preview-header .address {
            font-size: 10pt;
            font-style: italic;
            margin-top: 5px;
        }
        
        .preview-title {
            font-weight: bold;
            font-size: 14pt;
            margin-top: 25px;
            text-align: center;
            text-decoration: underline;
        }
        
        .preview-table {
            margin-top: 15px;
            width: 100%;
            font-size: 12pt;
        }
        
        .preview-table td {
            padding: 2px 0px;
            vertical-align: top;
        }
        
        .preview-table td:nth-child(1) { width: 30%; }
        .preview-table td:nth-child(2) { width: 2%; }
        .preview-table td:nth-child(3) {
            width: 68%;
            word-break: break-word;
        }
        
        .preview-magang-section {
            margin-top: 10px;
        }
        
        .preview-signature {
            font-size: 12pt;
            margin-top: 30px;
        }
        
        .signature-box {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }
        
        .signature-left {
            float: left;
            text-align: left;
        }
        
        .signature-right {
            float: right;
            text-align: center;
        }
        
        .signature-area {
            height: 100px;
            display: flex;
            align-items: center;
        }
        
        .signature-left .signature-area {
            justify-content: flex-start;
        }
        
        .signature-right .signature-area {
            justify-content: center;
        }
        
        .signature-area img {
            max-width: 150px;
            max-height: 100px;
        }
        
        .qr-code-box {
            display: inline-block;
            padding: 5px;
            background: white;
        }
        
        .qr-code-box img {
            width: 100px;
            height: 100px;
            display: block;
        }
    </style>
</head>
<body>

    <div class="print-toolbar no-print">
        <h5>Form Surat Pengantar (TTD Mahasiswa & QR Kaprodi)</h5>
        <button onclick="window.print()">Cetak / Download PDF</button>
    </div>

    <div class="content-wrapper">
        {{-- Header Pratinjau --}}
        <div class="preview-header">
            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo Universitas Trunojoyo Madura">
            <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong>
            <strong class="line-2">UNIVERSITAS TRUNOJOYO MADURA</strong>
            <strong class="line-3">FAKULTAS TEKNIK</strong>
            <div class="address">
                Sekretariat: Kampus Unijoyo PO Box 2 Telang Kamal Telp 031 7011147 Fax. 031 7011506
            </div>
            <div style="clear: both;"></div>
        </div>

        {{-- Judul --}}
        <p class="preview-title">FORM PENGAJUAN SURAT PENGANTAR</p>

        @php
            $dataMahasiswa = $magang->Data_Mahasiswa;
            if (is_string($dataMahasiswa)) {
                $dataMahasiswa = json_decode($dataMahasiswa, true);
            }
            if (!is_array($dataMahasiswa)) {
                $dataMahasiswa = [];
            }
        @endphp

        {{-- Tabel Data --}}
        <table class="preview-table">
            <tr>
                <td style="vertical-align: top;">Nama</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    @foreach($dataMahasiswa as $index => $mhs)
                        <div>
                            <strong>{{ $index + 1 }}. {{ $mhs['nama'] ?? '-' }}</strong><br>
                            <small>NIM: {{ $mhs['nim'] ?? '-' }} | Angkatan: {{ $mhs['angkatan'] ?? '-' }}</small>
                        </div>
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>Jurusan</td>
                <td>:</td>
                <td>{{ $mahasiswa->prodi->jurusan->Nama_Jurusan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Dosen Pembimbing</td>
                <td>:</td>
                <td>{{ $koordinator->Nama_Dosen ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Surat Pengantar*</td>
                <td style="vertical-align: top;">:</td>
                <td>
                    1. Pengantar Kerja Praktek<br>
                    2. Pengantar TA<br>
                    3. Pengantar Dosen Pembimbing I TA<br>
                    4. Magang
                </td>
            </tr>
            <tr>
                <td>Instansi/Perusahaan</td>
                <td>:</td>
                <td>{{ $magang->Nama_Instansi }}<br>
                    <small>{{ $magang->Alamat_Instansi }}</small>
                </td>
            </tr>
        </table>

        {{-- Bagian Khusus Magang --}}
        <div class="preview-magang-section">
            <strong><u>Isian berikut utk pengantar Magang</u></strong>
            <table class="preview-table" style="margin-top: 0;">
                <tr>
                    <td>Judul Penelitian</td>
                    <td>:</td>
                    <td>{{ $magang->Judul_Penelitian }}</td>
                </tr>
                <tr>
                    <td>Jangka waktu penelitian</td>
                    <td>:</td>
                    <td>
                        {{ $magang->Tanggal_Mulai ? \Carbon\Carbon::parse($magang->Tanggal_Mulai)->format('d M Y') : '-' }} 
                        s.d. 
                        {{ $magang->Tanggal_Selesai ? \Carbon\Carbon::parse($magang->Tanggal_Selesai)->format('d M Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td>Identitas Surat Balasan**</td>
                    <td>:</td>
                    <td></td>
                </tr>
            </table>
        </div>

        {{-- Tanda Tangan --}}
        <div class="preview-signature">
            <div class="signature-left signature-box">
                <p class="mb-1">Menyetujui<br>Koordinator KP/TA</p>
                <div class="signature-area">
                    @if($magang->Qr_code)
                        @php
                            $qrPath = $magang->Qr_code;
                            
                            // Coba beberapa kemungkinan path
                            $possiblePaths = [
                                public_path('storage/' . $qrPath),
                                storage_path('app/public/' . $qrPath),
                                public_path($qrPath),
                            ];
                            
                            $absoluteQrPath = null;
                            
                            foreach ($possiblePaths as $path) {
                                if (file_exists($path)) {
                                    $absoluteQrPath = $path;
                                    break;
                                }
                            }
                            
                            $qrImageSrc = '';
                            if ($absoluteQrPath && file_exists($absoluteQrPath)) {
                                $extension = strtolower(pathinfo($absoluteQrPath, PATHINFO_EXTENSION));
                                
                                if ($extension === 'svg') {
                                    $svgContent = file_get_contents($absoluteQrPath);
                                    $qrImageSrc = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                } else {
                                    $imageData = base64_encode(file_get_contents($absoluteQrPath));
                                    $mimeType = $extension === 'png' ? 'image/png' : 'image/jpeg';
                                    $qrImageSrc = 'data:' . $mimeType . ';base64,' . $imageData;
                                }
                            } else {
                                $qrImageSrc = asset('storage/' . $qrPath);
                            }
                        @endphp
                        <div class="qr-code-box">
                            <img src="{{ $qrImageSrc }}" alt="QR Code Kaprodi">
                        </div>
                    @else
                        <p style="color: #999; font-style: italic; font-size: 10pt;">Menunggu Persetujuan</p>
                    @endif
                </div>
                <p class="mb-0">( {{ $koordinator->Nama_Dosen ?? '[Nama Kaprodi]' }} )</p>
                <p>NIP. {{ $koordinator->NIP ?? '-' }}</p>
            </div>
            
            <div class="signature-right signature-box">
                <p class="mb-1">Bangkalan, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                <p class="mb-1">Pemohon</p>
                <div class="signature-area">
                    @if($magang->Foto_ttd)
                        @php
                            $ttdPath = $magang->Foto_ttd;
                            
                            if (!file_exists(public_path($ttdPath)) && file_exists(public_path('storage/' . $ttdPath))) {
                                $ttdPath = 'storage/' . $ttdPath;
                            }
                            
                            $absoluteTtdPath = public_path($ttdPath);
                            
                            $ttdImageSrc = '';
                            if (file_exists($absoluteTtdPath)) {
                                $ttdData = base64_encode(file_get_contents($absoluteTtdPath));
                                $ttdImageSrc = 'data:image/png;base64,' . $ttdData;
                            } else {
                                $ttdImageSrc = asset($ttdPath);
                            }
                        @endphp
                        <img src="{{ $ttdImageSrc }}" alt="TTD Mahasiswa">
                    @endif
                </div>
                @php
                    $namaPemohon = $dataMahasiswa[0]['nama'] ?? 'Mahasiswa';
                    $nimPemohon = $dataMahasiswa[0]['nim'] ?? '-';
                @endphp
                <p class="mb-0">( {{ $namaPemohon }} )</p>
                <p class="mt-1">NIM. {{ $nimPemohon }}</p>
            </div>
            <div style="clear: both;"></div>
        </div>

        <hr style="border-top: 1px dashed #000; margin-top: 15px;">
        <small style="font-size: 10pt;">
            Cat: *Tulis alamat Instansi/perusahaan yg dituju<br>
            **Diisi untuk permohonan kedua dan seterusnya
        </small>
    </div>

</body>
</html>
