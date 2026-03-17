@extends('layouts.mahasiswa')

@section('title', $title ?? 'Riwayat Surat')

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #e9ecef;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .card-clean {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: none;
    }
    
    .table-clean {
        font-size: 0.9rem;
    }
    
    .table-clean thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        color: #495057;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.75rem;
    }
    
    .table-clean tbody td {
        vertical-align: middle;
        padding: 0.75rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem 0;
        }
        .page-header h3 {
            font-size: 1.1rem;
        }
        .page-header p {
            font-size: 0.8rem;
        }
        .page-header .btn {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        .card-clean {
            margin: 0 -0.5rem;
            border-radius: 0;
            border-left: 0;
            border-right: 0;
        }
        .table-clean thead th {
            font-size: 0.7rem;
            padding: 0.5rem 0.4rem;
            white-space: nowrap;
        }
        .table-clean tbody td {
            padding: 0.5rem 0.4rem;
            font-size: 0.75rem;
        }
        .badge-clean {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }
    }
    
    .table-clean tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge-clean {
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }
</style>
@endpush

@section('content')

{{-- Header Halaman --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-1 fw-bold text-dark">
                <a href="{{ route('mahasiswa.riwayat') }}" class="text-decoration-none text-muted me-2">
                    <i class="fas fa-arrow-left"></i>
                </a>
                {{ $title ?? 'Riwayat Surat' }}
            </h3>
            <p class="mb-0 text-muted small">Pantau status pengajuan {{ strtolower($title ?? 'surat') }}</p>
        </div>
        <div>
            <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajukan Surat Baru
            </a>
        </div>
    </div>
</div>

{{-- Alert Success/Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-clean mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">Daftar Pengajuan</h6>
            <span class="badge bg-primary">{{ $riwayatSurat->count() }} Surat</span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($riwayatSurat->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum Ada Pengajuan</h5>
                <p class="text-muted">Silakan ajukan surat baru</p>
                <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Ajukan Surat
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-clean" id="tableSurat" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">#</th>
                            <th width="12%">Tanggal</th>
                            <th width="15%">Nomor Surat</th>
                            <th width="30%">Keperluan</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="23%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatSurat as $index => $surat)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>{{ $surat->Tanggal_Diberikan_Tugas_Surat ? \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($surat->Nomor_Surat)
                                        <strong>{{ $surat->Nomor_Surat }}</strong>
                                    @else
                                        <span class="text-muted">Belum Terbit</span>
                                    @endif
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($surat->Judul_Tugas_Surat ?? $surat->Deskripsi_Tugas_Surat, 50) }}</td>
                                <td class="text-center">
                                    @php
                                        // Gunakan accessor getStatusAttribute dari model TugasSurat
                                        $statusRaw = $surat->Status;
                                        $status = strtolower(trim($statusRaw ?? ''));
                                        $badgeClass = 'secondary';
                                        $icon = 'circle';

                                        if ($status === 'baru') {
                                            $badgeClass = 'info';
                                            $icon = 'clock';
                                        } elseif ($status === 'diterima admin' || $status === 'proses') {
                                            $badgeClass = 'primary';
                                            $icon = 'spinner';
                                        } elseif ($status === 'diajukan ke dekan' || $status === 'menunggu-ttd') {
                                            $badgeClass = 'warning';
                                            $icon = 'hourglass-half';
                                        } elseif ($status === 'selesai' || $status === 'telah ditandatangani dekan' || $status === 'success') {
                                            $badgeClass = 'success';
                                            $icon = 'check-circle';
                                        } elseif ($status === 'ditolak') {
                                            $badgeClass = 'danger';
                                            $icon = 'times-circle';
                                        } elseif ($status === 'terlambat') {
                                            $badgeClass = 'dark';
                                            $icon = 'exclamation-triangle';
                                        }
                                    @endphp
                                    <span class="badge badge-clean bg-{{ $badgeClass }}">
                                        <i class="fas fa-{{ $icon }} me-1"></i> {{ ucwords($statusRaw ?? 'Pending') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{-- Tombol Download jika selesai --}}
                                    @if($status === 'selesai' || $status === 'telah ditandatangani dekan' || $status === 'success')
                                        <a href="{{ route('mahasiswa.surat.download', $surat->Id_Tugas_Surat) }}" class="btn btn-sm btn-success" target="_blank" title="Download Surat">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    
                                    {{-- Tombol Detail (jika ada route detail) --}}
                                    {{-- <a href="#" class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tableSurat').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "order": [[ 1, "desc" ]] // Urutkan berdasarkan tanggal (kolom ke-2)
        });
    });
</script>
@endpush
