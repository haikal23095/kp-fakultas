@extends('layouts.mahasiswa')

@section('title', 'Riwayat Legalisir Online')

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .page-header h3 {
        color: white !important;
        font-weight: 600;
    }
    
    .page-header p {
        color: rgba(255, 255, 255, 0.9) !important;
    }
    
    .page-header .btn-back {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .page-header .btn-back:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .page-header .btn-primary {
        background: white;
        border: none;
        color: #667eea;
        font-weight: 600;
    }
    
    .page-header .btn-primary:hover {
        background: #f8f9fa;
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .card-clean {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .table-clean {
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    .table-clean thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .table-clean thead th {
        border: none;
        color: white;
        font-weight: 600;
        font-size: 0.813rem;
        padding: 1rem 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-clean tbody td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .table-clean tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-clean tbody tr:hover {
        background-color: #f8f9fe;
        transform: scale(1.01);
    }
    
    .badge-clean {
        padding: 0.4rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
        text-transform: capitalize;
    }
    
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }
    
    .empty-state i {
        opacity: 0.3;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }
        .page-header h3 {
            font-size: 1.25rem;
        }
        .table-clean thead th {
            font-size: 0.7rem;
            padding: 0.75rem 0.5rem;
        }
        .table-clean tbody td {
            padding: 0.75rem 0.5rem;
            font-size: 0.813rem;
        }
        .badge-clean {
            font-size: 0.688rem;
            padding: 0.3rem 0.6rem;
        }
        .btn-action {
            width: 28px;
            height: 28px;
        }
    }
</style>
@endpush

@section('content')

{{-- Header Halaman --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h3 class="mb-2 fw-bold">
                <a href="{{ route('mahasiswa.riwayat') }}" class="text-decoration-none me-2 btn-back btn btn-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Riwayat Legalisir Online
            </h3>
            <p class="mb-0 small">Pantau status pengajuan legalisir ijazah dan transkrip nilai</p>
        </div>
        <div>
            <a href="{{ route('mahasiswa.pengajuan.legalisir.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajukan Legalisir Baru
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

{{-- Card Riwayat Surat --}}
<div class="card card-clean">
    <div class="card-body p-0">
        @if($riwayatSurat->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada riwayat pengajuan legalisir</h5>
                <p class="text-muted mb-4">Anda belum pernah mengajukan legalisir dokumen.</p>
                <a href="{{ route('mahasiswa.pengajuan.legalisir.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajukan Legalisir Sekarang
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-clean mb-0" id="riwayatTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th style="width: 18%;">Jenis Dokumen</th>
                            <th style="width: 15%;">Tanggal Pengajuan</th>
                            <th class="text-center" style="width: 10%;">Jumlah</th>
                            <th class="text-center" style="width: 15%;">Status</th>
                            <th class="text-center" style="width: 15%;">Progress</th>
                            <th class="text-center" style="width: 12%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatSurat as $index => $surat)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                        <i class="fas fa-file-pdf text-primary"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block">{{ $surat->suratLegalisir->Jenis_Dokumen ?? '-' }}</strong>
                                        <small class="text-muted">Legalisir Dokumen</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                <small>{{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}</small>
                                <br>
                                <i class="fas fa-clock text-muted me-1"></i>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('H:i') }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info badge-clean">
                                    {{ $surat->suratLegalisir->Jumlah_Salinan ?? 1 }} copy
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    // Status dari accessor TugasSurat (mengambil dari child table)
                                    $statusTugas = $surat->Status ?? 'pending';
                                    $statusTugasLower = strtolower(trim($statusTugas));
                                    $statusBadge = 'secondary';
                                    
                                    if ($statusTugasLower == 'selesai') {
                                        $statusBadge = 'success';
                                    } elseif ($statusTugasLower == 'ditolak') {
                                        $statusBadge = 'danger';
                                    } elseif ($statusTugasLower == 'baru' || $statusTugasLower == 'pending') {
                                        $statusBadge = 'warning';
                                    } elseif (str_contains($statusTugasLower, 'proses') || str_contains($statusTugasLower, 'verifikasi')) {
                                        $statusBadge = 'info';
                                    }
                                @endphp
                                <span class="badge bg-{{ $statusBadge }} badge-clean">
                                    {{ ucwords(str_replace('_', ' ', $statusTugas)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusLegalisir = $surat->suratLegalisir->Status ?? 'pending';
                                    $statusLegalisirLower = strtolower(trim($statusLegalisir));
                                    $statusLegalisirBadge = 'secondary';
                                    
                                    if ($statusLegalisirLower == 'selesai' || $statusLegalisirLower == 'siap_diambil') {
                                        $statusLegalisirBadge = 'success';
                                    } elseif ($statusLegalisirLower == 'ditolak') {
                                        $statusLegalisirBadge = 'danger';
                                    } elseif ($statusLegalisirLower == 'pending' || $statusLegalisirLower == 'menunggu_pembayaran') {
                                        $statusLegalisirBadge = 'warning';
                                    } elseif (str_contains($statusLegalisirLower, 'proses') || str_contains($statusLegalisirLower, 'verifikasi')) {
                                        $statusLegalisirBadge = 'info';
                                    }
                                @endphp
                                <span class="badge bg-{{ $statusLegalisirBadge }} badge-clean">
                                    {{ ucwords(str_replace('_', ' ', $statusLegalisir)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- Tombol Lihat Detail --}}
                                    <button type="button" 
                                            class="btn btn-outline-info btn-action" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $surat->Id_Tugas_Surat }}"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    {{-- Tombol Download File Asli --}}
                                    @if($surat->suratLegalisir && $surat->suratLegalisir->Path_File)
                                    <a href="{{ asset('storage/' . $surat->suratLegalisir->Path_File) }}" 
                                       class="btn btn-outline-primary btn-action" 
                                       target="_blank"
                                       title="Download File">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                    
                                    {{-- Tombol Batalkan (jika status masih baru/pending) --}}
                                    @if(in_array($statusLegalisirLower, ['baru', 'pending']))
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-action" 
                                            onclick="confirmCancel({{ $surat->Id_Tugas_Surat }})"
                                            title="Batalkan Pengajuan">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Detail --}}
                        <div class="modal fade" id="detailModal{{ $surat->Id_Tugas_Surat }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-stamp me-2 text-primary"></i>
                                            Detail Legalisir
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Jenis Dokumen</th>
                                                <td>{{ $surat->suratLegalisir->Jenis_Dokumen ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Jumlah Salinan</th>
                                                <td>{{ $surat->suratLegalisir->Jumlah_Salinan ?? 1 }} salinan</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Pengajuan</th>
                                                <td>{{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('d F Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status Tugas</th>
                                                <td>
                                                    <span class="badge bg-{{ $statusBadge }}">{{ ucwords(str_replace('_', ' ', $statusTugas)) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Status Legalisir</th>
                                                <td>
                                                    <span class="badge bg-{{ $statusLegalisirBadge }}">
                                                        {{ ucwords(str_replace('_', ' ', $statusLegalisir)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @if($surat->penerimaTugas)
                                            <tr>
                                                <th>Diproses Oleh</th>
                                                <td>{{ $surat->penerimaTugas->Nama_User ?? '-' }}</td>
                                            </tr>
                                            @endif
                                            @if($surat->suratLegalisir && $surat->suratLegalisir->Catatan)
                                            <tr>
                                                <th>Catatan</th>
                                                <td>{{ $surat->suratLegalisir->Catatan }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        @if($surat->suratLegalisir && $surat->suratLegalisir->Path_File)
                                        <a href="{{ asset('storage/' . $surat->suratLegalisir->Path_File) }}" 
                                           class="btn btn-primary" 
                                           target="_blank">
                                            <i class="fas fa-download me-2"></i>Download File
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
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
        $('#riwayatTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            },
            order: [[2, 'desc']], // Sort by tanggal
            pageLength: 10,
            responsive: true
        });
    });

    function confirmCancel(id) {
        if (confirm('Apakah Anda yakin ingin membatalkan pengajuan legalisir ini?')) {
            // TODO: Implementasi fungsi cancel
            alert('Fitur pembatalan sedang dalam pengembangan');
        }
    }
</script>
@endpush
