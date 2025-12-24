@extends('layouts.admin_fakultas')

@section('title', 'Manajemen Surat')

@push('styles')
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #f0f0f0;
        padding: 2rem 0;
        margin-bottom: 2.5rem;
    }
    
    .card-jenis-surat {
        border: 1px solid #e3e6f0;
        border-radius: 16px;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    
    .card-jenis-surat:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        border-color: #4e73df;
    }
    
    .card-jenis-surat .card-body {
        padding: 2.5rem 2rem;
        text-align: center;
    }
    
    .card-icon {
        width: 90px;
        height: 90px;
        margin: 0 auto 1.75rem;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.75rem;
    }
    
    .card-icon.blue {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }
    
    .card-icon.green {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(28, 200, 138, 0.3);
    }
    
    .card-icon.orange {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(246, 194, 62, 0.3);
    }
    
    .card-jenis-surat h5 {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
    }
    
    .card-jenis-surat p {
        color: #858796;
        font-size: 0.95rem;
        margin-bottom: 0;
        line-height: 1.6;
    }
    
    .badge-count {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 0.9rem;
        padding: 0.6rem 1rem;
        border-radius: 10px;
        font-weight: 600;
    }
    
    .stats-row {
        display: flex;
        justify-content: space-around;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e3e6f0;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: #858796;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #5a5c69;
    }
    
    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 2rem;
        margin-top: 2rem;
    }
    
    .info-card h6 {
        color: white;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem 0;
        }
        .page-header h3 {
            font-size: 1.25rem;
        }
        .page-header p {
            font-size: 0.85rem;
        }
        .card-jenis-surat .card-body {
            padding: 2rem 1.5rem;
        }
        .card-icon {
            width: 70px;
            height: 70px;
            font-size: 2.25rem;
            margin-bottom: 1.25rem;
        }
        .card-jenis-surat h5 {
            font-size: 1.1rem;
        }
        .badge-count {
            font-size: 0.8rem;
            padding: 0.4rem 0.7rem;
        }
    }
</style>
@endpush

@section('content')

{{-- Header Halaman --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-2 fw-bold text-dark">Manajemen Surat Fakultas</h3>
            <p class="mb-0 text-muted">Pilih jenis surat untuk mengelola pengajuan dan persetujuan</p>
            <small class="text-info">Total: {{ $totalSemua ?? 0 }} surat</small>
        </div>
    </div>
</div>

{{-- Alert Success/Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #1cc88a;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #e74a3b;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Card Pilihan Jenis Surat --}}
<div class="row">
    {{-- Card Surat Keterangan Aktif --}}
    <div class="col-lg-4 col-md-6 mb-4">
        <a href="{{ route('admin_fakultas.surat.aktif') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-primary badge-count">
                    {{ $countAktif ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon blue">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h5>Surat Keterangan Aktif</h5>
                    <p>Kelola pengajuan surat keterangan mahasiswa aktif kuliah</p>
                    
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-label">Pending</div>
                            <div class="stat-value text-warning">{{ $pendingAktif ?? 0 }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Proses</div>
                            <div class="stat-value text-info">{{ $prosesAktif ?? 0 }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Selesai</div>
                            <div class="stat-value text-success">{{ $selesaiAktif ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Surat Pengantar Magang --}}
    <div class="col-lg-4 col-md-6 mb-4">
        <a href="{{ route('admin_fakultas.surat.magang') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-success badge-count">
                    {{ $countMagang ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon green">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h5>Surat Pengantar KP/Magang</h5>
                    <p>Kelola pengajuan surat pengantar kerja praktek dan magang</p>
                    
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-label">Pending</div>
                            <div class="stat-value text-warning">{{ $pendingMagang ?? 0 }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Proses</div>
                            <div class="stat-value text-info">{{ $prosesMagang ?? 0 }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Selesai</div>
                            <div class="stat-value text-success">{{ $selesaiMagang ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Legalisir Online --}}
    <div class="col-lg-4 col-md-6 mb-4">
        <a href="{{ route('admin_fakultas.surat_legalisir.index') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-warning badge-count">
                    {{ $countLegalisir ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon orange">
                        <i class="fas fa-stamp"></i>
                    </div>
                    <h5>Legalisir Online</h5>
                    <p>Kelola pengajuan legalisir ijazah dan transkrip nilai</p>
                    
                    <div class="stats-row">
                        <div class="stat-item">
                            <div class="stat-label">Pending</div>
                            <div class="stat-value text-warning">{{ $pendingLegalisir ?? 0 }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Proses</div>
                            <div class="stat-value text-info">{{ $prosesLegalisir ?? 0 }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Selesai</div>
                            <div class="stat-value text-success">{{ $selesaiLegalisir ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Info Card --}}
<div class="row">
    <div class="col-12">
        <div class="info-card shadow">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                <div>
                    <h6 class="fw-bold mb-2">Informasi Manajemen Surat</h6>
                    <p class="mb-0" style="opacity: 0.95;">
                        Klik salah satu card di atas untuk mengelola jenis surat tertentu. Setiap jenis surat memiliki 
                        alur persetujuan dan proses yang berbeda. Pastikan untuk memproses surat sesuai dengan 
                        prioritas dan tenggat waktu yang telah ditentukan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Smooth hover animation
    document.querySelectorAll('.card-jenis-surat').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = '#4e73df';
        });
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = '#e3e6f0';
        });
    });
</script>
@endpush
