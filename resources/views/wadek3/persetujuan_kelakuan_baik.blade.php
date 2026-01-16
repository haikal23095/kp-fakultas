@extends('layouts.wadek3')

@section('title', 'Persetujuan Surat Berkelakuan Baik')

@push('styles')
<style>
    .card-surat {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card-surat:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .badge-status {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-light mb-2">Persetujuan Surat Berkelakuan Baik</h1>
            <p class="text-muted">Tandatangani surat keterangan berkelakuan baik mahasiswa dengan QR digital</p>
        </div>
        <a href="{{ route('dashboard.wadek3') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
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

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Daftar Surat Menunggu Persetujuan</h5>
    </div>
    <div class="card-body">
        @if($daftarSurat->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada surat berkelakuan baik yang menunggu persetujuan Anda saat ini.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Prodi</th>
                            <th>Keperluan</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarSurat as $index => $surat)
                            @php
                                $mahasiswa = $surat->pemberiTugas->mahasiswa ?? $surat->suratKelakuanBaik->user->mahasiswa ?? null;
                                $kelakuanBaik = $surat->suratKelakuanBaik;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $mahasiswa->NIM ?? '-' }}</strong>
                                </td>
                                <td>{{ $mahasiswa->Nama_Mahasiswa ?? $surat->pemberiTugas->Name_User ?? '-' }}</td>
                                <td>{{ $mahasiswa->prodi->Nama_Prodi ?? '-' }}</td>
                                <td>
                                    <span class="text-muted small">{{ \Illuminate\Support\Str::limit($kelakuanBaik->Keperluan ?? '-', 50) }}</span>
                                </td>
                                <td>{{ $surat->Tanggal_Diberikan_Tugas_Surat ? $surat->Tanggal_Diberikan_Tugas_Surat->format('d M Y') : '-' }}</td>
                                <td>{{ $kelakuanBaik->Semester ?? '-' }} {{ $kelakuanBaik->Tahun_Akademik ?? '' }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark badge-status">
                                        <i class="fas fa-clock me-1"></i>Menunggu TTD
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('wadek3.kelakuan_baik.preview', $surat->Id_Tugas_Surat) }}" class="btn btn-info btn-sm" target="_blank">
                                        <i class="fas fa-file-pdf me-1"></i>Preview PDF
                                    </a>
                                    <form action="{{ route('wadek3.kelakuan_baik.approve', $surat->Id_Tugas_Surat) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menandatangani surat ini dengan QR digital?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-signature me-1"></i>Tanda Tangan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <div class="alert alert-success border-0" role="alert">
        <div class="d-flex align-items-start">
            <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-2">Informasi Tanda Tangan Digital</h6>
                <p class="mb-0">Setiap surat yang Anda tandatangani akan diberi QR code digital berisi informasi penandatangan, tanggal, dan detail mahasiswa. QR code ini dapat diverifikasi keasliannya melalui sistem.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Auto dismiss alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush
