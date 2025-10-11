@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Dashboard Dosen</h1>
        <p class="mb-0 text-muted">Selamat datang, {{ auth()->user()->Name_User ?? 'Bapak/Ibu Dosen' }}. Kelola pengajuan surat tugas Anda di sini.</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold">Ajukan Surat Tugas</h4>
            <p class="text-muted mb-md-0">Buat permohonan surat untuk kegiatan, seminar, atau keperluan dinas lainnya.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="#" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i>Buat Pengajuan
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-start border-warning border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Surat Dalam Proses</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">1</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-start fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-start border-success border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Surat Selesai</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">5</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-double fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-start border-secondary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Total Pengajuan Anda</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">6</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-primary">Riwayat Pengajuan Surat Anda</h6>
        <a href="#" class="btn btn-outline-primary btn-sm">Lihat Semua Riwayat</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Jenis Surat</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Surat Tugas Pemateri Seminar</td>
                        <td>10 Okt 2025</td>
                        <td><span class="badge bg-success">Selesai</span></td>
                        <td class="text-center"><a href="#" class="btn btn-primary btn-sm"><i class="fas fa-download"></i></a></td>
                    </tr>
                    <tr>
                        <td>Permohonan Izin Kegiatan</td>
                        <td>08 Okt 2025</td>
                        <td><span class="badge bg-info">Ditandatangani Dekan</span></td>
                        <td class="text-center">-</td>
                    </tr>
                    <tr>
                        <td>Surat Tugas Penelitian</td>
                        <td>05 Okt 2025</td>
                        <td><span class="badge bg-warning text-dark">Menunggu Persetujuan Jurusan</span></td>
                        <td class="text-center">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection