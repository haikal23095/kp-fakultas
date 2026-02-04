@extends('layouts.mahasiswa')

@section('title', 'Pengajuan Peminjaman Mobil Dinas')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Peminjaman Mobil Dinas</h1>
    <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan Peminjaman Mobil Dinas</h6>
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

        <form action="{{ route('mahasiswa.pengajuan.mobil.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="tujuan" class="form-label">Tujuan Pemakaian <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('tujuan') is-invalid @enderror" 
                       id="tujuan" name="tujuan" 
                       value="{{ old('tujuan') }}" 
                       placeholder="Contoh: Kunjungan Industri, Antar Mahasiswa KP, dll"
                       required>
                @error('tujuan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Jelaskan tujuan penggunaan mobil dinas dengan jelas
                </div>
            </div>

            <div class="mb-3">
                <label for="keperluan" class="form-label">Detail Keperluan <span class="text-danger">*</span></label>
                <textarea class="form-control @error('keperluan') is-invalid @enderror" 
                          id="keperluan" name="keperluan" 
                          rows="4" 
                          placeholder="Jelaskan detail keperluan seperti: tempat tujuan, agenda kegiatan, dll"
                          required>{{ old('keperluan') }}</textarea>
                @error('keperluan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Sertakan informasi lengkap: alamat tujuan, nama tempat/perusahaan, agenda acara
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tanggal_pemakaian_mulai" class="form-label">Tanggal & Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('tanggal_pemakaian_mulai') is-invalid @enderror" 
                           id="tanggal_pemakaian_mulai" name="tanggal_pemakaian_mulai" 
                           value="{{ old('tanggal_pemakaian_mulai') }}" 
                           required>
                    @error('tanggal_pemakaian_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="tanggal_pemakaian_selesai" class="form-label">Tanggal & Waktu Selesai <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('tanggal_pemakaian_selesai') is-invalid @enderror" 
                           id="tanggal_pemakaian_selesai" name="tanggal_pemakaian_selesai" 
                           value="{{ old('tanggal_pemakaian_selesai') }}" 
                           required>
                    @error('tanggal_pemakaian_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="jumlah_penumpang" class="form-label">Jumlah Penumpang <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('jumlah_penumpang') is-invalid @enderror" 
                       id="jumlah_penumpang" name="jumlah_penumpang" 
                       value="{{ old('jumlah_penumpang') }}" 
                       min="1" 
                       max="20"
                       placeholder="Masukkan jumlah penumpang"
                       required>
                @error('jumlah_penumpang')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    <i class="fas fa-info-circle me-1"></i>
                    Jumlah orang yang akan ikut dalam perjalanan (termasuk driver jika diperlukan)
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi:</strong>
                <ul class="mb-0 mt-2">
                    <li>Pengajuan akan diverifikasi oleh Admin Fakultas</li>
                    <li>Admin akan mengecek ketersediaan kendaraan sesuai tanggal yang Anda pilih</li>
                    <li>Setelah diverifikasi admin, pengajuan akan diteruskan ke Wakil Dekan 2 untuk persetujuan akhir</li>
                    <li>Pastikan mengajukan minimal 3 hari sebelum tanggal pemakaian</li>
                </ul>
            </div>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Perhatian:</strong> Pastikan semua data yang Anda isi sudah benar. Peminjaman mobil dinas diproses berdasarkan ketersediaan dan prioritas kepentingan akademik.
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validasi tanggal selesai tidak boleh lebih kecil dari tanggal mulai
    document.getElementById('tanggal_pemakaian_selesai').addEventListener('change', function() {
        const tanggalMulai = document.getElementById('tanggal_pemakaian_mulai').value;
        const tanggalSelesai = this.value;
        
        if (tanggalMulai && tanggalSelesai && tanggalSelesai < tanggalMulai) {
            alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');
            this.value = '';
        }
    });

    // Set minimum datetime untuk tanggal mulai (hari ini)
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    const minDateTime = now.toISOString().slice(0, 16);
    document.getElementById('tanggal_pemakaian_mulai').setAttribute('min', minDateTime);
    
    // Update minimum tanggal selesai berdasarkan tanggal mulai
    document.getElementById('tanggal_pemakaian_mulai').addEventListener('change', function() {
        document.getElementById('tanggal_pemakaian_selesai').setAttribute('min', this.value);
    });

    // Validasi jumlah penumpang
    document.getElementById('jumlah_penumpang').addEventListener('input', function() {
        if (this.value < 1) {
            this.value = 1;
        } else if (this.value > 20) {
            this.value = 20;
            alert('Jumlah penumpang maksimal 20 orang');
        }
    });
</script>
@endpush
