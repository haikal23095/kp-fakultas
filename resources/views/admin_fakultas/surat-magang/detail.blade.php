@extends('layouts.admin_fakultas')

@section('title', 'Detail Surat Magang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Detail Surat Pengantar Magang</h1>
        <p class="mb-0 text-muted">
            <a href="{{ route('admin_fakultas.surat.magang') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </p>
    </div>
</div>

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

<div class="row">
    {{-- Informasi Surat --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Surat</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nomor Surat:</div>
                    <div class="col-md-8">
                        <span class="badge bg-primary">{{ $surat->tugasSurat?->Nomor_Surat ?? $surat->Nomor_Surat ?? '-' }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Mahasiswa:</div>
                    <div class="col-md-8">
                        @foreach($dataMahasiswa as $idx => $mhs)
                        <div class="mb-2">
                            {{ $idx + 1 }}. <strong>{{ $mhs['nama'] ?? '' }}</strong><br>
                            <small class="text-muted">NIM: {{ $mhs['nim'] ?? '' }} | Angkatan: {{ $mhs['angkatan'] ?? '-' }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Program Studi:</div>
                    <div class="col-md-8">{{ $surat->tugasSurat?->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi ?? 'N/A' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dosen Pembimbing:</div>
                    <div class="col-md-8">
                        @if($dataDosenPembimbing)
                            @if(isset($dataDosenPembimbing['dosen_pembimbing_1']))
                                <div>1. {{ $dataDosenPembimbing['dosen_pembimbing_1'] }}</div>
                            @endif
                            @if(isset($dataDosenPembimbing['dosen_pembimbing_2']) && $dataDosenPembimbing['dosen_pembimbing_2'])
                                <div>2. {{ $dataDosenPembimbing['dosen_pembimbing_2'] }}</div>
                            @endif
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nama Instansi:</div>
                    <div class="col-md-8">{{ $surat->Nama_Instansi ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Alamat Instansi:</div>
                    <div class="col-md-8">{{ $surat->Alamat_Instansi ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Judul Penelitian:</div>
                    <div class="col-md-8">{{ $surat->Judul_Penelitian ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Periode Magang:</div>
                    <div class="col-md-8">
                        @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                            {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Koordinator yang Menyetujui:</div>
                    <div class="col-md-8">
                        {{ $surat->koordinator?->Nama_Dosen ?? 'N/A' }}
                        @if($surat->koordinator)
                            <br><small class="text-muted">NIP: {{ $surat->koordinator->NIP }}</small>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Catatan:</strong> Untuk melihat preview surat dan mengunduh dokumen, gunakan tombol <strong>"Review"</strong> di halaman daftar surat.
                </div>
            </div>
        </div>
    </div>

    {{-- Form Assign Nomor Surat --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4 border-primary">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-signature me-2"></i>Assign Nomor Surat
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Berikan nomor surat resmi, kemudian teruskan ke Dekan untuk persetujuan.</small>
                </div>

                <form action="{{ route('admin_fakultas.surat.magang.assign_nomor', $surat->id_no) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nomor_surat" class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nomor_surat') is-invalid @enderror" 
                               id="nomor_surat" 
                               name="nomor_surat" 
                               placeholder="Contoh: 123/UN16.FT/TU/2025"
                               value="{{ old('nomor_surat', $surat->tugasSurat?->Nomor_Surat ?? '') }}"
                               required>
                        @error('nomor_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: [Nomor]/UN16.FT/TU/[Tahun]</small>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-bold" onclick="return confirm('Anda yakin nomor surat sudah benar dan akan meneruskan ke Dekan?')">
                            <i class="fas fa-paper-plane me-2"></i>TERUSKAN KE DEKAN
                        </button>
                    </div>
                </form>

                <div class="alert alert-warning mt-3 mb-0 small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Pastikan nomor surat belum digunakan sebelumnya.
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-clipboard-list me-2"></i>Status Surat
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Status Saat Ini:</small>
                    <br>
                    <span class="badge bg-info">
                        <i class="fas fa-cog me-1"></i> {{ $surat->Status }}
                    </span>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Nomor Surat:</small>
                    <br>
                    @if($surat->tugasSurat && $surat->tugasSurat->Nomor_Surat)
                        <span class="badge bg-success"><i class="fas fa-check"></i> {{ $surat->tugasSurat->Nomor_Surat }}</span>
                    @else
                        <span class="badge bg-secondary"><i class="fas fa-clock"></i> Belum Diberikan</span>
                    @endif
                </div>
                <div class="mb-2">
                    <small class="text-muted">Acc Koordinator:</small>
                    <br>
                    @if($surat->Acc_Koordinator)
                        <span class="badge bg-success"><i class="fas fa-check"></i> Disetujui</span>
                    @else
                        <span class="badge bg-secondary"><i class="fas fa-clock"></i> Belum</span>
                    @endif
                </div>
                <div>
                    <small class="text-muted">Acc Dekan:</small>
                    <br>
                    @if($surat->Acc_Dekan)
                        <span class="badge bg-success"><i class="fas fa-check"></i> Disetujui</span>
                    @else
                        <span class="badge bg-warning"><i class="fas fa-clock"></i> Menunggu</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePreviewSurat() {
    var preview = document.getElementById('previewSurat');
    if (preview.style.display === 'none') {
        preview.style.display = 'block';
        preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        preview.style.display = 'none';
    }
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #previewSurat, #previewSurat * {
        visibility: visible;
    }
    #previewSurat {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    #previewSurat .text-center.mt-3 {
        display: none !important;
    }
    #previewSurat.card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        display: none !important;
    }
}
</style>

@endsection