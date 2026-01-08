@extends('layouts.mahasiswa')

@section('title', 'Form Peminjaman Ruang')

@push('styles')
<style>
    .form-section {
        background: #ffffff;
        border-radius: 10px;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    
    .form-section h5 {
        color: #4e73df;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e3e6f0;
    }
    
    .required-label::after {
        content: " *";
        color: #e74a3b;
    }
    
    .info-box {
        background: #d1ecf1;
        border-left: 4px solid #17a2b8;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 5px;
    }
    
    .info-box i {
        color: #17a2b8;
        margin-right: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Form Peminjaman Ruang</h1>
            <p class="text-muted mb-0">Isi formulir di bawah ini untuk mengajukan peminjaman ruang</p>
        </div>
        <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    {{-- Alert Error --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alert Session Error --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Box --}}
    <div class="info-box">
        <i class="fas fa-info-circle fa-lg"></i>
        <strong>Informasi Penting:</strong>
        <ul class="mb-0 mt-2">
            <li>Pastikan ruang yang Anda butuhkan tersedia pada tanggal yang diinginkan</li>
            <li>Upload proposal kegiatan dalam format PDF (maksimal 2MB)</li>
            <li>Pengajuan akan diverifikasi oleh admin maksimal 2x24 jam</li>
            <li>Admin akan menentukan ruang yang sesuai dengan kebutuhan Anda</li>
        </ul>
    </div>

    {{-- Form --}}
    <form action="{{ route('mahasiswa.pengajuan.ruang.store') }}" method="POST" enctype="multipart/form-data" id="formPeminjamanRuang">
        @csrf

        {{-- Informasi Kegiatan --}}
        <div class="form-section">
            <h5><i class="fas fa-calendar-alt me-2"></i>Informasi Kegiatan</h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_kegiatan" class="form-label required-label">Nama Kegiatan</label>
                    <input type="text" 
                           class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                           id="nama_kegiatan" 
                           name="nama_kegiatan" 
                           value="{{ old('nama_kegiatan') }}"
                           placeholder="Contoh: Rapat Koordinasi Panitia"
                           required>
                    @error('nama_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="penyelenggara" class="form-label required-label">Penyelenggara</label>
                    <input type="text" 
                           class="form-control @error('penyelenggara') is-invalid @enderror" 
                           id="penyelenggara" 
                           name="penyelenggara" 
                           value="{{ old('penyelenggara') }}"
                           placeholder="Contoh: HMTI / BEM Fakultas Teknik"
                           required>
                    @error('penyelenggara')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tanggal_mulai" class="form-label required-label">Tanggal & Waktu Mulai</label>
                    <input type="datetime-local" 
                           class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                           id="tanggal_mulai" 
                           name="tanggal_mulai" 
                           value="{{ old('tanggal_mulai') }}"
                           min="{{ date('Y-m-d\TH:i') }}"
                           required>
                    @error('tanggal_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tanggal_selesai" class="form-label required-label">Tanggal & Waktu Selesai</label>
                    <input type="datetime-local" 
                           class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                           id="tanggal_selesai" 
                           name="tanggal_selesai" 
                           value="{{ old('tanggal_selesai') }}"
                           min="{{ date('Y-m-d\TH:i') }}"
                           required>
                    @error('tanggal_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jumlah_peserta" class="form-label required-label">Jumlah Peserta</label>
                    <input type="number" 
                           class="form-control @error('jumlah_peserta') is-invalid @enderror" 
                           id="jumlah_peserta" 
                           name="jumlah_peserta" 
                           value="{{ old('jumlah_peserta') }}"
                           min="1"
                           placeholder="Masukkan jumlah peserta"
                           required>
                    @error('jumlah_peserta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Admin akan menentukan ruang berdasarkan kapasitas</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="file_lampiran" class="form-label required-label">Upload Proposal (PDF)</label>
                    <input type="file" 
                           class="form-control @error('file_lampiran') is-invalid @enderror" 
                           id="file_lampiran" 
                           name="file_lampiran"
                           accept=".pdf"
                           required>
                    @error('file_lampiran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Format: PDF, Maksimal 2MB</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan Tambahan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                          id="keterangan" 
                          name="keterangan" 
                          rows="3"
                          placeholder="Tambahkan keterangan jika diperlukan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="d-flex justify-content-between">
            <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Batal
            </a>
            <button type="submit" class="btn btn-primary" id="btnSubmit">
                <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('formPeminjamanRuang').addEventListener('submit', function(e) {
        const btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    });

    // Validasi tanggal selesai harus >= tanggal mulai
    document.getElementById('tanggal_mulai').addEventListener('change', function() {
        const tanggalMulai = this.value;
        document.getElementById('tanggal_selesai').min = tanggalMulai;
    });

    // Preview file
    document.getElementById('file_lampiran').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2048000) {
                alert('Ukuran file terlalu besar! Maksimal 2MB');
                this.value = '';
            }
        }
    });
</script>
@endpush
