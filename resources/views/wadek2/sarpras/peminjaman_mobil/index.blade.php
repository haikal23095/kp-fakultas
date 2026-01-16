@extends('layouts.wadek2')

@section('title', 'Persetujuan Peminjaman Mobil')

@push('styles')
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #f0f0f0;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }
    
    .badge-status {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
    }
    
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .table thead th {
        background: #f8f9fc;
        color: #5a5c69;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border: none;
    }
    
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-2 fw-bold text-dark">Persetujuan Peminjaman Mobil Dinas</h3>
            <p class="mb-0 text-muted">Kelola persetujuan peminjaman mobil fakultas yang sudah diverifikasi admin</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if(isset($pengajuan) && $pengajuan->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada pengajuan baru</h5>
                <p class="text-muted">Semua pengajuan sudah diproses</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Peminjam</th>
                            <th>Tujuan</th>
                            <th>Kendaraan</th>
                            <th>Tanggal Pemakaian</th>
                            <th>Nomor Surat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $item->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $item->created_at->format('H:i') }} WIB</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $item->user->Name_User ?? 'N/A' }}</div>
                                    <small class="text-muted">
                                        {{ $item->user->role->Name_Role ?? '' }}
                                    </small>
                                </td>
                                <td>{{ Str::limit($item->tujuan, 30) }}</td>
                                <td>
                                    <div class="fw-bold">{{ $item->kendaraan->nama_kendaraan ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $item->kendaraan->plat_nomor ?? '' }}</small>
                                </td>
                                <td>
                                    <small>
                                        {{ \Carbon\Carbon::parse($item->tanggal_pemakaian_mulai)->format('d M Y H:i') }}<br>
                                        s/d<br>
                                        {{ \Carbon\Carbon::parse($item->tanggal_pemakaian_selesai)->format('d M Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $item->nomor_surat ?? 'Belum ada' }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal{{ $item->id }}">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $item->id }}">
                                            <i class="fas fa-times me-1"></i>Tolak
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Tidak ada pengajuan yang perlu disetujui</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Modal Section --}}
@if(isset($pengajuan))
    @foreach($pengajuan as $item)
        {{-- Modal Detail & Preview --}}
        <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-alt me-2"></i>Detail & Preview Surat Peminjaman Mobil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{-- Kolom Kiri: Info --}}
                            <div class="col-lg-4">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">Info Peminjam</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted">Nama</td>
                                                <td class="fw-bold">{{ $item->user->Name_User ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Role</td>
                                                <td>{{ $item->user->role->Name_Role ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tujuan</td>
                                                <td>{{ $item->tujuan }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Keperluan</td>
                                                <td>{{ $item->keperluan }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Kendaraan</td>
                                                <td class="fw-bold">{{ $item->kendaraan->nama_kendaraan ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Plat Nomor</td>
                                                <td><span class="badge bg-secondary">{{ $item->kendaraan->plat_nomor ?? 'N/A' }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Penumpang</td>
                                                <td>{{ $item->jumlah_penumpang }} orang</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tanggal Mulai</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pemakaian_mulai)->format('d M Y, H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tanggal Selesai</td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pemakaian_selesai)->format('d M Y, H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">Status</h6>
                                    </div>
                                    <div class="card-body">
                                        <span class="badge bg-info badge-status">{{ $item->status_pengajuan }}</span>
                                        <div class="mt-2">
                                            <small class="text-muted">Nomor Surat:</small>
                                            <div class="fw-bold">{{ $item->nomor_surat ?? 'Belum ada' }}</div>
                                        </div>
                                    </div>
                                </div>

                                @if($item->rekomendasi_admin)
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold">Catatan Admin</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0">{{ $item->rekomendasi_admin }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Kolom Kanan: Preview Draft --}}
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">Preview Draft Surat</h6>
                                    </div>
                                    <div class="card-body" style="background: #f8f9fa; max-height: 600px; overflow-y: auto;">
                                        <div class="bg-white p-4" style="font-family: 'Times New Roman', serif;">
                                            {{-- Preview content akan sama dengan PDF --}}
                                            <div class="text-center mb-4">
                                                <h5 class="fw-bold">SURAT PEMINJAMAN MOBIL DINAS</h5>
                                                @if($item->nomor_surat)
                                                    <p class="mb-0">Nomor: {{ $item->nomor_surat }}</p>
                                                @else
                                                    <p class="mb-0 text-muted">[Nomor Surat Akan Diberikan]</p>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <table class="table table-sm table-bordered">
                                                    <tr>
                                                        <td width="30%"><strong>Peminjam</strong></td>
                                                        <td>{{ $item->user->Name_User ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Tujuan</strong></td>
                                                        <td>{{ $item->tujuan }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Keperluan</strong></td>
                                                        <td>{{ $item->keperluan }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Kendaraan</strong></td>
                                                        <td>{{ $item->kendaraan->nama_kendaraan ?? 'N/A' }} ({{ $item->kendaraan->plat_nomor ?? 'N/A' }})</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Tanggal Pemakaian</strong></td>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($item->tanggal_pemakaian_mulai)->format('d F Y, H:i') }} WIB<br>
                                                            s/d<br>
                                                            {{ \Carbon\Carbon::parse($item->tanggal_pemakaian_selesai)->format('d F Y, H:i') }} WIB
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Jumlah Penumpang</strong></td>
                                                        <td>{{ $item->jumlah_penumpang }} orang</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            @if($item->rekomendasi_admin)
                                                <div class="alert alert-info">
                                                    <strong>Catatan Admin:</strong><br>
                                                    {{ $item->rekomendasi_admin }}
                                                </div>
                                            @endif

                                            <div class="text-end mt-5">
                                                <p class="mb-1">Malang, {{ now()->format('d F Y') }}</p>
                                                <p class="mb-5">Wakil Dekan II</p>
                                                <p class="fw-bold mb-0">[Nama Wadek II]</p>
                                                <p>NIP. [NIP Wadek II]</p>
                                            </div>

                                            <div class="text-center mt-3">
                                                <span class="badge bg-warning text-dark" style="font-size: 1.5rem; padding: 1rem;">DRAFT</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Tutup
                        </button>
                        <a href="{{ route('wadek2.sarpras.peminjaman_mobil.preview_draft', $item->id) }}" 
                           target="_blank" 
                           class="btn btn-info">
                            <i class="fas fa-file-pdf me-2"></i>Lihat PDF
                        </a>
                        <button type="button" class="btn btn-success" 
                                data-bs-toggle="modal" 
                                data-bs-target="#approveModal{{ $item->id }}"
                                data-bs-dismiss="modal">
                            <i class="fas fa-check me-2"></i>Proses Persetujuan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Approve --}}
        <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle me-2"></i>Setujui Peminjaman Mobil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('wadek2.sarpras.peminjaman_mobil.setujui', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Konfirmasi Persetujuan</strong><br>
                                <small>Dengan menyetujui, surat akan diterbitkan dengan nomor: <strong>{{ $item->nomor_surat }}</strong> dan QR Code untuk TTE akan di-generate.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan Wadek II (Opsional)</label>
                                <textarea class="form-control" name="catatan_wadek2" rows="3" placeholder="Masukkan catatan jika diperlukan"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Setujui & Generate QR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Tolak --}}
        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-times-circle me-2"></i>Tolak Peminjaman Mobil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('wadek2.sarpras.peminjaman_mobil.tolak', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="alasan_penolakan" rows="4" required placeholder="Jelaskan alasan penolakan"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times me-2"></i>Konfirmasi Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

@endsection
