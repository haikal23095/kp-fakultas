<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Penguji Skripsi - {{ $sk->Nomor_Surat ?? 'Download' }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3.5px double #000;
            padding-bottom: 10px;
            position: relative;
        }

        .header img {
            width: 70px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .header-text {
            text-transform: uppercase;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
        }

        .header-main {
            font-size: 13pt;
        }

        .header-address {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 5px;
        }

        .title {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .content {
            text-align: justify;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        .content table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .content td {
            vertical-align: top;
            padding: 2px 0;
        }

        .signature {
            margin-top: 30px;
            float: right;
            width: 300px;
            text-align: left;
        }

        .signature img {
            width: 100px;
            height: 100px;
            margin: 10px 0;
            display: block;
        }

        .lampiran {
            page-break-before: always;
            margin-top: 20px;
        }

        .lampiran-header {
            font-size: 9pt;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .lampiran-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .table-data th, 
        .table-data td {
            border: 1px solid black;
            padding: 5px;
        }

        .table-data th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .clearfix {
            clear: both;
        }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 500);">
    
    <div class="header">
        <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
        <div class="header-text" style="font-size: 11pt;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</div>
        <div class="header-text header-main" style="font-size: 13pt;">UNIVERSITAS TRUNODJOYO MADURA</div>
        <div class="header-text" style="font-size: 12pt;">FAKULTAS TEKNIK</div>
        <div class="header-address">
            Jl. Raya Telang, PO. Box. 2 Kamal, Bangkalan – Madura<br>
            Telp : (031) 3011146, Fax. (031) 3011506<br>
            Laman : www.trunodjoyo.ac.id
        </div>
    </div>

    <div class="title">
        KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
        UNIVERSITAS TRUNODJOYO MADURA<br>
        NOMOR : {{ $sk->Nomor_Surat ?? '-' }}
    </div>

    <div style="text-align: center; font-weight: bold; margin-bottom: 10px;">TENTANG</div>

    <div class="title">
        PENETAPAN DOSEN PENGUJI SKRIPSI<br>
        PROGRAM STUDI S1 {{ strtoupper($sk->prodi->Nama_Prodi ?? 'FAKULTAS TEKNIK') }}<br>
        FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO MADURA<br>
        SEMESTER {{ strtoupper($sk->Semester ?? '') }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '' }}
    </div>

    <div style="font-weight: bold; margin-bottom: 15px;">DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO MADURA,</div>

    <div class="content">
        <table>
            <tr>
                <td style="width: 100px;">Menimbang</td>
                <td style="width: 20px;">:</td>
                <td>
                    <ol type="a" style="margin: 0; padding-left: 20px;">
                        <li>Bahwa untuk memperlancar pelaksanaan Ujian Skripsi mahasiswa, perlu menugaskan dosen sebagai penguji Skripsi;</li>
                        <li>Bahwa untuk melaksanakan butir a di atas, perlu ditetapkan dalam Keputusan Dekan Fakultas Teknik;</li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td>Mengingat</td>
                <td>:</td>
                <td>
                    <ol style="margin: 0; padding-left: 20px;">
                        <li>Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</li>
                        <li>Peraturan Pemerintah Nomor 4 Tahun 2012 Tentang Penyelenggaraan Pendidikan Tinggi;</li>
                        <li>Peraturan Presiden RI Nomor 4 Tahun 2014 Tentang Perubahan Penyelenggaraan dan Pengelolaan Perguruan Tinggi;</li>
                        <li>Keputusan Rektor Universitas Trunodjoyo Nomor 1357/UNM3/KP/2023 tentang Pengangkatan Pejabat Struktural Dekan Fakultas Teknik;</li>
                    </ol>
                </td>
            </tr>
        </table>

        <div style="text-align: center; font-weight: bold; margin: 15px 0;">MEMUTUSKAN :</div>

        <table>
            <tr>
                <td style="width: 100px;">Menetapkan</td>
                <td style="width: 20px;">:</td>
                <td style="font-weight: bold;">KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO MADURA TENTANG PENETAPAN DOSEN PENGUJI SKRIPSI SEMESTER {{ strtoupper($sk->Semester ?? '') }} TA {{ $sk->Tahun_Akademik ?? '' }}.</td>
            </tr>
            <tr>
                <td>KESATU</td>
                <td>:</td>
                <td>Dosen Penguji Skripsi sebagaimana tercantum dalam lampiran Keputusan ini;</td>
            </tr>
            <tr>
                <td>KEDUA</td>
                <td>:</td>
                <td>Keputusan ini berlaku sejak tanggal ditetapkan.</td>
            </tr>
        </table>
    </div>

    <div class="signature">
        <div>Ditetapkan di Bangkalan</div>
        <div>Pada tanggal : {{ \Carbon\Carbon::parse($sk->Tanggal_Persetujuan_Dekan ?? now())->isoFormat('D MMMM Y') }}</div>
        <div style="font-weight: bold; margin: 5px 0 10px 0;">DEKAN,</div>
        @if(isset($qrCodePath) && $qrCodePath)
            <img src="{{ $qrCodePath }}" alt="QR Code">
        @else
            <div style="height: 100px;"></div>
        @endif
        <div><strong style="text-decoration: underline;">{{ $dekanName ?? 'Dekan' }}</strong></div>
        <div>NIP. {{ $dekanNip ?? '-' }}</div>
    </div>

    <div class="clearfix"></div>

    <div class="lampiran">
        <div class="lampiran-header">
            SALINAN LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
            NOMOR : {{ $sk->Nomor_Surat ?? '-' }}<br>
            TANGGAL : {{ \Carbon\Carbon::parse($sk->Tanggal_Persetujuan_Dekan ?? now())->isoFormat('D MMMM Y') }}
        </div>

        <div class="lampiran-title">
            DAFTAR DOSEN PENGUJI SKRIPSI<br>
            PROGRAM STUDI S1 {{ strtoupper($sk->prodi->Nama_Prodi ?? 'FAKULTAS TEKNIK') }}
        </div>

        <table class="table-data">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">NIM</th>
                    <th style="width: 18%;">Nama Mahasiswa</th>
                    <th style="width: 25%;">Judul</th>
                    <th style="width: 13%;">Penguji 1</th>
                    <th style="width: 13%;">Penguji 2</th>
                    <th style="width: 13%;">Penguji 3</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $mhsList = is_string($sk->Data_Penguji_Skripsi) ? json_decode($sk->Data_Penguji_Skripsi, true) : $sk->Data_Penguji_Skripsi;
                @endphp
                @foreach($mhsList as $index => $m)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center;">{{ $m['nim'] ?? '-' }}</td>
                    <td>{{ $m['nama_mahasiswa'] ?? '-' }}</td>
                    <td><small>{{ $m['judul_skripsi'] ?? '-' }}</small></td>
                    <td>{{ $m['nama_penguji_1'] ?? '-' }}</td>
                    <td>{{ $m['nama_penguji_2'] ?? '-' }}</td>
                    <td>{{ $m['nama_penguji_3'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature">
            <div style="font-weight: bold; margin-bottom: 60px;">DEKAN,</div>
            <div><strong style="text-decoration: underline;">{{ $dekanName ?? 'Dekan' }}</strong></div>
            <div>NIP. {{ $dekanNip ?? '-' }}</div>
        </div>
    </div>

</body>
</html>
