<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {{-- Viewport diset fixed width agar tampilan di HP tetap seperti kertas A4 (zoom out) --}}
    <meta name="viewport" content="width=850, user-scalable=yes">
    <title>Surat Pengantar Magang</title>
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
        
        .ttd-name {
            margin-top: 10px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .ttd-position {
            margin-top: 5px;
            font-size: 11pt;
        }

        /* Default Desktop/Print Style for TTD */
        .ttd-section {
            display: flex; 
            justify-content: space-between; 
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <div class="print-toolbar no-print">
        <h5>Surat Pengantar Magang</h5>
        <button onclick="window.print()">Download PDF</button>
    </div>

    <div class="content-wrapper">
        <div class="kop-surat">
            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo Universitas">
            <div class="kop-surat-text">
                <h2>UNIVERSITAS TRUNOJOYO MADURA</h2>
                <h2>FAKULTAS TEKNIK</h2>
                <p>Jl. Raya Telang, PO Box 2 Kamal, Bangkalan - Madura</p>
                <p>Telp: (031) 3011146, Fax. (031) 3011506</p>
            </div>
        </div>

        <div class="nomor-surat">
            <h3>SURAT PENGANTAR PERMOHONAN KERJA PRAKTIK</h3>
            {{-- Nomor Surat Pengantar biasanya belum ada nomor resmi dari admin --}}
            {{-- Gunakan format sementara atau kosongkan jika belum ada nomor --}}
            <p>Nomor: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/KP/{{ \Carbon\Carbon::now()->format('Y') }}</p>
        </div>

        <div class="isi-surat">
            <p>Yth. Pimpinan HRD/Personalia<br>
            <strong>{{ $magang->Nama_Instansi }}</strong><br>
            {{ $magang->Alamat_Instansi }}</p>

            <p>Dengan hormat,</p>
            <p>Sehubungan dengan pelaksanaan mata kuliah Kerja Praktik (KP), kami memohon kesediaan Bapak/Ibu untuk menerima mahasiswa kami berikut ini untuk melaksanakan Kerja Praktik di instansi yang Bapak/Ibu pimpin:</p>

            @php
                $dataMahasiswa = $magang->Data_Mahasiswa;
                if (is_string($dataMahasiswa)) {
                    $dataMahasiswa = json_decode($dataMahasiswa, true);
                }
                if (!is_array($dataMahasiswa)) {
                    $dataMahasiswa = [];
                }
            @endphp

            <table border="1" style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <thead>
                    <tr style="background: #f0f0f0;">
                        <th style="padding: 8px; text-align: center;">No</th>
                        <th style="padding: 8px; text-align: left;">Nama</th>
                        <th style="padding: 8px; text-align: center;">NIM</th>
                        <th style="padding: 8px; text-align: center;">Program Studi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataMahasiswa as $index => $mhs)
                    <tr>
                        <td style="padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                        <td style="padding: 8px;">{{ $mhs['nama'] ?? '-' }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $mhs['nim'] ?? '-' }}</td>
                        <td style="padding: 8px; text-align: center;">{{ $mhs['program-studi'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Waktu pelaksanaan: 
                <strong>{{ $magang->Tanggal_Mulai ? \Carbon\Carbon::parse($magang->Tanggal_Mulai)->format('d M Y') : '-' }}</strong> s.d. 
                <strong>{{ $magang->Tanggal_Selesai ? \Carbon\Carbon::parse($magang->Tanggal_Selesai)->format('d M Y') : '-' }}</strong>
            </p>

            <p>Demikian surat permohonan ini kami sampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
        </div>

        <div class="ttd-section">
            {{-- Tanda Tangan Mahasiswa (Pemohon) --}}
            <div class="ttd-content" style="text-align: center; min-width: 200px;">
                <p>&nbsp;</p>
                <p><strong>Pemohon</strong></p>
                
                @if($magang->Foto_ttd)
                    @php
                        // Handle Foto TTD Mahasiswa path
                        $ttdPath = $magang->Foto_ttd;
                        
                        // Cek path storage
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
                    <div class="qr-code-box" style="margin: 10px auto;">
                        <img src="{{ $ttdImageSrc }}" alt="TTD Mahasiswa" style="width: 100px; height: auto; max-height: 100px;">
                    </div>
                @else
                    <div style="height: 100px;"></div>
                @endif

                {{-- Ambil nama mahasiswa pertama (Ketua/Pemohon) --}}
                @php
                    $namaPemohon = $dataMahasiswa[0]['nama'] ?? 'Mahasiswa';
                    $nimPemohon = $dataMahasiswa[0]['nim'] ?? '-';
                @endphp
                <div class="ttd-name">{{ $namaPemohon }}</div>
                <div class="ttd-position">NIM. {{ $nimPemohon }}</div>
            </div>

            {{-- Tanda Tangan Dekan (dengan QR Code Dekan) --}}
            <div class="ttd-content" style="text-align: center; min-width: 200px;">
                <p>Bangkalan, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                <p><strong>Dekan Fakultas Teknik</strong></p>
                
                @if($magang->Acc_Dekan && $magang->Qr_code_dekan)
                    @php
                        // Handle QR Code Dekan path
                        $qrDekanPath = $magang->Qr_code_dekan;
                        
                        if (!file_exists(public_path($qrDekanPath)) && file_exists(public_path('storage/' . $qrDekanPath))) {
                            $qrDekanPath = 'storage/' . $qrDekanPath;
                        }
                        
                        $absoluteDekanPath = public_path($qrDekanPath);
                        
                        $qrDekanSrc = '';
                        if (file_exists($absoluteDekanPath)) {
                            $dekanData = base64_encode(file_get_contents($absoluteDekanPath));
                            $qrDekanSrc = 'data:image/png;base64,' . $dekanData;
                        } else {
                            $qrDekanSrc = asset($qrDekanPath);
                        }
                    @endphp
                    <div class="qr-code-box" style="margin: 10px auto;">
                        <img src="{{ $qrDekanSrc }}" alt="QR Code Dekan" style="width: 120px; height: 120px;">
                    </div>
                @else
                    <div style="height: 120px; text-align: center; padding: 20px 0;">
                        <p style="font-size: 10pt; color: #999; font-style: italic;">Menunggu Persetujuan Dekan</p>
                    </div>
                @endif

                <div class="ttd-name">{{ $magang->dekan->Nama_Dosen ?? '[Nama Dekan]' }}</div>
                <div class="ttd-position">NIP. {{ $magang->Nip_Dekan ?? ($magang->dekan->NIP ?? '-') }}</div>
            </div>
        </div>
    </div>

</body>
</html>
