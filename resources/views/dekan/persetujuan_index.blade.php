@extends('layouts.dekan')

@section('title', 'Persetujuan Surat')

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
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }
    
    .card-icon.purple {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        color: #333;
    }
    
    .card-icon.teal {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        color: white;
    }
    
    .card-icon.pink {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        color: #333;
    }
    
    .card-icon.yellow {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        color: #333;
    }
    
    .card-icon.indigo {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
            <h3 class="mb-1 fw-bold text-dark">Persetujuan Surat</h3>
            <p class="mb-0 text-muted small">Pilih jenis surat untuk melihat daftar yang menunggu persetujuan</p>
        </div>
        <a href="{{ route('dashboard.dekan') }}" class="btn btn-outline-secondary">
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

{{-- Card Pilihan Jenis Surat --}}
<div class="row">
    {{-- Card Surat Keterangan Aktif --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <a href="{{ route('dekan.persetujuan.aktif') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-primary badge-count">
                    {{ $countAktif ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon blue">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h5>Surat Keterangan Aktif</h5>
                    <p>Verifikasi dan tandatangani surat keterangan mahasiswa aktif kuliah</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Surat Pengantar Magang --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <a href="{{ route('dekan.surat_magang.index') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-danger badge-count">
                    {{ $countMagang ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon green">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h5>Surat Pengantar KP/Magang</h5>
                    <p>Verifikasi dan tandatangani surat pengantar magang mahasiswa</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Legalisir --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <a href="{{ route('dekan.persetujuan.legalisir') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-success badge-count">
                    {{ $countLegalisir ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon orange">
                        <i class="fas fa-stamp"></i>
                    </div>
                    <h5>Legalisir Ijazah/Transkrip</h5>
                    <p>Tandatangani berkas legalisir ijazah dan transkrip nilai</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Surat Cuti Dosen --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <a href="{{ route('dekan.persetujuan.cuti_dosen') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-warning badge-count">
                    {{ $countCutiDosen ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon purple">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <h5>Surat Cuti Dosen</h5>
                    <p>Disposisi dan tandatangani surat ijin cuti dosen</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Surat Keterangan Tidak Menerima Beasiswa --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <a href="{{ route('dekan.persetujuan.tidak_beasiswa') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-info badge-count">
                    {{ $countTidakBeasiswa ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon teal">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h5>Tidak Menerima Beasiswa</h5>
                    <p>Verifikasi dan tandatangani surat keterangan tidak menerima beasiswa</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card SK Fakultas --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <a href="{{ route('dekan.persetujuan.sk_fakultas') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-secondary badge-count">
                    {{ $countSKFakultas ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon pink">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h5>SK Fakultas Teknik</h5>
                    <p>Keluarkan dan tandatangani Surat Keputusan fakultas</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Surat Tugas --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <a href="{{ route('dekan.persetujuan.surat_tugas') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-dark badge-count">
                    {{ $countSuratTugas ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon yellow">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5>Surat Tugas</h5>
                    <p>Keluarkan surat tugas untuk dosen dan pegawai</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Card Surat Rekomendasi MBKM --}}
    <div class="col-md-6 col-lg-4 mb-4">
        <a href="{{ route('dekan.persetujuan.mbkm') }}" class="text-decoration-none">
            <div class="card card-jenis-surat position-relative">
                <span class="badge bg-primary badge-count">
                    {{ $countMBKM ?? 0 }}
                </span>
                <div class="card-body">
                    <div class="card-icon indigo">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h5>Rekomendasi MBKM</h5>
                    <p>Tandatangani surat rekomendasi program MBKM mahasiswa</p>
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
                    <p class="mb-0">Klik salah satu card di atas untuk melihat daftar surat yang menunggu persetujuan dan tanda tangan Anda. Badge menunjukkan jumlah surat yang belum diproses.</p>
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
