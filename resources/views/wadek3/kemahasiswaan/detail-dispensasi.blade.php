@extends('layouts.wadek3')

@section('title', 'Detail Surat Dispensasi')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('wadek3.kemahasiswaan.validasi-dispensasi') }}">Validasi Dispensasi</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </nav>
    <h1 class="h2 fw-light mb-2">Detail Surat Dispensasi</h1>
</div>

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    {{-- Data Mahasiswa --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Data Mahasiswa</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="100">Nama</td>
                        <td class="fw-bold">{{ $tugasSurat->pemberiTugas->Name_User ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIM</td>
                        <td class="fw-bold">{{ $surat->user->mahasiswa->NIM ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Prodi</td>
                        <td>{{ $surat->user->mahasiswa->prodi->Nama_Prodi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Angkatan</td>
                        <td>{{ $surat->user->mahasiswa->Angkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $surat->user->mahasiswa->Email ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Data Kegiatan --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Data Kegiatan</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="100">Kegiatan</td>
                        <td class="fw-bold">{{ $surat->nama_kegiatan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Instansi</td>
                        <td>{{ $surat->instansi_penyelenggara ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tempat</td>
                        <td>{{ $surat->tempat_pelaksanaan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }}</strong> s/d
                            <strong>{{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Berkas --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-file-pdf me-2"></i>Berkas Pendukung</h6>
            </div>
            <div class="card-body">
                @if($surat->file_lampiran)
                    @php
                        $fileExists = Storage::disk('public')->exists($surat->file_lampiran);
                    @endphp
                    
                    @if($fileExists)
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-info btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#previewLampiranModal">
                                <i class="fas fa-eye me-1"></i>Preview Bukti Pendukung
                            </button>
                            <a href="{{ route('wadek3.kemahasiswaan.download-lampiran', $surat->id) }}" 
                               class="btn btn-outline-primary btn-sm" target="_blank">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                    @else
                        <div class="alert alert-danger small mb-0">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>File hilang!</strong> Data tercatat tapi file tidak ada di server.
                            <br><small class="text-muted">Path: {{ $surat->file_lampiran }}</small>
                        </div>
                    @endif
                @else
                    <p class="text-muted mb-0 text-center">
                        <i class="fas fa-info-circle me-2"></i>Tidak ada lampiran
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Preview Surat & Form ACC --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2"></i>Preview Surat Dispensasi</h6>
            </div>
            <div class="card-body">
                {{-- Preview Box --}}
                <div class="border rounded p-4 mb-4 bg-light" style="min-height: 400px;">
                    <div class="text-center mb-3">
                        <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo" style="width: 80px;">
                        <h6 class="mt-2 mb-0 fw-bold">UNIVERSITAS TRUNOJOYO MADURA</h6>
                        <p class="small mb-0">Fakultas Teknik</p>
                    </div>
                    <hr>
                    <div class="text-center mb-4">
                        <h6 class="fw-bold text-decoration-underline">SURAT DISPENSASI</h6>
                        @if($surat->nomor_surat)
                            <p class="mb-0">Nomor: <strong>{{ $surat->nomor_surat }}</strong></p>
                        @else
                            <p class="mb-0 text-muted">Nomor: <em>Belum ada nomor</em></p>
                        @endif
                    </div>

                    <p class="mb-3">Yang bertanda tangan di bawah ini, Wakil Dekan III Bidang Kemahasiswaan Fakultas Teknik Universitas Trunojoyo Madura, memberikan dispensasi kepada:</p>

                    <table class="table table-borderless table-sm mb-3" style="width: 100%;">
                        <tr>
                            <td width="150">Nama</td>
                            <td width="20">:</td>
                            <td><strong>{{ $surat->user->Name_User ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>NIM</td>
                            <td>:</td>
                            <td><strong>{{ $surat->user->mahasiswa->NIM ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Program Studi</td>
                            <td>:</td>
                            <td>{{ $surat->user->mahasiswa->prodi->Nama_Prodi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Keperluan</td>
                            <td>:</td>
                            <td><strong class="text-primary">{{ $surat->nama_kegiatan }}</strong></td>
                        </tr>
                        <tr>
                            <td>Penyelenggara</td>
                            <td>:</td>
                            <td>{{ $surat->instansi_penyelenggara ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tempat</td>
                            <td>:</td>
                            <td>{{ $surat->tempat_pelaksanaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($surat->tanggal_mulai)->translatedFormat('d F Y') }}</strong> s/d
                                <strong>{{ \Carbon\Carbon::parse($surat->tanggal_selesai)->translatedFormat('d F Y') }}</strong>
                            </td>
                        </tr>
                    </table>

                    <p class="mb-4">Demikian surat dispensasi ini dibuat untuk dapat digunakan sebagaimana mestinya.</p>

                    <div class="text-end mt-5">
                        <p class="mb-1">Bangkalan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                        <p class="mb-4">Wakil Dekan III</p>
                        <div style="height: 80px;" class="mb-2">
                            <div class="border border-dashed rounded p-2 d-inline-block">
                                <small class="text-muted">QR Code akan muncul<br>setelah ACC</small>
                            </div>
                        </div>
                        <p class="mb-0 fw-bold text-decoration-underline">{{ Auth::user()->Name_User }}</p>
                        <p class="mb-0">NIP. {{ Auth::user()->dosen->NIP ?? Auth::user()->pegawaiFakultas->NIP ?? '-' }}</p>
                    </div>
                </div>

                {{-- Form ACC --}}
                @if(!$surat->acc_wadek3_by)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian!</strong> Setelah ACC, sistem akan:
                        <ul class="mb-0 mt-2">
                            <li>Generate QR Code untuk tanda tangan digital</li>
                            <li>Generate PDF surat dengan QR Code</li>
                            <li>Mengubah status menjadi "Selesai"</li>
                            <li>Mengirim notifikasi ke mahasiswa</li>
                        </ul>
                    </div>

                    <form action="{{ route('wadek3.kemahasiswaan.approve-dispensasi', $tugasSurat->Id_Tugas_Surat) }}" 
                          method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menyetujui surat ini? QR Code dan PDF akan digenerate otomatis.')">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle me-2"></i>ACC & GENERATE QR CODE + PDF
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Surat telah disetujui</strong> pada {{ \Carbon\Carbon::parse($surat->acc_wadek3_at)->format('d M Y H:i') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Preview Lampiran --}}
@if($surat->file_lampiran && Storage::disk('public')->exists($surat->file_lampiran))
<div class="modal fade" id="previewLampiranModal" tabindex="-1" aria-labelledby="previewLampiranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewLampiranModalLabel">
                    <i class="fas fa-file me-2"></i>Preview Bukti Pendukung
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center" style="background: #f8f9fc;">
                @php
                    $extension = pathinfo($surat->file_lampiran, PATHINFO_EXTENSION);
                    $fileUrl = asset('storage/' . $surat->file_lampiran);
                @endphp
                
                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ $fileUrl }}" alt="Bukti Pendukung" class="img-fluid" style="max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                @elseif(strtolower($extension) === 'pdf')
                    <embed src="{{ $fileUrl }}" type="application/pdf" width="100%" height="600px" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Preview tidak tersedia untuk tipe file ini. Silakan download untuk melihat.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <a href="{{ route('wadek3.kemahasiswaan.download-lampiran', $surat->id) }}" 
                   class="btn btn-primary" target="_blank">
                    <i class="fas fa-download me-2"></i>Download File
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
