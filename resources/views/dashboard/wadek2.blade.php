@extends('layouts.wadek2')

@section('title', 'Dashboard Wadek 2')

@section('content')

<div class="mb-5">
    <h1 class="h2 fw-light mb-0">Selamat Datang, {{ auth()->user()->Name_User ?? 'Bapak/Ibu Wadek 2' }}!</h1>
    <p class="text-muted">Berikut adalah ringkasan data Sarana Prasarana dan SDM fakultas Anda.</p>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-building fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Total Ruang</p>
                    <h4 class="fw-bold mb-0">45</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-car fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Mobil Dinas</p>
                    <h4 class="fw-bold mb-0">5</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-users fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Total Pegawai</p>
                    <h4 class="fw-bold mb-0">32</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-file-signature fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">SK Pending</p>
                    <h4 class="fw-bold mb-0">8</h4>
                </div>
            </div>
        </div>
    </div>
</div>


<h2 class="h4 fw-light mt-4 mb-3">Menu Aksi Utama</h2>
<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm lift">
            <div class="card-body text-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-building fs-4"></i>
                </div>
                <h5 class="card-title">Sarana Prasarana</h5>
                <p class="card-text text-muted">Kelola persetujuan peminjaman ruang dan mobil.</p>
                <a href="{{ route('wadek2.sarpras.persetujuan-mobil') }}" class="btn btn-outline-primary btn-sm mt-2">Buka Menu</a>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm lift">
            <div class="card-body text-center">
                <div class="bg-success bg-opacity-10 text-success rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-users fs-4"></i>
                </div>
                <h5 class="card-title">SDM Kepegawaian</h5>
                <p class="card-text text-muted">Validasi cuti dan lembur pegawai fakultas.</p>
                <a href="{{ route('wadek2.sdm.validasi-cuti') }}" class="btn btn-outline-success btn-sm mt-2">Buka Menu</a>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm lift">
            <div class="card-body text-center">
                <div class="bg-info bg-opacity-10 text-info rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-file-signature fs-4"></i>
                </div>
                <h5 class="card-title">Surat Keputusan</h5>
                <p class="card-text text-muted">Validasi SK tingkat fakultas.</p>
                <a href="{{ route('wadek2.sk.validasi-sk-fakultas') }}" class="btn btn-outline-info btn-sm mt-2">Buka Menu</a>
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
