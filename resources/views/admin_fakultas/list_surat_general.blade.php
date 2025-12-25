@extends('layouts.admin_fakultas')

@section('title', 'Daftar Surat')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">Daftar Surat</h1>
        <p class="text-muted small mb-0">Kelola pengajuan surat</p>
    </div>
    <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

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

<div class="card shadow mb-4">
    <div class="card-header py-3" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-list me-2"></i>Tabel Pengajuan Surat
        </h6>
    </div>
    <div class="card-body">
        @if($daftarTugas->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <p class="text-muted mb-0">Tidak ada data surat untuk saat ini.</p>
                <small class="text-muted">Data akan muncul setelah ada pengajuan dari mahasiswa atau dosen.</small>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead style="background-color: #f8f9fc;">
                        <tr>
                            <th>Tgl. Masuk</th>
                            <th>Nomor Surat</th>
                            <th>Pemohon</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarTugas as $tugas)
                        <tr>
                            <td>
                                {{ $tugas->Tanggal_Diberikan_Tugas_Surat->format('d M Y') }}
                            </td>
                            <td>
                                @if($tugas->Nomor_Surat)
                                    <span class="fw-bold text-dark">{{ $tugas->Nomor_Surat }}</span>
                                @else
                                    <span class="text-muted fst-italic small">Belum ada nomor</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $tugas->pemberiTugas?->Name_User ?? 'N/A' }}</div>
                                <small class="text-muted">
                                    {{ $tugas->pemberiTugas?->role?->Name_Role ?? 'N/A' }}
                                </small>
                            </td>
                            <td class="align-middle text-center">
                                @php 
                                    $status = $tugas->Status ?? 'baru';
                                    $status = trim($status);
                                @endphp
                                
                                @if(strtolower($status) === 'selesai' || strtolower($status) === 'disetujui' || strtolower($status) === 'success')
                                    <span class="badge rounded-pill bg-success px-3 py-2"><i class="fas fa-check me-1"></i> {{ $status }}</span>
                                @elseif(strtolower($status) === 'terlambat' || strtolower($status) === 'ditolak')
                                    <span class="badge rounded-pill bg-danger px-3 py-2"><i class="fas fa-times me-1"></i> {{ $status }}</span>
                                @elseif(strtolower($status) === 'proses' || strtolower($status) === 'dikerjakan-admin')
                                    <span class="badge rounded-pill bg-primary px-3 py-2"><i class="fas fa-spinner fa-spin me-1"></i> {{ $status }}</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary px-3 py-2">{{ $status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin_fakultas.surat.detail', $tugas->Id_Tugas_Surat) }}" 
                                   class="btn btn-sm btn-outline-primary shadow-sm">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Info Box --}}
<div class="alert alert-info" role="alert">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Informasi:</strong> Halaman ini akan menampilkan daftar surat setelah tabel database dibuat dan ada data pengajuan.
</div>

@endsection
