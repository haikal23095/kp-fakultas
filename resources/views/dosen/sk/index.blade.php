@extends('layouts.dosen')

@section('title', 'SK Dosen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Surat Keputusan (SK) Dosen</h1>
        <p class="mb-0 text-muted">Lihat SK yang melibatkan Anda</p>
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
                <p class="card-text text-muted small mb-4">
                    Lihat SK beban mengajar yang melibatkan Anda
                </p>
                <a href="{{ route('dosen.sk.beban-mengajar.index') }}" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-eye me-2"></i>Lihat SK
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
                    Lihat SK dosen wali yang melibatkan Anda
                </p>
                <a href="{{ route('dosen.sk.dosen-wali.index') }}" class="btn btn-success btn-sm w-100">
                    <i class="fas fa-eye me-2"></i>Lihat SK
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
                    Lihat SK pembimbing skripsi yang melibatkan Anda
                </p>
                <a href="{{ route('dosen.sk.pembimbing-skripsi.index') }}" class="btn btn-warning btn-sm w-100">
                    <i class="fas fa-eye me-2"></i>Lihat SK
                </a>
            </div>
        </div>
    </div>

    <!-- SK Penguji Skripsi -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card opacity-50">
            <div class="card-body text-center p-4">
                <div class="icon-wrapper mb-3">
                    <div class="icon-circle bg-danger bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                        <i class="fas fa-user-check fa-2x text-danger"></i>
                    </div>
                </div>
                <h5 class="card-title fw-bold mb-2">SK Penguji Skripsi</h5>
                <p class="card-text text-muted small mb-4">
                    Lihat SK penguji skripsi yang melibatkan Anda
                </p>
                <button class="btn btn-danger btn-sm w-100" disabled>
                    <i class="fas fa-lock me-2"></i>Segera Hadir
                </button>
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
                            <li>Halaman ini menampilkan SK yang telah disetujui dan melibatkan Anda sebagai dosen</li>
                            <li>SK yang sudah ditandatangani oleh Dekan akan otomatis muncul di sini</li>
                            <li>Anda dapat melihat detail dan mengunduh SK dalam format PDF</li>
                            <li>Jika ada pertanyaan terkait SK, silakan hubungi admin fakultas atau Kaprodi</li>
                        </ul>
                    </div>
                </div>
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
    
    .hover-card:hover:not(.opacity-50) {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .icon-circle {
        transition: transform 0.2s ease;
    }
    
    .hover-card:hover .icon-circle {
        transform: scale(1.1);
    }
    
    .opacity-50 {
        opacity: 0.6;
        cursor: not-allowed !important;
    }
</style>
@endpush

@endsection
