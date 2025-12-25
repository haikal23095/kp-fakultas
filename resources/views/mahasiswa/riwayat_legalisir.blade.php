@extends('layouts.mahasiswa')

@section('title', 'Riwayat & Status Legalisir')

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
    
    .page-header h3 { color: white !important; font-weight: 600; }
    .page-header p { color: rgba(255, 255, 255, 0.9) !important; }
    
    .card-clean {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .table-clean { font-size: 0.875rem; margin-bottom: 0; }
    .table-clean thead { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    
    .table-clean thead th {
        border: none;
        color: white;
        font-weight: 600;
        padding: 1rem 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-clean tbody td { vertical-align: middle; padding: 1rem 0.75rem; border-bottom: 1px solid #f0f0f0; }
    .badge-clean { padding: 0.4rem 0.75rem; border-radius: 20px; font-weight: 500; font-size: 0.75rem; }
    
    .info-box {
        background: #fff3cd;
        border-left: 5px solid #ffc107;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')

{{-- Header Halaman --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h3 class="mb-2 fw-bold">Pelacakan Status Legalisir</h3>
            <p class="mb-0 small">Data di bawah ini diinput oleh Admin setelah Anda melakukan verifikasi berkas fisik di loket.</p>
        </div>
    </div>
</div>

{{-- Kotak Informasi Alur Offline --}}
<div class="info-box">
    <div class="d-flex align-items-center">
        <i class="fas fa-info-circle fa-2x text-warning me-3"></i>
        <div>
            <strong>Informasi:</strong> Pengajuan dilakukan secara <b>offline</b>. Datanglah ke loket fakultas membawa fotocopy dokumen. 
            Setelah Admin menginput data, Anda dapat memantau progresnya di sini.
        </div>
    </div>
</div>

<div class="card card-clean">
    <div class="card-body p-0">
        @if($daftarRiwayat->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada data pengajuan</h5>
                <p class="text-muted">Silakan kunjungi loket fakultas untuk memulai proses legalisir.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-clean mb-0" id="riwayatTable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Jenis Dokumen</th>
                            <th>Tgl Input</th>
                            <th>Biaya</th>
                            <th>Tgl Bayar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarRiwayat as $index => $item)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->Jenis_Dokumen }}</strong><br>
                                <small class="text-muted">{{ $item->Jumlah_Salinan }} Salinan</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d/m/Y') }}</td>
                            <td>
                                @if($item->Biaya)
                                    Rp {{ number_format($item->Biaya, 0, ',', '.') }}
                                @else
                                    <span class="text-muted small">Menunggu Konfirmasi</span>
                                @endif
                            </td>
                            <td>{{ $item->Tanggal_Bayar ? \Carbon\Carbon::parse($item->Tanggal_Bayar)->format('d/m/Y') : '-' }}</td>
                            <td class="text-center">
                                @php
                                    $status = strtolower($item->Status);
                                    $color = 'secondary';
                                    if(str_contains($status, 'lunas') || str_contains($status, 'selesai')) $color = 'success';
                                    elseif(str_contains($status, 'pembayaran') || str_contains($status, 'pending')) $color = 'warning';
                                    elseif(str_contains($status, 'proses')) $color = 'info';
                                @endphp
                                <span class="badge bg-{{ $color }} badge-clean">
                                    {{ str_replace('_', ' ', $item->Status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-3" 
                                        data-bs-toggle="modal" data-bs-target="#detail{{ $item->id_no }}">
                                    <i class="fas fa-search me-1"></i> Detail
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Detail --}}
                        <div class="modal fade" id="detail{{ $item->id_no }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold">Detail Pengajuan #{{ $item->id_no }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3 border-bottom pb-2">
                                            <label class="text-muted small d-block">Status Saat Ini</label>
                                            <span class="fw-bold text-primary text-uppercase">{{ str_replace('_', ' ', $item->Status) }}</span>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="text-muted small d-block">Jenis Dokumen</label>
                                                <p class="fw-bold mb-0">{{ $item->Jenis_Dokumen }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="text-muted small d-block">Jumlah Salinan</label>
                                                <p class="fw-bold mb-0">{{ $item->Jumlah_Salinan }} Lembar</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="text-muted small d-block">Biaya Legalisir</label>
                                                <p class="fw-bold mb-0 text-success">{{ $item->Biaya ? 'Rp '.number_format($item->Biaya, 0, ',', '.') : '-' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <label class="text-muted small d-block">Tanggal Bayar</label>
                                                <p class="fw-bold mb-0">{{ $item->Tanggal_Bayar ? \Carbon\Carbon::parse($item->Tanggal_Bayar)->format('d F Y') : 'Belum Bayar' }}</p>
                                            </div>
                                        </div>
                                        @if($item->Status == 'siap_diambil')
                                        <div class="mt-4 alert alert-success py-2 small">
                                            <i class="fas fa-check-circle me-1"></i> Berkas sudah selesai diproses. Silakan ambil di loket fakultas.
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
            order: [[2, 'desc']]
        });
    });
</script>
@endpush