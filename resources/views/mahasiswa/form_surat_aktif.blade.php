@extends('layouts.mahasiswa')

@section('title', 'Form Surat Keterangan Aktif')

@section('content')

{{-- Menampilkan pesan sukses setelah submit --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Menampilkan pesan error dari Controller (try...catch) --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <strong>Terjadi Kesalahan:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Menampilkan pesan error jika validasi gagal --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Form Surat Keterangan Aktif Kuliah</h1>
        <p class="text-muted mb-0">Isi formulir di bawah untuk mengajukan surat keterangan aktif kuliah</p>
    </div>
    <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan</h6>
    </div>
    <div class="card-body">
        
        <form method="POST" action="{{ route('mahasiswa.pengajuan.aktif.store') }}" enctype="multipart/form-data">
            @csrf
            
            {{-- Hidden field untuk ID Jenis Surat --}}
            <input type="hidden" name="Id_Jenis_Surat" value="{{ $jenisSurat->Id_Jenis_Surat ?? '' }}">

            <h5 class="mb-3">Data Mahasiswa</h5>
            
            {{-- Data Mahasiswa (Sekarang terisi otomatis dari Auth) --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Nama Mahasiswa</strong></label>
                    {{-- Menggunakan data dari tabel Mahasiswa, jika tidak ada, ambil dari Users --}}
                    <input type="text" class="form-control" value="{{ $mahasiswa->Nama_Mahasiswa ?? Auth::user()->Name_User }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>NPM/NIM</strong></label>
                    <input type="text" class="form-control" value="{{ $mahasiswa->NIM ?? 'NIM Tidak Ditemukan' }}" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Program Studi</strong></label>
                    <input type="text" class="form-control" value="{{ $prodi->Nama_Prodi ?? 'Prodi Tidak Ditemukan' }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><strong>Jurusan</strong></label>
                    {{-- Menggunakan Nama_Prodi sebagai Jurusan, sesuai struktur DB Anda --}}
                    <input type="text" class="form-control" value="{{ $prodi->Nama_Prodi ?? 'Jurusan Tidak Ditemukan' }}" readonly>
                </div>
            </div>
            
            <hr>
            <h5 class="mb-3">Data Akademik</h5>

            {{-- Ini adalah data yang akan masuk ke 'data_spesifik' (JSON) --}}
             <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="semester" class="form-label"><strong>Semester Saat Ini</strong></label>
                    <input type="number" class="form-control" name="data_spesifik[semester]" placeholder="Contoh: 7" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tahun_akademik" class="form-label"><strong>Tahun Akademik</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[tahun_akademik]" placeholder="Contoh: 2024/2025" required>
                </div>
            </div>

            {{-- Ini adalah kolom 'Deskripsi_Tugas_Surat' --}}
            <div class="mb-3">
                <label for="keperluan_aktif" class="form-label"><strong>Jelaskan Keperluan Anda</strong></label>
                <textarea class="form-control" name="Deskripsi_Tugas_Surat_Aktif" rows="3" placeholder="Contoh: Untuk keperluan administrasi beasiswa." required></textarea>
            </div>

            <hr>
            <h5 class="mb-3">Dokumen Pendukung</h5>

            {{-- Ini adalah 'file_pendukung' yang akan disimpan ke 'File_Arsip' --}}
            <div class="mb-4">
                <label for="dokumen_krs" class="form-label"><strong>Unggah KRS Terakhir</strong></label>
                <input class="form-control" type="file" name="file_pendukung_aktif" required>
                <div class="form-text">Wajib mengunggah Scan KRS sebagai bukti. Format: PDF (Maks. 2MB).</div>
            </div>

            {{-- Tombol Aksi --}}
            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
