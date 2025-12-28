@extends('layouts.mahasiswa')

@section('title', 'Form Surat Keterangan Tidak Menerima Beasiswa')

@section('content')
<div class="container-fluid">
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Pengajuan Surat Keterangan</h1>
            <p class="text-muted small mb-0">Surat Keterangan Tidak Menerima Beasiswa</p>
        </div>
        <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Main Form Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-file-alt me-2"></i>Formulir Surat Keterangan Tidak Menerima Beasiswa
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('mahasiswa.pengajuan.tidak_beasiswa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="Id_Jenis_Surat" value="{{ $jenisSurat->Id_Jenis_Surat }}">

                {{-- Data Mahasiswa (Readonly) --}}
                <h5 class="mb-3 text-gray-800 border-bottom pb-2">
                    <i class="fas fa-user-graduate me-2"></i>Data Mahasiswa
                </h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control bg-light" value="{{ $mahasiswa->Nama_Mahasiswa }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIM</label>
                        <input type="text" class="form-control bg-light" value="{{ $mahasiswa->NIM }}" readonly>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Program Studi</label>
                        <input type="text" class="form-control bg-light" value="{{ $mahasiswa->prodi->Nama_Prodi ?? '-' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Angkatan</label>
                        <input type="text" class="form-control bg-light" value="{{ $mahasiswa->Angkatan ?? '-' }}" readonly>
                    </div>
                </div>

                {{-- Data Orang Tua --}}
                <h5 class="mb-3 text-gray-800 border-bottom pb-2">
                    <i class="fas fa-users me-2"></i>Data Orang Tua
                </h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            Nama Orang Tua <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="nama_orang_tua" 
                               class="form-control @error('nama_orang_tua') is-invalid @enderror" 
                               value="{{ old('nama_orang_tua') }}" 
                               required 
                               placeholder="Nama Ayah/Ibu/Wali">
                        @error('nama_orang_tua') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            Pekerjaan Orang Tua <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="pekerjaan_orang_tua" 
                               class="form-control @error('pekerjaan_orang_tua') is-invalid @enderror" 
                               value="{{ old('pekerjaan_orang_tua') }}" 
                               required 
                               placeholder="PNS, Wiraswasta, Petani, dll">
                        @error('pekerjaan_orang_tua') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            Pendapatan Orang Tua Per Bulan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   name="pendapatan_orang_tua" 
                                   class="form-control @error('pendapatan_orang_tua') is-invalid @enderror" 
                                   value="{{ old('pendapatan_orang_tua') }}" 
                                   required 
                                   min="0"
                                   step="1000"
                                   placeholder="Contoh: 2500000">
                            @error('pendapatan_orang_tua') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>
                        <div class="form-text text-muted">
                            <small>Perkiraan pendapatan total orang tua per bulan</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIP Orang Tua (Opsional)</label>
                        <input type="text" 
                               name="nip_orang_tua" 
                               class="form-control" 
                               value="{{ old('nip_orang_tua') }}" 
                               placeholder="Isi jika orang tua PNS/ASN">
                        <div class="form-text text-muted">
                            <small>Kosongkan jika orang tua bukan PNS/ASN</small>
                        </div>
                    </div>
                </div>

                {{-- Data Pengajuan --}}
                <h5 class="mb-3 text-gray-800 border-bottom pb-2">
                    <i class="fas fa-clipboard-list me-2"></i>Detail Pengajuan
                </h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Keperluan Surat <span class="text-danger">*</span>
                    </label>
                    <textarea name="keperluan" 
                              class="form-control @error('keperluan') is-invalid @enderror" 
                              rows="3" 
                              required 
                              placeholder="Contoh: Persyaratan Beasiswa Bank Indonesia, Syarat Pendaftaran Beasiswa LPDP, dll">{{ old('keperluan') }}</textarea>
                    @error('keperluan') 
                        <div class="invalid-feedback">{{ $message }}</div> 
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">
                        Upload Surat Pernyataan <span class="text-danger">*</span>
                    </label>
                    <input type="file" 
                           name="file_pernyataan" 
                           class="form-control @error('file_pernyataan') is-invalid @enderror" 
                           accept=".pdf" 
                           required>
                    <div class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Format:</strong> PDF | <strong>Maksimal:</strong> 2MB<br>
                        <small class="text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Surat pernyataan bermaterai bahwa tidak sedang menerima beasiswa lain dari pihak manapun.
                        </small>
                    </div>
                    @error('file_pernyataan') 
                        <div class="invalid-feedback">{{ $message }}</div> 
                    @enderror
                </div>

                {{-- Info Box --}}
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Catatan Penting:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Pastikan surat pernyataan sudah ditandatangani dan bermaterai.</li>
                        <li>Pengajuan akan diproses maksimal 3 hari kerja.</li>
                        <li>Silakan cek status pengajuan di menu <strong>Riwayat Surat</strong>.</li>
                    </ul>
                </div>

                <hr class="my-4">

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
