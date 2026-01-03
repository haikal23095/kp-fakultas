@extends('layouts.admin_fakultas')

@section('title', 'SK Dosen')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Manajemen Surat Keputusan (SK) Dosen</h1>
        <p class="mb-0 text-muted">Kelola pengajuan SK dari Kaprodi</p>
    </div>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRiwayatSK">
            <i class="fas fa-history me-2"></i>Riwayat Pengajuan SK
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- SK Beban Mengajar -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body text-center p-4">
                <div class="icon-wrapper mb-3">
                    <div class="icon-circle bg-primary bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                        <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold mb-2">SK Beban Mengajar</h5>
                <p class="card-text text-muted small mb-3">
                    Kelola surat keputusan beban mengajar dosen
                </p>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">Request Baru</small>
                    <span class="badge bg-primary">{{ $skBebanMengajarCount }}</span>
                </div>
                <a href="#" class="btn btn-primary btn-sm w-100 disabled">
                    <i class="fas fa-list me-2"></i>Lihat Request
                </a>
                <small class="text-muted d-block mt-2" style="font-size: 0.7rem;">Coming Soon</small>
            </div>
        </div>
    </div>

    <!-- SK Dosen Wali -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body text-center p-4">
                <div class="icon-wrapper mb-3">
                    <div class="icon-circle bg-success bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                        <i class="fas fa-user-graduate fa-2x text-success"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold mb-2">SK Dosen Wali</h5>
                <p class="card-text text-muted small mb-3">
                    Kelola surat keputusan penetapan dosen wali mahasiswa
                </p>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">Request Baru</small>
                    <span class="badge bg-success">{{ $skDosenWaliCount }}</span>
                </div>
                <a href="{{ route('admin_fakultas.sk.dosen-wali') }}" class="btn btn-success btn-sm w-100">
                    <i class="fas fa-list me-2"></i>Lihat Request
                </a>
                <small class="text-success d-block mt-2" style="font-size: 0.7rem;">Total: {{ $skDosenWaliTotal }} SK</small>
            </div>
        </div>
    </div>

    <!-- SK Pembimbing Skripsi -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body text-center p-4">
                <div class="icon-wrapper mb-3">
                    <div class="icon-circle bg-warning bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                        <i class="fas fa-book-reader fa-2x text-warning"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold mb-2">SK Pembimbing Skripsi</h5>
                <p class="card-text text-muted small mb-3">
                    Kelola surat keputusan dosen pembimbing skripsi
                </p>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">Request Baru</small>
                    <span class="badge bg-warning text-dark">{{ $skPembimbingSkripsiCount }}</span>
                </div>
                <a href="#" class="btn btn-warning btn-sm w-100 disabled">
                    <i class="fas fa-list me-2"></i>Lihat Request
                </a>
                <small class="text-muted d-block mt-2" style="font-size: 0.7rem;">Coming Soon</small>
            </div>
        </div>
    </div>

    <!-- SK Penguji Skripsi -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body text-center p-4">
                <div class="icon-wrapper mb-3">
                    <div class="icon-circle bg-danger bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                        <i class="fas fa-user-check fa-2x text-danger"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold mb-2">SK Penguji Skripsi</h5>
                <p class="card-text text-muted small mb-3">
                    Kelola surat keputusan dosen penguji ujian skripsi
                </p>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">Request Baru</small>
                    <span class="badge bg-danger">{{ $skPengujiSkripsiCount }}</span>
                </div>
                <a href="#" class="btn btn-danger btn-sm w-100 disabled">
                    <i class="fas fa-list me-2"></i>Lihat Request
                </a>
                <small class="text-muted d-block mt-2" style="font-size: 0.7rem;">Coming Soon</small>
            </div>
        </div>
    </div>
</div>

