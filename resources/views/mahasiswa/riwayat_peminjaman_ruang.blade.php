@extends('layouts.mahasiswa')

@section('title', $title ?? 'Riwayat Peminjaman Ruang')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">{{ $title ?? 'Riwayat Peminjaman Ruang' }}</h1>
            <p class="text-muted mb-0">Daftar pengajuan peminjaman ruang yang telah Anda ajukan</p>
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
            <a href="{{ route('mahasiswa.pengajuan.ruang.create') }}" class="btn btn-primary btn-sm">
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
                            <th>Nama Kegiatan</th>
                            <th>Penyelenggara</th>
                            <th>Tanggal Kegiatan</th>
                            <th>Peserta</th>
                            <th>Ruangan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatSurat as $index => $tugas)
                            @php
                                $surat = $tugas->suratPeminjamanRuang;
                            @endphp
                            @if($surat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($tugas->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $surat->nama_kegiatan ?? '-' }}</div>
                                </td>
                                <td>{{ $surat->penyelenggara ?? '-' }}</td>
                                <td>
                                    <small>
                                        {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y H:i') }}<br>
                                        s/d<br>
                                        {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y H:i') }}
                                    </small>
                                </td>
                                <td>{{ $surat->jumlah_peserta ?? 0 }} orang</td>
                                <td>
                                    @if($surat->ruangan)
                                        <span class="badge bg-success">{{ $surat->ruangan->Nama_Ruangan }}</span>
                                    @else
                                        <span class="badge bg-secondary">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td>
                                    @if($surat->status_pengajuan == 'Selesai')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Selesai
                                        </span>
                                    @elseif($surat->status_pengajuan == 'Disetujui_Wadek2')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-check me-1"></i>Disetujui
                                        </span>
                                    @elseif($surat->status_pengajuan == 'Diverifikasi_Admin')
                                        <span class="badge bg-info">
                                            <i class="fas fa-hourglass-half me-1"></i>Diverifikasi Admin
                                        </span>
                                    @elseif($surat->status_pengajuan == 'Ditolak')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-paper-plane me-1"></i>{{ $surat->status_pengajuan }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada riwayat pengajuan peminjaman ruang.</p>
                                    <a href="{{ route('mahasiswa.pengajuan.ruang.create') }}" class="btn btn-primary btn-sm mt-2">
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
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        },
        order: [[1, 'desc']],
    });
});
</script>
@endpush
