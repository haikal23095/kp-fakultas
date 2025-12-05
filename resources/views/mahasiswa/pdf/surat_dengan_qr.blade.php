<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {{-- Viewport diset fixed width agar tampilan di HP tetap seperti kertas A4 (zoom out) --}}
    <meta name="viewport" content="width=850, user-scalable=yes">
    <title>{{ $jenisSurat->Nama_Surat ?? 'Surat Resmi' }}</title>
    <style>
        @page {
            size: A4;
            margin: 2.5cm 2.5cm 2cm 2.5cm;
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
        
        .print-toolbar .btn-group {
            display: flex;
            gap: 10px;
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
        }
        
        .btn-print {
            background: #27ae60;
            color: white;
        }
        
        .btn-print:hover {
            background: #229954;
        }
        
        .btn-close {
            background: #e74c3c;
            color: white;
        }
        
        .btn-close:hover {
            background: #c0392b;
        }
        
        .content-wrapper {
            margin-top: 80px;
            width: 21cm; /* Fixed width A4 */
            margin-left: auto;
            margin-right: auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 2cm 2.5cm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .kop-surat {
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .kop-surat img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            flex-shrink: 0;
        }
        
        .kop-surat-text {
            flex: 1;
            text-align: center;
        }
        
        .kop-surat h2 {
            margin: 5px 0;
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .kop-surat p {
            margin: 2px 0;
            font-size: 11pt;
        }
        
        .nomor-surat {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        
        .nomor-surat h3 {
            margin: 5px 0;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .nomor-surat p {
            margin: 5px 0;
            font-size: 11pt;
        }
        
        .isi-surat {
            text-align: justify;
            margin: 20px 0;
        }
        
        .isi-surat p {
            margin: 10px 0;
        }
        
        .data-mahasiswa {
            margin: 20px 0 20px 40px;
        }
        
        .data-mahasiswa table {
            border-collapse: collapse;
        }
        
        .data-mahasiswa td {
            padding: 5px;
            vertical-align: top;
        }
        
        .data-mahasiswa td:first-child {
            width: 150px;
        }
        
        .data-mahasiswa td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .ttd-section {
            margin-top: 40px;
            text-align: right;
        }
        
        .ttd-content {
            display: inline-block;
            text-align: center;
            min-width: 250px;
        }
        
        .qr-code-box {
            margin: 20px auto 10px auto;
            display: block;
            text-align: center;
        }
        
        .qr-code-box img {
            width: 100px;
            height: 100px;
            border: 2px solid #333;
            padding: 8px;
            background: white;
            display: inline-block;
        }
        
        .qr-info {
            font-size: 8pt;
            color: #666;
            margin-top: 5px;
        }
        
        .ttd-name {
            margin-top: 10px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .ttd-position {
            margin-top: 5px;
            font-size: 11pt;
        }
        
        .footer-note {
            margin-top: 30px;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    {{-- TOOLBAR PRINT (HANYA TAMPIL DI BROWSER) --}}
    <div class="print-toolbar no-print">
        <h5>{{ $jenisSurat->Nama_Surat ?? 'Surat Resmi' }}</h5>
        <div class="btn-group">
            <button onclick="window.print()" class="btn-print">Download PDF</button>
            <button onclick="window.close()" class="btn-close">Tutup</button>
        </div>
    </div>

    <div class="content-wrapper">
    {{-- KOP SURAT --}}
    <div class="kop-surat">
        <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo Universitas">
        
        <div class="kop-surat-text">
            <h2>UNIVERSITAS TRUNOJOYO MADURA</h2>
            <h2>FAKULTAS TEKNIK</h2>
            <p>Jl. Raya Telang, PO Box 2 Kamal, Bangkalan - Madura</p>
            <p>Telp: (031) 3011146, Fax. (031) 3011506</p>
        </div>
    </div>

    {{-- NOMOR DAN JUDUL SURAT --}}
    <div class="nomor-surat">
        @if($surat->suratMagang)
            <h3>SURAT KETERANGAN</h3>
        @else
            <h3>{{ $jenisSurat->Nama_Surat ?? 'SURAT KETERANGAN' }}</h3>
        @endif
        <p>Nomor: {{ $surat->Nomor_Surat ?? '.......' }}/FT/{{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('m/Y') }}</p>
    </div>

    {{-- ISI SURAT --}}
    <div class="isi-surat">
        <p>Yang bertanda tangan di bawah ini:</p>
        
        <div class="data-mahasiswa">
            <table>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><strong>{{ $verification->signed_by ?? 'Dekan Fakultas Teknik' }}</strong></td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>Dekan Fakultas Teknik</td>
                </tr>
            </table>
        </div>

        <p>Dengan ini menerangkan bahwa:</p>

        @if($surat->suratMagang)
            @php
                $dataMahasiswa = $surat->suratMagang->Data_Mahasiswa;
                if (is_string($dataMahasiswa)) {
                    $dataMahasiswa = json_decode($dataMahasiswa, true);
                }
                if (!is_array($dataMahasiswa)) {
                    $dataMahasiswa = [];
                }
            @endphp
            
            @foreach($dataMahasiswa as $mhs)
            <div class="data-mahasiswa">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td><strong>{{ $mhs['nama'] ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>NIM</td>
                        <td>:</td>
                        <td><strong>{{ $mhs['nim'] ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>:</td>
                        <td>{{ $mhs['program-studi'] ?? 'Teknik Informatika' }}</td>
                    </tr>
                    <tr>
                        <td>Fakultas</td>
                        <td>:</td>
                        <td>Fakultas Teknik</td>
                    </tr>
                </table>
            </div>
            @endforeach
        @else
            <div class="data-mahasiswa">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td><strong>{{ $mahasiswa->Nama_Mahasiswa ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>NIM</td>
                        <td>:</td>
                        <td><strong>{{ $mahasiswa->NIM ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->prodi->Nama_Prodi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Fakultas</td>
                        <td>:</td>
                        <td>Fakultas Teknik</td>
                    </tr>
                </table>
            </div>
        @endif

        @if($surat->jenisSurat && str_contains(strtolower($surat->jenisSurat->Nama_Surat), 'aktif'))
            <p>
                Adalah benar mahasiswa aktif di Fakultas Teknik dan terdaftar sebagai mahasiswa pada 
                Semester {{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('Y/Y') }} 
                Tahun Akademik {{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('Y') }}.
            </p>
        @elseif($surat->suratMagang)
            <p>
                Mahasiswa tersebut di atas akan melaksanakan kegiatan Kerja Praktik/Magang di:
            </p>
            <div class="data-mahasiswa">
                <table>
                    <tr>
                        <td>Nama Instansi</td>
                        <td>:</td>
                        <td><strong>{{ $surat->suratMagang->Nama_Instansi ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Periode</td>
                        <td>:</td>
                        <td>
                            {{ $surat->suratMagang->Tanggal_Mulai ? \Carbon\Carbon::parse($surat->suratMagang->Tanggal_Mulai)->format('d M Y') : '-' }}
                            s.d.
                            {{ $surat->suratMagang->Tanggal_Selesai ? \Carbon\Carbon::parse($surat->suratMagang->Tanggal_Selesai)->format('d M Y') : '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        @else
            <p>{{ $surat->Judul_Tugas_Surat }}</p>
        @endif

        <p>
            Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
        </p>
    </div>

    {{-- TANDA TANGAN & QR CODE --}}
    <div class="ttd-section">
        <div class="ttd-content">
            <p>
                {{ $verification->signed_at ? 
                    \Carbon\Carbon::parse($verification->signed_at)->locale('id')->isoFormat('D MMMM YYYY') : 
                    \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') 
                }}
            </p>
            <p><strong>Dekan Fakultas Teknik</strong></p>
            
            {{-- QR CODE DIGITAL SIGNATURE --}}
            @php
                // Prioritas: QR Code Dekan untuk Surat Magang, atau QR dari verification
                $qrCodeToDisplay = null;
                
                // Jika ini Surat Magang dan ada Qr_code_dekan, gunakan itu
                if($surat->suratMagang && !empty($surat->suratMagang->Qr_code_dekan)) {
                    $qrPath = $surat->suratMagang->Qr_code_dekan;
                    
                    // Handle path storage
                    if (!file_exists(public_path($qrPath)) && file_exists(public_path('storage/' . $qrPath))) {
                        $qrPath = 'storage/' . $qrPath;
                    }
                    
                    $absoluteQrPath = public_path($qrPath);
                    
                    if (file_exists($absoluteQrPath)) {
                        $imageData = base64_encode(file_get_contents($absoluteQrPath));
                        $qrCodeToDisplay = 'data:image/png;base64,' . $imageData;
                    } else {
                        $qrCodeToDisplay = asset($qrPath);
                    }
                }
                // Fallback ke verification->qr_path jika tidak ada QR Dekan
                elseif($verification && !empty($verification->qr_path)) {
                    $parsed = parse_url($verification->qr_path);
                    $relativePath = ltrim($parsed['path'] ?? '', '/');
                    $absolutePath = public_path($relativePath);
                    
                    if (file_exists($absolutePath)) {
                        $imageData = base64_encode(file_get_contents($absolutePath));
                        $qrCodeToDisplay = 'data:image/png;base64,' . $imageData;
                    } else {
                        $qrCodeToDisplay = $verification->qr_path;
                    }
                }
            @endphp
            
            @if($qrCodeToDisplay)
                <div class="qr-code-box">
                    <img src="{{ $qrCodeToDisplay }}" 
                         alt="QR Code Digital Signature" 
                         style="width: 150px; height: 150px; display: block; margin: 0 auto;">
                    <div class="qr-info">
                        <p style="font-size: 9pt; margin-top: 5px;">Scan untuk verifikasi keaslian dokumen</p>
                    </div>
                </div>
            @else
                <div style="height: 100px; text-align: center; color: #999; margin: 20px 0;">
                    <p><em>(Menunggu Tanda Tangan Digital)</em></p>
                </div>
            @endif

            <div class="ttd-name">{{ $verification->signed_by ?? '[Nama Dekan]' }}</div>
            <div class="ttd-position">NIP. {{ $verification->penandatangan->pegawai->Nip_Pegawai ?? $verification->penandatangan->dosen->NIP ?? '-' }}</div>
        </div>
    </div>

    {{-- FOOTER NOTE --}}
    <div class="footer-note">
        <p>
            <strong>Informasi Verifikasi Digital:</strong><br>
            @if($verification)
                Dokumen ini telah ditandatangani secara digital pada 
                {{ $verification->signed_at ? \Carbon\Carbon::parse($verification->signed_at)->format('d M Y, H:i:s') : '-' }} WIB.<br>
                Verifikasi keaslian dokumen: <strong>{{ route('surat.verify', $verification->token) }}</strong><br>
                Token Verifikasi: <code>{{ substr($verification->token, 0, 16) }}...{{ substr($verification->token, -8) }}</code>
            @else
                Dokumen ini adalah salinan resmi dari sistem informasi akademik.
            @endif
        </p>
    </div>
    </div>{{-- End content-wrapper --}}

</body>
</html>
