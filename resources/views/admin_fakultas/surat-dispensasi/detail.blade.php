@extends('layouts.admin_fakultas')

@section('title', 'Detail Surat Dispensasi')

@push('styles')
<style>
    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .info-value {
        font-size: 1rem;
        color: #2c3e50;
    }
    .section-title {
        font-weight: 700;
        color: #4e73df;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .preview-box {
        border: 2px dashed #e3e6f0;
        border-radius: 12px;
        padding: 2rem;
        background: #f8f9fc;
        text-align: center;
    }
    .nomor-surat-preview {
        font-size: 1.5rem;
        font-weight: 700;
        color: #4e73df;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border: 2px solid #4e73df;
        margin-top: 1rem;
        letter-spacing: 1px;
    }
    .file-preview-btn {
        transition: all 0.3s ease;
    }
    .file-preview-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">Detail Surat Dispensasi</h1>
        <p class="text-muted small">Verifikasi berkas dan berikan nomor surat</p>
    </div>
    <a href="{{ route('admin_fakultas.surat.dispensasi') }}" class="btn btn-secondary btn-icon-split shadow-sm">
        <span class="icon text-white-50">
            <i class="fas fa-arrow-left"></i>
        </span>
        <span class="text">Kembali</span>
    </a>
</div>

{{-- Alert Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    {{-- Left Column: Data Mahasiswa & Dispensasi --}}
    <div class="col-lg-8 mb-4">
        
        {{-- Data Mahasiswa --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-user-graduate me-2"></i>Data Mahasiswa
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value">{{ $mahasiswa->Nama_Mahasiswa }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="info-label">NIM</div>
                        <div class="info-value">{{ $mahasiswa->NIM }}</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="info-label">Angkatan</div>
                        <div class="info-value">{{ $mahasiswa->Angkatan ?? '-' }}</div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="info-label">Program Studi</div>
                        <div class="info-value">{{ $mahasiswa->prodi->Nama_Prodi ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Dispensasi --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-calendar-alt me-2"></i>Detail Dispensasi
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="info-label">Nama Kegiatan / Alasan</div>
                        <div class="info-value fw-bold text-dark">{{ $surat->nama_kegiatan }}</div>
                    </div>
                    
                    @if($surat->instansi_penyelenggara)
                    <div class="col-md-12 mb-3">
                        <div class="info-label">Instansi Penyelenggara</div>
                        <div class="info-value">{{ $surat->instansi_penyelenggara }}</div>
                    </div>
                    @endif

                    @if($surat->tempat_pelaksanaan)
                    <div class="col-md-12 mb-3">
                        <div class="info-label">Tempat Pelaksanaan</div>
                        <div class="info-value">{{ $surat->tempat_pelaksanaan }}</div>
                    </div>
                    @endif

                    <div class="col-md-6 mb-3">
                        <div class="info-label">Tanggal Mulai</div>
                        <div class="info-value">
                            <i class="fas fa-calendar-check text-success me-2"></i>
                            {{ Carbon\Carbon::parse($surat->tanggal_mulai)->translatedFormat('d F Y') }}
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="info-label">Tanggal Selesai</div>
                        <div class="info-value">
                            <i class="fas fa-calendar-times text-danger me-2"></i>
                            {{ Carbon\Carbon::parse($surat->tanggal_selesai)->translatedFormat('d F Y') }}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Durasi Dispensasi:</strong>
                            {{ Carbon\Carbon::parse($surat->tanggal_mulai)->diffInDays(Carbon\Carbon::parse($surat->tanggal_selesai)) + 1 }} hari
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Berkas Pendukung --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-paperclip me-2"></i>Berkas Pendukung
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="info-label mb-2">
                            <i class="fas fa-file-image text-primary me-2"></i>Bukti Pendukung
                        </div>
                        @if($surat->file_lampiran)
                            @php
                                $fileExists = Storage::disk('public')->exists($surat->file_lampiran);
                            @endphp
                            
                            @if($fileExists)
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-info btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#previewLampiranModal">
                                        <i class="fas fa-eye me-2"></i>Preview Bukti Pendukung
                                    </button>
                                    <a href="{{ route('admin_fakultas.surat.dispensasi.download_lampiran', $surat->id) }}" 
                                       class="btn btn-primary btn-sm" target="_blank">
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-danger small mb-0">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>File hilang!</strong> Data tercatat di database tapi file tidak ditemukan di server.
                                    <br><small class="text-muted">Path: {{ $surat->file_lampiran }}</small>
                                </div>
                            @endif
                        @else
                            <p class="text-muted small">Tidak ada file</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview Surat --}}
        <div class="card shadow mb-4 border-left-info">
            <div class="card-header py-3 bg-gradient-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-eye me-2"></i>Preview Surat Dispensasi
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Preview surat yang akan digenerate. Klik tombol di bawah untuk melihat tampilan lengkap dengan logo dan stempel.
                </p>
                <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#previewSuratModal">
                    <i class="fas fa-file-alt me-2"></i>Lihat Preview Surat Lengkap
                </button>
            </div>
        </div>

    </div>

    {{-- Right Column: Form Nomor Surat & Preview --}}
    <div class="col-lg-4">
        
        {{-- Status Card --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Status Surat
                </h6>
            </div>
            <div class="card-body text-center">
                @php
                    $status = strtolower($surat->tugasSurat->Status ?? 'baru');
                @endphp
                
                @if($status === 'baru')
                    <span class="badge bg-warning text-dark p-3 fs-6">
                        <i class="fas fa-clock me-2"></i>Menunggu Verifikasi
                    </span>
                @elseif(in_array($status, ['proses', 'dikerjakan-admin']))
                    <span class="badge bg-primary p-3 fs-6">
                        <i class="fas fa-spinner fa-spin me-2"></i>Dalam Proses
                    </span>
                @elseif($status === 'diajukan-ke-wadek3')
                    <span class="badge bg-info p-3 fs-6">
                        <i class="fas fa-paper-plane me-2"></i>Diteruskan ke Wadek3
                    </span>
                @else
                    <span class="badge bg-secondary p-3 fs-6">{{ ucfirst($status) }}</span>
                @endif

                @if($surat->nomor_surat)
                    <div class="mt-3 pt-3 border-top">
                        <div class="text-muted small mb-2">Nomor Surat</div>
                        <div class="fw-bold text-dark">{{ $surat->nomor_surat }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Form Assign Nomor Surat --}}
        @if(!$surat->nomor_surat)
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3 bg-gradient-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-hashtag me-2"></i>Berikan Nomor Surat
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin_fakultas.surat.dispensasi.assign_nomor', $surat->id) }}" method="POST" id="formNomorSurat">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nomor_surat" class="form-label fw-bold">
                            Nomor Surat <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('nomor_surat') is-invalid @enderror" 
                               id="nomor_surat" 
                               name="nomor_surat" 
                               placeholder="Contoh: 001/FT-DISPEN/I/2026"
                               required
                               autocomplete="off">
                        @error('nomor_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Format: XXX/FT-DISPEN/Bulan/Tahun
                        </div>
                    </div>

                    {{-- Live Preview --}}
                    <div class="preview-box" id="previewBox" style="display: none;">
                        <div class="text-muted small mb-2">
                            <i class="fas fa-eye me-1"></i>Preview Nomor Surat
                        </div>
                        <div class="nomor-surat-preview" id="nomorSuratPreview">
                            -
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>
                            <strong>Perhatian:</strong> Setelah nomor surat diberikan, PDF akan otomatis digenerate dengan stempel dan surat akan diteruskan ke Wadek 3.
                        </small>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check-circle me-2"></i>Verifikasi & Generate PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        {{-- PDF sudah digenerate --}}
        <div class="card shadow mb-4 border-left-success">
            <div class="card-header py-3 bg-gradient-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-pdf me-2"></i>Surat PDF
                </h6>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-file-pdf fa-4x text-success mb-3"></i>
                <p class="text-muted">PDF surat telah digenerate dengan stempel</p>
                <a href="{{ route('admin_fakultas.surat.dispensasi.download_pdf', $surat->id) }}" 
                   class="btn btn-success w-100" target="_blank">
                    <i class="fas fa-download me-2"></i>Download/Preview PDF
                </a>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Modal Preview Surat --}}
<div class="modal fade" id="previewSuratModal" tabindex="-1" aria-labelledby="previewSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewSuratModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Preview Surat Dispensasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #f8f9fc;">
                
                {{-- Preview Surat dalam Box Putih (Mirip Kertas) --}}
                <div class="surat-preview-container" style="background: white; padding: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
                    
                    {{-- Header Surat --}}
                    <div class="surat-header" style="text-align: center; border-bottom: 3px double #000; padding-bottom: 15px; margin-bottom: 20px; position: relative;">
                        <img src="{{ asset('images/logo_unijoyo.png') }}" 
                             alt="Logo UTM" 
                             style="position: absolute; left: 0; top: 0; width: 80px; height: auto;">
                        
                        <div style="margin-left: 100px;">
                            <h4 style="font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase;">
                                KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI
                            </h4>
                            <h3 style="font-size: 15pt; font-weight: bold; margin: 5px 0; text-transform: uppercase;">
                                UNIVERSITAS TRUNOJOYO MADURA
                            </h3>
                            <h3 style="font-size: 15pt; font-weight: bold; margin: 5px 0; text-transform: uppercase;">
                                FAKULTAS TEKNIK
                            </h3>
                            <p style="font-size: 9pt; margin: 5px 0; font-style: italic;">
                                Sekretariat: Kampus Unijoyo PO Box 2 Telang Kamal Telp 031 7011147 Fax. 031 7011506
                            </p>
                        </div>
                    </div>

                    {{-- Nomor Surat --}}
                    <div class="surat-nomor" style="text-align: center; margin: 25px 0;">
                        <span style="font-weight: bold; font-size: 12pt;">
                            Nomor: <span id="preview-nomor-display" style="color: #4e73df;">{{ $surat->nomor_surat ?? '[Akan diisi setelah assign nomor]' }}</span>
                        </span>
                    </div>

                    {{-- Judul Surat --}}
                    <div class="surat-judul" style="text-align: center; font-weight: bold; text-decoration: underline; font-size: 14pt; margin: 20px 0; text-transform: uppercase;">
                        SURAT DISPENSASI
                    </div>

                    {{-- Isi Pembuka --}}
                    <div class="surat-isi" style="text-align: justify; line-height: 1.8; font-size: 11pt;">
                        <p style="margin: 15px 0;">
                            Yang bertanda tangan di bawah ini Wakil Dekan III Fakultas Teknik Universitas Trunojoyo Madura, menerangkan bahwa:
                        </p>
                    </div>

                    {{-- Data Mahasiswa --}}
                    <table style="width: 100%; margin: 20px 0; font-size: 11pt;">
                        <tr>
                            <td style="width: 30%; padding: 5px 0; vertical-align: top;">Nama</td>
                            <td style="width: 2%; padding: 5px 0; vertical-align: top;">:</td>
                            <td style="width: 68%; padding: 5px 0; vertical-align: top;"><strong>{{ $mahasiswa->Nama_Mahasiswa }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0; vertical-align: top;">NIM</td>
                            <td style="padding: 5px 0; vertical-align: top;">:</td>
                            <td style="padding: 5px 0; vertical-align: top;"><strong>{{ $mahasiswa->NIM }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0; vertical-align: top;">Program Studi</td>
                            <td style="padding: 5px 0; vertical-align: top;">:</td>
                            <td style="padding: 5px 0; vertical-align: top;">{{ $mahasiswa->prodi->Nama_Prodi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0; vertical-align: top;">Angkatan</td>
                            <td style="padding: 5px 0; vertical-align: top;">:</td>
                            <td style="padding: 5px 0; vertical-align: top;">{{ $mahasiswa->Angkatan ?? '-' }}</td>
                        </tr>
                    </table>

                    {{-- Isi Dispensasi --}}
                    <div class="surat-isi" style="text-align: justify; line-height: 1.8; font-size: 11pt;">
                        <p style="margin: 15px 0;">
                            Adalah benar mahasiswa tersebut di atas telah mengajukan permohonan <strong>dispensasi kehadiran kuliah</strong> 
                            pada tanggal <strong>{{ Carbon\Carbon::parse($surat->tanggal_mulai)->translatedFormat('d F Y') }}</strong> 
                            sampai dengan <strong>{{ Carbon\Carbon::parse($surat->tanggal_selesai)->translatedFormat('d F Y') }}</strong> 
                            dengan alasan/keperluan:
                        </p>
                        
                        <p style="margin: 15px 0 15px 40px;">
                            <strong>"{{ $surat->nama_kegiatan }}"</strong>
                        </p>

                        @if($surat->instansi_penyelenggara && $surat->instansi_penyelenggara !== '-')
                        <p style="margin: 15px 0;">
                            yang diselenggarakan oleh <strong>{{ $surat->instansi_penyelenggara }}</strong>
                            @if($surat->tempat_pelaksanaan && $surat->tempat_pelaksanaan !== '-')
                                di <strong>{{ $surat->tempat_pelaksanaan }}</strong>
                            @endif.
                        </p>
                        @endif

                        <p style="margin: 15px 0;">
                            Surat dispensasi ini diberikan untuk digunakan sebagaimana mestinya. Demikian surat ini dibuat dengan 
                            sebenarnya agar dapat dipergunakan sebagaimana mestinya.
                        </p>
                    </div>

                    {{-- Tanda Tangan & Stempel --}}
                    <div class="surat-ttd" style="margin-top: 40px; position: relative;">
                        <div style="float: right; width: 45%; text-align: center;">
                            <p style="margin: 0;">Bangkalan, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                            <p style="margin: 5px 0; font-weight: bold;">Wakil Dekan III</p>
                            
                            {{-- Stempel Preview --}}
                            <div style="position: relative; height: 100px; margin: 20px 0;">
                                <img src="{{ asset('images/stempel.png') }}" 
                                     alt="Stempel" 
                                     style="position: absolute; right: -20px; top: 0; width: 140px; opacity: 0.85;">
                            </div>
                            
                            <p style="margin: 10px 0; font-size: 10pt; font-style: italic; color: #666;">
                                ( TTD akan ditambahkan oleh Wadek 3 )
                            </p>
                            <p style="margin: 10px 0; font-weight: bold; text-decoration: underline;">
                                _____________________
                            </p>
                            <p style="margin: 0; font-size: 10pt;">
                                NIP. __________________
                            </p>
                        </div>
                        <div style="clear: both;"></div>
                    </div>

                    {{-- Footer Note --}}
                    <div style="margin-top: 60px; font-size: 9pt; font-style: italic; color: #666;">
                        <p><em>Catatan: Surat ini dicetak secara otomatis melalui Sistem Manajemen Surat Fakultas Teknik UTM</em></p>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Cetak Preview
                </button>
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
                <a href="{{ route('admin_fakultas.surat.dispensasi.download_lampiran', $surat->id) }}" 
                   class="btn btn-primary" target="_blank">
                    <i class="fas fa-download me-2"></i>Download File
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    // Live Preview Nomor Surat
    document.getElementById('nomor_surat').addEventListener('input', function() {
        const value = this.value.trim();
        const previewBox = document.getElementById('previewBox');
        const previewText = document.getElementById('nomorSuratPreview');
        const previewModal = document.getElementById('preview-nomor-display');
        
        if (value.length > 0) {
            previewBox.style.display = 'block';
            previewText.textContent = value;
            // Update nomor di modal preview juga
            previewModal.textContent = value;
            previewModal.style.color = '#4e73df';
        } else {
            previewBox.style.display = 'none';
            previewText.textContent = '-';
            previewModal.textContent = '[Akan diisi setelah assign nomor]';
            previewModal.style.color = '#999';
        }
    });

    // Konfirmasi sebelum submit
    document.getElementById('formNomorSurat').addEventListener('submit', function(e) {
        const nomorSurat = document.getElementById('nomor_surat').value.trim();
        
        if (!confirm(`Apakah Anda yakin ingin memberikan nomor surat:\n\n${nomorSurat}\n\nPDF akan otomatis digenerate dan surat akan diteruskan ke Wadek 3.`)) {
            e.preventDefault();
        }
    });
</script>
@endpush
