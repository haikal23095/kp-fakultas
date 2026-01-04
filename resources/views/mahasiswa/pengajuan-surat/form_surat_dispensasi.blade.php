@extends('layouts.mahasiswa')

@section('title', 'Pengajuan Surat Dispensasi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Surat Dispensasi</h1>
    <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan Surat Dispensasi</h6>
    </div>
    <div class="card-body">
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

        <form action="{{ route('mahasiswa.pengajuan.dispen.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="nama_kegiatan" class="form-label">Nama Kegiatan / Alasan <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                       id="nama_kegiatan" name="nama_kegiatan" 
                       value="{{ old('nama_kegiatan') }}" 
                       placeholder="Contoh: Sakit Demam, Lomba Web Development, dll"
                       required>
                @error('nama_kegiatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="instansi_penyelenggara" class="form-label">Instansi Penyelenggara</label>
                <input type="text" class="form-control @error('instansi_penyelenggara') is-invalid @enderror" 
                       id="instansi_penyelenggara" name="instansi_penyelenggara" 
                       value="{{ old('instansi_penyelenggara') }}"
                       placeholder="Contoh: HMTI, BEM Fakultas, dll">
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Kosongkan jika sakit/keperluan pribadi
                </div>
                @error('instansi_penyelenggara')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tempat_pelaksanaan" class="form-label">Tempat Pelaksanaan</label>
                <input type="text" class="form-control @error('tempat_pelaksanaan') is-invalid @enderror" 
                       id="tempat_pelaksanaan" name="tempat_pelaksanaan" 
                       value="{{ old('tempat_pelaksanaan') }}"
                       placeholder="Contoh: Rumah, Aula Kampus, Hotel XYZ, dll">
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Opsional - Kosongkan jika tidak relevan
                </div>
                @error('tempat_pelaksanaan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                           id="tanggal_mulai" name="tanggal_mulai" 
                           value="{{ old('tanggal_mulai') }}" 
                           required>
                    @error('tanggal_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                           id="tanggal_selesai" name="tanggal_selesai" 
                           value="{{ old('tanggal_selesai') }}" 
                           required>
                    @error('tanggal_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="file_permohonan" class="form-label">Upload Surat Permohonan (PDF) <span class="text-danger">*</span></label>
                <input type="file" class="form-control @error('file_permohonan') is-invalid @enderror" 
                       id="file_permohonan" name="file_permohonan" 
                       accept="application/pdf" 
                       required>
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Upload <strong>Surat Permohonan Resmi</strong> dalam format PDF (Max: 2MB)
                </div>
                @error('file_permohonan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="file_lampiran" class="form-label">Upload Bukti Pendukung (PDF/Gambar)</label>
                <input type="file" class="form-control @error('file_lampiran') is-invalid @enderror" 
                       id="file_lampiran" name="file_lampiran" 
                       accept="application/pdf,image/jpeg,image/jpg,image/png">
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Upload <strong>Undangan</strong> atau <strong>Surat Dokter</strong> jika ada (Opsional, Max: 2MB)
                </div>
                @error('file_lampiran')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Perhatian:</strong> Pastikan semua data yang Anda isi sudah benar sebelum mengajukan. Pengajuan akan diproses oleh Admin dan Wakil Dekan 3.
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Ajukan Surat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validasi tanggal selesai tidak boleh lebih kecil dari tanggal mulai
    document.getElementById('tanggal_selesai').addEventListener('change', function() {
        const tanggalMulai = document.getElementById('tanggal_mulai').value;
        const tanggalSelesai = this.value;
        
        if (tanggalMulai && tanggalSelesai && tanggalSelesai < tanggalMulai) {
            alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');
            this.value = '';
        }
    });

    // Set minimum date untuk tanggal mulai (hari ini)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_mulai').setAttribute('min', today);
    
    // Update minimum tanggal selesai berdasarkan tanggal mulai
    document.getElementById('tanggal_mulai').addEventListener('change', function() {
        document.getElementById('tanggal_selesai').setAttribute('min', this.value);
    });
</script>
@endpush
