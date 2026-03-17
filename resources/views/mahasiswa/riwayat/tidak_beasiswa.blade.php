@extends('layouts.mahasiswa')

@section('title', 'Riwayat Surat Tidak Menerima Beasiswa')

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
                Riwayat Surat Tidak Menerima Beasiswa
            </h3>
            <p class="mb-0 text-muted small">Pantau status pengajuan surat keterangan tidak menerima beasiswa</p>
        </div>
        <div>
            <a href="{{ route('mahasiswa.pengajuan.tidak_beasiswa.create') }}" class="btn btn-primary">
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
                <p class="text-muted">Silakan ajukan surat keterangan tidak menerima beasiswa</p>
                <a href="{{ route('mahasiswa.pengajuan.tidak_beasiswa.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Ajukan Surat
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-clean" id="tableSurat" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal Ajuan</th>
                            <th>Nomor Surat</th>
                            <th>Nama Orang Tua</th>
                            <th>Pendapatan/Bulan</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatSurat as $tugas)
                        <tr>
                            {{-- Tanggal --}}
                            <td>
                                {{ $tugas->Tanggal_Diberikan_Tugas_Surat->format('d M Y') }}
                            </td>

                            {{-- Nomor Surat --}}
                            <td>
                                @if($tugas->Nomor_Surat)
                                    <span class="fw-bold">{{ $tugas->Nomor_Surat }}</span>
                                @else
                                    <span class="text-muted fst-italic">-</span>
                                @endif
                            </td>

                            {{-- Nama Orang Tua --}}
                            <td>
                                {{ $tugas->suratTidakBeasiswa->Nama_Orang_Tua ?? '-' }}
                                <br>
                                <small class="text-muted">{{ $tugas->suratTidakBeasiswa->Pekerjaan_Orang_Tua ?? '-' }}</small>
                            </td>

                            {{-- Pendapatan --}}
                            <td>
                                @if($tugas->suratTidakBeasiswa && $tugas->suratTidakBeasiswa->Pendapatan_Orang_Tua)
                                    <span class="fw-bold">Rp {{ number_format($tugas->suratTidakBeasiswa->Pendapatan_Orang_Tua, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Keperluan --}}
                            <td>
                                {{ \Illuminate\Support\Str::limit($tugas->suratTidakBeasiswa->Keperluan ?? '-', 40) }}
                            </td>

                            {{-- Status --}}
                            <td>
                                @php 
                                    $status = strtolower(trim($tugas->Status ?? 'baru')); 
                                @endphp
                                
                                @if($status === 'selesai' || $status === 'disetujui' || $status === 'success')
                                    <span class="badge badge-clean bg-success"><i class="fas fa-check me-1"></i>Selesai</span>
                                @elseif($status === 'terlambat' || $status === 'ditolak')
                                    <span class="badge badge-clean bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>
                                @elseif($status === 'proses' || $status === 'dikerjakan-admin')
                                    <span class="badge badge-clean bg-primary"><i class="fas fa-spinner fa-spin me-1"></i>Diproses</span>
                                @elseif($status === 'diajukan-ke-koordinator')
                                    <span class="badge badge-clean bg-info text-dark"><i class="fas fa-user-tie me-1"></i>Koordinator</span>
                                @elseif($status === 'diajukan-ke-dekan')
                                    <span class="badge badge-clean bg-warning text-dark"><i class="fas fa-signature me-1"></i>Dekan</span>
                                @else
                                    <span class="badge badge-clean bg-secondary">{{ ucfirst($status) }}</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                @php 
                                    $statusCheck = strtolower(trim($tugas->Status ?? '')); 
                                    $isSelesai = in_array($statusCheck, ['selesai', 'telah ditandatangani dekan']);
                                @endphp
                                
                                @if($isSelesai)
                                    {{-- Download surat resmi dengan QR --}}
                                    <a href="{{ route('mahasiswa.surat.download_tidak_beasiswa', $tugas->Id_Tugas_Surat) }}" 
                                       class="btn btn-sm btn-success" 
                                       target="_blank">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                    @if($tugas->verification)
                                        <a href="{{ route('surat.verify', $tugas->verification->token) }}" 
                                           class="btn btn-sm btn-outline-info mt-1" 
                                           target="_blank">
                                            <i class="fas fa-qrcode me-1"></i>Verifikasi
                                        </a>
                                    @endif
                                @elseif($tugas->suratTidakBeasiswa && $tugas->suratTidakBeasiswa->File_Pernyataan)
                                    {{-- Lihat file pernyataan yang diupload --}}
                                    <a href="{{ Storage::url($tugas->suratTidakBeasiswa->File_Pernyataan) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       target="_blank">
                                        <i class="fas fa-file-pdf me-1"></i>Lihat
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
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
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            "order": [[0, "desc"]],
            "pageLength": 10,
            "responsive": true
        });
    });
</script>
@endpush
