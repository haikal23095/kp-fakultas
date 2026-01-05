<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Dispensasi</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            position: relative;
        }
        
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: auto;
        }
        
        .header-text {
            margin-left: 70px;
        }
        
        .header h2 {
            margin: 1px 0;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }
        
        .header p {
            margin: 1px 0;
            font-size: 9pt;
            line-height: 1.1;
        }
        
        .title {
            text-align: center;
            margin: 15px 0 10px;
        }
        
        .title h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .nomor {
            text-align: center;
            margin-bottom: 12px;
            font-size: 10pt;
            font-weight: bold;
        }
        
        .content {
            text-align: justify;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .data-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 10pt;
        }
        
        .data-table td:nth-child(1) {
            width: 140px;
        }
        
        .data-table td:nth-child(2) {
            width: 15px;
            text-align: center;
        }
        
        .closing {
            margin-top: 15px;
            line-height: 1.5;
        }
        
        .signature {
            margin-top: 20px;
            float: right;
            text-align: center;
            width: 200px;
            position: relative;
        }
        
        .signature p {
            margin: 2px 0;
            font-size: 10pt;
        }
        
        .qr-stempel-wrapper {
            position: relative;
            height: 100px;
            margin: 10px 0 5px;
        }
        
        .qr-code {
            position: absolute;
            width: 80px;
            height: 80px;
            left: 50%;
            transform: translateX(-50%);
            top: 8px;
            z-index: 1;
        }
        
        .stempel {
            position: absolute;
            width: 115px;
            height: auto;
            left: 50%;
            transform: translateX(-50%);
            top: 0;
            opacity: 0.75;
            z-index: 2;
        }
        
        .signature-name {
            margin-top: 3px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .footer-note {
            margin-top: 40px;
            font-size: 8pt;
            font-style: italic;
            color: #666;
            clear: both;
        }
    </style>
</head>
<body>
    {{-- Header with Logo --}}
    <div class="header">
        @if(file_exists($logo_path))
            <img src="{{ $logo_path }}" alt="Logo" class="logo">
        @endif
        <div class="header-text">
            <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</h2>
            <h2>RISET, DAN TEKNOLOGI</h2>
            <h2>UNIVERSITAS TRUNOJOYO MADURA</h2>
            <h2>FAKULTAS TEKNIK</h2>
            <p>Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura</p>
            <p>Telp : (031) 3011146, Fax. (031) 3011506</p>
            <p>Laman : <span style="color: blue;">ft.trunojoyo.ac.id</span>, E-mail: info@trunojoyo.ac.id</p>
        </div>
    </div>

    {{-- Nomor Surat --}}
    <div class="nomor">
        Nomor: {{ $nomor_surat }}
    </div>

    {{-- Title --}}
    <div class="title">
        <h3>SURAT DISPENSASI</h3>
    </div>

    {{-- Content --}}
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Wakil Dekan III Bidang Kemahasiswaan Fakultas Teknik Universitas Trunojoyo Madura, dengan ini menerangkan bahwa:</p>
    </div>

    {{-- Data Mahasiswa --}}
    <table class="data-table">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td><strong>{{ $nama_mahasiswa }}</strong></td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td><strong>{{ $nim }}</strong></td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>{{ $prodi }}</td>
        </tr>
        <tr>
            <td>Fakultas</td>
            <td>:</td>
            <td>Fakultas Teknik</td>
        </tr>
    </table>

    <div class="content">
        <p>Mahasiswa tersebut diberikan <strong>dispensasi perkuliahan</strong> pada tanggal <strong>{{ $tanggal_mulai }}</strong> sampai dengan <strong>{{ $tanggal_selesai }}</strong> dikarenakan:</p>
    </div>

    {{-- Data Kegiatan --}}
    <table class="data-table">
        <tr>
            <td>Nama Kegiatan</td>
            <td>:</td>
            <td><strong>{{ $nama_kegiatan }}</strong></td>
        </tr>
        @if($instansi_penyelenggara && $instansi_penyelenggara !== '-')
        <tr>
            <td>Penyelenggara</td>
            <td>:</td>
            <td>{{ $instansi_penyelenggara }}</td>
        </tr>
        @endif
        @if($tempat_pelaksanaan && $tempat_pelaksanaan !== '-')
        <tr>
            <td>Tempat Pelaksanaan</td>
            <td>:</td>
            <td>{{ $tempat_pelaksanaan }}</td>
        </tr>
        @endif
        <tr>
            <td>Waktu Pelaksanaan</td>
            <td>:</td>
            <td><strong>{{ $tanggal_mulai }}</strong> s/d <strong>{{ $tanggal_selesai }}</strong></td>
        </tr>
    </table>

    {{-- Closing --}}
    <div class="content closing">
        <p>Demikian surat dispensasi ini dibuat untuk dapat dipergunakan sebagaimana mestinya. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
    </div>

    {{-- Signature with QR Code & Stempel --}}
    <div class="clearfix">
        <div class="signature">
            <p>Bangkalan, {{ $tanggal_surat }}</p>
            <p><strong>Wakil Dekan III,</strong></p>
            <p><strong>Bidang Kemahasiswaan</strong></p>
            
            {{-- QR Code & Stempel Wrapper --}}
            <div class="qr-stempel-wrapper">
                {{-- QR Code di bawah --}}
                @if(file_exists($qr_code_path))
                    <img src="{{ $qr_code_path }}" alt="QR Code" class="qr-code">
                @endif
                {{-- Stempel di atas (numpuk) --}}
                @php
                    $stempel_path = public_path('images/stempel.png');
                @endphp
                @if(file_exists($stempel_path))
                    <img src="{{ $stempel_path }}" alt="Stempel" class="stempel">
                @endif
            </div>
            
            <p class="signature-name">{{ $penandatangan_nama }}</p>
            <p>NIP. {{ $penandatangan_nip }}</p>
        </div>
    </div>

    {{-- Footer Note --}}
    <div class="footer-note">
        <p>Catatan: Surat ini dibuat secara otomatis melalui Sistem Manajemen Surat Fakultas Teknik UTM</p>
    </div>
</body>
</html>
