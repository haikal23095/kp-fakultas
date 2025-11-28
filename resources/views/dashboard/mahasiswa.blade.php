@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')

@push('styles')
<style>
    @media (max-width: 768px) {
        .mb-4 h1 {
            font-size: 1.5rem;
        }
        .mb-4 p {
            font-size: 0.9rem;
        }
        .card-body {
            padding: 1rem !important;
        }
        .card-body h4 {
            font-size: 1.1rem;
        }
        .card-body p {
            font-size: 0.85rem;
        }
        .btn-lg {
            font-size: 0.95rem;
            padding: 0.6rem 1.2rem;
        }
        .col-lg-4, .col-md-6 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        .card {
            margin-bottom: 0.75rem !important;
        }
    }
</style>
@endpush

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
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-times-circle fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <p class="text-muted mb-1">Ditolak</p>
                    <h4 class="fw-bold mb-0">{{ $ditolak ?? 0 }}</h4>
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
                    <p class="text-muted mb-1">Diterima</p>
                    <h4 class="fw-bold mb-0">{{ $diterima ?? 0 }}</h4>
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
                    <h4 class="fw-bold mb-0">{{ $totalPengajuan ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-primary">Riwayat Pengajuan Terkini</h6>
        <a href="{{ route('mahasiswa.riwayat') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-history"></i> Lihat Semua Riwayat
        </a>
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
                    @forelse($riwayatTerkini ?? [] as $surat)
                    @php
                        // Status sekarang ada di tabel Surat_Magang
                        $statusRaw = optional($surat->suratMagang)->Status ?? '';
                        $status = strtolower(trim($statusRaw));
                        $badgeClass = match($status) {
                            'success' => 'bg-success',
                            'ditolak' => 'bg-danger',
                            'diajukan-ke-koordinator' => 'bg-warning text-dark',
                            'dikerjakan-admin' => 'bg-info',
                            'diajukan-ke-dekan' => 'bg-primary',
                            default => 'bg-secondary'
                        };
                        $statusText = match($status) {
                            'success' => 'Success',
                            'ditolak' => 'Ditolak',
                            'diajukan-ke-koordinator' => 'Diajukan ke Koordinator',
                            'dikerjakan-admin' => 'Dikerjakan Admin',
                            'diajukan-ke-dekan' => 'Diajukan ke Dekan',
                            default => ucfirst($surat->Status ?? '-')
                        };
                    @endphp
                    <tr>
                        <td>{{ $surat->tugasSurat->jenisSurat->Nama_Surat ?? 'Tidak Diketahui' }}</td>
                        <td>{{ $surat->tugasSurat ? \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') : '-' }}</td>
                        <td><span class="badge {{ $badgeClass }}">{{ $statusText }}</span></td>
                        <td class="text-center">
                            @if($status === 'success' && $surat->Surat_Pengantar_Magang)
                                <a href="{{ asset('storage/' . $surat->Surat_Pengantar_Magang) }}" class="btn btn-primary btn-sm" download>
                                    <i class="fas fa-download"></i>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada riwayat pengajuan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection