<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $jenisSurat->Nama_Surat ?? 'Surat Keterangan Berkelakuan Baik' }}</title>
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
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .kop-surat img {
            width: 80px;
            height: auto;
            float: left;
            margin-right: 15px;
        }
        
        .kop-surat .text-kop {
            text-align: center;
        }
        
        .kop-surat h2 {
            margin: 0 0 3px 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .kop-surat h3 {
            margin: 0 0 3px 0;
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .kop-surat p {
            margin: 2px 0;
            font-size: 10pt;
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
        
        .ttd-nip {
            margin-top: 5px;
            font-size: 11pt;
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
                background: white;
                padding: 0;
                margin: 0;
            }
            .content-wrapper {
                margin: 0;
                max-width: 100%;
                box-shadow: none;
                padding: 0;
                border-radius: 0;
            }
            .footer-note {
                page-break-inside: avoid;
                margin-top: 20px;
            }
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
            Surat Keterangan Berkelakuan Baik
        </h5>
        <div class="btn-group">
            <button onclick="window.print()" class="btn-print">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block;vertical-align:middle;margin-right:6px">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <rect x="6" y="11" width="12" height="9"></rect>
                    <rect x="6" y="17" width="12" height="3"></rect>
                </svg>
                Cetak Dokumen
            </button>
            <button onclick="window.close()" class="btn-close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block;vertical-align:middle;margin-right:6px">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Tutup
            </button>
        </div>
    </div>

    {{-- KONTEN SURAT --}}
    <div class="content-wrapper">
        
        {{-- KOP SURAT --}}
        <div class="kop-surat">
            @if(file_exists(public_path('images/logo_unijoyo.png')))
                <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo Universitas">
            @endif
            <div class="text-kop">
                <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
                <h3>UNIVERSITAS TRUNODJOYO</h3>
                <h3>FAKULTAS TEKNIK</h3>
                <p>Jl. Raya Telang, PO Box 2 Kamal, Bangkalan - Madura</p>
                <p>Telp: (031) 3011146, Fax. (031) 3011506</p>
                <p>Website: www.trunojoyo.ac.id | Email: humas@trunojoyo.ac.id</p>
            </div>
        </div>

        {{-- NOMOR SURAT --}}
        <div class="nomor-surat">
            <h3>SURAT KETERANGAN BERKELAKUAN BAIK</h3>
            <p>Nomor: {{ $surat->Nomor_Surat ?? '......................../UN46.2/KM/'.date('Y') }}</p>
        </div>

        {{-- ISI SURAT --}}
        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini:</p>
            
            <div class="data-mahasiswa">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td>
                            @if($verification && $verification->penandatangan)
                                @if($verification->penandatangan->dosen)
                                    {{ $verification->penandatangan->dosen->Nama_Dosen }}
                                @elseif($verification->penandatangan->pegawai)
                                    {{ $verification->penandatangan->pegawai->Nama_Pegawai }}
                                @else
                                    {{ $verification->signed_by }}
                                @endif
                            @elseif($suratKelakuanBaik && $suratKelakuanBaik->Nama_Pejabat)
                                {{ $suratKelakuanBaik->Nama_Pejabat }}
                            @else
                                [Nama Wakil Dekan III]
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>NIP</td>
                        <td>:</td>
                        <td>
                            @if($verification && $verification->penandatangan)
                                @if($verification->penandatangan->dosen)
                                    {{ $verification->penandatangan->dosen->NIP }}
                                @elseif($verification->penandatangan->pegawai)
                                    {{ $verification->penandatangan->pegawai->Nip_Pegawai }}
                                @else
                                    -
                                @endif
                            @elseif($suratKelakuanBaik && $suratKelakuanBaik->NIP_Pejabat)
                                {{ $suratKelakuanBaik->NIP_Pejabat }}
                            @else
                                [NIP Wakil Dekan III]
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>Wakil Dekan III Bidang Kemahasiswaan</td>
                    </tr>
                </table>
            </div>

            <p>Menerangkan bahwa:</p>

            <div class="data-mahasiswa">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td><strong>{{ $mahasiswa->Nama_Mahasiswa ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td>NIM</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->NIM ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->prodi->Nama_Prodi ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Angkatan</td>
                        <td>:</td>
                        <td>{{ $mahasiswa->Angkatan ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Semester</td>
                        <td>:</td>
                        <td>{{ $suratKelakuanBaik->Semester ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Tahun Akademik</td>
                        <td>:</td>
                        <td>{{ $suratKelakuanBaik->Tahun_Akademik ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            <p>
                Adalah benar mahasiswa Fakultas Teknik Universitas Trunodjoyo yang memiliki <strong>KELAKUAN BAIK</strong> 
                dan tidak pernah terlibat dalam pelanggaran tata tertib/kode etik mahasiswa selama menjalani pendidikan 
                di Universitas Trunodjoyo.
            </p>

            <p>
                Surat keterangan ini dibuat untuk keperluan: <strong>{{ $suratKelakuanBaik->Keperluan ?? 'N/A' }}</strong>
            </p>

            <p>
                Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
            </p>
        </div>

        {{-- TANDA TANGAN --}}
        <div class="ttd-section">
            <div class="ttd-content">
                <p style="margin-bottom: 5px;">
                    Bangkalan, 
                    @if($verification && $verification->signed_at)
                        {{ \Carbon\Carbon::parse($verification->signed_at)->translatedFormat('d F Y') }}
                    @elseif($suratKelakuanBaik && $suratKelakuanBaik->Tanggal_TTD)
                        {{ \Carbon\Carbon::parse($suratKelakuanBaik->Tanggal_TTD)->translatedFormat('d F Y') }}
                    @else
                        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    @endif
                </p>
                <p style="font-weight: 600; margin-bottom: 10px;">Wakil Dekan III</p>
                
                {{-- QR CODE TANDA TANGAN ELEKTRONIK --}}
                @if($verification && $verification->qr_path)
                    <div class="qr-code-box">
                        <img src="{{ asset($verification->qr_path) }}" alt="QR Code Verifikasi">
                        <div class="qr-info">
                            <small>Dokumen ditandatangani secara elektronik</small><br>
                            <small>Scan QR code untuk verifikasi keaslian</small>
                        </div>
                    </div>
                @elseif($suratKelakuanBaik && $suratKelakuanBaik->Qr_Code)
                    <div class="qr-code-box">
                        <img src="{{ asset('storage/' . $suratKelakuanBaik->Qr_Code) }}" alt="QR Code Verifikasi">
                        <div class="qr-info">
                            <small>Dokumen ditandatangani secara elektronik</small><br>
                            <small>Scan QR code untuk verifikasi keaslian</small>
                        </div>
                    </div>
                @else
                    <div style="height: 80px; margin: 20px 0;">
                        <p style="font-size: 9pt; color: #999; font-style: italic;">
                            [Menunggu tanda tangan elektronik]
                        </p>
                    </div>
                @endif
                
                <div class="ttd-name">
                    @if($verification && $verification->penandatangan)
                        @if($verification->penandatangan->dosen)
                            {{ $verification->penandatangan->dosen->Nama_Dosen }}
                        @elseif($verification->penandatangan->pegawai)
                            {{ $verification->penandatangan->pegawai->Nama_Pegawai }}
                        @else
                            {{ $verification->signed_by }}
                        @endif
                    @elseif($suratKelakuanBaik && $suratKelakuanBaik->Nama_Pejabat)
                        {{ $suratKelakuanBaik->Nama_Pejabat }}
                    @else
                        [Nama Wakil Dekan III]
                    @endif
                </div>
                
                <div class="ttd-nip">
                    NIP. 
                    @if($verification && $verification->penandatangan)
                        @if($verification->penandatangan->dosen)
                            {{ $verification->penandatangan->dosen->NIP }}
                        @elseif($verification->penandatangan->pegawai)
                            {{ $verification->penandatangan->pegawai->Nip_Pegawai }}
                        @else
                            -
                        @endif
                    @elseif($suratKelakuanBaik && $suratKelakuanBaik->NIP_Pejabat)
                        {{ $suratKelakuanBaik->NIP_Pejabat }}
                    @else
                        [NIP Wakil Dekan III]
                    @endif
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer-note">
            <p style="margin: 5px 0;">
                <strong>Catatan:</strong> Dokumen ini diterbitkan secara elektronik dan dilengkapi dengan 
                tanda tangan elektronik berupa QR Code untuk menjamin keaslian dokumen.
            </p>
            <p style="margin: 5px 0;">
                Untuk verifikasi keaslian dokumen, silakan scan QR Code di atas atau kunjungi 
                <strong>{{ url('/verify') }}</strong>
            </p>
        </div>
        
    </div>

</body>
</html>
