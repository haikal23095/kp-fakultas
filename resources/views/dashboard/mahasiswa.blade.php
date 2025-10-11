@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')

<div class="mb-4">
    <h1 class="h2 fw-light mb-0">Selamat Datang, {{ auth()->user()->Name_User ?? 'Mahasiswa' }}!</h1>
    <p class="text-muted">Ini adalah pusat aktivitas pengajuan surat Anda.</p>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold">Butuh Surat Keterangan?</h4>
            <p class="text-muted mb-md-0">Ajukan surat aktif kuliah, keterangan tidak menerima beasiswa, atau surat pengantar di sini.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-pen-to-square me-2"></i>Buat Pengajuan Baru
            </a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-hourglass-half fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Menunggu Proses</p>
                    <h4 class="fw-bold mb-0">1</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-check-circle fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Selesai & Dapat Diunduh</p>
                    <h4 class="fw-bold mb-0">3</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-file-alt fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Total Pengajuan</p>
                    <h4 class="fw-bold mb-0">4</h4>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-primary">Riwayat Pengajuan Terkini</h6>
        <a href="{{ route('mahasiswa.riwayat.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua Riwayat</a>
    </div>
    <div class="card-body">
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
                        <td>Surat Keterangan Aktif Kuliah</td>
                        <td>10 Okt 2025</td>
                        <td><span class="badge bg-success">Selesai</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Surat Keterangan Tidak Menerima Beasiswa</td>
                        <td>08 Okt 2025</td>
                        <td><span class="badge bg-info">Ditandatangani</span></td>
                        <td class="text-center">-</td>
                    </tr>
                     <tr>
                        <td>Surat Pengantar Kerja Praktik</td>
                        <td>05 Okt 2025</td>
                        <td><span class="badge bg-warning text-dark">Diproses Admin</span></td>
                         <td class="text-center">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection