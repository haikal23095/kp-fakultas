@extends('layouts.mahasiswa')

@section('title', 'Buat Pengajuan Surat')

@section('content')
    <h1 class="h3 fw-bold mb-4">Formulir Pengajuan Surat</h1>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Isi Data Pengajuan</h6>
        </div>
        <div class="card-body">
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="jenis_surat" class="form-label">Jenis Surat</label>
                    <select class="form-select" id="jenis_surat" name="jenis_surat" required>
                        <option selected disabled value="">Pilih jenis surat...</option>
                        <option value="aktif_kuliah">Surat Keterangan Aktif Kuliah</option>
                        <option value="tidak_beasiswa">Surat Keterangan Tidak Menerima Beasiswa</option>
                        <option value="pengantar">Surat Pengantar (Kerja Praktik/Penelitian)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keperluan" class="form-label">Keperluan</label>
                    <textarea class="form-control" id="keperluan" name="keperluan" rows="4" placeholder="Contoh: Untuk mengajukan beasiswa Bank Indonesia" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="dokumen" class="form-label">Upload Dokumen Pendukung (jika perlu)</label>
                    <input class="form-control" type="file" id="dokumen" name="dokumen">
                    <div class="form-text">Contoh: Scan Kartu Rencana Studi (KRS) terakhir untuk surat aktif kuliah.</div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection