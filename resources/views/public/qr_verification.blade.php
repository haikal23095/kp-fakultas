<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat - Universitas Trunojoyo Madura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .verification-card {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .card-header-custom img {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: bold;
            margin: 20px 0;
        }
        .status-approved {
            background: #10b981;
            color: white;
        }
        .status-pending {
            background: #f59e0b;
            color: white;
        }
        .info-row {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .info-value {
            color: #1f2937;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <div class="card-header-custom">
            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
            <h2 class="mb-0">Verifikasi Surat Pengantar KP/Magang</h2>
            <p class="mb-0 mt-2">Universitas Trunojoyo Madura</p>
        </div>

        <div class="card-body p-4">
            @if($status === 'approved')
                <div class="text-center">
                    <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                    <div class="status-badge status-approved">
                        <i class="fas fa-check me-2"></i>SURAT TERVERIFIKASI
                    </div>
                    <p class="text-muted">Surat ini telah disetujui dan terverifikasi oleh sistem</p>
                </div>

                <hr class="my-4">

                <div class="info-row">
                    <div class="info-label">Status Persetujuan</div>
                    <div class="info-value">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Disetujui oleh <strong>{{ $koordinatorName }}</strong>
                    </div>
                    <small class="text-muted">NIP: {{ $koordinatorNIP }}</small>
                </div>

                <div class="info-row">
                    <div class="info-label">Mahasiswa Pengaju</div>
                    <div class="info-value">
                        @foreach($dataMahasiswa as $idx => $mhs)
                            <div class="mb-2">
                                {{ $idx + 1 }}. <strong>{{ $mhs['nama'] ?? 'N/A' }}</strong>
                                <br><small class="text-muted">NIM: {{ $mhs['nim'] ?? 'N/A' }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Nama Instansi</div>
                    <div class="info-value">{{ $surat->Nama_Instansi ?? 'N/A' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Alamat Instansi</div>
                    <div class="info-value">{{ $surat->Alamat_Instansi ?? 'N/A' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Periode Magang</div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} - 
                        {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                    </div>
                </div>

                @if($dataDosenPembimbing)
                <div class="info-row">
                    <div class="info-label">Dosen Pembimbing</div>
                    <div class="info-value">
                        @if(isset($dataDosenPembimbing['dosen_pembimbing_1']))
                            <div>1. {{ $dataDosenPembimbing['dosen_pembimbing_1'] }}</div>
                        @endif
                        @if(isset($dataDosenPembimbing['dosen_pembimbing_2']) && $dataDosenPembimbing['dosen_pembimbing_2'])
                            <div>2. {{ $dataDosenPembimbing['dosen_pembimbing_2'] }}</div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Keterangan:</strong> Surat pengantar untuk KP/Magang ini telah disetujui oleh <strong>{{ $koordinatorName }}</strong> dan sah untuk digunakan.
                </div>

            @else
                <div class="text-center">
                    <i class="fas fa-clock text-warning" style="font-size: 64px;"></i>
                    <div class="status-badge status-pending">
                        <i class="fas fa-clock me-2"></i>MENUNGGU PERSETUJUAN
                    </div>
                    <p class="text-muted mt-3">{{ $message }}</p>
                </div>
            @endif
        </div>

        <div class="card-footer text-center text-muted p-3" style="background: #f9fafb;">
            <small>
                <i class="fas fa-shield-alt me-1"></i>
                Dokumen ini terverifikasi melalui sistem Universitas Trunojoyo Madura
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
