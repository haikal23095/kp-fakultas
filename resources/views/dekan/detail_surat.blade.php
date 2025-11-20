@extends('layouts.dekan')

@section('title', 'Detail Surat')

@section('content')
@php $status = trim(optional($surat)->Status ?? ''); @endphp

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Detail Surat</h1>
        <p class="text-muted mb-0">Nomor: <strong>{{ optional($surat)->Nomor_Surat ?? 'N/A' }}</strong> &middot; ID: <strong>{{ optional($surat)->Id_Tugas_Surat ?? '-' }}</strong></p>
    </div>
    <div>
        @if(strtolower($status) === 'selesai' || strtolower($status) === 'disetujui')
            <span class="badge bg-success fs-6">{{ $surat->Status }}</span>
        @elseif(strtolower($status) === 'terlambat' || strtolower($status) === 'ditolak')
            <span class="badge bg-danger fs-6">{{ $surat->Status }}</span>
        @elseif(strtolower($status) === 'proses' || strtolower($status) === 'menunggu-ttd')
            <span class="badge bg-warning fs-6">{{ $surat->Status }}</span>
        @else
            <span class="badge bg-secondary fs-6">{{ $surat->Status ?? '-' }}</span>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center">
                <i class="fas fa-user-circle fa-lg text-primary me-2"></i>
                <strong>Detail Pengaju</strong>
            </div>
            <div class="card-body">
                <h5 class="mb-1">{{ optional($surat->pemberiTugas)->Name_User ?? '-' }}</h5>
                <div class="text-muted mb-3">{{ optional(optional($surat->pemberiTugas)->role)->Name_Role ?? '-' }}</div>

                <p class="mb-1"><small class="text-muted">Tanggal Pengajuan</small><br>{{ optional($surat->Tanggal_Diberikan_Tugas_Surat) ? optional($surat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y H:i') : '-' }}</p>

                @if(!empty($detailPengaju))
                    <hr />
                    <p class="mb-1"><small class="text-muted">NIM</small><br>{{ $detailPengaju->NIM ?? '-' }}</p>
                    <p class="mb-0"><small class="text-muted">Alamat</small><br>{{ $detailPengaju->Alamat_Mahasiswa ?? '-' }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center">
                <i class="fas fa-info-circle fa-lg text-primary me-2"></i>
                <strong>Informasi Surat</strong>
            </div>
            <div class="card-body">
                <p class="mb-2"><small class="text-muted">Jenis Surat</small><br>{{ optional($surat->jenisSurat)->Nama_Surat ?? '-' }}</p>
                <p class="mb-2"><small class="text-muted">Judul</small><br>{{ $surat->Judul_Tugas_Surat ?? '-' }}</p>
                <p class="mb-0"><small class="text-muted">Deskripsi</small><br>{{ $surat->Deskripsi_Tugas_Surat ?? $surat->Deskripsi_Tugas ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center">
                <i class="fas fa-folder-open fa-lg text-primary me-2"></i>
                <strong>Dokumen & Status</strong>
            </div>
            <div class="card-body">
                <p class="mb-2"><small class="text-muted">Diproses oleh</small><br>{{ optional($surat->penerimaTugas)->Name_User ?? '-' }} <br><span class="text-muted">({{ optional(optional($surat->penerimaTugas)->role)->Name_Role ?? '-' }})</span></p>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Dokumen Pendukung</small>
                    @if(!empty($surat->data_spesifik['dokumen_pendukung'] ?? null))
                        <a href="{{ route('dekan.surat.download', $surat->Id_Tugas_Surat) }}" class="btn btn-outline-primary btn-sm" title="Lihat / Unduh Dokumen Pendukung"><i class="fas fa-download me-1"></i> Lihat / Unduh</a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Draft Final (Arsip)</small>
                    @if(optional($surat->fileArsip)->Path_File)
                        <a href="{{ asset('storage/' . ltrim(optional($surat->fileArsip)->Path_File, '/')) }}" target="_blank" class="btn btn-outline-success btn-sm"><i class="fas fa-file-pdf me-1"></i> Lihat Draft</a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>

                @if($status === 'menunggu-ttd')
                    <hr />
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('dekan.surat.approve', $surat->Id_Tugas_Surat) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui dan menandatangani surat ini?\n\n(TTE dengan QR Code akan diintegrasikan pada tahap selanjutnya)');">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-signature me-1"></i> Setujui & Tanda Tangan
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times me-1"></i> Tolak
                        </button>
                    </div>

                    <!-- Modal Tolak Surat -->
                    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('dekan.surat.reject', $surat->Id_Tugas_Surat) }}">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel">Tolak Surat</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Anda akan menolak surat ini. Silakan berikan alasan penolakan.
                                        </div>
                                        <div class="mb-3">
                                            <label for="komentar" class="form-label">Alasan Penolakan (Opsional)</label>
                                            <textarea class="form-control" id="komentar" name="komentar" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                                            <div class="form-text">Maksimal 500 karakter</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times me-1"></i> Tolak Surat
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('dekan.persetujuan.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

@endsection
