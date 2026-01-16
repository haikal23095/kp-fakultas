@extends('layouts.wadek3')

@section('title', 'Validasi Dispensasi Mahasiswa')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-light mb-2">Validasi Dispensasi Mahasiswa</h1>
    <p class="text-muted">Validasi dan tandatangani pengajuan dispensasi kegiatan mahasiswa dengan QR Code</p>
</div>

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Statistics Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-25 rounded-3 p-3">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Menunggu Persetujuan</h6>
                        <h3 class="mb-0 fw-bold">{{ $daftarSurat->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Daftar Pengajuan Dispensasi</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tableDispensasi">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Prodi</th>
                        <th>Keperluan</th>
                        <th>Tanggal Kegiatan</th>
                        <th>Nomor Surat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarSurat as $index => $tugas)
                        @php
                            $surat = $tugas->suratDispensasi;
                            $mahasiswa = $tugas->pemberiTugas->mahasiswa ?? null;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary">{{ $mahasiswa->NIM ?? '-' }}</span></td>
                            <td>
                                <div class="fw-bold">{{ $tugas->pemberiTugas->Name_User ?? '-' }}</div>
                                <small class="text-muted">{{ $mahasiswa->Email ?? '-' }}</small>
                            </td>
                            <td>{{ $mahasiswa->prodi->Nama_Prodi ?? '-' }}</td>
                            <td>
                                <div class="fw-bold text-primary">{{ $surat->nama_kegiatan }}</div>
                                @if($surat->instansi_penyelenggara)
                                    <small class="text-muted">{{ $surat->instansi_penyelenggara }}</small>
                                @endif
                            </td>
                            <td>
                                <small>
                                    {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}
                                </small>
                            </td>
                            <td>
                                @if($surat->nomor_surat)
                                    <span class="badge bg-info text-dark">{{ $surat->nomor_surat }}</span>
                                @else
                                    <span class="badge bg-secondary">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock me-1"></i>Menunggu
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('wadek3.kemahasiswaan.detail-dispensasi', $tugas->Id_Tugas_Surat) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i>Detail & ACC
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada pengajuan dispensasi yang menunggu persetujuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#tableDispensasi').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        },
        order: [[0, 'asc']],
        pageLength: 25,
    });
});
</script>
@endpush
