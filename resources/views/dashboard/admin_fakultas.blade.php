@extends('layouts.admin_fakultas')

@section('title', 'Dashboard Administrator Fakultas')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Dashboard Administrasi Admin Fakultas {{ $namaFakultas ?? 'Fakultas' }}</h1>
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
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $permohonanBaru }}</div>
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
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $menungguTTE }}</div>
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
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $suratSelesaiBulanIni }}</div>
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
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($totalArsip) }}</div>
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
                <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-outline-primary btn-sm">Lihat Semua Permohonan</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Jenis Surat</th>
                                <th>Pemohon</th>
                                <th>Tgl. Masuk</th>
                                <th>Civitas Akademika</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($antrianSurat as $surat)
                            <tr>
                                <td>{{ $surat->jenisSurat->Nama_Surat ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        // Ambil nama pemohon (pemberi tugas = yang mengajukan)
                                        $namaPemohon = $surat->pemberiTugas->Name_User ?? 'N/A';
                                    @endphp
                                    {{ $namaPemohon }}
                                </td>
                                <td>{{ $surat->Tanggal_Diberikan_Tugas_Surat ? $surat->Tanggal_Diberikan_Tugas_Surat->format('d M Y') : '-' }}</td>
                                <td>
                                    @php
                                        $roleName = $surat->pemberiTugas->role->Name_Role ?? 'N/A';
                                        $badgeClass = 'secondary';
                                        
                                        if (str_contains($roleName, 'Dosen')) {
                                            $badgeClass = 'primary';
                                        } elseif (str_contains($roleName, 'Mahasiswa')) {
                                            $badgeClass = 'info';
                                        } elseif (str_contains($roleName, 'Dekan')) {
                                            $badgeClass = 'danger';
                                        } elseif (str_contains($roleName, 'Kajur') || str_contains($roleName, 'Kaprodi')) {
                                            $badgeClass = 'warning';
                                        } elseif (str_contains($roleName, 'Admin') || str_contains($roleName, 'Pegawai')) {
                                            $badgeClass = 'success';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $roleName }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin_fakultas.surat.detail', $surat->Id_Tugas_Surat) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Proses
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada permohonan surat saat ini</p>
                                </td>
                            </tr>
                            @endforelse
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
