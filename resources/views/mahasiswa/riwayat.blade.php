@extends('layouts.mahasiswa')

@section('title', $title ?? 'Riwayat Pengajuan Surat')

@push('styles')
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #e9ecef;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .card-jenis-surat {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
        background: #ffffff;
    }
    
    .card-jenis-surat:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #4e73df;
    }
    
    .card-jenis-surat .card-body {
        padding: 2rem;
        text-align: center;
    }
    
    .card-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }
    
    .card-icon.blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .card-icon.green {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .card-jenis-surat h5 {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .card-jenis-surat p {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0;
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
        .page-header p {
            font-size: 0.8rem;
        }
        .page-header .btn {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        .card-jenis-surat .card-body {
            padding: 1.5rem 1rem;
        }
        .card-icon {
            width: 60px;
            height: 60px;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .card-jenis-surat h5 {
            font-size: 1rem;
        }
        .badge-count {
            font-size: 0.75rem;
            padding: 0.35rem 0.6rem;
        }
    }
</style>
@endpush

@section('content')

{{-- Header Halaman --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-1 fw-bold text-dark">Riwayat Pengajuan Surat</h3>
            <p class="mb-0 text-muted small">Pilih jenis surat untuk melihat riwayat pengajuan</p>
        </div>
        <div>
            <a href="{{ route('mahasiswa.riwayat') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajukan Surat Baru
            </a>
        </div>
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

{{-- Card Pilihan Jenis Surat --}}
<div class="row">
    {{-- Card Surat Keterangan Aktif --}}
    <div class="col-md-6 mb-4">
        <a href="{{ route('mahasiswa.riwayat.aktif') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-primary badge-count">
                    {{ $countAktif ?? 0 }} Surat
                </span>
                <div class="card-body">
                    <div class="card-icon blue">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h5>Surat Keterangan Aktif Kuliah</h5>
                    <p>Lihat riwayat pengajuan surat keterangan mahasiswa aktif</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Surat Pengantar Magang --}}
    <div class="col-md-6 mb-4">
        <a href="{{ route('mahasiswa.riwayat.magang') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-danger badge-count">
                    {{ $countMagang ?? 0 }} Surat
                </span>
                <div class="card-body">
                    <div class="card-icon green">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h5>Surat Pengantar KP/Magang</h5>
                    <p>Lihat riwayat pengajuan surat pengantar kerja praktek dan magang</p>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Info Card --}}
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info border-0" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-2">Informasi</h6>
                    <p class="mb-0">Klik salah satu card di atas untuk melihat riwayat pengajuan surat sesuai jenisnya. Setiap jenis surat memiliki status dan alur persetujuan yang berbeda.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Add hover effect
    document.querySelectorAll('.card-jenis-surat').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = '#4e73df';
        });
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = '#e9ecef';
        });
    });
</script>
@endpush