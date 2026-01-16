@extends('layouts.admin_fakultas')

@section('title', 'Manajemen Surat Tidak Beasiswa')

@section('content')
<div class="container-fluid">
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Surat Keterangan Tidak Menerima Beasiswa</h1>
            <p class="text-muted small mb-0">Kelola pengajuan surat keterangan tidak menerima beasiswa</p>
        </div>
        <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    {{-- Alert Messages --}}
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

    {{-- Main Table Card --}}
    <div class="card shadow mb-4" style="border-radius: 12px; border: none;">
        <div class="card-header py-3" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 12px 12px 0 0;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-graduation-cap me-2"></i>Daftar Pengajuan Surat
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead style="background-color: #f8f9fc;">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Tanggal</th>
                            <th>Mahasiswa</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarTugas as $index => $tugas)
                            @php
                                $mhs = $tugas->pemberiTugas->mahasiswa ?? null;
                                $detail = $tugas->suratTidakBeasiswa;
                                
                                // Status Badge Styling
                                $statusClass = match(strtolower($tugas->Status)) {
                                    'baru', 'pending' => 'bg-warning text-dark',
                                    'proses', 'dikerjakan-admin' => 'bg-info text-white',
                                    'selesai', 'success' => 'bg-success text-white',
                                    'ditolak' => 'bg-danger text-white',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <tr>
                                <td>{{ $daftarTugas->firstItem() + $index }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($tugas->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $mhs->Nama_Mahasiswa ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $mhs->NIM ?? '-' }}</small><br>
                                    <small class="text-muted">{{ $mhs->prodi->Nama_Prodi ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 250px;" title="{{ $detail->Keperluan ?? '-' }}">
                                        {{ $detail->Keperluan ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $statusClass }} px-3 py-2">
                                        {{ ucfirst($tugas->Status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin_fakultas.surat.detail', $tugas->Id_Tugas_Surat) }}" 
                                       class="btn btn-info btn-sm" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                        <p class="mb-0">Belum ada pengajuan surat.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($daftarTugas->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Menampilkan {{ $daftarTugas->firstItem() ?? 0 }} - {{ $daftarTugas->lastItem() ?? 0 }} dari {{ $daftarTugas->total() }} data
                    </div>
                    <div>
                        {{ $daftarTugas->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $daftarTugas->where('Status', 'baru')->count() }}
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Diproses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $daftarTugas->where('Status', 'proses')->count() }}
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
                                Selesai
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $daftarTugas->where('Status', 'selesai')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Ditolak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $daftarTugas->where('Status', 'ditolak')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
