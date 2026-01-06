@extends('layouts.dekan')

@section('title', 'SK Dosen - Dekan')

@push('styles')
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #e9ecef;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }

    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        height: 100%;
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

    .badge-count {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1rem 0;
        }
        .page-header h3 {
            font-size: 1.1rem;
        }
        .icon-circle {
            width: 60px !important;
            height: 60px !important;
        }
        .icon-circle i {
            font-size: 1.5rem !important;
        }
    }
</style>
@endpush

@section('content')

{{-- Header Halaman --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Surat Keputusan (SK) Dosen</h3>
            <p class="mb-0 text-muted small">Pilih jenis SK Dosen yang akan ditandatangani</p>
        </div>
        <a href="{{ route('dekan.persetujuan.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

{{-- Alert Success/Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Card Pilihan Jenis SK Dosen --}}
<div class="row g-4">
    {{-- SK Beban Mengajar --}}
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('dekan.sk.beban-mengajar.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm hover-card position-relative">
                <span class="badge bg-primary badge-count">
                    {{ $skBebanMengajarCount ?? 0 }}
                </span>
                <div class="card-body text-center p-4">
                    <div class="icon-wrapper mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold mb-2">SK Beban Mengajar</h5>
                    <p class="card-text text-muted small mb-3">
                        Tandatangani SK beban mengajar dosen
                    </p>
                    <button class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-list me-2"></i>Lihat Daftar
                    </button>
                    <small class="text-primary d-block mt-2" style="font-size: 0.7rem;">Total: {{ $skBebanMengajarTotal ?? 0 }} SK</small>
                </div>
            </div>
        </a>
    </div>

    {{-- SK Dosen Wali --}}
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('dekan.persetujuan.sk_dosen_wali') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm hover-card position-relative">
                <span class="badge bg-success badge-count">
                    {{ $skDosenWaliCount ?? 0 }}
                </span>
                <div class="card-body text-center p-4">
                    <div class="icon-wrapper mb-3">
                        <div class="icon-circle bg-success bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-user-graduate fa-2x text-success"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold mb-2">SK Dosen Wali</h5>
                    <p class="card-text text-muted small mb-3">
                        Tandatangani SK penetapan dosen wali mahasiswa
                    </p>
                    <button class="btn btn-success btn-sm w-100">
                        <i class="fas fa-list me-2"></i>Lihat Daftar
                    </button>
                    <small class="text-success d-block mt-2" style="font-size: 0.7rem;">Total: {{ $skDosenWaliTotal ?? 0 }} SK</small>
                </div>
            </div>
        </a>
    </div>

    {{-- SK Pembimbing Skripsi --}}
    <div class="col-xl-3 col-md-6">
        <a href="#" class="text-decoration-none">
            <div class="card border-0 shadow-sm hover-card position-relative">
                <span class="badge bg-warning badge-count">
                    {{ $skPembimbingSkripsiCount ?? 0 }}
                </span>
                <div class="card-body text-center p-4">
                    <div class="icon-wrapper mb-3">
                        <div class="icon-circle bg-warning bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-book-reader fa-2x text-warning"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold mb-2">SK Pembimbing Skripsi</h5>
                    <p class="card-text text-muted small mb-3">
                        Tandatangani SK dosen pembimbing skripsi
                    </p>
                    <button class="btn btn-warning btn-sm w-100 disabled">
                        <i class="fas fa-list me-2"></i>Lihat Daftar
                    </button>
                    <small class="text-muted d-block mt-2" style="font-size: 0.7rem;">Coming Soon</small>
                </div>
            </div>
        </a>
    </div>

    {{-- SK Penguji Skripsi --}}
    <div class="col-xl-3 col-md-6">
        <a href="#" class="text-decoration-none">
            <div class="card border-0 shadow-sm hover-card position-relative">
                <span class="badge bg-danger badge-count">
                    {{ $skPengujiSkripsiCount ?? 0 }}
                </span>
                <div class="card-body text-center p-4">
                    <div class="icon-wrapper mb-3">
                        <div class="icon-circle bg-danger bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-user-check fa-2x text-danger"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold mb-2">SK Penguji Skripsi</h5>
                    <p class="card-text text-muted small mb-3">
                        Tandatangani SK dosen penguji ujian skripsi
                    </p>
                    <button class="btn btn-danger btn-sm w-100 disabled">
                        <i class="fas fa-list me-2"></i>Lihat Daftar
                    </button>
                    <small class="text-muted d-block mt-2" style="font-size: 0.7rem;">Coming Soon</small>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Info Card --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info border-0" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-2">Informasi</h6>
                    <ul class="mb-0 small">
                        <li>Klik salah satu card di atas untuk melihat daftar SK Dosen yang menunggu tanda tangan Anda.</li>
                        <li>Badge menunjukkan jumlah SK yang belum ditandatangani.</li>
                        <li>SK yang telah disetujui oleh Wadek 1 akan muncul di daftar untuk ditandatangani oleh Dekan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Add hover effect
    document.querySelectorAll('.hover-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (!this.classList.contains('disabled')) {
                this.style.borderColor = '#4e73df';
            }
        });
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = '#e9ecef';
        });
    });
</script>
@endpush
