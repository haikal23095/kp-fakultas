@extends('layouts.app')

@section('title', 'Buat Pengajuan Surat')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Formulir Pengajuan Surat Keterangan</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Isi Data Pengajuan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('mahasiswa.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="jenis_surat" class="form-label">Pilih Jenis Surat</label>
                    <select class="form-select" id="jenis_surat" name="jenis_surat" required>
                        <option selected disabled value="">Pilih...</option>
                        <option value="aktif_kuliah">Surat Keterangan Aktif Kuliah</option>
                        <option value="tidak_beasiswa">Surat Keterangan Tidak Menerima Beasiswa</option>
                        <option value="pengantar_kp">Surat Pengantar Kerja Praktik</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="dokumen" class="form-label">Upload Dokumen Pendukung (KRS Terakhir)</label>
                    <input class="form-control" type="file" id="dokumen" name="dokumen" required>
                    <div class="form-text">File yang diizinkan: PDF, JPG, PNG. Maksimal 2MB.</div>
                </div>
                 <div class="mb-3">
                    <label for="keperluan" class="form-label">Keperluan Surat</label>
                    <textarea class="form-control" id="keperluan" name="keperluan" rows="3" placeholder="Contoh: Untuk mengajukan beasiswa..." required></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="is_urgent" name="is_urgent">
                    <label class="form-check-label" for="is_urgent">
                        Tandai sebagai URGEN (alasan mendesak)
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
            </form>
        </div>
    </div>
@endsection