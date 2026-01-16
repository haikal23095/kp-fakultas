<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=850, user-scalable=yes">
    <title>{{ $jenisSurat->Nama_Surat ?? 'Surat Keterangan Tidak Menerima Beasiswa' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 2cm 2.5cm 1.5cm 2.5cm;
        }
        
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
                background: white;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-toolbar {
                display: none !important;
            }
            .content-wrapper {
                margin: 0;
                max-width: 100%;
                box-shadow: none;
                padding: 0;
                border-radius: 0;
            }
        }
        
        .print-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1f2937;
            padding: 14px 32px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #374151;
        }
        
        .print-toolbar h5 {
            margin: 0;
            color: white;
            font-size: 14pt;
            font-weight: 500;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .print-toolbar .btn-group {
            display: flex;
            gap: 10px;
        }
        
        .print-toolbar button {
            padding: 8px 20px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 10pt;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        .btn-print {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }
        
        .btn-print:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }
        
        .btn-close {
            background: white;
            color: #374151;
            border-color: #d1d5db;
        }
        
        .btn-close:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }
        
        .content-wrapper {
            margin-top: 100px;
            max-width: 21cm;
            margin-left: auto;
            margin-right: auto;
            background: white;
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
            padding: 1.5cm 2cm;
            border-radius: 8px;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .kop-surat {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        
        .kop-surat img {
            width: 80px;
            height: auto;
            flex-shrink: 0;
        }
        
        .kop-surat-text {
            flex: 1;
            text-align: center;
        }
        
        .kop-surat-text h2 {
            margin: 0;
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }
        
        .kop-surat-text h2:nth-child(2) {
            font-size: 13.5pt;
            margin-top: 1px;
        }
        
        .kop-surat-text h2:nth-child(3) {
            font-size: 12.5pt;
            margin-top: 1px;
        }
        
        .kop-surat-text p {
            margin: 6px 0 0 0;
            font-size: 9pt;
            line-height: 1.3;
        }
        
        .nomor-surat {
            text-align: center;
            margin: 20px 0 15px 0;
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
            margin: 15px 0 15px 40px;
        }
        
        .data-mahasiswa table {
            border-collapse: collapse;
        }
        
        .data-mahasiswa td {
            padding: 5px;
            vertical-align: top;
        }
        
        .data-mahasiswa td:first-child {
            width: 180px;
        }
        
        .data-mahasiswa td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .ttd-section {
            margin-top: 30px;
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
            border-radius: 6px;
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
            background: #f9fafb;
            padding: 10px 14px;
            border-radius: 4px;
            border-left: 2px solid #d1d5db;
        }
        
        .footer-note p {
            font-family: 'Times New Roman', serif;
            margin: 0;
            font-size: 8.5pt;
            color: #4b5563;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    {{-- TOOLBAR PRINT (HANYA TAMPIL DI BROWSER) --}}
    <div class="print-toolbar no-print">
        <h5>
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            {{ $jenisSurat->Nama_Surat ?? 'Surat Keterangan Tidak Menerima Beasiswa' }}
        </h5>
        <div class="btn-group">
            <button onclick="window.print()" class="btn-print">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block;vertical-align:middle;margin-right:6px">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn-close">
                Tutup
            </button>
        </div>
    </div>

    <div class="content-wrapper">
        {{-- KOP SURAT --}}
        @php
            $fakultasName = 'Fakultas Teknik';
            if ($mahasiswa && $mahasiswa->prodi && $mahasiswa->prodi->fakultas) {
                $fakultasName = $mahasiswa->prodi->fakultas->Nama_Fakultas;
            }
        @endphp
        
        <div class="kop-surat">
            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo Universitas">
            <div class="kop-surat-text">
                <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
                <h2>UNIVERSITAS TRUNODJOYO</h2>
                <h2>{{ strtoupper($fakultasName) }}</h2>
                <p>
                    Jl. Raya Telang, Perumahan Telang Inda, Telang, Kec. Kamal, Kabupaten Bangkalan, Jawa Timur 69162
                </p>
            </div>
        </div>

        {{-- NOMOR DAN JUDUL SURAT --}}
        <div class="nomor-surat">
            <h3>{{ $jenisSurat->Nama_Surat ?? 'SURAT KETERANGAN TIDAK MENERIMA BEASISWA' }}</h3>
            <p>Nomor: {{ $surat->Nomor_Surat ?? '[Nomor Surat]' }}</p>
        </div>

        {{-- ISI SURAT --}}
        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini:</p>
            
            <div class="data-mahasiswa">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td><strong>{{ $verification->signed_by ?? $surat->penerimaTugas->Name_User ?? '[Nama Penandatangan]' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>Wakil Dekan III Bidang Kemahasiswaan</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>{{ $fakultasName }}</td>
                    </tr>
                </table>
            </div>

            <p>Dengan ini menerangkan bahwa:</p>

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
                        <td>Angkatan</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->Angkatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <p>Adalah benar mahasiswa aktif {{ $fakultasName }}, Universitas Trunodjoyo yang bersangkutan <strong>TIDAK SEDANG MENERIMA BEASISWA</strong> dari pihak manapun baik dari pemerintah, swasta, maupun institusi lainnya.</p>
            
            <p style="margin-top: 15px;">Data Orang Tua/Wali:</p>
            
            <div class="data-mahasiswa">
                <table>
                    <tr>
                        <td>Nama Orang Tua/Wali</td>
                        <td>:</td>
                        <td>{{ $suratTidakBeasiswa->Nama_Orang_Tua ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $suratTidakBeasiswa->Pekerjaan_Orang_Tua ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Pendapatan per Bulan</td>
                        <td>:</td>
                        <td>Rp {{ number_format($suratTidakBeasiswa->Pendapatan_Orang_Tua ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @if($suratTidakBeasiswa->NIP_Orang_Tua)
                    <tr>
                        <td>NIP</td>
                        <td>:</td>
                        <td>{{ $suratTidakBeasiswa->NIP_Orang_Tua }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <p>
                Surat keterangan ini dibuat untuk keperluan: <strong>{{ $suratTidakBeasiswa->Keperluan ?? '-' }}</strong>
            </p>

            <p>
                Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
            </p>
        </div>

        {{-- TANDA TANGAN & QR CODE --}}
        <div class="ttd-section">
            <div class="ttd-content">
                <p>
                    Bangkalan, {{ $verification && $verification->signed_at ? 
                        \Carbon\Carbon::parse($verification->signed_at)->locale('id')->isoFormat('D MMMM YYYY') : 
                        \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') 
                    }}
                </p>
                <p><strong>Wakil Dekan III</strong></p>
                <p><strong>Bidang Kemahasiswaan</strong></p>
                
                {{-- QR CODE DIGITAL SIGNATURE --}}
                @if($verification && !empty($verification->qr_path))
                    @php
                        $parsed = parse_url($verification->qr_path);
                        $relativePath = ltrim($parsed['path'] ?? '', '/');
                        $absolutePath = public_path($relativePath);
                        
                        $qrImageSrc = '';
                        if (file_exists($absolutePath)) {
                            $imageData = base64_encode(file_get_contents($absolutePath));
                            $qrImageSrc = 'data:image/png;base64,' . $imageData;
                        } else {
                            $qrImageSrc = $verification->qr_path;
                        }
                    @endphp
                    <div class="qr-code-box">
                        <img src="{{ $qrImageSrc }}" 
                             alt="QR Code Digital Signature" 
                             style="width: 100px; height: 100px; display: block; margin: 0 auto;">
                        <div class="qr-info">
                            <p style="font-size: 8pt; margin-top: 8px; color: #6b7280; font-family: 'Times New Roman', serif;">Kode Verifikasi Digital</p>
                        </div>
                    </div>
                @else
                    <div style="height: 100px; text-align: center; color: #999; margin: 20px 0;">
                        <p><em>(Menunggu Tanda Tangan Digital)</em></p>
                    </div>
                @endif

                <div class="ttd-name">{{ $verification->signed_by ?? $surat->penerimaTugas->Name_User ?? '[Nama Wakil Dekan III]' }}</div>
                <div class="ttd-position">
                    NIP. 
                    @if($verification && $verification->penandatangan)
                        {{ $verification->penandatangan->pegawai->Nip_Pegawai ?? $verification->penandatangan->dosen->NIP ?? '-' }}
                    @elseif($surat->penerimaTugas && $surat->penerimaTugas->dosen)
                        {{ $surat->penerimaTugas->dosen->NIP ?? '-' }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        {{-- FOOTER NOTE --}}
        @if($verification && $verification->signed_at)
        <div class="footer-note">
            <p>
                Dokumen ini telah ditandatangani secara sah pada {{ \Carbon\Carbon::parse($verification->signed_at)->locale('id')->isoFormat('D MMMM YYYY') }}.
                @if($verification->token)
                    Untuk memverifikasi keaslian dokumen, silakan pindai kode QR di atas atau kunjungi: 
                    {{ route('surat.verify', $verification->token) }}
                @endif
            </p>
        </div>
        @endif
    </div>

</body>
</html>
