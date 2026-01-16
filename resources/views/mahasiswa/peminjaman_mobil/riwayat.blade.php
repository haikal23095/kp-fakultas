@extends('layouts.mahasiswa')

@section('title', 'Riwayat Peminjaman Mobil Dinas')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Riwayat Peminjaman Mobil Dinas</h1>
            <p class="text-muted mb-0">Daftar pengajuan peminjaman mobil dinas yang telah Anda ajukan</p>
        </div>
        <a href="{{ route('mahasiswa.riwayat') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Riwayat</h6>
            <a href="{{ route('mahasiswa.pengajuan.mobil.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Ajukan Peminjaman Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Tujuan</th>
                            <th>Tanggal Pemakaian</th>
                            <th>Kendaraan</th>
                            <th>Nomor Surat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $index => $peminjaman)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $peminjaman->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <div class="fw-bold">{{ Str::limit($peminjaman->tujuan, 30) }}</div>
                                    <small class="text-muted">{{ $peminjaman->jumlah_penumpang }} penumpang</small>
                                </td>
                                <td>
                                    <small>
                                        <strong>Mulai:</strong> {{ \Carbon\Carbon::parse($peminjaman->tanggal_pemakaian_mulai)->format('d M Y H:i') }}<br>
                                        <strong>Selesai:</strong> {{ \Carbon\Carbon::parse($peminjaman->tanggal_pemakaian_selesai)->format('d M Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    @if($peminjaman->kendaraan)
                                        <span class="badge bg-info">
                                            <i class="fas fa-car me-1"></i>{{ $peminjaman->kendaraan->nama_kendaraan }}
                                        </span>
                                        <br><small class="text-muted">{{ $peminjaman->kendaraan->plat_nomor }}</small>
                                    @else
                                        <span class="badge bg-secondary">Belum Ditentukan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($peminjaman->nomor_surat)
                                        <span class="badge bg-success">{{ $peminjaman->nomor_surat }}</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Terbit</span>
                                    @endif
                                </td>
                                <td>
                                    @if($peminjaman->status_pengajuan == 'Diajukan')
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>Menunggu Verifikasi Admin
                                        </span>
                                    @elseif($peminjaman->status_pengajuan == 'Diverifikasi_Admin')
                                        <span class="badge bg-info">
                                            <i class="fas fa-hourglass-half me-1"></i>Menunggu Persetujuan Wadek 2
                                        </span>
                                    @elseif($peminjaman->status_pengajuan == 'Selesai')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Selesai
                                        </span>
                                    @elseif($peminjaman->status_pengajuan == 'Ditolak')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $peminjaman->status_pengajuan }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal{{ $peminjaman->id }}">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                        
                                        @if($peminjaman->status_pengajuan == 'Selesai')
                                            <a href="{{ route('mahasiswa.peminjaman.mobil.preview', $peminjaman->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-file-alt me-1"></i>Lihat Surat
                                            </a>
                                            <a href="{{ route('mahasiswa.peminjaman.mobil.download', $peminjaman->id) }}" 
                                               class="btn btn-sm btn-success">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal Detail --}}
                            <div class="modal fade" id="detailModal{{ $peminjaman->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">
                                                <i class="fas fa-car me-2"></i>Detail Peminjaman Mobil Dinas
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-4 fw-bold">Status:</div>
                                                <div class="col-md-8">
                                                    @if($peminjaman->status_pengajuan == 'Diajukan')
                                                        <span class="badge bg-warning text-dark">Menunggu Verifikasi Admin</span>
                                                    @elseif($peminjaman->status_pengajuan == 'Diverifikasi_Admin')
                                                        <span class="badge bg-info">Menunggu Persetujuan Wadek 2</span>
                                                    @elseif($peminjaman->status_pengajuan == 'Selesai')
                                                        <span class="badge bg-success">Selesai</span>
                                                    @elseif($peminjaman->status_pengajuan == 'Ditolak')
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-4 fw-bold">Tujuan:</div>
                                                <div class="col-md-8">{{ $peminjaman->tujuan }}</div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-4 fw-bold">Keperluan:</div>
                                                <div class="col-md-8">{{ $peminjaman->keperluan }}</div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-4 fw-bold">Tanggal Pemakaian:</div>
                                                <div class="col-md-8">
                                                    <strong>Mulai:</strong> {{ \Carbon\Carbon::parse($peminjaman->tanggal_pemakaian_mulai)->format('d M Y H:i') }}<br>
                                                    <strong>Selesai:</strong> {{ \Carbon\Carbon::parse($peminjaman->tanggal_pemakaian_selesai)->format('d M Y H:i') }}
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-4 fw-bold">Jumlah Penumpang:</div>
                                                <div class="col-md-8">{{ $peminjaman->jumlah_penumpang }} orang</div>
                                            </div>

                                            @if($peminjaman->kendaraan)
                                                <div class="row mb-3">
                                                    <div class="col-md-4 fw-bold">Kendaraan:</div>
                                                    <div class="col-md-8">
                                                        <strong>{{ $peminjaman->kendaraan->nama_kendaraan }}</strong><br>
                                                        <small>Plat Nomor: {{ $peminjaman->kendaraan->plat_nomor }}</small><br>
                                                        <small>Kapasitas: {{ $peminjaman->kendaraan->kapasitas }} orang</small>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($peminjaman->rekomendasi_admin)
                                                <div class="row mb-3">
                                                    <div class="col-md-4 fw-bold">Rekomendasi Admin:</div>
                                                    <div class="col-md-8">{{ $peminjaman->rekomendasi_admin }}</div>
                                                </div>
                                            @endif

                                            @if($peminjaman->nomor_surat)
                                                <div class="row mb-3">
                                                    <div class="col-md-4 fw-bold">Nomor Surat:</div>
                                                    <div class="col-md-8">
                                                        <span class="badge bg-success">{{ $peminjaman->nomor_surat }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($peminjaman->status_pengajuan == 'Ditolak' && $peminjaman->alasan_penolakan)
                                                <div class="alert alert-danger mt-3">
                                                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Alasan Penolakan:</strong>
                                                    <p class="mb-0 mt-2">{{ $peminjaman->alasan_penolakan }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            @if($peminjaman->status_pengajuan == 'Selesai')
                                                <a href="{{ route('mahasiswa.peminjaman.mobil.preview', $peminjaman->id) }}" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-file-alt me-2"></i>Lihat Surat
                                                </a>
                                                <a href="{{ route('mahasiswa.peminjaman.mobil.download', $peminjaman->id) }}" 
                                                   class="btn btn-success">
                                                    <i class="fas fa-download me-2"></i>Download Surat
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-car fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada riwayat peminjaman mobil dinas.</p>
                                    <a href="{{ route('mahasiswa.pengajuan.mobil.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus me-2"></i>Ajukan Sekarang
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info border-0">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-2">Informasi Peminjaman Mobil Dinas</h6>
                        <ul class="mb-0">
                            <li><strong>Diajukan:</strong> Pengajuan Anda sedang menunggu verifikasi dari Admin Fakultas</li>
                            <li><strong>Diverifikasi Admin:</strong> Admin sudah memverifikasi dan menentukan kendaraan, menunggu persetujuan Wadek 2</li>
                            <li><strong>Selesai:</strong> Pengajuan telah disetujui dan ditandatangani Wadek 2, surat dapat dilihat dan didownload</li>
                            <li><strong>Ditolak:</strong> Pengajuan ditolak, lihat alasan penolakan di detail</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add hover effect on table rows
    document.querySelectorAll('#dataTable tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fc';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
</script>
@endpush
