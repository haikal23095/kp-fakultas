<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Penguji Skripsi - {{ $sk->Nomor_Surat }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }

        .container {
            width: 21cm;
            margin: 0 auto;
            padding: 0.5cm 1cm;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            position: relative;
        }

        .header img {
            position: absolute;
            width: 65px;
            height: auto;
            left: 0;
            top: 0;
        }

        .header strong {
            display: block;
            text-transform: uppercase;
        }

        .header .line-1 { font-size: 11pt; font-weight: bold; }
        .header .line-2 { font-size: 13pt; font-weight: bold; }
        .header .line-3 { font-size: 11pt; font-weight: bold; }
        .header .address {
            font-size: 8pt;
            margin-top: 5px;
            font-weight: normal;
        }

        .title {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            font-size: 11pt;
        }

        .nomor {
            text-align: center;
            margin: 5px 0;
            font-size: 12pt;
        }

        .subtitle {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            font-size: 10pt;
        }

        .content {
            text-align: justify;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .content table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .content table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .content ol {
            margin: 0;
            padding-left: 20px;
        }

        .content ol li {
            margin-bottom: 5px;
        }

        .table-mahasiswa {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
            border: 1px solid #000;
        }

        .table-mahasiswa th,
        .table-mahasiswa td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
            line-height: 1.3;
        }

        .table-mahasiswa thead th {
            background-color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .table-mahasiswa tbody td {
            font-size: 8pt;
            vertical-align: top;
        }

        .table-mahasiswa tbody td:nth-child(1) {
            text-align: center;
            vertical-align: top;
        }

        .signature {
            margin-top: 40px;
            font-size: 10pt;
            text-align: right;
        }

        .signature img {
            width: 100px;
            height: 100px;
            margin: 10px 0;
            border: 1px solid #000;
        }

        .lampiran-section {
            page-break-before: always;
            margin-top: 30px;
        }

        .lampiran-section.first {
            page-break-before: auto;
            margin-top: 30px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                width: 100%;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 2cm 2.5cm;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="no-print" style="text-align: center; padding: 20px; background: #f8f9fa;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14pt; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 5px;">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 14pt; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
            <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</strong>
            <strong class="line-1">RISET DAN TEKNOLOGI</strong>
            <strong class="line-2">UNIVERSITAS TRUNODJOYO</strong>
            <strong class="line-3">FAKULTAS TEKNIK</strong>
            <div class="address">
                Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
                Telp: (031) 3011146, Fax. (031) 3011506<br>
                Laman: www.trunojoyo.ac.id
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Title -->
        <div class="title">
            KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
            UNIVERSITAS TRUNODJOYO
        </div>

        <!-- Nomor Surat -->
        <div class="nomor">
            NOMOR: {{ $sk->Nomor_Surat }}
        </div>

        <!-- Subtitle: TENTANG -->
        <div class="subtitle">
            TENTANG
        </div>

        <!-- Subject -->
        @php
            $semesterUpper = strtoupper($sk->Semester);
            $namaProdi = $prodi ? strtoupper($prodi->Nama_Prodi) : 'TEKNIK INFORMATIKA';
        @endphp
        <div class="subtitle">
            PENETAPAN DOSEN PENGUJI SKRIPSI<br>
            PROGRAM STUDI S1 {{ $namaProdi }} FAKULTAS TEKNIK<br>
            UNIVERSITAS TRUNODJOYO SEMESTER {{ $semesterUpper }} <br>
            TAHUN AKADEMIK {{ $sk->Tahun_Akademik }}
        </div>

        <!-- Opening -->
        <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
            DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO,
        </div>

        <!-- Content: Menimbang, Mengingat, etc. -->
        <div class="content">
            <table>
                <tr>
                    <td style="width: 100px;"><strong>Menimbang</strong></td>
                    <td style="width: 20px;">:</td>
                    <td>
                        <table style="border: none;">
                            <tr>
                                <td style="width: 25px; border: none;">a.</td>
                                <td style="border: none;">Bahwa untuk memperlancar penyelesaian Skripsi mahasiswa, perlu menugaskan dosen sebagai penguji Skripsi;</td>
                            </tr>
                            <tr>
                                <td style="border: none; padding-top: 5px;">b.</td>
                                <td style="border: none; padding-top: 5px;">Bahwa untuk melaksanakan butir a di atas, perlu ditetapkan dalam Keputusan Dekan Fakultas Teknik;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td style="width: 100px;"><strong>Mengingat</strong></td>
                    <td style="width: 20px;">:</td>
                    <td>
                        <ol>
                            <li>Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</li>
                            <li>Undang-undang Nomor 12 Tahun 2012 Tentang Pendidikan Tinggi;</li>
                            <li>Peraturan Pemerintah Nomor 4 Tahun 2014 Tentang Penyelenggaraan Pendidikan Tinggi dan Pengelolaan Perguruan Tinggi;</li>
                            <li>Keputusan Presiden RI Nomor 85 tahun 2001, tentang Pendirian Universitas Trunodjoyo;</li>
                            <li>Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/U/2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</li>
                            <li>Keputusan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi Nomor 73649/MPK.A/KP.06.02/2022 tentang pengangkatan Rektor UTM periode 2022-2026;</li>
                            <li>Keputusan Rektor Universitas Trunodjoyo Nomor 1357/UN46/KP/2023 tentang Pengangkatan Dekan Fakultas Teknik Universitas Trunodjoyo periode 2021-2025;</li>
                        </ol>
                    </td>
                </tr>
            </table>

            @php
                $dataPenguji = $sk->Data_Penguji_Skripsi;
                if (is_string($dataPenguji)) {
                    $dataPenguji = json_decode($dataPenguji, true);
                }
                if (!is_array($dataPenguji)) {
                    $dataPenguji = [];
                }

                // Group by jurusan/prodi
                $groupedByProdi = [];
                foreach ($dataPenguji as $mhs) {
                    $pName = isset($mhs['prodi']) ? $mhs['prodi'] : (isset($mhs['nama_prodi']) ? $mhs['nama_prodi'] : ($prodi ? $prodi->Nama_Prodi : '-'));
                    if (!isset($groupedByProdi[$pName])) {
                        $groupedByProdi[$pName] = [
                            'prodi' => $pName,
                            'mahasiswa' => []
                        ];
                    }
                    $groupedByProdi[$pName]['mahasiswa'][] = $mhs;
                }
            @endphp

            <p><strong>Memperhatikan:</strong>
                @foreach(array_keys($groupedByProdi) as $index => $pName)
                    @if($index > 0){{ $index == count($groupedByProdi) - 1 ? ' dan ' : ', ' }}@endif
                    Surat dari Kaprodi {{ $pName }} tentang permohonan SK Dosen Penguji Skripsi
                @endforeach;
            </p>

            <div style="text-align: center; margin: 30px 0 20px 0; font-weight: bold;">
                MEMUTUSKAN
            </div>

            <table>
                <tr>
                    <td style="width: 20%; font-weight: bold;">Menetapkan</td>
                    <td style="width: 3%;">:</td>
                    <td>PENETAPAN DOSEN PENGUJI SKRIPSI PROGRAM STUDI S1 {{ $namaProdi }} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik }}.</td>
                </tr>
            </table>

            <table style="margin-bottom: 10px;">
                <tr>
                    <td style="width: 20%; font-weight: bold;">Kesatu</td>
                    <td style="width: 3%;">:</td>
                    <td>Dosen Penguji Skripsi sebagaimana tercantum dalam lampiran Keputusan ini;</td>
                </tr>
            </table>

            <table>
                <tr>
                    <td style="width: 20%; font-weight: bold;">Kedua</td>
                    <td style="width: 3%;">:</td>
                    <td>Keputusan ini berlaku sejak tanggal ditetapkan.</td>
                </tr>
            </table>
        </div>

        <!-- Signature -->
        <div class="signature">
            <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
            <p style="margin: 0 0 10px 0;">pada tanggal {{ $sk->Tanggal_Persetujuan_Dekan ? \Carbon\Carbon::parse($sk->Tanggal_Persetujuan_Dekan)->locale('id')->isoFormat('D MMMM YYYY') : date('d F Y') }}</p>
            <p style="margin: 0 0 10px 0;"><strong>DEKAN,</strong></p>
            @if($sk->QR_Code)
            <img src="{{ $qrCodePath }}" alt="QR Code">
            @else
            <div style="height: 70px;"></div>
            @endif
            <p style="margin: 10px 0 0 0;">
                <strong><u>{{ $dekanName }}</u></strong><br>
                NIP. {{ $dekanNip }}
            </p>
        </div>

        <!-- Lampiran sections -->
        @foreach($groupedByProdi as $index => $data)
        <div class="lampiran-section {{ $index == 0 ? 'first' : '' }}">
            <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR {{ $sk->Nomor_Surat }}</p>
                <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">PENETAPAN DOSEN PENGUJI SKRIPSI PROGRAM STUDI {{ strtoupper($data['prodi']) }} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik }}</p>
                <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">DAFTAR MAHASISWA DAN DOSEN PENGUJI SKRIPSI</p>
                <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">PROGRAM STUDI {{ strtoupper($data['prodi']) }} FAKULTAS TEKNIK</p>
                <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">UNIVERSITAS TRUNODJOYO</p>
                <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik }}</p>
            </div>

            <table class="table-mahasiswa">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 10%">NIM</th>
                        <th style="width: 20%">Nama Mahasiswa</th>
                        <th style="width: 20%">Judul Skripsi</th>
                        <th style="width: 15%">Penguji 1</th>
                        <th style="width: 15%">Penguji 2</th>
                        <th style="width: 15%">Penguji 3</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['mahasiswa'] as $idx => $mhs)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="text-align: center;">{{ $mhs['nim'] ?? '-' }}</td>
                        <td>{{ $mhs['nama_mahasiswa'] ?? '-' }}</td>
                        <td style="font-size: 7.5pt;">{{ $mhs['judul_skripsi'] ?? '-' }}</td>
                        <td style="font-size: 8pt;">
                            {{ $mhs['nama_penguji_1'] ?? '-' }}
                            @if(isset($mhs['nip_penguji_1']))<br><small>NIP: {{ $mhs['nip_penguji_1'] }}</small>@endif
                        </td>
                        <td style="font-size: 8pt;">
                            {{ $mhs['nama_penguji_2'] ?? '-' }}
                            @if(isset($mhs['nip_penguji_2']))<br><small>NIP: {{ $mhs['nip_penguji_2'] }}</small>@endif
                        </td>
                        <td style="font-size: 8pt;">
                            {{ $mhs['nama_penguji_3'] ?? '-' }}
                            @if(isset($mhs['nip_penguji_3']))<br><small>NIP: {{ $mhs['nip_penguji_3'] }}</small>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Signature for lampiran -->
            <div class="signature" style="margin-top: 50px;">
                <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
                <p style="margin: 0 0 10px 0;">pada tanggal {{ $sk->Tanggal_Persetujuan_Dekan ? \Carbon\Carbon::parse($sk->Tanggal_Persetujuan_Dekan)->locale('id')->isoFormat('D MMMM YYYY') : date('d F Y') }}</p>
                <p style="margin: 0 0 10px 0;"><strong>DEKAN,</strong></p>
                @if($sk->QR_Code)
                <img src="{{ $qrCodePath }}" alt="QR Code">
                @else
                <div style="height: 70px;"></div>
                @endif
                <p style="margin: 10px 0 0 0;">
                    <strong><u>{{ $dekanName }}</u></strong><br>
                    NIP. {{ $dekanNip }}
                </p>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>