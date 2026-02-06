<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Beban Mengajar - {{ $sk->Nomor_Surat ?? 'Download' }}</title>
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

        .header-sub {
            font-size: 10pt;
            font-weight: normal;
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

        .beban-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin: 15px 0;
            font-size: 10pt;
        }

        .beban-table th,
        .beban-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }

        .beban-table th {
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

    <div class="title">
        BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK<br>
        UNIVERSITAS TRUNODJOYO<br>
        SEMESTER {{ strtoupper($sk->Semester ?? 'GANJIL') }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}
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
                <td>bahwa untuk kelancaran perkuliahan Program S1 di Fakultas Teknik Universitas Trunodjoyo, maka perlu menetapkan beban mengajar dosen;</td>
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
                <td>Keputusan Presiden RI Nomor 85 tahun 2001, tentang pendirian Universitas Trunodjoyo;</td>
            </tr>
            <tr>
                <td></td>
                <td>4.</td>
                <td>Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/U/2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</td>
            </tr>
            <tr>
                <td></td>
                <td>5.</td>
                <td>Keputusan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi tentang pengangkatan Rektor Universitas Trunodjoyo;</td>
            </tr>
            <tr>
                <td></td>
                <td>6.</td>
                <td>Keputusan Rektor Universitas Trunodjoyo tentang Pengangkatan Dekan Fakultas Teknik Universitas Trunodjoyo;</td>
            </tr>
        </table>

        <p style="margin-bottom: 10px; font-weight: normal;">Memperhatikan</p>
        <table>
            <tr>
                <td style="width: 10%;">:</td>
                <td style="width: 5%;">1.</td>
                <td>Keputusan Rektor Universitas Trunodjoyo tentang Buku Pedoman Akademik Universitas Trunodjoyo;</td>
            </tr>
            <tr>
                <td></td>
                <td>2.</td>
                <td>Surat dari masing-masing Ketua Jurusan Fakultas Teknik tentang permohonan SK Beban Mengajar {{ $sk->Semester ?? 'Ganjil' }} {{ $sk->Tahun_Akademik ?? '2023/2024' }};</td>
            </tr>
        </table>

        <div style="text-align: center; margin: 15px 0; font-weight: bold;">MEMUTUSKAN :</div>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Menetapkan</td>
                <td style="width: 3%;">:</td>
                <td style="font-weight: bold;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER {{ strtoupper($sk->Semester ?? 'GANJIL') }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}.</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Kesatu</td>
                <td style="width: 3%;">:</td>
                <td>Beban mengajar dosen Program Studi S1 di lingkungan Fakultas Teknik Universitas Trunodjoyo Semester {{ $sk->Semester ?? 'Ganjil' }} Tahun Akademik {{ $sk->Tahun_Akademik ?? '2023/2024' }} sebagaimana terlampir dalam surat keputusan ini.</td>
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
        $bebanData = $sk->Data_Beban_Mengajar ?? [];
        if (is_string($bebanData)) {
            $bebanData = json_decode($bebanData, true);
        }
        
        $groupedByProdi = [];
        if (is_array($bebanData)) {
            foreach ($bebanData as $item) {
                $prodiName = $item['Nama_Prodi'] ?? $item['prodi'] ?? 'Tidak Diketahui';
                if (!isset($groupedByProdi[$prodiName])) {
                    $groupedByProdi[$prodiName] = [];
                }
                $groupedByProdi[$prodiName][] = $item;
            }
        }
    @endphp

    @foreach($groupedByProdi as $prodiName => $items)
    <div class="lampiran">
        <div class="lampiran-header">
            <p style="margin: 0 0 3px 0;">SALINAN</p>
            <p style="margin: 0 0 3px 0;">LAMPIRAN {{ $loop->iteration }} KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
            <p style="margin: 0 0 3px 0;">NOMOR {{ $sk->Nomor_Surat ?? '-' }}</p>
            <p style="margin: 0 0 3px 0;">TENTANG</p>
            <p style="margin: 0 0 10px 0;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER {{ strtoupper($sk->Semester ?? 'GANJIL') }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}</p>
        </div>
        
        <div class="lampiran-title">
            BEBAN MENGAJAR DOSEN {{ strtoupper($prodiName) }} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO<br>
            SEMESTER {{ strtoupper($sk->Semester ?? 'GANJIL') }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik ?? '2023/2024' }}
        </div>

        <table class="beban-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 35%;">Nama Dosen / NIP</th>
                    <th style="width: 40%;">Mata Kuliah</th>
                    <th style="width: 10%;">Kelas</th>
                    <th style="width: 10%;">SKS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}.</td>
                    <td>{{ $item['nama_dosen'] ?? $item['Nama_Dosen'] ?? '-' }}<br><small>NIP. {{ $item['nip'] ?? $item['NIP'] ?? '-' }}</small></td>
                    <td>{{ $item['nama_mata_kuliah'] ?? $item['mata_kuliah'] ?? $item['Nama_Matakuliah'] ?? '-' }}</td>
                    <td style="text-align: center;">{{ $item['kelas'] ?? $item['Kelas'] ?? '-' }}</td>
                    <td style="text-align: center;">{{ $item['sks'] ?? $item['SKS'] ?? 0 }}</td>
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
