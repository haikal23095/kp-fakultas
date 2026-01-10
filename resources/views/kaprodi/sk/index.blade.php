@extends('layouts.kaprodi')

@section('title', 'Ajukan SK')

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
        <h1 class="h3 fw-bold mb-0">Ajukan Surat Keputusan (SK)</h1>
        <p class="mb-0 text-muted">Pilih jenis SK yang ingin Anda ajukan</p>
    </div>
    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#riwayatModal">
        <i class="fas fa-history me-2"></i>Riwayat Pengajuan SK
    </button>
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
                <p class="card-text text-muted small mb-4">
                    Ajukan surat keputusan untuk beban mengajar dosen di semester aktif
                </p>
                <a href="{{ route('kaprodi.sk.beban-mengajar.create') }}" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-plus-circle me-2"></i>Ajukan SK
                </a>
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
                <p class="card-text text-muted small mb-4">
                    Ajukan surat keputusan untuk penetapan dosen wali mahasiswa
                </p>
                <a href="{{ route('kaprodi.sk.dosen-wali.create') }}" class="btn btn-success btn-sm w-100">
                    <i class="fas fa-plus-circle me-2"></i>Ajukan SK
                </a>
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
                <p class="card-text text-muted small mb-4">
                    Ajukan surat keputusan untuk dosen pembimbing skripsi mahasiswa
                </p>
                <a href="{{ route('kaprodi.sk.pembimbing-skripsi.create') }}" class="btn btn-warning btn-sm w-100">
                    <i class="fas fa-plus-circle me-2"></i>Ajukan SK
                </a>
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
                <p class="card-text text-muted small mb-4">
                    Ajukan surat keputusan untuk dosen penguji ujian skripsi
                </p>
                <a href="{{ route('kaprodi.sk.penguji-skripsi.create') }}" class="btn btn-danger btn-sm w-100">
                    <i class="fas fa-plus-circle me-2"></i>Ajukan SK
                </a>
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
                            <li>Pastikan data yang diinput sudah benar sebelum mengajukan SK</li>
                            <li>SK yang telah diajukan akan diproses oleh admin fakultas</li>
                            <li>Anda dapat memantau status pengajuan SK di menu masing-masing</li>
                            <li>SK yang disetujui akan tersedia untuk diunduh dalam format PDF</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Pengajuan SK -->
<div class="modal fade" id="riwayatModal" tabindex="-1" aria-labelledby="riwayatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="riwayatModalLabel">
                    <i class="fas fa-history me-2"></i>Riwayat Pengajuan SK
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Pilih jenis SK untuk melihat riwayat pengajuan Anda</p>
                
                <div class="row g-4">
                    <!-- SK Beban Mengajar -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card border hover-card-modal h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="icon-circle bg-primary bg-opacity-10 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fas fa-chalkboard-teacher fa-lg text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">SK Beban Mengajar</h6>
                                    <p class="mb-0 small text-muted">Lihat riwayat pengajuan SK beban mengajar</p>
                                </div>
                                <a href="{{ route('kaprodi.sk.beban-mengajar.history') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- SK Dosen Wali -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card border hover-card-modal h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="icon-circle bg-success bg-opacity-10 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fas fa-user-graduate fa-lg text-success"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">SK Dosen Wali</h6>
                                    <p class="mb-0 small text-muted">Lihat riwayat pengajuan SK dosen wali</p>
                                </div>
                                <a href="{{ route('kaprodi.sk.dosen-wali.index') }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- SK Pembimbing Skripsi -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card border hover-card-modal h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="icon-circle bg-warning bg-opacity-10 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fas fa-book-reader fa-lg text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">SK Pembimbing Skripsi</h6>
                                    <p class="mb-0 small text-muted">Lihat riwayat pengajuan SK pembimbing</p>
                                </div>
                                <a href="{{ route('kaprodi.sk.pembimbing-skripsi.history') }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- SK Penguji Skripsi -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card border hover-card-modal h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="icon-circle bg-danger bg-opacity-10 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fas fa-user-check fa-lg text-danger"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">SK Penguji Skripsi</h6>
                                    <p class="mb-0 small text-muted">Lihat riwayat pengajuan SK penguji</p>
                                </div>
                                <a href="{{ route('kaprodi.sk.penguji-skripsi.history') }}" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
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

    .hover-card-modal {
        transition: all 0.2s ease;
    }

    .hover-card-modal:hover {
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
        transform: translateX(5px);
    }
</style>
@endpush

@endsection
