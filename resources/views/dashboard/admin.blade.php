@extends('layouts.admin')

@section('title', 'Dashboard Administrator')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Dashboard Administrasi Surat</h1>
        <p class="mb-0 text-muted">Selamat datang, {{ auth()->user()->Name_User ?? 'Administrator' }}. Berikut adalah ringkasan aktivitas sistem persuratan.</p>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-danger border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Permohonan Baru</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">5</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-inbox fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Menunggu TTE Dekan</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">2</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-signature fa-2x text-gray-300"></i>
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
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Surat Selesai (Bulan Ini)</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">48</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Total Arsip Surat</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">1,250</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-archive fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Antrian Permohonan Surat Terkini</h6>
                <a href="#" class="btn btn-outline-primary btn-sm">Lihat Semua Permohonan</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Jenis Surat</th>
                                <th>Pemohon</th>
                                <th>Tgl. Masuk</th>
                                <th>Prioritas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Surat Tugas Dosen</td>
                                <td>Dr. Siti Aminah, M.Pd.</td>
                                <td>11 Okt 2025</td>
                                <td><span class="badge bg-danger">Urgent</span></td>
                                <td><a href="#" class="btn btn-primary btn-sm">Proses</a></td>
                            </tr>
                            <tr>
                                <td>Surat Aktif Kuliah</td>
                                <td>Budi Santoso</td>
                                <td>11 Okt 2025</td>
                                <td><span class="badge bg-secondary">Normal</span></td>
                                <td><a href="#" class="btn btn-primary btn-sm">Proses</a></td>
                            </tr>
                            <tr>
                                <td>Tidak Menerima Beasiswa</td>
                                <td>Citra Lestari</td>
                                <td>10 Okt 2025</td>
                                <td><span class="badge bg-secondary">Normal</span></td>
                                <td><a href="#" class="btn btn-primary btn-sm">Proses</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark">Log Kendala Sistem</h6>
                <a href="#" class="btn btn-outline-dark btn-sm"><i class="fas fa-plus"></i></a>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Server Unnes Down</div>
                            <small class="text-muted">10 Okt 2025 - 14:30</small>
                        </div>
                        <span class="badge bg-danger rounded-pill">Major</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Jaringan Lambat</div>
                            <small class="text-muted">10 Okt 2025 - 09:00</small>
                        </div>
                        <span class="badge bg-warning rounded-pill text-dark">Minor</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection