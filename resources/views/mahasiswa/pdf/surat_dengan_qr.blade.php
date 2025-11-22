<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .print-toolbar h5 {
            margin: 0;
            color: white;
            font-size: 16pt;
            font-weight: bold;
        }
        
        .print-toolbar .btn-group {
            display: flex;
            gap: 10px;
        }
        
        .print-toolbar button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 11pt;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            font-family: Arial, sans-serif;
        }
        
        .btn-print {
            background: #28a745;
            color: white;
        }
        
        .btn-print:hover {
            background: #218838;
        }
        
        .btn-close {
            background: #dc3545;
            color: white;
        }
        
        .btn-close:hover {
            background: #c82333;
        }
        
        .content-wrapper {
            margin-top: 80px;
            max-width: 21cm;
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
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .kop-surat img {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
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
        
        .digital-signature-badge {
            display: inline-block;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: bold;
            margin-top: 10px;
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
        <h5>
            <i style="margin-right: 10px;">📄</i>
            {{ $jenisSurat->Nama_Surat ?? 'Surat Resmi' }}
        </h5>
        <div class="btn-group">
            <button onclick="window.print()" class="btn-print">
                🖨️ Download PDF
            </button>
            <button onclick="window.close()" class="btn-close">
                ✖️ Tutup
            </button>
        </div>
    </div>

    <div class="content-wrapper">
    {{-- KOP SURAT --}}
    <div class="kop-surat">
        {{-- Jika ada logo universitas, uncomment baris berikut --}}
        {{-- <img src="{{ public_path('images/logo-univ.png') }}" alt="Logo"> --}}
        
        <h2>UNIVERSITAS [NAMA UNIVERSITAS]</h2>
        <h2>FAKULTAS TEKNIK</h2>
        <p>Jl. Alamat Universitas No. 123, Kota, Provinsi 12345</p>
        <p>Telp: (021) 1234-5678 | Email: fakultas@univ.ac.id | Website: www.ft.univ.ac.id</p>
    </div>

    {{-- NOMOR DAN JUDUL SURAT --}}
    <div class="nomor-surat">
        <h3>{{ $jenisSurat->Nama_Surat ?? 'SURAT KETERANGAN' }}</h3>
        <p>Nomor: {{ $surat->Id_Tugas_Surat }}/FT/{{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('m/Y') }}</p>
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
                    <td><strong>{{ $mahasiswa->Nim_Mahasiswa ?? '-' }}</strong></td>
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
                        <td><strong>{{ $surat->suratMagang->nama_instansi ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Periode</td>
                        <td>:</td>
                        <td>
                            {{ $surat->suratMagang->tanggal_mulai ? \Carbon\Carbon::parse($surat->suratMagang->tanggal_mulai)->format('d M Y') : '-' }}
                            s.d.
                            {{ $surat->suratMagang->tanggal_selesai ? \Carbon\Carbon::parse($surat->suratMagang->tanggal_selesai)->format('d M Y') : '-' }}
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
            @if($verification && !empty($verification->qr_path))
                <div class="qr-code-box">
                    <img src="{{ $verification->qr_path }}" 
                         alt="QR Code Digital Signature" 
                         style="width: 150px; height: 150px; display: block; margin: 0 auto;">
                    <div class="qr-info">
                        <div class="digital-signature-badge">
                            ✓ DIGITAL SIGNATURE
                        </div>
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