<!-- Info Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-2">Informasi</h6>
                        <ul class="mb-0 small text-muted">
                            <li>Review dan verifikasi setiap pengajuan SK dari Kaprodi</li>
                            <li>SK yang disetujui akan diteruskan ke Dekan untuk persetujuan final</li>
                            <li>Pastikan data yang ada sudah sesuai sebelum meneruskan ke Dekan</li>
                            <li>SK yang ditolak akan dikembalikan ke Kaprodi dengan alasan penolakan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">Statistik SK Bulan Ini</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-primary mb-0">{{ $skDosenWaliCount }}</h3>
                            <small class="text-muted">Menunggu Proses</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-warning mb-0">0</h3>
                            <small class="text-muted">Di Dekan</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-success mb-0">0</h3>
                            <small class="text-muted">Selesai</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-danger mb-0">0</h3>
                        <small class="text-muted">Ditolak</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Pengajuan SK -->
<div class="modal fade" id="modalRiwayatSK" tabindex="-1" aria-labelledby="modalRiwayatSKLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalRiwayatSKLabel">
                    <i class="fas fa-history me-2"></i>Riwayat Pengajuan SK
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Pilih jenis SK untuk melihat riwayat pengajuan:</p>
                
                <div class="row g-3">
                    <!-- SK Beban Mengajar -->
                    <div class="col-md-6">
                        <div class="card h-100 border shadow-sm sk-card-disabled">
                            <div class="card-body text-center p-4">
                                <div class="icon-circle bg-primary bg-opacity-10 mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-2">SK Beban Mengajar</h6>
                                <p class="small text-muted mb-3">Riwayat SK beban mengajar dosen</p>
                                <span class="badge bg-secondary mb-2">Coming Soon</span>
                                <button class="btn btn-primary btn-sm w-100 disabled">
                                    <i class="fas fa-list me-1"></i>Lihat Riwayat
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- SK Dosen Wali -->
                    <div class="col-md-6">
                        <a href="{{ route('admin_fakultas.sk.dosen-wali.history') }}" class="text-decoration-none">
                            <div class="card h-100 border shadow-sm sk-card-active">
                                <div class="card-body text-center p-4">
                                    <div class="icon-circle bg-success bg-opacity-10 mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="fas fa-user-graduate fa-2x text-success"></i>
                                    </div>
                                    <h6 class="fw-bold mb-2">SK Dosen Wali</h6>
                                    <p class="small text-muted mb-3">Riwayat SK dosen wali mahasiswa</p>
                                    <span class="badge bg-success mb-2">{{ $skDosenWaliTotal }} Total SK</span>
                                    <button class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-list me-1"></i>Lihat Riwayat
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- SK Pembimbing Skripsi -->
                    <div class="col-md-6">
                        <div class="card h-100 border shadow-sm sk-card-disabled">
                            <div class="card-body text-center p-4">
                                <div class="icon-circle bg-warning bg-opacity-10 mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fas fa-book-reader fa-2x text-warning"></i>
                                </div>
                                <h6 class="fw-bold mb-2">SK Pembimbing Skripsi</h6>
                                <p class="small text-muted mb-3">Riwayat SK pembimbing skripsi</p>
                                <span class="badge bg-secondary mb-2">Coming Soon</span>
                                <button class="btn btn-warning btn-sm w-100 disabled">
                                    <i class="fas fa-list me-1"></i>Lihat Riwayat
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- SK Penguji Skripsi -->
                    <div class="col-md-6">
                        <div class="card h-100 border shadow-sm sk-card-disabled">
                            <div class="card-body text-center p-4">
                                <div class="icon-circle bg-danger bg-opacity-10 mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fas fa-user-check fa-2x text-danger"></i>
                                </div>
                                <h6 class="fw-bold mb-2">SK Penguji Skripsi</h6>
                                <p class="small text-muted mb-3">Riwayat SK penguji skripsi</p>
                                <span class="badge bg-secondary mb-2">Coming Soon</span>
                                <button class="btn btn-danger btn-sm w-100 disabled">
                                    <i class="fas fa-list me-1"></i>Lihat Riwayat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .icon-circle {
        transition: transform 0.2s ease;
    }
    
    .hover-card:hover .icon-circle {
        transform: scale(1.1);
    }

    .sk-card-active {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .sk-card-active:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .sk-card-disabled {
        opacity: 0.7;
    }
</style>
@endpush

@endsection
