<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Dosen Wali - {{ $sk->Semester }} {{ $sk->Tahun_Akademik }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm 2.5cm;
        }
        
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
            background: #fff;
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
            top: 0;
        }
        
        .header .title {
            display: block;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .header .title-1 { font-size: 14pt; }
        .header .title-2 { font-size: 16pt; }
        .header .title-3 { font-size: 14pt; }
        
        .header .address {
            font-size: 10pt;
            margin-top: 5px;
            font-weight: normal;
        }
        
        .section-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin: 20px 0;
        }
        
        .content {
            text-align: justify;
            font-size: 10pt;
        }
        
        .content p {
            margin-bottom: 10px;
        }
        
        .content table {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .content table td {
            vertical-align: top;
        }
        
        .signature {
            text-align: right;
            margin-top: 40px;
            margin-bottom: 30px;
        }
        
        .signature p {
            margin: 0 0 3px 0;
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
            text-align: left;
            margin-bottom: 10px;
        }
        
        .lampiran-header p {
            margin: 0 0 3px 0;
        }
        
        .lampiran-title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
        }
        
        .table-dosen {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            font-size: 10pt;
            margin-bottom: 20px;
        }
        
        .table-dosen th,
        .table-dosen td {
            border: 1px solid #000;
            padding: 8px;
        }
        
        .table-dosen th {
            background-color: #fff;
            font-weight: bold;
            text-align: center;
        }
        
        .table-dosen td:nth-child(1),
        .table-dosen td:nth-child(3) {
            text-align: center;
        }
        
        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .no-print:hover {
            background: #45a049;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print / Save as PDF
    </button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
            <span class="title title-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span>
            <span class="title title-2">UNIVERSITAS TRUNOJOYO MADURA</span>
            <span class="title title-3">FAKULTAS TEKNIK</span>
            <div class="address">
                Kampus UTM, Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
                Telp: (031) 3011146, Fax: (031) 3011506
            </div>
        </div>

        <!-- Nomor Surat -->
        <div class="section-title">
            KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
            UNIVERSITAS TRUNOJOYO MADURA<br>
            NOMOR {{ $accSK->Nomor_Surat ?? '-' }}
        </div>

        <div class="section-title">TENTANG</div>

        <div class="section-title">
            DOSEN WALI MAHASISWA FAKULTAS TEKNIK<br>
            UNIVERSITAS TRUNOJOYO MADURA<br>
            SEMESTER {{ strtoupper($sk->Semester) }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik }}
        </div>

        <div style="margin: 20px 0; font-weight: bold;">
            DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA,
        </div>

        <!-- Content -->
        <div class="content">
            <p style="font-weight: bold;">Menimbang :</p>
            <table>
                <tr>
                    <td width="5%">a.</td>
                    <td>bahwa dalam rangka membantu mahasiswa menyelesaikan program sarjana/diploma sesuai rencana studi, perlu menugaskan dosen tetap di lingkungan Fakultas Teknik Universitas Trunojoyo Madura sebagai dosen wali;</td>
                </tr>
                <tr>
                    <td>b.</td>
                    <td>bahwa untuk pelaksanaan butir a di atas, perlu menerbitkan Surat Keputusan Dekan Fakultas Teknik;</td>
                </tr>
            </table>

            <div class="section-title">MEMUTUSKAN :</div>

            <table>
                <tr>
                    <td width="20%" style="font-weight: bold;">Menetapkan</td>
                    <td width="3%">:</td>
                    <td style="font-weight: bold;">DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER {{ strtoupper($sk->Semester) }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik }}.</td>
                </tr>
            </table>

            <table>
                <tr>
                    <td width="20%" style="font-weight: bold;">Kesatu</td>
                    <td width="3%">:</td>
                    <td>Menugaskan dosen tetap di Fakultas Teknik Universitas Trunojoyo Madura yang namanya tersebut dalam lampiran Surat Keputusan ini sebagai dosen wali Semester {{ $sk->Semester }} Tahun Akademik {{ $sk->Tahun_Akademik }};</td>
                </tr>
            </table>
        </div>

        <!-- Signature -->
        <div class="signature">
            <p>Ditetapkan di Bangkalan</p>
            <p>pada tanggal {{ \Carbon\Carbon::parse($accSK->{'Tanggal-Persetujuan-Dekan'})->translatedFormat('d F Y') }}</p>
            <p><strong>DEKAN,</strong></p>
            @if($accSK->QR_Code)
                <img src="{{ asset('storage/' . $accSK->QR_Code) }}" alt="QR Code">
            @endif
            <p>
                <strong><u>{{ $dekanName }}</u></strong><br>
                NIP. {{ $dekanNip }}
            </p>
        </div>

        <!-- Lampiran -->
        <div class="lampiran">
            <div class="lampiran-header">
                <p>SALINAN</p>
                <p>LAMPIRAN I KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA</p>
                <p>NOMOR {{ $accSK->Nomor_Surat ?? '-' }}</p>
                <p>PERIHAL DOSEN WALI MAHASISWA FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA</p>
                <p>SEMESTER {{ strtoupper($sk->Semester) }} TAHUN AKADEMIK {{ $sk->Tahun_Akademik }}</p>
            </div>

            <div class="lampiran-title">
                Daftar Dosen Wali Mahasiswa Prodi {{ $sk->prodi->Nama_Prodi ?? 'Prodi' }}
            </div>

            <table class="table-dosen">
                <thead>
                    <tr>
                        <th width="8%">No.</th>
                        <th width="67%">Nama Dosen</th>
                        <th width="25%">Jumlah Anak Wali</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sk->Data_Dosen_Wali as $index => $dosen)
                    <tr>
                        <td>{{ $index + 1 }}.</td>
                        <td>{{ $dosen['nama_dosen'] ?? '-' }}</td>
                        <td>{{ $dosen['jumlah_anak_wali'] ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="signature">
                <p>Ditetapkan di Bangkalan</p>
                <p>pada tanggal {{ \Carbon\Carbon::parse($accSK->{'Tanggal-Persetujuan-Dekan'})->translatedFormat('d F Y') }}</p>
                <p><strong>DEKAN,</strong></p>
                @if($accSK->QR_Code)
                    <img src="{{ asset('storage/' . $accSK->QR_Code) }}" alt="QR Code">
                @endif
                <p>
                    <strong><u>{{ $dekanName }}</u></strong><br>
                    NIP. {{ $dekanNip }}
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto trigger print dialog after page load
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
