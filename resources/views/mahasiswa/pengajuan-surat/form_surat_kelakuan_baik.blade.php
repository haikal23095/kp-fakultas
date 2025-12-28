@extends('layouts.mahasiswa')

@section('title', 'Form Surat Keterangan Berkelakuan Baik')

@section('content')
<div class="container-fluid">
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Pengajuan Surat Keterangan</h1>
            <p class="text-muted small mb-0">Surat Keterangan Berkelakuan Baik</p>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulir Berkelakuan Baik</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('mahasiswa.pengajuan.kelakuan_baik.store') }}" method="POST">
                @csrf
                <input type="hidden" name="Id_Jenis_Surat" value="{{ $jenisSurat->Id_Jenis_Surat }}">

                {{-- Data Mahasiswa (Readonly) --}}
                <h5 class="mb-3 text-gray-800 border-bottom pb-2">Data Mahasiswa</h5>
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

                {{-- Data Pengajuan --}}
                <h5 class="mb-3 text-gray-800 border-bottom pb-2">Detail Pengajuan</h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Keperluan Surat <span class="text-danger">*</span>
                    </label>
                    <textarea name="keperluan" 
                              class="form-control @error('keperluan') is-invalid @enderror" 
                              rows="4" 
                              required 
                              placeholder="Contoh: Untuk syarat pendaftaran TNI Angkatan Darat, Untuk melamar pekerjaan di PT XYZ, dll">{{ old('keperluan') }}</textarea>
                    @error('keperluan') 
                        <div class="invalid-feedback">{{ $message }}</div> 
                    @enderror
                    <div class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Jelaskan keperluan penggunaan surat ini dengan detail. Maksimal 500 karakter.
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="alert alert-info border-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Informasi Penting</h6>
                            <ul class="mb-0 small">
                                <li>Surat ini menyatakan bahwa Anda tidak pernah melakukan pelanggaran akademik atau peraturan kampus.</li>
                                <li>Proses verifikasi dilakukan oleh Admin Fakultas (tidak perlu upload berkas).</li>
                                <li>Pengajuan akan diproses maksimal <strong>5 hari kerja</strong>.</li>
                                <li>Silakan cek status pengajuan di menu <strong>Riwayat Surat</strong>.</li>
                            </ul>
                        </div>
                    </div>
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
