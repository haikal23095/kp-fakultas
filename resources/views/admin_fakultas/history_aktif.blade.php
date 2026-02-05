@extends('layouts.admin_fakultas')

@section('title', 'History Surat Keterangan Aktif')

@push('styles')
<style>
    .modal-lg-custom {
        max-width: 700px;
    }
    .detail-label {
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.85rem;
    }
    .detail-value {
        color: #2c3e50;
    }
    .status-timeline {
        border-left: 3px solid #4e73df;
        padding-left: 15px;
        margin-left: 10px;
    }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">History Surat Keterangan Aktif</h1>
        <p class="text-muted small mb-0">Riwayat pengajuan surat keterangan mahasiswa aktif kuliah</p>
    </div>
    <a href="{{ route('admin_fakultas.surat.aktif') }}" class="btn btn-outline-secondary">
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
    <div class="card-header py-3" style="background: linear-gradient(135deg, #36b9cc 0%, #1a8a9c 100%); border-radius: 12px 12px 0 0;">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-history me-2"></i>Riwayat Surat Keterangan Aktif
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" width="100%" cellspacing="0">
                <thead style="background-color: #f8f9fc;">
                    <tr>
                        <th>Tgl. Pengajuan</th>
                        <th>Nomor Surat</th>
                        <th>Mahasiswa</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historyData as $surat)
                    <tr>
                        <td>
                            @if($surat->Tanggal_Diberikan)
                                {{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan)->format('d M Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                            @if($surat->is_urgent)
                                <br><span class="badge bg-danger"><i class="fas fa-exclamation-circle me-1"></i>URGENT</span>
                            @endif
                        </td>
                        <td>
                            @if($surat->Nomor_Surat)
                                <span class="fw-bold text-dark">{{ $surat->Nomor_Surat }}</span>
                            @else
                                <span class="text-muted fst-italic small">Belum ada nomor</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $surat->pemberiTugas?->Name_User ?? 'N/A' }}</div>
                            <small class="text-muted">
                                {{ $surat->pemberiTugas?->mahasiswa?->NIM ?? '' }} - 
                                {{ $surat->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi ?? 'N/A' }}
                            </small>
                        </td>
                        <td>
                            {{ \Illuminate\Support\Str::limit($surat->Deskripsi ?? 'N/A', 40) }}
                        </td>
                        <td class="align-middle text-center">
                            @php 
                                $status = $surat->Status ?? 'baru';
                                $status = trim($status);
                            @endphp
                            
                            @if(strtolower($status) === 'selesai' || strtolower($status) === 'success')
                                <span class="badge rounded-pill bg-success px-3 py-2"><i class="fas fa-check me-1"></i> {{ $status }}</span>
                            @elseif(str_contains(strtolower($status), 'ditolak'))
                                <span class="badge rounded-pill bg-danger px-3 py-2"><i class="fas fa-times me-1"></i> {{ $status }}</span>
                            @elseif(strtolower($status) === 'diajukan-ke-dekan')
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fas fa-signature me-1"></i> Ke Dekan</span>
                            @else
                                <span class="badge rounded-pill bg-secondary px-3 py-2">{{ $status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-info shadow-sm btn-detail"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailModal"
                                    data-id="{{ $surat->id_no }}"
                                    data-nomor="{{ $surat->Nomor_Surat ?? '-' }}"
                                    data-nama="{{ $surat->pemberiTugas?->Name_User ?? 'N/A' }}"
                                    data-nim="{{ $surat->pemberiTugas?->mahasiswa?->NIM ?? '-' }}"
                                    data-prodi="{{ $surat->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi ?? '-' }}"
                                    data-tahun="{{ $surat->Tahun_Akademik ?? '-' }}"
                                    data-deskripsi="{{ $surat->Deskripsi ?? '-' }}"
                                    data-status="{{ $surat->Status ?? '-' }}"
                                    data-urgent="{{ $surat->is_urgent ? 'Ya' : 'Tidak' }}"
                                    data-urgent-reason="{{ $surat->urgent_reason ?? '-' }}"
                                    data-tanggal-diberikan="{{ $surat->Tanggal_Diberikan ? \Carbon\Carbon::parse($surat->Tanggal_Diberikan)->format('d M Y H:i') : '-' }}"
                                    data-tanggal-diselesaikan="{{ $surat->Tanggal_Diselesaikan ? \Carbon\Carbon::parse($surat->Tanggal_Diselesaikan)->format('d M Y H:i') : '-' }}"
                                    data-penerima="{{ $surat->penerimaTugas?->Name_User ?? '-' }}"
                                    data-role-penerima="{{ $surat->penerimaTugas?->role?->Name_Role ?? '-' }}">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data history surat keterangan aktif.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($historyData->hasPages())
        <div class="mt-3">
            {{ $historyData->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg-custom modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 16px 16px 0 0;">
                <h5 class="modal-title text-white" id="detailModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail Surat Keterangan Aktif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="detail-label mb-1">ID Surat</p>
                        <p class="detail-value" id="modal-id">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Nomor Surat</p>
                        <p class="detail-value fw-bold" id="modal-nomor">-</p>
                    </div>
                </div>

                <hr>

                <h6 class="text-primary mb-3"><i class="fas fa-user-graduate me-2"></i>Data Mahasiswa</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Nama Mahasiswa</p>
                        <p class="detail-value" id="modal-nama">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="detail-label mb-1">NIM</p>
                        <p class="detail-value" id="modal-nim">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Program Studi</p>
                        <p class="detail-value" id="modal-prodi">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Tahun Akademik</p>
                        <p class="detail-value" id="modal-tahun">-</p>
                    </div>
                </div>

                <hr>

                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Surat</h6>
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="detail-label mb-1">Keperluan/Deskripsi</p>
                        <p class="detail-value" id="modal-deskripsi">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Urgent</p>
                        <p class="detail-value" id="modal-urgent">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Alasan Urgent</p>
                        <p class="detail-value" id="modal-urgent-reason">-</p>
                    </div>
                </div>

                <hr>

                <h6 class="text-primary mb-3"><i class="fas fa-clock me-2"></i>Timeline</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Tanggal Pengajuan</p>
                        <p class="detail-value" id="modal-tanggal-diberikan">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Tanggal Diselesaikan</p>
                        <p class="detail-value" id="modal-tanggal-diselesaikan">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Status</p>
                        <p class="detail-value" id="modal-status-container">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="detail-label mb-1">Penerima Tugas</p>
                        <p class="detail-value" id="modal-penerima">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailButtons = document.querySelectorAll('.btn-detail');
    
    detailButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nomor = this.dataset.nomor;
            const nama = this.dataset.nama;
            const nim = this.dataset.nim;
            const prodi = this.dataset.prodi;
            const tahun = this.dataset.tahun;
            const deskripsi = this.dataset.deskripsi;
            const status = this.dataset.status;
            const urgent = this.dataset.urgent;
            const urgentReason = this.dataset.urgentReason;
            const tanggalDiberikan = this.dataset.tanggalDiberikan;
            const tanggalDiselesaikan = this.dataset.tanggalDiselesaikan;
            const penerima = this.dataset.penerima;
            const rolePenerima = this.dataset.rolePenerima;

            document.getElementById('modal-id').textContent = id;
            document.getElementById('modal-nomor').textContent = nomor;
            document.getElementById('modal-nama').textContent = nama;
            document.getElementById('modal-nim').textContent = nim;
            document.getElementById('modal-prodi').textContent = prodi;
            document.getElementById('modal-tahun').textContent = tahun;
            document.getElementById('modal-deskripsi').textContent = deskripsi;
            document.getElementById('modal-urgent').textContent = urgent;
            document.getElementById('modal-urgent-reason').textContent = urgentReason;
            document.getElementById('modal-tanggal-diberikan').textContent = tanggalDiberikan;
            document.getElementById('modal-tanggal-diselesaikan').textContent = tanggalDiselesaikan;
            document.getElementById('modal-penerima').textContent = penerima + ' (' + rolePenerima + ')';

            // Status badge
            let statusBadge = '';
            const statusLower = status.toLowerCase();
            if (statusLower === 'selesai' || statusLower === 'success') {
                statusBadge = '<span class="badge rounded-pill bg-success px-3 py-2"><i class="fas fa-check me-1"></i> ' + status + '</span>';
            } else if (statusLower.includes('ditolak')) {
                statusBadge = '<span class="badge rounded-pill bg-danger px-3 py-2"><i class="fas fa-times me-1"></i> ' + status + '</span>';
            } else if (statusLower === 'diajukan-ke-dekan') {
                statusBadge = '<span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fas fa-signature me-1"></i> Ke Dekan</span>';
            } else {
                statusBadge = '<span class="badge rounded-pill bg-secondary px-3 py-2">' + status + '</span>';
            }
            document.getElementById('modal-status-container').innerHTML = statusBadge;
        });
    });
});
</script>
@endpush
