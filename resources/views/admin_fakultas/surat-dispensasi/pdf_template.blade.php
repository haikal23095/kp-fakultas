<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Dispensasi - {{ $nim }}</title>
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
        
        /* Header Surat */
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .header img.logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 100px;
            height: auto;
        }
        
        .header-text {
            margin-left: 120px;
        }
        
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 16pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        
        .header h3 {
            font-size: 16pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        
        .header p {
            font-size: 10pt;
            margin: 5px 0;
            font-style: italic;
        }
        
        /* Nomor dan Tanggal Surat */
        .nomor-surat {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        
        .nomor-surat .nomor {
            font-weight: bold;
            font-size: 12pt;
        }
        
        /* Judul Surat */
        .judul {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 14pt;
            margin: 20px 0;
            text-transform: uppercase;
        }
        
        /* Isi Surat */
        .isi {
            text-align: justify;
            margin: 20px 0;
        }
        
        .isi p {
            margin: 10px 0;
        }
        
        /* Tabel Data Mahasiswa */
        .data-table {
            width: 100%;
            margin: 20px 0;
        }
        
        .data-table td {
            padding: 5px 10px;
            vertical-align: top;
        }
        
        .data-table td:nth-child(1) {
            width: 30%;
        }
        
        .data-table td:nth-child(2) {
            width: 2%;
        }
        
        .data-table td:nth-child(3) {
            width: 68%;
        }
        
        /* Tanda Tangan */
        .ttd-section {
            margin-top: 40px;
            width: 100%;
        }
        
        .ttd-kiri {
            float: left;
            width: 45%;
            text-align: center;
        }
        
        .ttd-kanan {
            float: right;
            width: 45%;
            text-align: center;
        }
        
        .ttd-space {
            height: 80px;
        }
        
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 5px;
        }
        
        .ttd-jabatan {
            font-weight: bold;
        }
        
        /* Stempel */
        .stempel {
            position: absolute;
            right: 100px;
            margin-top: -60px;
            opacity: 0.8;
        }
        
        .stempel img {
            width: 150px;
            height: auto;
        }
        
        .clearfix {
            clear: both;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            font-size: 10pt;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>

    {{-- Header Surat --}}
    <div class="header">
        <img src="{{ $logo_path }}" alt="Logo" class="logo">
        <div class="header-text">
            <h1>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h1>
            <h2>UNIVERSITAS TRUNOJOYO MADURA</h2>
            <h3>FAKULTAS TEKNIK</h3>
            <p>Sekretariat: Kampus Unijoyo PO Box 2 Telang Kamal Telp 031 7011147 Fax. 031 7011506</p>
        </div>
    </div>

    {{-- Nomor Surat --}}
    <div class="nomor-surat">
        <span class="nomor">Nomor: {{ $nomor_surat }}</span>
    </div>

    {{-- Judul Surat --}}
    <div class="judul">
        SURAT DISPENSASI
    </div>

    {{-- Isi Surat Pembuka --}}
    <div class="isi">
        <p>Yang bertanda tangan di bawah ini Wakil Dekan III Fakultas Teknik Universitas Trunojoyo Madura, menerangkan bahwa:</p>
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

    {{-- Isi Dispensasi --}}
    <div class="isi">
        <p>
            Adalah benar mahasiswa tersebut di atas telah mengajukan permohonan <strong>dispensasi kehadiran kuliah</strong> 
            pada tanggal <strong>{{ $tanggal_mulai }}</strong> sampai dengan <strong>{{ $tanggal_selesai }}</strong> 
            dengan alasan/keperluan:
        </p>
        
        <p style="margin-left: 40px;">
            <strong>"{{ $nama_kegiatan }}"</strong>
        </p>

        @if($instansi_penyelenggara && $instansi_penyelenggara !== '-')
        <p>
            yang diselenggarakan oleh <strong>{{ $instansi_penyelenggara }}</strong>
            @if($tempat_pelaksanaan && $tempat_pelaksanaan !== '-')
                di <strong>{{ $tempat_pelaksanaan }}</strong>
            @endif.
        </p>
        @endif

        <p>
            Surat dispensasi ini diberikan untuk digunakan sebagaimana mestinya. Demikian surat ini dibuat dengan 
            sebenarnya agar dapat dipergunakan sebagaimana mestinya.
        </p>
    </div>

    {{-- Tanda Tangan --}}
    <div class="ttd-section">
        <div class="ttd-kanan">
            <p style="margin: 0;">Bangkalan, {{ $tanggal_surat }}</p>
            <p class="ttd-jabatan" style="margin: 5px 0;">Wakil Dekan III</p>
            <div class="ttd-space"></div>
            
            {{-- Placeholder untuk TTD Wadek3 --}}
            <div style="height: 20px; margin-top: 10px;">
                <p style="margin: 0; font-size: 10pt; font-style: italic; color: #666;">
                    ( TTD akan ditambahkan oleh Wadek 3 )
                </p>
            </div>
            
            {{-- Nama Wadek3 akan diisi setelah ACC --}}
            <p class="ttd-nama" style="margin: 10px 0;">
                _____________________
            </p>
            <p style="margin: 0; font-size: 11pt;">
                NIP. __________________
            </p>
        </div>
        
        <div class="clearfix"></div>
    </div>

    {{-- Stempel (di pojok kanan bawah) --}}
    <div class="stempel">
        <img src="{{ $stempel_path }}" alt="Stempel Fakultas">
    </div>

    {{-- Footer Notes --}}
    <div class="footer">
        <p style="margin-top: 60px;">
            <em>Catatan: Surat ini dicetak secara otomatis melalui Sistem Manajemen Surat Fakultas Teknik UTM</em>
        </p>
    </div>

</body>
</html>
