<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Surat Dispensasi</title>
    <style>
        @page {
            margin: 2cm 2.5cm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 11pt;
        }
        
        .title {
            text-align: center;
            margin: 30px 0 20px;
        }
        
        .title h3 {
            margin: 5px 0;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .nomor {
            text-align: center;
            margin-bottom: 25px;
            font-size: 11pt;
        }
        
        .content {
            text-align: justify;
            margin-bottom: 20px;
        }
        
        .data-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        
        .data-table td:nth-child(1) {
            width: 180px;
        }
        
        .data-table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .closing {
            margin-top: 30px;
        }
        
        .signature {
            margin-top: 40px;
            float: right;
            text-align: center;
            width: 250px;
        }
        
        .signature p {
            margin: 3px 0;
        }
        
        .qr-code {
            margin: 15px auto;
            width: 120px;
            height: 120px;
            display: block;
        }
        
        .signature-name {
            margin-top: 10px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .stempel {
            position: absolute;
            right: 30px;
            top: 80px;
            width: 140px;
            height: auto;
            opacity: 0.8;
            z-index: 10;
        }
        
        .clearfix::after {
            content: "";
            display: table;
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
        <h2>UNIVERSITAS TRUNOJOYO MADURA</h2>
        <p><strong>FAKULTAS TEKNIK</strong></p>
        <p style="font-size: 10pt;">
            Jl. Raya Telang PO Box 2 Kamal, Bangkalan - Madura<br>
            Telp: (031) 3011146, Fax. (031) 3011506<br>
            Website: www.trunojoyo.ac.id, E-mail: info@trunojoyo.ac.id
        </p>
    </div>

    {{-- Title --}}
    <div class="title">
        <h3>SURAT DISPENSASI</h3>
    </div>

    {{-- Nomor Surat --}}
    <div class="nomor">
        <strong>Nomor: {{ $nomor_surat }}</strong>
    </div>

    {{-- Content --}}
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, Wakil Dekan III Bidang Kemahasiswaan Fakultas Teknik Universitas Trunojoyo Madura, dengan ini memberikan dispensasi kepada:</p>
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
            <td>Angkatan</td>
            <td>:</td>
            <td>{{ $angkatan }}</td>
        </tr>
    </table>

    <div class="content">
        <p>Untuk tidak mengikuti perkuliahan/kegiatan akademik dikarenakan:</p>
    </div>

    {{-- Data Kegiatan --}}
    <table class="data-table">
        <tr>
            <td>Nama Kegiatan</td>
            <td>:</td>
            <td><strong>{{ $nama_kegiatan }}</strong></td>
        </tr>
        <tr>
            <td>Penyelenggara</td>
            <td>:</td>
            <td>{{ $instansi_penyelenggara }}</td>
        </tr>
        <tr>
            <td>Tempat Pelaksanaan</td>
            <td>:</td>
            <td>{{ $tempat_pelaksanaan }}</td>
        </tr>
        <tr>
            <td>Waktu Pelaksanaan</td>
            <td>:</td>
            <td><strong>{{ $tanggal_mulai }}</strong> s/d <strong>{{ $tanggal_selesai }}</strong></td>
        </tr>
    </table>

    {{-- Closing --}}
    <div class="content closing">
        <p>Demikian surat dispensasi ini dibuat untuk dapat digunakan sebagaimana mestinya. Atas perhatiannya kami ucapkan terima kasih.</p>
    </div>

    {{-- Signature with QR Code --}}
    <div class="clearfix">
        <div class="signature" style="position: relative;">
            <p>Bangkalan, {{ $tanggal_surat }}</p>
            <p><strong>Wakil Dekan III</strong></p>
            <p><strong>Bidang Kemahasiswaan</strong></p>
            
            {{-- Stempel --}}
            @php
                $stempel_path = public_path('images/stempel.png');
            @endphp
            @if(file_exists($stempel_path))
                <img src="{{ $stempel_path }}" alt="Stempel" class="stempel">
            @endif
            
            {{-- QR Code --}}
            @if(file_exists($qr_code_path))
                <img src="{{ $qr_code_path }}" alt="QR Code" class="qr-code" style="position: relative; z-index: 20;">
                <p style="font-size: 8pt; color: #666; margin: 5px 0;">
                    <em>Tanda Tangan Digital</em>
                </p>
            @endif
            
            <p class="signature-name">{{ $penandatangan_nama }}</p>
            <p>NIP. {{ $penandatangan_nip }}</p>
        </div>
    </div>
</body>
</html>
