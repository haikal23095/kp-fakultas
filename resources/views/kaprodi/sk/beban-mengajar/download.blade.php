<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Beban Mengajar - {{ $sk->Nomor_Surat }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm 2.5cm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
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
        }
        
        .header img {
            width: 80px;
            float: left;
            margin-top: -5px;
        }
        
        .header strong {
            display: block;
            text-transform: uppercase;
        }
        
        .header .line-1 { font-size: 14pt; font-weight: bold; }
        .header .line-2 { font-size: 16pt; font-weight: bold; }
        .header .line-3 { font-size: 14pt; font-weight: bold; }
        
        .header .address {
            font-size: 10pt;
            margin-top: 5px;
            font-weight: normal;
        }
        
        .title {
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 11pt;
        }
        
        .content {
            font-size: 10pt;
            text-align: justify;
        }
        
        .content table {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .content table td {
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
        }
        
        .beban-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
            border: 1px solid #000;
        }
        
        .beban-table th,
        .beban-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
            line-height: 1.3;
        }
        
        .beban-table thead th {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        
        .beban-table tbody td {
            font-size: 9pt;
            vertical-align: top;
        }
        
        .beban-table tbody td:nth-child(1) {
            text-align: center;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $bebanData = $sk->Data_Beban_Mengajar;
        if (is_string($bebanData)) {
            $bebanData = json_decode($bebanData, true);
        }
        
        // Group by prodi
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
        
        $semesterUpper = strtoupper($sk->Semester);
        $tahunAkademik = $sk->Tahun_Akademik;
        $nomorSurat = $sk->Nomor_Surat;
        
        // Convert Logo UTM ke base64
        $logoImageSrc = null;
        $logoPath = public_path('images/logo_unijoyo.png');
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoImageSrc = 'data:image/png;base64,' . $logoData;
        }
        
        // Ambil QR Code dari Acc_SK_Beban_Mengajar
        $qrCodePath = $sk->accSKBebanMengajar->QR_Code ?? null;
        $qrImageSrc = null;
        
        if ($qrCodePath) {
            // Ekstrak nama file saja dari path (karena bisa berupa absolute path dari sistem lain)
            $filename = basename($qrCodePath);
            
            // Bersihkan path jika berupa relative path
            $cleanPath = $qrCodePath;
            $cleanPath = str_replace('\\', '/', $cleanPath); // Normalize separator
            $cleanPath = preg_replace('#^[A-Z]:#i', '', $cleanPath); // Remove drive letter
            $cleanPath = str_replace('public/', '', $cleanPath);
            $cleanPath = str_replace('storage/', '', $cleanPath);
            $cleanPath = ltrim($cleanPath, '/');
            
            // Coba berbagai kemungkinan lokasi file
            $possiblePaths = [
                storage_path('app/public/qr-codes/' . $filename),
                storage_path('app/public/qr_codes/' . $filename),
                storage_path('app/public/' . $filename),
                storage_path('app/public/' . $cleanPath),
                storage_path('app/' . $cleanPath),
                public_path('storage/qr-codes/' . $filename),
                public_path('storage/qr_codes/' . $filename),
                public_path('storage/' . $cleanPath),
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path) && is_file($path)) {
                    $imageData = base64_encode(file_get_contents($path));
                    $mimeType = mime_content_type($path);
                    $qrImageSrc = 'data:' . $mimeType . ';base64,' . $imageData;
                    break;
                }
            }
        }
        
        $tanggalPersetujuan = $sk->accSKBebanMengajar->{'Tanggal-Persetujuan-Dekan'} ?? now();
    @endphp

    {{-- Halaman Utama SK --}}
    <div class="header">
        @if($logoImageSrc)
            <img src="{{ $logoImageSrc }}" alt="Logo UTM">
        @endif
        <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong>
        <strong class="line-2">UNIVERSITAS TRUNODJOYO</strong>
        <strong class="line-3">FAKULTAS TEKNIK</strong>
        <div class="address">
            Kampus UTM, Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
            Telp: (031) 3011146, Fax: (031) 3011506
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="title">
        KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
        UNIVERSITAS TRUNODJOYO<br>
        NOMOR {{ $nomorSurat }}
    </div>

    <div class="title">TENTANG</div>

    <div class="title">
        BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK<br>
        UNIVERSITAS TRUNODJOYO<br>
        SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $tahunAkademik }}
    </div>

    <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
        DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO,
    </div>

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
                <td>Surat dari masing-masing Ketua Jurusan Fakultas Teknik tentang permohonan SK Beban Mengajar {{ $semesterUpper }} {{ $tahunAkademik }};</td>
            </tr>
        </table>

        <div class="title">MEMUTUSKAN :</div>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Menetapkan</td>
                <td style="width: 3%;">:</td>
                <td style="font-weight: bold;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $tahunAkademik }}.</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width: 15%; font-weight: normal;">Kesatu</td>
                <td style="width: 3%;">:</td>
                <td>Beban mengajar dosen Program Studi S1 di lingkungan Fakultas Teknik Universitas Trunodjoyo Semester {{ $semesterUpper }} Tahun Akademik {{ $tahunAkademik }} sebagaimana terlampir dalam surat keputusan ini.</td>
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

    <div class="signature">
        <p style="margin-bottom: 3px;">Ditetapkan di Bangkalan</p>
        <p style="margin-bottom: 3px;">pada tanggal {{ \Carbon\Carbon::parse($tanggalPersetujuan)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
        <p style="margin-bottom: {{ $qrImageSrc ? '10px' : '70px' }};"><strong>DEKAN,</strong></p>
        @if($qrImageSrc)
            <img src="{{ $qrImageSrc }}" alt="QR Code">
        @endif
        <p style="margin-bottom: 0;">
            <strong><u>{{ $dekanName }}</u></strong><br>
            NIP. {{ $dekanNip }}
        </p>
    </div>

    {{-- Lampiran per Prodi --}}
    @foreach($groupedByProdi as $prodiName => $items)
        <div class="page-break"></div>
        
        <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
            <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
            <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN {{ $loop->iteration }} KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
            <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR {{ $nomorSurat }}</p>
            <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
            <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">BEBAN MENGAJAR DOSEN PROGRAM STUDI S1 FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $tahunAkademik }}</p>
            <p style="margin: 0 0 13px 0; text-align: center; font-weight: bold;">BEBAN MENGAJAR DOSEN {{ strtoupper($prodiName) }} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER {{ $semesterUpper }} TAHUN AKADEMIK {{ $tahunAkademik }}</p>
        </div>

        <table class="beban-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Dosen / NIP</th>
                    <th>Mata Kuliah</th>
                    <th>Kelas</th>
                    <th>SKS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $idx => $item)
                    @php
                        $mataKuliah = $item['nama_mata_kuliah'] ?? $item['mata_kuliah'] ?? $item['Nama_Matakuliah'] ?? $item['Nama_MK'] ?? $item['nama-mata-kuliah'] ?? '-';
                        $kelas = $item['kelas'] ?? $item['Kelas'] ?? '-';
                        $sks = $item['sks'] ?? $item['SKS'] ?? 0;
                        $namaDosen = $item['nama_dosen'] ?? $item['Nama_Dosen'] ?? '-';
                        $nip = $item['nip'] ?? $item['NIP'] ?? '-';
                    @endphp
                    <tr>
                        <td>{{ $idx + 1 }}.</td>
                        <td>{{ $namaDosen }}<br><small>{{ $nip }}</small></td>
                        <td>{{ $mataKuliah }}</td>
                        <td>{{ $kelas }}</td>
                        <td>{{ $sks }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature">
            <p style="margin-bottom: 3px;">Ditetapkan di Bangkalan</p>
            <p style="margin-bottom: 3px;">pada tanggal {{ \Carbon\Carbon::parse($tanggalPersetujuan)->locale('id')->isoFormat('D MMMM YYYY') }}</p>
            <p style="margin-bottom: {{ $qrImageSrc ? '10px' : '70px' }};"><strong>DEKAN,</strong></p>
            @if($qrImageSrc)
                <img src="{{ $qrImageSrc }}" alt="QR Code">
            @endif
            <p style="margin-bottom: 0;">
                <strong><u>{{ $dekanName }}</u></strong><br>
                NIP. {{ $dekanNip }}
            </p>
        </div>
    @endforeach

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
