@extends('layouts.admin_prodi')

@section('title', 'Dashboard Administrator')

@section('content')

<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Dashboard Overview</h2>
            <p class="text-muted mb-0">
                Selamat datang kembali, <span class="fw-semibold text-primary">{{ auth()->user()->Name_User ?? 'Administrator' }}</span>.
                @if(isset($namaProdi))
                    <br><small class="text-secondary">Administrator Program Studi: <strong>{{ $namaProdi }}</strong></small>
                @endif
            </p>
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-white text-secondary border px-3 py-2 shadow-sm">
                <i class="far fa-calendar-alt me-2"></i> {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Permohonan Baru -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase fw-bold text-muted small mb-1">Permohonan Baru</p>
                            <h3 class="fw-bold text-dark mb-0">{{ $permohonanBaru }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-inbox text-primary fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-arrow-up me-1"></i> Perlu Tindakan
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Menunggu TTE -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase fw-bold text-muted small mb-1">Menunggu TTE</p>
                            <h3 class="fw-bold text-dark mb-0">{{ $menungguTTE }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="fas fa-signature text-warning fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-warning bg-opacity-10 text-warning">
                            Proses Dekan
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Selesai Bulan Ini -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase fw-bold text-muted small mb-1">Selesai (Bulan Ini)</p>
                            <h3 class="fw-bold text-dark mb-0">{{ $suratSelesaiBulanIni }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-muted small">Dokumen diterbitkan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Arsip -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase fw-bold text-muted small mb-1">Total Arsip</p>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($totalArsip) }}</h3>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-2 rounded">
                            <i class="fas fa-archive text-secondary fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-muted small">Seluruh Waktu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Permohonan Surat Terbaru</h5>
                <small class="text-muted">Daftar 5 pengajuan surat terakhir yang masuk</small>
            </div>
            <a href="{{ route('admin_prodi.surat.manage') }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-uppercase small fw-bold text-muted">Jenis Surat</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Pemohon</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Tanggal</th>
                            <th class="py-3 text-uppercase small fw-bold text-muted">Status Pemohon</th>
                            <th class="py-3 text-end px-4 text-uppercase small fw-bold text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antrianSurat as $surat)
                        <tr>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-file-alt text-primary"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-dark d-block">{{ $surat->jenisSurat->Nama_Surat ?? 'N/A' }}</span>
                                        <small class="text-muted">ID: #{{ $surat->Id_Tugas_Surat }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $surat->pemberiTugas->Name_User ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $surat->Tanggal_Diberikan_Tugas_Surat ? $surat->Tanggal_Diberikan_Tugas_Surat->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $roleName = $surat->pemberiTugas->role->Name_Role ?? 'N/A';
                                    $badgeClass = 'bg-secondary';
                                    
                                    if (str_contains($roleName, 'Dosen')) {
                                        $badgeClass = 'bg-info text-dark';
                                    } elseif (str_contains($roleName, 'Mahasiswa')) {
                                        $badgeClass = 'bg-success';
                                    } elseif (str_contains($roleName, 'Dekan')) {
                                        $badgeClass = 'bg-danger';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-3">{{ $roleName }}</span>
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('admin_prodi.surat.detail', $surat->Id_Tugas_Surat) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                    Proses <i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="bg-light rounded-circle p-4 mb-3">
                                        <i class="fas fa-inbox fa-3x text-muted"></i>
                                    </div>
                                    <h6 class="fw-bold text-muted">Tidak ada permohonan baru</h6>
                                    <p class="text-muted small mb-0">Semua permohonan surat telah diproses.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection