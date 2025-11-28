@extends('layouts.mahasiswa')

@section('title', 'Riwayat Pengajuan Surat')

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
        .card-clean .card-header {
            padding: 0.75rem 1rem;
        }
        .card-clean .card-header h6 {
            font-size: 0.9rem;
        }
        .table-responsive {
            font-size: 0.75rem;
            overflow-x: auto;
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
        .btn-action {
            padding: 0.25rem 0.4rem;
            font-size: 0.65rem;
            min-width: 65px;
            gap: 0.2rem;
        }
        .btn-action i {
            font-size: 0.7rem;
        }
        .badge-clean {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
        }
        .action-buttons {
            gap: 0.2rem;
            flex-wrap: wrap;
        }
        .info-box {
            padding: 0.75rem;
            font-size: 0.8rem;
        }
        .info-box h6 {
            font-size: 0.9rem;
        }
        .info-box .row {
            font-size: 0.75rem;
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
    
    .action-buttons {
        display: flex;
        gap: 0.4rem;
        justify-content: center;
        flex-wrap: nowrap;
    }
    
    .btn-action {
        padding: 0.4rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        border: 1px solid;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        white-space: nowrap;
        min-width: 90px;
        justify-content: center;
    }
    
    .btn-download {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }
    
    .btn-download:hover {
        background: #059669;
        border-color: #059669;
        color: white;
    }
    
    .btn-verify {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    
    .btn-verify:hover {
        background: #2563eb;
        border-color: #2563eb;
        color: white;
    }
    
    .btn-reason {
        background: #ef4444;
        border-color: #ef4444;
        color: white;
    }
    
    .btn-reason:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: white;
    }
    
    .btn-waiting {
        background: #f1f3f5;
        border-color: #dee2e6;
        color: #868e96;
        cursor: not-allowed;
    }
    
    .info-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1rem;
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
            <h3 class="mb-1 fw-bold text-dark">Riwayat Pengajuan Surat</h3>
            <p class="mb-0 text-muted small">Pantau status dan unduh surat Anda</p>
        </div>
        <div>
            <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajukan Surat
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
        <button type="button"="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-clean mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">Daftar Pengajuan</h6>
            <span class="badge bg-secondary">{{ $riwayatSurat->count() }} Surat</span>
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
                            <th width="20%">Jenis Surat</th>
                            <th width="28%">Detail / Keperluan</th>
                            <th width="12%" class="text-center">Status</th>
                            <th width="23%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatSurat as $index => $surat)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>{{ $surat->Tanggal_Diberikan_Tugas_Surat ? \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') : '-' }}</td>
                                <td><strong>{{ $surat->jenisSurat->Nama_Surat ?? 'N/A' }}</strong></td>
                                <td>{{ \Illuminate\Support\Str::limit($surat->Judul_Tugas_Surat, 50) }}</td>
                                <td class="text-center">
                                    @php
                                        // Prioritaskan status dari tabel spesifik (Surat_Magang) jika ada
                                        if ($surat->suratMagang) {
                                            $statusRaw = $surat->suratMagang->Status;
                                        } else {
                                            $statusRaw = $surat->Status;
                                        }
                                        
                                        $status = strtolower(trim($statusRaw));
                                        $badgeClass = 'secondary';
                                        $icon = 'circle';
                                        
                                        if ($status === 'baru') {
                                            $badgeClass = 'info';
                                            $icon = 'clock';
                                        } elseif ($status === 'diterima admin' || $status === 'proses') {
                                            $badgeClass = 'primary';
                                            $icon = 'spinner';
                                        } elseif ($status === 'menunggu-ttd' || $status === 'diajukan-ke-koordinator' || $status === 'diajukan-ke-dekan') {
                                            $badgeClass = 'warning';
                                            $icon = 'hourglass-half';
                                        } elseif ($status === 'selesai' || $status === 'telah ditandatangani dekan' || $status === 'success') {
                                            $badgeClass = 'success';
                                            $icon = 'check-circle';
                                        } elseif ($status === 'ditolak') {
                                            $badgeClass = 'danger';
                                            $icon = 'times-circle';
                                        }
                                    @endphp
                                    <span class="badge badge-clean bg-{{ $badgeClass }}">
                                        <i class="fas fa-{{ $icon }} me-1"></i>{{ ucfirst($statusRaw) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        // Gunakan status yang sudah dinormalisasi
                                        $statusLower = $status;
                                        $isSelesai = ($statusLower === 'selesai' || $statusLower === 'telah ditandatangani dekan' || $statusLower === 'success');
                                        $isDitolak = ($statusLower === 'ditolak');
                                    @endphp
                                    
                                    <div class="action-buttons">
                                        @if($isSelesai)
                                            <a href="{{ route('mahasiswa.surat.download', $surat->Id_Tugas_Surat) }}" 
                                               class="btn btn-action btn-download" 
                                               title="Download Surat"
                                               target="_blank">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            @if($surat->verification)
                                                <a href="{{ route('surat.verify', $surat->verification->token) }}" 
                                                   class="btn btn-action btn-verify" 
                                                   title="Verifikasi"
                                                   target="_blank">
                                                    <i class="fas fa-qrcode"></i> Verifikasi
                                                </a>
                                            @endif
                                        @elseif($isDitolak)
                                            @php
                                                $dataSpesifik = $surat->data_spesifik ?? [];
                                            @endphp
                                            <button type="button" 
                                                    class="btn btn-action btn-reason btn-lihat-alasan"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalAlasan"
                                                    data-alasan="{{ $dataSpesifik['alasan_penolakan'] ?? 'Tidak ada alasan yang diberikan.' }}"
                                                    data-penolak="{{ $dataSpesifik['ditolak_oleh'] ?? 'Admin' }}"
                                                    data-tanggal="{{ $dataSpesifik['tanggal_penolakan'] ?? '-' }}">
                                                <i class="fas fa-eye"></i> Alasan
                                            </button>
                                        @else
                                            <button class="btn btn-action btn-waiting" disabled>
                                                <i class="fas fa-clock"></i> Proses
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Info Card --}}
<div class="row">
    <div class="col-md-12">
        <div class="info-box">
            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Keterangan Status</h6>
            <div class="row small">
                <div class="col-md-4 mb-2">
                    <span class="badge bg-info me-2">Baru</span> Menunggu diproses admin
                </div>
                <div class="col-md-4 mb-2">
                    <span class="badge bg-primary me-2">Proses</span> Sedang diverifikasi
                </div>
                <div class="col-md-4 mb-2">
                    <span class="badge bg-warning me-2">Menunggu-TTD</span> Menunggu tanda tangan
                </div>
                <div class="col-md-4 mb-2">
                    <span class="badge bg-success me-2">Selesai</span> Siap diunduh
                </div>
                <div class="col-md-4 mb-2">
                    <span class="badge bg-danger me-2">Ditolak</span> Pengajuan ditolak
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Lihat Alasan Penolakan --}}
<div class="modal fade" id="modalAlasan" tabindex="-1" aria-labelledby="modalAlasanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalAlasanLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Alasan Penolakan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold small">Ditolak Oleh:</label>
                    <p id="modalPenolak" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold small">Tanggal:</label>
                    <p id="modalTanggal" class="text-muted mb-0"></p>
                </div>
                <div class="mb-0">
                    <label class="fw-bold small">Alasan:</label>
                    <div class="alert alert-danger">
                        <p id="modalAlasanText" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Pastikan jQuery dimuat untuk DataTables dan Script Modal --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable if exists
        if ($.fn.DataTable) {
            $('#tableSurat').DataTable({
                "order": [[1, "desc"]], // Sort by tanggal descending
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                },
                "responsive": true,
                "scrollX": true,
                "autoWidth": false,
                "columnDefs": [
                    { "width": "5%", "targets": 0 },
                    { "width": "12%", "targets": 1 },
                    { "width": "20%", "targets": 2 },
                    { "width": "25%", "targets": 3 },
                    { "width": "12%", "targets": 4 },
                    { "width": "26%", "targets": 5 }
                ]
            });
        }

        // Handle Modal Alasan Penolakan
        $(document).on('click', '.btn-lihat-alasan', function() {
            var alasan = $(this).attr('data-alasan');
            var penolak = $(this).attr('data-penolak');
            var tanggal = $(this).attr('data-tanggal');
            
            $('#modalAlasanText').text(alasan);
            $('#modalPenolak').text(penolak);
            $('#modalTanggal').text(tanggal);
        });
    });
</script>
@endpush