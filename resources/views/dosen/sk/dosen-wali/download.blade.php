<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Dosen Wali - {{ $sk->Nomor_Surat ?? 'Download' }}</title>
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
            font-size: 12pt;
        }

        .header-sub {
            font-size: 9pt;
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

        .dosen-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin: 15px 0;
            font-size: 10pt;
        }

        .dosen-table th,
        .dosen-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }

        .dosen-table th {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
        }

        .dosen-table td:first-child {
            text-align: center;
            width: 8%;
        }

        .dosen-table td:nth-child(2) {
            width: 67%;
        }

        .dosen-table td:last-child {
            text-align: center;
            width: 25%;
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
        <div class="header-text header-sub">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</div>
        <div class="header-text header-main">UNIVERSITAS TRUNOJOYO MADURA</div>
        <div class="header-text header-sub">FAKULTAS TEKNIK</div>
        <div class="header-address">
            Kampus UTM, Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
            Telp: (031) 3011146, Fax: (031) 3011506
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
        UNIVERSITAS TRUNOJOYO MADURA<br>
        NOMOR {{ $sk->Nomor_Surat ?? '-' }}
    </div>

    <div class="title" style="margin: 15px 0;">TENTANG</div>

    <div class="title">
        DOSEN WALI MAHASISWA FAKULTAS TEKNIK<br>
        UNIVERSITAS TRUNOJOYO MADURA<br>
        SEMESTER {{ strtoupper($sk->Semester ?? 'GANJIL') }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}
    </div>

    <div style="margin: 20px 0; font-weight: bold;">
        DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA,
    </div>

    <!-- Content -->
    <div class="content">
        <p style="margin-bottom: 10px; font-weight: normal;">Menimbang</p>
        <table>
            <tr>
                <td style="width: 10%;">:</td>
                <td style="width: 5%;">a.</td>
                <td>bahwa dalam rangka membantu mahasiswa menyelesaikan program sarjana/diploma sesuai rencana studi, perlu menugaskan dosen tetap di lingkungan Fakultas Teknik Universitas Trunojoyo Madura sebagai dosen wali;</td>
            </tr>
            <tr>
                <td></td>
                <td>b.</td>
                <td>bahwa untuk pelaksanaan butir a di atas, perlu menerbitkan Surat Keputusan Dekan Fakultas Teknik;</td>
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
                <td>Peraturan Pemerintah Nomor 60 tahun 1999, tentang Pendidikan Tinggi;</td>
            </tr>
            <tr>
                <td></td>
                <td>3.</td>
                <td>Keputusan Presiden RI Nomor 85 tahun 2001, tentang pendirian Universitas Trunojoyo Madura;</td>
            </tr>
            <tr>
                <td></td>
                <td>4.</td>
                <td>Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/U/2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</td>
            </tr>
            <tr>
                <td></td>
                <td>5.</td>
                <td>Keputusan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi Nomor 73649/MPK.A/KP.06.02/2022 tentang pengangkatan Rektor UTM periode 2022-2026;</td>
            </tr>
            <tr>
                <td></td>
                <td>6.</td>
                <td>Keputusan Rektor Universitas Trunojoyo Madura Nomor 1357/UN46/KP/2023 tentang Pengangkatan Dekan Fakultas Teknik Universitas Trunojoyo Madura periode 2021-2025;</td>
            </tr>
        </table>

        <p style="margin-bottom: 10px; font-weight: normal;">Memperhatikan</p>
        <table>
            <tr>
                <td style="width: 10%;">:</td>
                <td style="width: 5%;">1.</td>
                <td>Keputusan Rektor Universitas Trunojoyo Madura Nomor 190/UN46/2016, tentang Buku Pedoman Akademik Universitas Trunojoyo Madura Tahun Akademik 2016/2017;</td>
            </tr>
            <tr>
                <td></td>
                <td>2.</td>
                <td>Surat dari masing-masing Ketua Jurusan Fakultas Teknik tentang permohonan SK Dosen Wali {{ $sk->Semester ?? 'Ganjil' }} {{ $sk->Tahun_Akademik ?? '2023/2024' }};</td>
            </tr>
        </table>

        <div style="text-align: center; margin: 15px 0; font-weight: bold;">MEMUTUSKAN :</div>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Menetapkan</td>
                <td style="width: 3%;">:</td>
                <td style="font-weight: bold;">DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER {{ strtoupper($sk->Semester ?? 'GANJIL') }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}.</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Kesatu</td>
                <td style="width: 3%;">:</td>
                <td>Menugaskan dosen tetap di Fakultas Teknik Universitas Trunojoyo Madura yang namanya tersebut dalam lampiran Surat Keputusan ini sebagai dosen wali Semester {{ $sk->Semester ?? 'Ganjil' }} Tahun Akademik {{ $sk->Tahun_Akademik ?? '2023/2024' }};</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Kedua</td>
                <td style="width: 3%;">:</td>
                <td>Tugas dan fungsi dosen wali tersebut yaitu:<br>
                    <span style="margin-left: 15px;">a. Membantu mengarahkan dan mengesahkan rencana studi;</span><br>
                    <span style="margin-left: 15px;">b. Memberi bimbingan dan nasehat mengenai berbagai masalah yang bersifat kurikuler akademik;</span>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Ketiga</td>
                <td style="width: 3%;">:</td>
                <td>Keputusan ini berlaku sejak tanggal ditetapkan.</td>
            </tr>
        </table>
    </div>

    <!-- Signature -->
    <div class="signature">
        <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
        <p style="margin: 0 0 10px 0;">pada tanggal 
            @if($sk->{'Tanggal-Persetujuan-Dekan'})
                {{ \Carbon\Carbon::parse($sk->{'Tanggal-Persetujuan-Dekan'})->isoFormat('D MMMM Y') }}
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

    <!-- Lampiran per Prodi -->
    @php
        $dosenList = $sk->Data_Dosen_Wali ?? [];
        $groupedByProdi = [];
        
        foreach ($dosenList as $dosen) {
            $prodiName = $dosen['prodi'] ?? '-';
            if (!isset($groupedByProdi[$prodiName])) {
                $groupedByProdi[$prodiName] = [];
            }
            $groupedByProdi[$prodiName][] = $dosen;
        }
    @endphp

    @foreach($groupedByProdi as $prodiName => $dosenProdi)
    <div class="lampiran">
        <div class="lampiran-header">
            <p style="margin: 0 0 3px 0;">SALINAN</p>
            <p style="margin: 0 0 3px 0;">LAMPIRAN I KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA</p>
            <p style="margin: 0 0 3px 0;">NOMOR {{ $sk->Nomor_Surat ?? '-' }}</p>
            <p style="margin: 0 0 10px 0;">PERIHAL</p>
            <p style="margin: 0 0 10px 0;">DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER {{ strtoupper($sk->Semester ?? 'GANJIL') }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}</p>
        </div>
        
        <div class="lampiran-title">
            DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA<br>
            SEMESTER {{ strtoupper($sk->Semester ?? 'GANJIL') }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}<br>
            <u>Daftar Dosen Wali Mahasiswa Prodi {{ $prodiName }}</u>
        </div>

        <table class="dosen-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Dosen</th>
                    <th>Jumlah Anak Wali</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dosenProdi as $index => $dosen)
                <tr>
                    <td>{{ $index + 1 }}.</td>
                    <td>{{ $dosen['nama_dosen'] ?? '-' }}</td>
                    <td>{{ $dosen['jumlah_anak_wali'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature">
            <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
            <p style="margin: 0 0 10px 0;">pada tanggal 
                @if($sk->{'Tanggal-Persetujuan-Dekan'})
                    {{ \Carbon\Carbon::parse($sk->{'Tanggal-Persetujuan-Dekan'})->isoFormat('D MMMM Y') }}
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
