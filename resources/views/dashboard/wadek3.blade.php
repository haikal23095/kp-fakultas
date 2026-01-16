@extends('layouts.wadek3')

@section('title', 'Dashboard Wadek 3')

@section('content')

<div class="mb-5">
    <h1 class="h2 fw-light mb-0">Selamat Datang, {{ auth()->user()->Name_User ?? 'Bapak/Ibu Wadek 3' }}!</h1>
    <p class="text-muted">Berikut adalah ringkasan data Kemahasiswaan fakultas Anda.</p>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-graduate fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Total Mahasiswa</p>
                    <h4 class="fw-bold mb-0">850</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-trophy fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Prestasi Aktif</p>
                    <h4 class="fw-bold mb-0">42</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-calendar-check fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Dispensasi Pending</p>
                    <h4 class="fw-bold mb-0">12</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-certificate fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">SKBK Pending</p>
                    <h4 class="fw-bold mb-0">5</h4>
                </div>
            </div>
        </div>
    </div>
</div>


<h2 class="h4 fw-light mt-4 mb-3">Menu Aksi Utama</h2>
<div class="row">
    <div class="col-lg-6 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm lift">
            <div class="card-body text-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-calendar-check fs-4"></i>
                </div>
                <h5 class="card-title">Validasi Dispensasi</h5>
                <p class="card-text text-muted">Kelola validasi dispensasi kegiatan mahasiswa.</p>
                <a href="{{ route('wadek3.kemahasiswaan.validasi-dispensasi') }}" class="btn btn-outline-primary btn-sm mt-2">Buka Menu</a>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm lift">
            <div class="card-body text-center">
                <div class="bg-success bg-opacity-10 text-success rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-certificate fs-4"></i>
                </div>
                <h5 class="card-title">Surat Kelakuan Baik</h5>
                <p class="card-text text-muted">Validasi dan persetujuan surat kelakuan baik mahasiswa.</p>
                <a href="{{ route('wadek3.kemahasiswaan.validasi-kelakuan-baik') }}" class="btn btn-outline-success btn-sm mt-2">Buka Menu</a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card.lift {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .card.lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endpush

@endsection
