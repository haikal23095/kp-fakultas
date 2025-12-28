@extends('layouts.admin_fakultas')

@section('title', 'Manajemen Surat Berkelakuan Baik')

@section('content')
<div class="container-fluid">
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Surat Keterangan Berkelakuan Baik</h1>
            <p class="text-muted small mb-0">Kelola pengajuan surat keterangan berkelakuan baik mahasiswa</p>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Masuk</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Tanggal</th>
                            <th>Mahasiswa</th>
                            <th>Semester/TA</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarPengajuan as $index => $tugas)
                            @php
                                $mhs = $tugas->pemberiTugas->mahasiswa ?? null;
                                $detail = $tugas->suratKelakuanBaik;
                                $statusClass = match(strtolower($tugas->Status)) {
                                    'baru', 'pending' => 'bg-warning text-dark',
                                    'selesai', 'success' => 'bg-success text-white',
                                    'ditolak' => 'bg-danger text-white',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($tugas->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $mhs->Nama_Mahasiswa ?? 'User Unknown' }}</div>
                                    <small class="text-muted">{{ $mhs->NIM ?? '-' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $detail->Semester ?? '-' }}</small><br>
                                    <small class="text-muted">{{ $detail->Tahun_Akademik ?? '-' }}</small>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($detail->Keperluan ?? '-', 50) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($tugas->Status) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin_fakultas.surat.detail', $tugas->Id_Tugas_Surat) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada pengajuan surat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
