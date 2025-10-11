@extends('layouts.app')

@section('title', 'Dashboard Kepala Jurusan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Dashboard Kepala Jurusan</h1>
        <p class="mb-0 text-muted">Selamat datang, {{ auth()->user()->Name_User ?? 'Kepala Jurusan' }}. Kelola persetujuan surat dan monitor aktivitas jurusan Anda.</p>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-danger border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Permohonan Persetujuan</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">1</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-inbox fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Surat Dikirim ke Fakultas</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">5</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Dosen di Jurusan</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">12</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-secondary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Total Arsip Jurusan</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">75</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-archive fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">Antrian Persetujuan Surat Dosen</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Jenis Surat</th>
                        <th>Tgl. Masuk</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Dr. Anisa Rahmawati, M.T.</td>
                        <td>Surat Tugas Penelitian</td>
                        <td>11 Okt 2025</td>
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-sm">Detail</a>
                            <a href="#" class="btn btn-success btn-sm">Setujui</a>
                            <a href="#" class="btn btn-danger btn-sm">Tolak</a>
                        </td>
                    </tr>
                    {{-- Baris lain akan muncul jika ada pengajuan baru --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <a href="#" class="card lift h-100 text-decoration-none">
            <div class="card-body text-center">
                <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                <h5 class="card-title mb-2">Ajukan Surat Pribadi</h5>
                <p class="card-text text-muted">Buat pengajuan surat tugas untuk diri sendiri.</p>
            </div>
        </a>
    </div>
    <div class="col-lg-6 mb-4">
        <a href="#" class="card lift h-100 text-decoration-none">
            <div class="card-body text-center">
                <i class="fas fa-history fa-3x text-secondary mb-3"></i>
                <h5 class="card-title mb-2">Riwayat & Arsip Jurusan</h5>
                <p class="card-text text-muted">Lihat semua surat yang telah diproses oleh jurusan.</p>
            </div>
        </a>
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