<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Pembimbing Skripsi - {{ $sk->Nomor_Surat ?? 'Download' }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm 2.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            position: relative;
        }

        .header img {
            width: 80px;
            position: absolute;
            left: 0;
            top: -5px;
        }

        .header-text {
            text-transform: uppercase;
            font-weight: bold;
        }

        .header-main {
            font-size: 14pt;
        }

        .header-address {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 5px;
        }

        .title {
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
        }

        .content {
            text-align: justify;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .content table {
            width: 100%;
            margin-bottom: 15px;
        }

        .content td {
            vertical-align: top;
        }

        .content ol {
            margin: 0;
            padding-left: 20px;
        }

        .signature {
            margin-top: 40px;
            text-align: right;
            font-size: 10pt;
        }

        .signature img {
            width: 100px;
            height: 100px;
            margin: 10px 0;
            border: 1px solid #000;
        }

        .lampiran {
            page-break-before: always;
            margin-top: 30px;
        }

        .lampiran-header {
            font-size: 9pt;
            margin-bottom: 10px;
        }

        .lampiran-title {
            text-align: center;
            font-weight: bold;
            margin: 10px 0;
        }

        .mhs-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin: 15px 0;
            font-size: 10pt;
        }

        .mhs-table th,
        .mhs-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }

        .mhs-table th {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .lampiran {
                page-break-before: always;
            }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 100);">
    
    <!-- Header -->
    <div class="header">
        <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
        <div class="header-text" style="font-size: 12pt;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</div>
        <div class="header-text header-main">UNIVERSITAS TRUNODJOYO</div>
        <div class="header-text">FAKULTAS TEKNIK</div>
        <div class="header-address">
            Kampus UTM, Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
            Telp: (031) 3011146, Fax: (031) 3011506
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
        UNIVERSITAS TRUNODJOYO<br>
        NOMOR {{ $sk->Nomor_Surat ?? '-' }}
    </div>

    <div class="title" style="margin: 15px 0;">TENTANG</div>

    @php
        $semesterUpper = strtoupper($sk->Semester ?? 'GANJIL');
        $namaProdi = $sk->prodi ? strtoupper($sk->prodi->Nama_Prodi) : 'FAKULTAS TEKNIK';
    @endphp

    <div class="title">
        PENETAPAN DOSEN PEMBIMBING SKRIPSI<br>
        PROGRAM STUDI S1 {{ $namaProdi }}<br>
        FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO<br>
        SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}
    </div>

    <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
        DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO,
    </div>

    <!-- Content -->
    <div class="content">
        <p style="margin-bottom: 10px; font-weight: normal;">Menimbang</p>
        <table>
            <tr>
                <td style="width: 10%;">:</td>
                <td style="width: 5%;">a.</td>
                <td>Bahwa untuk memperlancar penyusunan Skripsi mahasiswa, perlu menugaskan dosen sebagai pembimbing Skripsi;</td>
            </tr>
            <tr>
                <td></td>
                <td>b.</td>
                <td>Bahwa untuk melaksanakan butir a di atas, perlu ditetapkan dalam Keputusan Dekan Fakultas Teknik;</td>
            </tr>
        </table>

        <p style="margin-bottom: 10px; font-weight: normal;">Mengingat</p>
        <table>
            <tr>
                <td style="width: 10%;">:</td>
                <td style="width: 5%;">1.</td>
                <td>Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</td>
            </tr>
            <tr>
                <td></td>
                <td>2.</td>
                <td>Peraturan Pemerintah Nomor 4 Tahun 2012 Tentang Penyelenggaraan Pendidikan Tinggi;</td>
            </tr>
            <tr>
                <td></td>
                <td>3.</td>
                <td>Peraturan Presiden RI Nomor 4 Tahun 2014 Tentang Perubahan Penyelenggaraan dan Pengelolaan Perguruan Tinggi;</td>
            </tr>
            <tr>
                <td></td>
                <td>4.</td>
                <td>Keputusan RI Nomor 85 tahun 2001, tentang Statuta Universitas Trunodjoyo;</td>
            </tr>
            <tr>
                <td></td>
                <td>5.</td>
                <td>Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/ U/ 2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</td>
            </tr>
            <tr>
                <td></td>
                <td>6.</td>
                <td>Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi RI Nomor 79/M/MPK.A/ KP.09.02/ 2022 tentang pengangkatan Rektor UTM periode 2022-2026;</td>
            </tr>
            <tr>
                <td></td>
                <td>7.</td>
                <td>Keputusan Rektor Universitas Trunodjoyo Nomor 1357/UNM3/KP/ 2023 tentang Pengangkatan Pejabat Struktural Dekan Fakultas Teknik;</td>
            </tr>
        </table>

        @php
            $dataPembimbing = $sk->Data_Pembimbing_Skripsi;
            if (is_string($dataPembimbing)) {
                $dataPembimbing = json_decode($dataPembimbing, true);
            }
            if (!is_array($dataPembimbing)) {
                $dataPembimbing = [];
            }

            // Group by jurusan
            $groupedByJurusan = [];
            foreach ($dataPembimbing as $mhs) {
                $jurusanName = isset($mhs['prodi_data']['jurusan']['Nama_Jurusan']) 
                    ? $mhs['prodi_data']['jurusan']['Nama_Jurusan'] 
                    : (isset($mhs['prodi']) ? $mhs['prodi'] : '-');
                
                if (!isset($groupedByJurusan[$jurusanName])) {
                    $groupedByJurusan[$jurusanName] = [];
                }
                $groupedByJurusan[$jurusanName][] = $mhs;
            }
        @endphp

        <p style="margin-bottom: 10px; font-weight: normal;">Memperhatikan</p>
        <table>
            <tr>
                <td style="width: 10%;">:</td>
                <td style="width: 5%;">1.</td>
                <td>Surat dari Ketua Jurusan di lingkungan Fakultas Teknik tentang permohonan SK Dosen Pembimbing Skripsi.</td>
            </tr>
        </table>

        <div style="text-align: center; margin: 15px 0; font-weight: bold;">MEMUTUSKAN :</div>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Menetapkan</td>
                <td style="width: 3%;">:</td>
                <td style="font-weight: bold;">PENETAPAN DOSEN PEMBIMBING SKRIPSI PROGRAM STUDI S1 {{ $namaProdi }} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}.</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Kesatu</td>
                <td style="width: 3%;">:</td>
                <td>Dosen Pembimbing Skripsi sebagaimana tercantum dalam lampiran Keputusan ini;</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Kedua</td>
                <td style="width: 3%;">:</td>
                <td>Keputusan ini berlaku sejak tanggal ditetapkan.</td>
            </tr>
        </table>
    </div>

    <!-- Signature -->
    <div class="signature">
        <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
        <p style="margin: 0 0 10px 0;">pada tanggal 
            @if($sk->Tanggal_Persetujuan_Dekan)
                {{ \Carbon\Carbon::parse($sk->Tanggal_Persetujuan_Dekan)->isoFormat('D MMMM Y') }}
            @else
                {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
            @endif
        </p>
        <p style="margin: 0 0 10px 0;"><strong>DEKAN,</strong></p>
        @if($sk->QR_Code)
            <img src="{{ asset('storage/' . $sk->QR_Code) }}" alt="QR Code">
        @else
            <div style="height: 100px;"></div>
        @endif
        <p style="margin: 0 0 0 0;">
            <strong><u>{{ $dekanName }}</u></strong><br>
            NIP. {{ $dekanNip }}
        </p>
    </div>

    <!-- Lampiran sections -->
    @foreach($groupedByJurusan as $jurusanName => $mahasiswa)
    <div class="lampiran">
        <div class="lampiran-header">
            <p style="margin: 0 0 3px 0;">SALINAN</p>
            <p style="margin: 0 0 3px 0;">LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
            <p style="margin: 0 0 3px 0;">NOMOR {{ $sk->Nomor_Surat ?? '-' }}</p>
            <p style="margin: 0 0 10px 0;">TENTANG</p>
            <p style="margin: 0 0 10px 0;">PENETAPAN DOSEN PEMBIMBING SKRIPSI PROGRAM STUDI {{ $namaProdi }} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}</p>
        </div>
        
        <div class="lampiran-title">
            DAFTAR MAHASISWA DAN DOSEN PEMBIMBING SKRIPSI<br>
            PROGRAM STUDI {{ $namaProdi }} FAKULTAS TEKNIK<br>
            UNIVERSITAS TRUNODJOYO SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}
        </div>

        <table class="mhs-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">NIM</th>
                    <th style="width: 20%;">Nama Mahasiswa</th>
                    <th style="width: 20%;">Judul Skripsi</th>
                    <th style="width: 20%;">Pembimbing 1</th>
                    <th style="width: 20%;">Pembimbing 2</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswa as $index => $mhs)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center;">{{ $mhs['nim'] ?? '-' }}</td>
                    <td>{{ $mhs['nama_mahasiswa'] ?? '-' }}</td>
                    <td><small>{{ $mhs['judul_skripsi'] ?? '-' }}</small></td>
                    <td><small>{{ $mhs['pembimbing_1']['nama_dosen'] ?? '-' }}</small></td>
                    <td><small>{{ $mhs['pembimbing_2']['nama_dosen'] ?? '-' }}</small></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Signature for lampiran -->
        <div class="signature">
            <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
            <p style="margin: 0 0 10px 0;">pada tanggal 
                @if($sk->Tanggal_Persetujuan_Dekan)
                    {{ \Carbon\Carbon::parse($sk->Tanggal_Persetujuan_Dekan)->isoFormat('D MMMM Y') }}
                @else
                    {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
                @endif
            </p>
            <p style="margin: 0 0 10px 0;"><strong>DEKAN,</strong></p>
            @if($sk->QR_Code)
                <img src="{{ asset('storage/' . $sk->QR_Code) }}" alt="QR Code">
            @else
                <div style="height: 100px;"></div>
            @endif
            <p style="margin: 0 0 0 0;">
                <strong><u>{{ $dekanName }}</u></strong><br>
                NIP. {{ $dekanNip }}
            </p>
        </div>
    </div>
    @endforeach

</body>
</html>
