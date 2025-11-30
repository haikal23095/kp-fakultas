<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        body {
            background: #f8f9fa;
            padding: 2rem 0;
        }
        .verification-card {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .verification-header {
            background: #10b981;
            color: white;
            padding: 2rem;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .verification-body {
            padding: 2rem;
        }
        .info-row {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .info-value {
            color: #212529;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-card">
            <div class="verification-header">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h3 class="mb-0">Surat Terverifikasi</h3>
                <p class="mb-0 mt-2 opacity-75">Dokumen ini telah ditandatangani secara digital</p>
            </div>
            
            <div class="verification-body">
                <div class="info-row">
                    <div class="info-label">Jenis Surat</div>
                    <div class="info-value">{{ $surat->jenisSurat->Nama_Surat ?? 'N/A' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Judul Surat</div>
                    <div class="info-value">{{ $surat->Judul_Tugas_Surat }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Nama Mahasiswa</div>
                    <div class="info-value">
                        {{ $surat->pemberiTugas->mahasiswa->Nama_Mahasiswa ?? 'N/A' }}
                        <br>
                        <small class="text-muted">NIM: {{ $surat->pemberiTugas->mahasiswa->NIM ?? 'N/A' }}</small>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Program Studi</div>
                    <div class="info-value">{{ $surat->pemberiTugas->mahasiswa->prodi->Nama_Prodi ?? 'N/A' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Ditandatangani Oleh</div>
                    <div class="info-value">
                        {{ $verification->signed_by }}
                        <br>
                        <small class="text-muted">{{ $verification->signed_at ? $verification->signed_at->format('d F Y, H:i') : '-' }} WIB</small>
                    </div>
                </div>
                
                <div class="info-row border-0">
                    <div class="info-label">Token Verifikasi</div>
                    <div class="info-value">
                        <code class="text-primary">{{ $verification->token }}</code>
                    </div>
                </div>
                
                <div class="alert alert-success border-0 mt-4">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Dokumen Asli</strong> - Surat ini telah diverifikasi menggunakan tanda tangan digital dan QR Code yang sah.
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="/" class="btn btn-outline-primary">
                <i class="fas fa-home me-2"></i>Kembali ke Beranda
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
