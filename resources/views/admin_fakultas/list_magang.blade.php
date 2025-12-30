@extends('layouts.admin_fakultas')

@section('title', 'Daftar Surat Pengantar Magang')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">Daftar Surat Pengantar Magang/KP</h1>
        <p class="text-muted small mb-0">Kelola pengajuan surat pengantar magang dan kerja praktek mahasiswa</p>
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
            <i class="fas fa-briefcase me-2"></i>Tabel Pengajuan Surat Pengantar Magang/KP
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
                        <th>Instansi</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarTugas as $tugas)
                    @php
                        $suratMagang = $tugas->suratMagang;
                        $dataMahasiswa = is_array($suratMagang->Data_Mahasiswa) 
                            ? $suratMagang->Data_Mahasiswa 
                            : json_decode($suratMagang->Data_Mahasiswa, true);
                        $mahasiswaPertama = $dataMahasiswa[0] ?? null;
                    @endphp
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
                            <div class="fw-bold">{{ $mahasiswaPertama['nama'] ?? 'N/A' }}</div>
                            <small class="text-muted">
                                NIM: {{ $mahasiswaPertama['nim'] ?? 'N/A' }}
                                @if(count($dataMahasiswa) > 1)
                                    <span class="badge bg-info text-dark">+{{ count($dataMahasiswa) - 1 }} lainnya</span>
                                @endif
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $suratMagang->Nama_Instansi ?? 'N/A' }}</div>
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($suratMagang->Alamat_Instansi ?? '', 40) }}</small>
                        </td>
                        <td>
                            <small>
                                {{ \Carbon\Carbon::parse($suratMagang->Tanggal_Mulai)->format('d M Y') }} 
                                s/d 
                                {{ \Carbon\Carbon::parse($suratMagang->Tanggal_Selesai)->format('d M Y') }}
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
                            @elseif(strtolower($status) === 'diajukan-ke-koordinator')
                                <span class="badge rounded-pill bg-info text-dark px-3 py-2"><i class="fas fa-user-tie me-1"></i> Ke Koordinator</span>
                            @elseif(strtolower($status) === 'diajukan-ke-dekan')
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fas fa-signature me-1"></i> Ke Dekan</span>
                            @else
                                <span class="badge rounded-pill bg-secondary px-3 py-2">{{ $status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin_fakultas.surat_magang.show', $suratMagang->id_no) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                <p class="mb-0">Belum ada pengajuan surat pengantar magang/KP</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($daftarTugas->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $daftarTugas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
