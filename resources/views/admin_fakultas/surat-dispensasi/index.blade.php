@extends('layouts.admin_fakultas')

@section('title', 'Manajemen Surat Dispensasi')

@push('styles')
<style>
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">Manajemen Surat Dispensasi</h1>
        <p class="text-muted small">Verifikasi dan proses pengajuan dispensasi mahasiswa</p>
    </div>
    <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-secondary btn-icon-split shadow-sm">
        <span class="icon text-white-50">
            <i class="fas fa-arrow-left"></i>
        </span>
        <span class="text">Kembali</span>
    </a>
</div>

{{-- Alert Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Statistik Cards --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Verifikasi
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $daftarSurat->where('tugasSurat.Status', 'baru')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Dalam Proses
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $daftarSurat->whereIn('tugasSurat.Status', ['proses', 'dikerjakan-admin'])->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-spinner fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Pengajuan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $daftarSurat->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Rata-rata Waktu Proses
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            2 Hari
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Daftar Surat --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list me-2"></i>Daftar Pengajuan Surat Dispensasi
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Tanggal Ajuan</th>
                        <th width="20%">Mahasiswa</th>
                        <th width="25%">Kegiatan/Alasan</th>
                        <th width="12%">Periode</th>
                        <th width="10%">Status</th>
                        <th width="18%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarSurat as $index => $surat)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            {{ Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Pembuatan_Tugas)->format('d M Y') }}
                            <br>
                            <small class="text-muted">
                                {{ Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Pembuatan_Tugas)->diffForHumans() }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $surat->user->mahasiswa->Nama_Mahasiswa ?? 'N/A' }}</div>
                            <small class="text-muted">
                                NIM: {{ $surat->user->mahasiswa->NIM ?? '-' }}<br>
                                {{ $surat->user->mahasiswa->prodi->Nama_Prodi ?? '-' }}
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $surat->nama_kegiatan }}</div>
                            @if($surat->instansi_penyelenggara)
                                <small class="text-muted">
                                    <i class="fas fa-building me-1"></i>{{ $surat->instansi_penyelenggara }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <small>
                                <i class="fas fa-calendar-alt me-1 text-primary"></i>
                                {{ Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M') }}
                                -
                                {{ Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}
                                <br>
                                <span class="badge bg-secondary">
                                    {{ Carbon\Carbon::parse($surat->tanggal_mulai)->diffInDays(Carbon\Carbon::parse($surat->tanggal_selesai)) + 1 }} hari
                                </span>
                            </small>
                        </td>
                        <td>
                            @php
                                $status = strtolower($surat->tugasSurat->Status ?? 'baru');
                            @endphp
                            
                            @if($status === 'baru')
                                <span class="badge bg-warning text-dark status-badge">
                                    <i class="fas fa-clock me-1"></i>Pending
                                </span>
                            @elseif(in_array($status, ['proses', 'dikerjakan-admin']))
                                <span class="badge bg-primary status-badge">
                                    <i class="fas fa-spinner fa-spin me-1"></i>Proses
                                </span>
                            @elseif($status === 'diajukan-ke-wadek3')
                                <span class="badge bg-info status-badge">
                                    <i class="fas fa-paper-plane me-1"></i>Ke Wadek3
                                </span>
                            @else
                                <span class="badge bg-secondary status-badge">{{ ucfirst($status) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin_fakultas.surat.dispensasi.detail', $surat->id) }}" 
                               class="btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-eye me-1"></i>Detail & Proses
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada pengajuan surat dispensasi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "order": [[ 1, "desc" ]], // Sort by tanggal descending
            "pageLength": 25
        });
    });
</script>
@endpush
