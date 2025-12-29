@extends('layouts.mahasiswa')

@section('title', 'Form Izin Kegiatan Malam')

@section('content')
<div class="container-fluid">
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Pengajuan Izin Kegiatan Malam</h1>
            <p class="text-muted small mb-0">Izin untuk berkegiatan di kampus di luar jam operasional</p>
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
            <h6 class="m-0 font-weight-bold text-primary">Formulir Izin Kegiatan Malam</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('mahasiswa.pengajuan.izin_malam.store') }}" method="POST">
                @csrf
                <input type="hidden" name="Id_Jenis_Surat" value="{{ $jenisSurat->Id_Jenis_Surat }}">

                {{-- Data Mahasiswa (Readonly) --}}
                <h5 class="mb-3 text-gray-800 border-bottom pb-2">Data Pemohon</h5>
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

                {{-- Data Kegiatan --}}
                <h5 class="mb-3 text-gray-800 border-bottom pb-2">Detail Kegiatan</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Nama Kegiatan <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="nama_kegiatan" 
                           class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                           value="{{ old('nama_kegiatan') }}"
                           required 
                           placeholder="Contoh: Rapat Koordinasi BEM, Latihan Kesenian, dll">
                    @error('nama_kegiatan') 
                        <div class="invalid-feedback">{{ $message }}</div> 
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            Waktu Mulai <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="waktu_mulai" 
                               class="form-control @error('waktu_mulai') is-invalid @enderror" 
                               value="{{ old('waktu_mulai') }}"
                               required>
                        @error('waktu_mulai') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            Waktu Selesai <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local" 
                               name="waktu_selesai" 
                               class="form-control @error('waktu_selesai') is-invalid @enderror" 
                               value="{{ old('waktu_selesai') }}"
                               required>
                        @error('waktu_selesai') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">
                            Lokasi Kegiatan <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="lokasi_kegiatan" 
                               class="form-control @error('lokasi_kegiatan') is-invalid @enderror" 
                               value="{{ old('lokasi_kegiatan') }}"
                               required 
                               placeholder="Contoh: Aula Lantai 3, Ruang Seminar, Lab Komputer">
                        @error('lokasi_kegiatan') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            Jumlah Peserta <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               name="jumlah_peserta" 
                               class="form-control @error('jumlah_peserta') is-invalid @enderror" 
                               value="{{ old('jumlah_peserta') }}"
                               min="1"
                               required 
                               placeholder="0">
                        @error('jumlah_peserta') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Alasan/Keperluan Kegiatan <span class="text-danger">*</span>
                    </label>
                    <textarea name="alasan" 
                              class="form-control @error('alasan') is-invalid @enderror" 
                              rows="5" 
                              required 
                              placeholder="Jelaskan secara detail mengapa kegiatan ini perlu dilaksanakan di malam hari dan manfaat yang akan diperoleh">{{ old('alasan') }}</textarea>
                    @error('alasan') 
                        <div class="invalid-feedback">{{ $message }}</div> 
                    @enderror
                    <div class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Jelaskan alasan mengapa kegiatan ini harus dilaksanakan di luar jam operasional kampus.
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="alert alert-info border-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-2">Informasi Penting</h6>
                            <ul class="mb-0 small">
                                <li>Izin kegiatan malam diperlukan untuk kegiatan yang dilaksanakan di luar jam operasional kampus (setelah pukul 21.00 atau hari libur).</li>
                                <li>Pastikan semua data yang diisi sudah benar dan sesuai dengan jadwal kegiatan.</li>
                                <li>Pengajuan akan diproses maksimal <strong>5 hari kerja</strong>.</li>
                                <li>Mahasiswa wajib menjaga kebersihan dan keamanan lokasi kegiatan.</li>
                                <li>Surat izin harus sudah disetujui <strong>minimal 3 hari sebelum</strong> kegiatan dilaksanakan.</li>
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

@push('scripts')
<script>
    // Validasi waktu mulai dan selesai
    document.querySelector('input[name="waktu_mulai"]').addEventListener('change', function() {
        const waktuMulai = new Date(this.value);
        const waktuSelesaiInput = document.querySelector('input[name="waktu_selesai"]');
        
        if (waktuSelesaiInput.value) {
            const waktuSelesai = new Date(waktuSelesaiInput.value);
            if (waktuSelesai <= waktuMulai) {
                alert('Waktu selesai harus setelah waktu mulai!');
                waktuSelesaiInput.value = '';
            }
        }
    });

    document.querySelector('input[name="waktu_selesai"]').addEventListener('change', function() {
        const waktuSelesai = new Date(this.value);
        const waktuMulaiInput = document.querySelector('input[name="waktu_mulai"]');
        
        if (waktuMulaiInput.value) {
            const waktuMulai = new Date(waktuMulaiInput.value);
            if (waktuSelesai <= waktuMulai) {
                alert('Waktu selesai harus setelah waktu mulai!');
                this.value = '';
            }
        }
    });
</script>
@endpush
@endsection
