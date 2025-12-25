@extends('layouts.admin_fakultas')

@section('title', 'Daftar Surat Pengantar Magang')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">Daftar Surat Pengantar KP/Magang</h1>
        <p class="text-muted small mb-0">Kelola pengajuan surat pengantar kerja praktek dan magang</p>
    </div>
    <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #1cc88a;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #e74a3b;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow mb-4" style="border-radius: 12px; border: none;">
    <div class="card-header py-3" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border-radius: 12px 12px 0 0;">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-briefcase me-2"></i>Tabel Pengajuan Surat Pengantar Magang
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" width="100%" cellspacing="0">
                <thead style="background-color: #f8f9fc;">
                    <tr>
                        <th>Tgl. Masuk</th>
                        <th>Nomor Surat</th>
                        <th>Mahasiswa</th>
                        <th>Perusahaan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarTugas as $tugas)
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
                                {{ $tugas->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi ?? 'N/A' }}
                            </small>
                        </td>
                        <td>
                            {{ optional($tugas->suratMagang)->nama_perusahaan ?? 'N/A' }}
                            <br>
                            <small class="text-muted">
                                {{ optional($tugas->suratMagang)->kota_perusahaan ?? '' }}
                            </small>
                        </td>
                        <td class="align-middle text-center">
                            @php 
                                $status = optional($tugas->suratMagang)->Status ?? $tugas->Status ?? 'baru';
                                $status = trim($status);
                            @endphp
                            
                            @if(strtolower($status) === 'selesai' || strtolower($status) === 'disetujui' || strtolower($status) === 'success')
                                <span class="badge rounded-pill bg-success px-3 py-2"><i class="fas fa-check me-1"></i> {{ $status }}</span>
                            @elseif(strtolower($status) === 'terlambat' || strtolower($status) === 'ditolak')
                                <span class="badge rounded-pill bg-danger px-3 py-2"><i class="fas fa-times me-1"></i> {{ $status }}</span>
                            @elseif(strtolower($status) === 'proses' || strtolower($status) === 'dikerjakan-admin')
                                <span class="badge rounded-pill bg-primary px-3 py-2"><i class="fas fa-spinner fa-spin me-1"></i> {{ $status }}</span>
                            @elseif(strtolower($status) === 'diajukan-ke-koordinator')
                                <span class="badge rounded-pill bg-info text-dark px-3 py-2"><i class="fas fa-user-tie me-1"></i> Ke Koordinator</span>
                            @elseif(strtolower($status) === 'diajukan-ke-dekan')
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fas fa-signature me-1"></i> Ke Dekan</span>
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
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data surat pengantar magang.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($daftarTugas->hasPages())
        <div class="mt-3">
            {{ $daftarTugas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
