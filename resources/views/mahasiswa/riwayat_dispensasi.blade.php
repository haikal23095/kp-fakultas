@extends('layouts.mahasiswa')

@section('title', $title ?? 'Riwayat Surat Dispensasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">{{ $title ?? 'Riwayat Surat Dispensasi' }}</h1>
            <p class="text-muted mb-0">Daftar pengajuan surat dispensasi yang telah Anda ajukan</p>
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
            <a href="{{ route('mahasiswa.pengajuan.dispen.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Ajukan Dispensasi Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Nomor Surat</th>
                            <th>Keperluan</th>
                            <th>Tanggal Kegiatan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatSurat as $index => $tugas)
                            @php
                                $surat = $tugas->suratDispensasi;
                                $verification = $tugas->verification;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($tugas->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}</td>
                                <td>
                                    @if($surat && $surat->nomor_surat)
                                        <span class="badge bg-info text-dark">{{ $surat->nomor_surat }}</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Terbit</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $surat->nama_kegiatan ?? 'Permohonan Surat Dispensasi' }}</div>
                                    @if($surat && $surat->instansi_penyelenggara)
                                        <small class="text-muted">{{ $surat->instansi_penyelenggara }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($surat)
                                        <small>
                                            {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($surat && $surat->acc_wadek3_by)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Selesai
                                        </span>
                                    @elseif($surat && $surat->nomor_surat)
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>Menunggu ACC Wadek3
                                        </span>
                                    @elseif($surat && $surat->verifikasi_admin_by)
                                        <span class="badge bg-info">
                                            <i class="fas fa-hourglass-half me-1"></i>Diproses Admin
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-paper-plane me-1"></i>Baru
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($surat && $surat->acc_wadek3_by && $surat->file_surat_selesai)
                                        <a href="{{ route('mahasiswa.download.dispensasi', $tugas->Id_Tugas_Surat) }}" 
                                           class="btn btn-success btn-sm" target="_blank">
                                            <i class="fas fa-download me-1"></i>Download PDF
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fas fa-hourglass me-1"></i>Belum Selesai
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada riwayat pengajuan surat dispensasi.</p>
                                    <a href="{{ route('mahasiswa.pengajuan.dispen.create') }}" class="btn btn-primary btn-sm mt-2">
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
