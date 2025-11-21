<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen Surat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-card {
            max-width: 700px;
            margin: 50px auto;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .card-header-custom {
            padding: 30px;
            text-align: center;
            color: white;
        }
        .card-header-valid {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .card-header-invalid {
            background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
        }
        .status-icon {
            font-size: 80px;
            margin-bottom: 15px;
            animation: bounce 1s ease infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .info-row {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
        }
        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }
        .badge-verified {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
        }
        .footer-text {
            text-align: center;
            color: white;
            margin-top: 30px;
            font-size: 14px;
        }
        .back-button {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-card card">
            @if($status === 'valid')
                {{-- DOKUMEN VALID --}}
                <div class="card-header-custom card-header-valid">
                    <div class="status-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="fw-bold mb-2">{{ $message }}</h2>
                    <span class="badge-verified">
                        <i class="fas fa-shield-alt me-2"></i>Dokumen Resmi Terverifikasi
                    </span>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Dokumen ini telah diverifikasi dan ditandatangani secara digital oleh pihak berwenang.</strong>
                    </div>

                    <h5 class="mb-4 fw-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i>Informasi Dokumen
                    </h5>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Jenis Surat</div>
                            <div class="col-md-8 info-value">
                                {{ optional($surat->jenisSurat)->Nama_Surat ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Judul/Perihal</div>
                            <div class="col-md-8 info-value">
                                {{ $surat->Judul_Tugas_Surat ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Pengaju</div>
                            <div class="col-md-8 info-value">
                                {{ optional($surat->pemberiTugas)->Name_User ?? 'N/A' }}
                                @if(optional($surat->pemberiTugas)->mahasiswa)
                                    <br><small class="text-muted">NIM: {{ optional($surat->pemberiTugas->mahasiswa)->NIM }}</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Status Surat</div>
                            <div class="col-md-8">
                                <span class="badge bg-success">{{ $surat->Status }}</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-4 fw-bold text-success">
                        <i class="fas fa-signature me-2"></i>Informasi Penandatangan
                    </h5>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Ditandatangani Oleh</div>
                            <div class="col-md-8 info-value">
                                {{ $verification->signed_by }}
                                <br><small class="text-muted">
                                    {{ optional(optional($verification->penandatangan)->role)->Name_Role ?? 'Pejabat' }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-4 info-label">Tanggal & Waktu</div>
                            <div class="col-md-8 info-value">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                {{ $verification->signed_at->format('d F Y') }}
                                <br>
                                <i class="fas fa-clock me-2 text-primary"></i>
                                {{ $verification->signed_at->format('H:i') }} WIB
                            </div>
                        </div>
                    </div>

                    <div class="info-row border-0">
                        <div class="row">
                            <div class="col-md-4 info-label">Token Verifikasi</div>
                            <div class="col-md-8">
                                <code class="bg-light p-2 d-inline-block rounded">
                                    {{ substr($verification->token, 0, 16) }}...
                                </code>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Catatan:</strong> Dokumen ini telah melalui proses verifikasi digital dan dilindungi dengan teknologi QR Code. 
                        Setiap dokumen memiliki token unik yang tidak dapat diduplikasi.
                    </div>
                </div>

            @else
                {{-- DOKUMEN TIDAK VALID --}}
                <div class="card-header-custom card-header-invalid">
                    <div class="status-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h2 class="fw-bold mb-2">Dokumen Tidak Valid</h2>
                    <p class="mb-0">Token verifikasi tidak dikenali</p>
                </div>

                <div class="card-body p-5 text-center">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>{{ $message }}</strong>
                    </div>

                    <p class="text-muted mb-4">
                        Dokumen yang Anda scan mungkin:
                    </p>

                    <ul class="list-unstyled text-start" style="max-width: 400px; margin: 0 auto;">
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i> Tidak terdaftar di sistem</li>
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i> Token verifikasi sudah kadaluarsa</li>
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i> Telah diubah atau dipalsukan</li>
                        <li class="mb-2"><i class="fas fa-times text-danger me-2"></i> QR Code rusak atau tidak lengkap</li>
                    </ul>

                    <div class="alert alert-warning mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Jika Anda yakin dokumen ini sah, silakan hubungi pihak fakultas untuk verifikasi manual.
                    </div>
                </div>
            @endif

            <div class="card-footer bg-light text-center py-3">
                <small class="text-muted">
                    <i class="fas fa-university me-2"></i>
                    Sistem Manajemen Surat Fakultas
                    <br>
                    © {{ date('Y') }} - Powered by Digital Signature Technology
                </small>
            </div>
        </div>

        <div class="back-button">
            <a href="{{ route('login') }}" class="btn btn-light btn-lg shadow">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Login
            </a>
        </div>

        <p class="footer-text">
            <i class="fas fa-lock me-2"></i>
            Sistem ini menggunakan enkripsi dan teknologi QR Code untuk menjamin keaslian dokumen
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
