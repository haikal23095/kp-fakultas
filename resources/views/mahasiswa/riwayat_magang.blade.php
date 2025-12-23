@extends('layouts.mahasiswa')

@section('title', 'Riwayat Surat Pengantar Magang')

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
                Riwayat Surat Pengantar KP/Magang
            </h3>
            <p class="mb-0 text-muted small">Pantau status pengajuan surat pengantar kerja praktek dan magang</p>
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
            <span class="badge bg-danger">{{ $riwayatSurat->count() }} Surat</span>
        </div>
    </div>
    <div class="card-body p-0">
        @if($riwayatSurat->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum Ada Pengajuan</h5>
                <p class="text-muted">Silakan ajukan surat pengantar magang/KP</p>
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
                            <th width="25%">Instansi</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="28%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatSurat as $index => $surat)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>{{ $surat->Tanggal_Diberikan_Tugas_Surat ? \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($surat->suratMagang && $surat->suratMagang->tugasSurat && $surat->suratMagang->tugasSurat->Nomor_Surat)
                                        <strong>{{ $surat->suratMagang->tugasSurat->Nomor_Surat }}</strong>
                                    @elseif($surat->Nomor_Surat)
                                        <strong>{{ $surat->Nomor_Surat }}</strong>
                                    @else
                                        <span class="text-muted">Belum Terbit</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $surat->suratMagang->Nama_Instansi ?? '-' }}</strong>
                                </td>
                                <td class="text-center">
                                    @php
                                        // Status dari Surat_Magang
                                        $statusRaw = $surat->suratMagang->Status ?? $surat->Status;
                                        $status = strtolower(trim($statusRaw ?? ''));
                                        $badgeClass = 'secondary';
                                        $icon = 'circle';

                                        if ($status === 'draft') {
                                            $badgeClass = 'info';
                                            $icon = 'edit';
                                        } elseif ($status === 'diajukan-ke-koordinator') {
                                            $badgeClass = 'warning';
                                            $icon = 'paper-plane';
                                        } elseif ($status === 'dikerjakan-admin') {
                                            $badgeClass = 'primary';
                                            $icon = 'spinner';
                                        } elseif ($status === 'diajukan-ke-dekan') {
                                            $badgeClass = 'warning';
                                            $icon = 'hourglass-half';
                                        } elseif ($status === 'success') {
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
                                        $statusLower = $status;
                                        $isSuccess = ($statusLower === 'success');
                                        $isDitolak = ($statusLower === 'ditolak');
                                        $hasKaprodiApproval = $surat->suratMagang && $surat->suratMagang->Acc_Koordinator;
                                    @endphp
                                    
                                    {{-- Tombol Trigger Modal Aksi --}}
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" data-bs-target="#actionModal"
                                            data-title="Surat Pengantar KP/Magang"
                                            data-nomor="{{ $surat->suratMagang->tugasSurat->Nomor_Surat ?? $surat->Nomor_Surat ?? 'Belum Terbit' }}"
                                            data-target-content="#action-content-{{ $index }}">
                                        <i class="fas fa-bars me-1"></i> Menu Aksi
                                    </button>

                                    {{-- Hidden Content untuk Modal --}}
                                    <div id="action-content-{{ $index }}" class="d-none">
                                        <div class="d-grid gap-2">
                                            {{-- 2. Aksi Utama Berdasarkan Status --}}
                                            @if($isSuccess)
                                                {{-- Surat Pengantar (Jika sudah ACC Kaprodi dan Success) --}}
                                                @if($hasKaprodiApproval)
                                                    <a href="{{ route('mahasiswa.surat.download_pengantar', $surat->Id_Tugas_Surat) }}" 
                                                       class="btn btn-info text-white" target="_blank">
                                                        <i class="fas fa-file-alt me-2"></i> Download Surat Pengantar
                                                    </a>
                                                @endif
                                                
                                                @if($surat->verification)
                                                    <a href="{{ route('surat.verify', $surat->verification->token) }}" 
                                                       class="btn btn-primary" target="_blank">
                                                        <i class="fas fa-qrcode me-2"></i> Verifikasi Dokumen
                                                    </a>
                                                @endif
                                            @elseif($isDitolak)
                                                @php
                                                    $komentarPenolakan = $surat->suratMagang->Komentar ?? 'Tidak ada komentar';
                                                @endphp
                                                <div class="alert alert-danger text-start mb-0">
                                                    <h6 class="alert-heading fw-bold"><i class="fas fa-times-circle me-2"></i>Pengajuan Ditolak</h6>
                                                    <hr class="my-2">
                                                    <div class="mt-2 p-2 bg-white rounded border border-danger text-danger">
                                                        <strong>Komentar:</strong><br>
                                                        {{ $komentarPenolakan }}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center py-4 bg-light rounded border border-dashed">
                                                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                                    <h6 class="text-muted fw-bold">Sedang Diproses</h6>
                                                    <p class="text-muted small mb-0">Surat sedang dalam tahap verifikasi dan persetujuan.</p>
                                                    @if($statusLower === 'diajukan-ke-koordinator')
                                                        <p class="text-info small mt-2 mb-0"><i class="fas fa-info-circle me-1"></i>Menunggu persetujuan Kaprodi</p>
                                                    @elseif($statusLower === 'dikerjakan-admin')
                                                        <p class="text-primary small mt-2 mb-0"><i class="fas fa-info-circle me-1"></i>Sedang diproses oleh Admin Fakultas</p>
                                                    @elseif($statusLower === 'diajukan-ke-dekan')
                                                        <p class="text-warning small mt-2 mb-0"><i class="fas fa-info-circle me-1"></i>Menunggu persetujuan Dekan</p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
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
        <div class="alert alert-info border-0" role="alert">
            <h6 class="alert-heading fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Keterangan Status</h6>
            <div class="row small">
                <div class="col-md-4 mb-2">
                    <span class="badge bg-info me-2">Draft</span> Masih dalam draft
                </div>
                <div class="col-md-4 mb-2">
                    <span class="badge bg-warning me-2">Diajukan-ke-koordinator</span> Menunggu Kaprodi
                </div>
                <div class="col-md-4 mb-2">
                    <span class="badge bg-primary me-2">Dikerjakan-admin</span> Diproses Admin
                </div>
                <div class="col-md-4 mb-2">
                    <span class="badge bg-warning me-2">Diajukan-ke-dekan</span> Menunggu Dekan
                </div>
                <div class="col-md-4 mb-2">
                    <span class="badge bg-success me-2">Success</span> Selesai disetujui
                </div>
                <div class="col-md-4 mb-2">
                    <span class="badge bg-danger me-2">Ditolak</span> Pengajuan ditolak
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Action Menu --}}
<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title fw-bold mb-0">Detail Surat</h5>
                    <small class="text-white-50">Nomor: <span id="modalNomorSurat">-</span></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-content p-4">
                {{-- Content will be loaded here via JS --}}
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        if ($.fn.DataTable) {
            $('#tableSurat').DataTable({
                "order": [[1, "desc"]], // Sort by tanggal descending
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                },
                "responsive": true,
                "scrollX": true,
                "autoWidth": false
            });
        }

        // Handle Action Modal
        var actionModal = document.getElementById('actionModal');
        if (actionModal) {
            actionModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var title = button.getAttribute('data-title');
                var nomor = button.getAttribute('data-nomor');
                var targetId = button.getAttribute('data-target-content');
                var content = document.querySelector(targetId).innerHTML;
                
                var modalTitle = actionModal.querySelector('.modal-title');
                var modalNomor = actionModal.querySelector('#modalNomorSurat');
                var modalBody = actionModal.querySelector('.modal-body-content');
                
                modalTitle.textContent = title;
                modalNomor.textContent = nomor;
                modalBody.innerHTML = content;
            });
        }
    });
</script>
@endpush
