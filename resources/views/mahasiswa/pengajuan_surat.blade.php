@extends('layouts.mahasiswa')

@section('title', 'Pengajuan Surat Baru')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Surat Baru</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan</h6>
    </div>
    <div class="card-body">
        <form>
            {{-- Pilihan Jenis Surat --}}
            <div class="mb-3">
                <label for="jenisSurat" class="form-label"><strong>Pilih Jenis Surat</strong></label>
                <select class="form-select" id="jenisSurat" required>
                    <option selected disabled value="">-- Silakan pilih jenis surat yang akan diajukan --</option>
                    <option value="1">Surat Keterangan Aktif Kuliah</option>
                    <option value="2">Surat Rekomendasi Beasiswa</option>
                    <option value="3">Surat Izin Penelitian</option>
                    <option value="4">Surat Keterangan Lulus</option>
                </select>
            </div>

            {{-- Detail Keperluan --}}
            <div class="mb-3">
                <label for="keperluan" class="form-label"><strong>Jelaskan Keperluan Anda</strong></label>
                <textarea class="form-control" id="keperluan" rows="4" placeholder="Contoh: Untuk keperluan pengajuan beasiswa Bank Indonesia tahun 2025." required></textarea>
            </div>

            {{-- Upload Dokumen Pendukung --}}
            <div class="mb-4">
                <label for="dokumen" class="form-label"><strong>Unggah Dokumen Pendukung (jika ada)</strong></label>
                <input class="form-control" type="file" id="dokumen">
                <div id="dokumenHelp" class="form-text">Contoh: Scan Kartu Hasil Studi (KHS), proposal penelitian, dll. Format: PDF, JPG, PNG (Maks. 2MB).</div>
            </div>

            <hr>

            {{-- Tombol Aksi --}}
            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-secondary me-2">Reset Form</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection