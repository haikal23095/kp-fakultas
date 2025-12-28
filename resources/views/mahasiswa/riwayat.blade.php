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

    .card-icon.orange {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        color: white;
    }
    
    .card-icon.teal {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }
    
    .card-icon.red {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
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
            <a href="{{ route('dashboard.mahasiswa') }}" class="btn btn-outline-secondary me-2">
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
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.aktif') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-primary badge-count">
                    {{ $countAktif ?? 0 }} Surat
                </span>
                <div class="card-body">
                    <div class="card-icon blue">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h5>Surat Keterangan Aktif</h5>
                    <p>Riwayat pengajuan surat aktif kuliah</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Surat Pengantar Magang --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.magang') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-danger badge-count">
                    {{ $countMagang ?? 0 }} Surat
                </span>
                <div class="card-body">
                    <div class="card-icon green">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h5>Surat Pengantar Magang</h5>
                    <p>Riwayat pengajuan surat magang/KP</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Legalisir Online --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.legalisir') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-success badge-count">
                    {{ $countLegalisir ?? 0 }} Surat
                </span>
                <div class="card-body">
                    <div class="card-icon orange">
                        <i class="fas fa-stamp"></i>
                    </div>
                    <h5>Legalisir Online</h5>
                    <p>Riwayat pengajuan legalisir</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Peminjaman Mobil Dinas --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.mobil_dinas') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-info badge-count">{{ $countMobilDinas ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon teal"><i class="fas fa-car"></i></div>
                    <h5>Peminjaman Mobil Dinas</h5>
                    <p>Riwayat peminjaman mobil dinas</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Tidak Menerima Beasiswa --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.tidak_beasiswa') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-warning badge-count">{{ $countTidakBeasiswa ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon red"><i class="fas fa-money-bill-wave"></i></div>
                    <h5>Ket. Tidak Menerima Beasiswa</h5>
                    <p>Riwayat surat keterangan beasiswa</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Cek Plagiasi --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.cek_plagiasi') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-secondary badge-count">{{ $countCekPlagiasi ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon blue"><i class="fas fa-search"></i></div>
                    <h5>Cek Plagiasi</h5>
                    <p>Riwayat permohonan cek plagiasi</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Dispensasi --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.dispensasi') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-primary badge-count">{{ $countDispensasi ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon green"><i class="fas fa-clock"></i></div>
                    <h5>Surat Dispensasi</h5>
                    <p>Riwayat pengajuan dispensasi</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Berkelakuan Baik --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.berkelakuan_baik') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-success badge-count">{{ $countBerkelakuanBaik ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon orange"><i class="fas fa-user-check"></i></div>
                    <h5>Ket. Berkelakuan Baik</h5>
                    <p>Riwayat surat berkelakuan baik</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Surat Tugas --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.surat_tugas') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-info badge-count">{{ $countSuratTugas ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon teal"><i class="fas fa-tasks"></i></div>
                    <h5>Surat Tugas</h5>
                    <p>Riwayat permohonan surat tugas</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card MBKM --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.mbkm') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-warning badge-count">{{ $countMBKM ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon red"><i class="fas fa-graduation-cap"></i></div>
                    <h5>Rekomendasi MBKM</h5>
                    <p>Riwayat surat rekomendasi MBKM</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Peminjaman Gedung --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.peminjaman_gedung') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-danger badge-count">{{ $countPeminjamanGedung ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon blue"><i class="fas fa-building"></i></div>
                    <h5>Peminjaman Gedung</h5>
                    <p>Riwayat peminjaman gedung</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Lembur --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.lembur') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-secondary badge-count">{{ $countLembur ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon green"><i class="fas fa-moon"></i></div>
                    <h5>Surat Perintah Lembur</h5>
                    <p>Riwayat surat perintah lembur</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Peminjaman Ruang --}}
    <div class="col-md-4 mb-4">
        <a href="{{ route('mahasiswa.riwayat.peminjaman_ruang') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-primary badge-count">{{ $countPeminjamanRuang ?? 0 }} Surat</span>
                <div class="card-body">
                    <div class="card-icon orange"><i class="fas fa-door-open"></i></div>
                    <h5>Peminjaman Ruang</h5>
                    <p>Riwayat peminjaman ruang</p>
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