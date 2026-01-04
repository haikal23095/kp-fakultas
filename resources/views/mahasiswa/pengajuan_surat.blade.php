@extends('layouts.mahasiswa')

@section('title', 'Pengajuan Surat Baru')

{{-- CSS Khusus untuk Pratinjau --}}
@push('styles')
<style>
    /* Style untuk pratinjau agar mirip dokumen */
    .preview-document-wrapper {
        margin-top: 30px;
    }

    .preview-document {
        font-family: 'Times New Roman', Times, serif;
        background: #fdfdfd;
        color: #000;
        border: 1px solid #ccc;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-size: 12pt; /* Ukuran font standar dokumen */
        line-height: 1.5;
    }
    .preview-header {
        text-align: center;
        margin-bottom: 10px;
        border-bottom: 3px double #000;
        padding-bottom: 5px; 
    }
    .preview-header img {
        width: 120px; /* diperbesar sedikit dari 70px */
        float: left;
        margin-top: -12px; /* sesuaikan posisi vertical setelah perbesaran */
    }
    .preview-header strong {
        display: block;
        text-transform: uppercase;
    }
    .preview-header .line-1 { font-size: 13pt; }
    .preview-header .line-2 { font-size: 15pt; }
    .preview-header .line-3 { font-size: 15pt; }
    .preview-header .address {
        font-size: 10pt;
        font-style: italic;
        margin-top: 5px;
    }
    .preview-title {
        font-weight: bold;
        font-size: 14pt;
        margin-top: 25px;
        text-align: center;
        text-decoration: underline;
    }
    .preview-table {
        margin-top: 15px;
        width: 100%;
        font-size: 12pt;
    }
    .preview-table td {
        padding: 2px 0px;
        vertical-align: top;
    }
    .preview-table td:nth-child(1) { width: 30%; }
    .preview-table td:nth-child(2) { width: 2%; }
    .preview-table td:nth-child(3) {
        width: 68%;
        word-break: break-word;
    }
    .preview-magang-section {
        margin-top: 10px;
    }
    .preview-signature {
        font-size: 12pt;
        margin-top: 30px;
    }
    .preview-placeholder {
        color: #999;
        font-style: italic;
    }
    #preview-ttd-image {
        max-height: 80px; /* Batas tinggi ttd */
        max-width: 200px;
        display: none; /* Sembunyi sampai ada gambar */
        margin: 10px auto;
    }
    
    /* Style untuk autocomplete */
    .autocomplete-results {
        position: absolute;
        background: white;
        border: 1px solid #ddd;
        max-height: 200px;
        overflow-y: auto;
        width: calc(100% - 30px);
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .autocomplete-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }
    .autocomplete-item:hover {
        background-color: #f0f8ff;
    }
    .autocomplete-item strong {
        color: #333;
    }
    .autocomplete-item small {
        color: #666;
    }
    
    /* Style untuk card mahasiswa */
    .mahasiswa-item {
        border-left: 3px solid #4e73df;
        position: relative;
    }
    
    /* Style untuk preview mahasiswa list */
    .preview-mahasiswa-item {
        margin-bottom: 8px;
    }
    .preview-mahasiswa-item:last-child {
        margin-bottom: 0;
    }
    #preview-mahasiswa-list .preview-mhs-nama {
        font-weight: normal;
    }
</style>
@endpush

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
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Surat Baru</h1>
</div>

{{-- Card Grid untuk Semua Jenis Surat --}}
<div class="row mb-4">
    {{-- Card Surat Keterangan Aktif --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-primary border-4 h-100" style="cursor: pointer;" onclick="selectJenisSurat(3)">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Surat Keterangan Aktif</h6>
                </div>
                <p class="card-text text-muted small mb-0">Surat keterangan bahwa Anda masih terdaftar sebagai mahasiswa aktif</p>
            </div>
        </div>
    </div>

    {{-- Card Surat Pengantar Magang/KP --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-success border-4 h-100" style="cursor: pointer;" onclick="selectJenisSurat(13)">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-success text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Surat Pengantar KP/Magang</h6>
                </div>
                <p class="card-text text-muted small mb-0">Surat pengantar untuk keperluan Kerja Praktik atau Magang di perusahaan/instansi</p>
            </div>
        </div>
    </div>

    {{-- Card Legalisir --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-warning border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-warning text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-stamp"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Legalisir Dokumen</h6>
                </div>
                <p class="card-text text-muted small mb-2">Ajukan legalisir ijazah atau transkrip nilai secara online</p>
                <a href="{{ route('mahasiswa.pengajuan.legalisir.create') }}" class="btn btn-sm btn-warning text-white">
                    <i class="fas fa-arrow-right me-1"></i>Buka Halaman
                </a>
            </div>
        </div>
    </div>

    {{-- Card Peminjaman Mobil Dinas --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-info border-4 h-100" style="cursor: pointer;" onclick="selectJenisSurat(15)">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-info text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-car"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Peminjaman Mobil Dinas</h6>
                </div>
                <p class="card-text text-muted small mb-0">Ajukan permohonan peminjaman mobil dinas fakultas</p>
            </div>
        </div>
    </div>

    {{-- Card Tidak Menerima Beasiswa --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-danger border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-danger text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Tidak Menerima Beasiswa</h6>
                </div>
                <p class="card-text text-muted small mb-2">Surat keterangan tidak menerima beasiswa</p>
                <a href="{{ route('mahasiswa.pengajuan.tidak_beasiswa.create') }}" class="btn btn-sm btn-danger text-white">
                    <i class="fas fa-arrow-right me-1"></i>Buka Halaman
                </a>
            </div>
        </div>
    </div>

    {{-- Card Cek Plagiasi --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-secondary border-4 h-100" style="cursor: pointer;" onclick="selectJenisSurat(17)">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-secondary text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-search"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Cek Plagiasi (Turnitin)</h6>
                </div>
                <p class="card-text text-muted small mb-0">Permohonan cek plagiasi dokumen/skripsi</p>
            </div>
        </div>
    </div>

    {{-- Card Dispensasi --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-primary border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Surat Dispensasi</h6>
                </div>
                <p class="card-text text-muted small mb-2">Dispensasi kehadiran kuliah</p>
                <a href="{{ route('mahasiswa.pengajuan.dispen.create') }}" class="btn btn-sm btn-primary text-white">
                    <i class="fas fa-arrow-right me-1"></i>Buka Halaman
                </a>
            </div>
        </div>
    </div>

    {{-- Card Berkelakuan Baik --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-success border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-success text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Berkelakuan Baik</h6>
                </div>
                <p class="card-text text-muted small mb-2">Surat keterangan berkelakuan baik</p>
                <a href="{{ route('mahasiswa.pengajuan.kelakuan_baik.create') }}" class="btn btn-sm btn-success text-white">
                    <i class="fas fa-arrow-right me-1"></i>Buka Halaman
                </a>
            </div>
        </div>
    </div>

    {{-- Card Surat Tugas --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-info border-4 h-100" style="cursor: pointer;" onclick="selectJenisSurat(20)">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-info text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Surat Tugas Kegiatan</h6>
                </div>
                <p class="card-text text-muted small mb-0">Permohonan surat tugas untuk kegiatan</p>
            </div>
        </div>
    </div>

    {{-- Card MBKM --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-warning border-4 h-100" style="cursor: pointer;" onclick="selectJenisSurat(21)">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-warning text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Rekomendasi MBKM</h6>
                </div>
                <p class="card-text text-muted small mb-0">Surat rekomendasi untuk program MBKM</p>
            </div>
        </div>
    </div>

    {{-- Card Peminjaman Gedung --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-danger border-4 h-100" style="cursor: pointer;" onclick="selectJenisSurat(22)">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-danger text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-building"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Peminjaman Gedung</h6>
                </div>
                <p class="card-text text-muted small mb-0">Ajukan peminjaman gedung dan ruangan</p>
            </div>
        </div>
    </div>

    {{-- Card Lembur --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-secondary border-4 h-100" style="cursor: pointer;" onclick="selectJenisSurat(23)">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-secondary text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Surat Perintah Lembur</h6>
                </div>
                <p class="card-text text-muted small mb-0">Permohonan surat perintah lembur</p>
            </div>
        </div>
    </div>

    {{-- Card Peminjaman Ruang --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card shadow-sm border-start border-primary border-4 h-100" style="cursor: pointer;" onclick="selectJenisSurat(24)">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary text-white rounded-circle p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold">Peminjaman Ruang</h6>
                </div>
                <p class="card-text text-muted small mb-0">Ajukan peminjaman ruang rapat/lab</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4" id="form-container" style="display: none;">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan</h6>
        <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('form-container').style.display='none'; document.getElementById('jenisSurat').value='';">
            <i class="fas fa-times"></i> Tutup Form
        </button>
    </div>
    <div class="card-body">
        
        <form method="POST" action="" id="formPengajuan" enctype="multipart/form-data">
            @csrf {{-- Wajib untuk keamanan Laravel --}}

            {{-- Pilihan Jenis Surat (The "Trigger") --}}
            <div class="mb-3">
                <label for="jenisSurat" class="form-label"><strong>Pilih Jenis Surat</strong></label>
                
                {{-- Dropdown ini sekarang diisi dari tabel 'Jenis_Surat' --}}
                <select class="form-select" id="jenisSurat" name="Id_Jenis_Surat" required>
                    <option selected disabled value="">-- Silakan pilih jenis surat --</option>
                    @foreach ($jenis_surats as $surat)
                        {{-- Value-nya adalah ID dari database, misal '3' --}}
                        <option value="{{ $surat->Id_Jenis_Surat }}" data-nama="{{ $surat->Nama_Surat }}">{{ $surat->Nama_Surat }}</option>
                    @endforeach
                </select>
            </div>

            <hr>

            {{-- FORM SPESIFIK: SURAT KETERANGAN MAHASISWA AKTIF --}}
            <div id="form-surat-aktif" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Surat Keterangan Mahasiswa Aktif</h5>
                
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
                    {{-- [PERBAIKAN] Mengganti name dari '...AktIF' ke '...Aktif' agar sesuai controller --}}
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_Aktif" rows="3" placeholder="Contoh: Untuk keperluan administrasi beasiswa." required></textarea>
                </div>

                {{-- Ini adalah 'file_pendukung' yang akan disimpan ke 'File_Arsip' --}}
                <div class="mb-4">
                    <label for="dokumen_krs" class="form-label"><strong>Unggah KRS Terakhir</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_aktif" required>
                    <div class="form-text">Wajib mengunggah Scan KRS sebagai bukti. Format: PDF (Maks. 2MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: SURAT PENGANTAR MAGANG/KP --}}
            {{-- Container ini sekarang full width tanpa 2 kolom --}}
            <div id="form-surat-magang" class="dynamic-form" style="display: none;">
                
                <h5 class="mb-3">Formulir Surat Pengantar Magang/KP</h5>
        
                {{-- Container untuk daftar mahasiswa yang ikut magang --}}
                <div id="mahasiswa-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Data Mahasiswa</h6>
                        <button type="button" class="btn btn-success btn-sm" id="btn-tambah-mahasiswa">
                            <i class="fas fa-plus"></i> Tambah Mahasiswa
                        </button>
                    </div>

                    {{-- Item mahasiswa pertama (yang login) - tidak bisa dihapus --}}
                    <div class="mahasiswa-item card mb-3 p-3" data-index="0">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label"><strong>Nama Mahasiswa</strong></label>
                                <input type="text" class="form-control mahasiswa-nama" 
                                       name="mahasiswa[0][nama]"
                                       value="{{ $mahasiswa->Nama_Mahasiswa ?? Auth::user()->Name_User }}" 
                                       readonly
                                       data-index="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><strong>NPM/NIM</strong></label>
                                <input type="text" class="form-control mahasiswa-nim" 
                                       name="mahasiswa[0][nim]"
                                       value="{{ $mahasiswa->NIM ?? 'NIM Tidak Ditemukan' }}" 
                                       readonly
                                       data-index="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label"><strong>Angkatan</strong></label>
                                <input type="text" class="form-control mahasiswa-angkatan" 
                                       name="mahasiswa[0][angkatan]"
                                       value="{{ $mahasiswa->Angkatan ?? 'Angkatan Tidak Ditemukan' }}" 
                                       readonly
                                       data-index="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label"><strong>Jurusan</strong></label>
                                <input type="text" class="form-control mahasiswa-jurusan" 
                                       name="mahasiswa[0][jurusan]"
                                       value="{{ $prodi->Nama_Prodi ?? 'Jurusan Tidak Ditemukan' }}" 
                                       readonly
                                       data-index="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><strong>No WhatsApp</strong></label>
                                <input type="text" class="form-control mahasiswa-no-wa" 
                                       name="mahasiswa[0][no_wa]"
                                       placeholder="08xxxxxxxxxx"
                                       required
                                       pattern="[0-9]{10,15}"
                                       title="Masukkan nomor WhatsApp yang valid (10-15 digit)"
                                       data-index="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden template untuk mahasiswa tambahan --}}
                <template id="mahasiswa-template">
                    <div class="mahasiswa-item card mb-3 p-3" data-index="">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Mahasiswa #<span class="item-number"></span></h6>
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-mahasiswa">
                                <i class="fas fa-times"></i> Hapus
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label"><strong>Nama Mahasiswa</strong></label>
                                <input type="text" class="form-control mahasiswa-nama autocomplete-mahasiswa" 
                                       name="" 
                                       placeholder="Ketik nama atau NIM..."
                                       autocomplete="off"
                                       data-index="">
                                <div class="autocomplete-results" style="display:none;"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><strong>NPM/NIM</strong></label>
                                <input type="text" class="form-control mahasiswa-nim" 
                                       name=""
                                       placeholder="NIM otomatis terisi"
                                       readonly
                                       data-index="">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label"><strong>Angkatan</strong></label>
                                <input type="text" class="form-control mahasiswa-angkatan" 
                                       name=""
                                       placeholder="Angkatan otomatis terisi"
                                       readonly
                                       data-index="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label"><strong>Jurusan</strong></label>
                                <input type="text" class="form-control mahasiswa-jurusan" 
                                       name=""
                                       placeholder="Jurusan otomatis terisi"
                                       readonly
                                       data-index="">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><strong>No WhatsApp</strong></label>
                                <input type="text" class="form-control mahasiswa-no-wa" 
                                       name=""
                                       placeholder="08xxxxxxxxxx"
                                       required
                                       pattern="[0-9]{10,15}"
                                       title="Masukkan nomor WhatsApp yang valid (10-15 digit)"
                                       data-index="">
                            </div>
                        </div>
                    </div>
                </template>

                <hr>

                <div class="mb-3">
                    <label for="dospem1" class="form-label"><strong>Dosen Pembimbing 1</strong></label>
                    <select class="form-select" name="data_spesifik[dosen_pembimbing_1]" required 
                            id="input-dospem1-magang"> {{-- ID untuk JS --}}
                        <option value="" selected disabled>-- Pilih Dosen Pembimbing 1 --</option>
                        @foreach ($dosens as $dosen)
                            <option value="{{ $dosen->Nama_Dosen }}">{{ $dosen->Nama_Dosen }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- [BARU] Input Dosen Pembimbing 2 (Optional) --}}
                <div class="mb-3">
                    <label for="dospem2" class="form-label"><strong>Dosen Pembimbing 2</strong> <small class="text-muted">(Opsional)</small></label>
                    <select class="form-select" name="data_spesifik[dosen_pembimbing_2]" 
                            id="input-dospem2-magang"> {{-- ID untuk JS --}}
                        <option value="" selected>-- Pilih Dosen Pembimbing 2 (Opsional) --</option>
                        @foreach ($dosens as $dosen)
                            <option value="{{ $dosen->Nama_Dosen }}">{{ $dosen->Nama_Dosen }}</option>
                        @endforeach
                    </select>
                </div>
                
                <hr>
                <h6 class="mb-3">Detail Magang/KP</h6>

                {{-- [BARU] Input Judul Penelitian --}}
                <div class="mb-3">
                    <label for="judul_penelitian" class="form-label"><strong>Judul Penelitian/Magang</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[judul_penelitian]" placeholder="Contoh: Analisis Sentimen... (Kosongi jika belum ada)" 
                           id="input-judul-magang"> {{-- ID untuk JS --}}
                </div>

                {{-- Ini semua adalah data yang akan masuk ke 'data_spesifik' (JSON) --}}
                <div class="mb-3">
                    <label for="nama_instansi" class="form-label"><strong>Nama Instansi/Perusahaan Tujuan</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[nama_instansi]" placeholder="Contoh: PT. Google Indonesia" required 
                           id="input-instansi-magang"> {{-- ID untuk JS --}}
                </div>
                <div class="mb-3">
                    <label for="alamat_instansi" class="form-label"><strong>Alamat Lengkap Instansi</strong></label>
                    <textarea class="form-control" name="data_spesifik[alamat_instansi]" rows="3" placeholder="Contoh: Jl. Jenderal Sudirman Kav. 52-53, Jakarta Selatan" required></textarea>
                </div>
                
                {{-- Input Tanggal Sesuai .docx --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_mulai" class="form-label"><strong>Tanggal Mulai Magang</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_mulai]" 
                               id="input-mulai-magang" required> {{-- ID untuk JS --}}
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_selesai" class="form-label"><strong>Tanggal Selesai Magang</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_selesai]" 
                               id="input-selesai-magang" required> {{-- ID untuk JS --}}
                    </div>
                </div>
                
                <hr>
                <h6 class="mb-3">Dokumen Pendukung</h6>
                
                {{-- [PERUBAHAN] Ganti "Form KP" menjadi "Proposal" --}}
                <div class="mb-4">
                    <label for="dokumen_form_kp" class="form-label"><strong>Unggah Proposal</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_magang" required>
                    <div class="form-text">Wajib mengunggah Proposal Magang/KP. Format: PDF (Maks. 2MB).</div>
                </div>

                {{-- [BARU] Unggah Tanda Tangan --}}
                <div class="mb-4">
                    <label for="file_tanda_tangan" class="form-label"><strong>Unggah Foto Tanda Tangan</strong></label>
                    <input class="form-control" type="file" name="file_tanda_tangan" id="input-ttd-magang" required accept="image/png, image/jpeg">
                    <div class="form-text">Wajib diunggah. Tanda tangan di atas kertas putih. Format: PNG, JPG (Maks. 1MB). Background akan dihapus otomatis.</div>
                    <div id="ttd-processing" style="display:none;" class="mt-2">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span class="ms-2">Memproses gambar...</span>
                    </div>
                </div>

                {{-- PREVIEW DITARUH DI BAWAH FORM --}}
                <div class="preview-document-wrapper">
                    <h6 class="mb-3 text-center"><i class="fas fa-eye me-1"></i> Pratinjau Surat Pengantar</h6>
                    
                    <div class="preview-document">
                        {{-- Header Pratinjau (Sesuai image_fc2a9f.png) --}}
                        <div class="preview-header">
                            {{-- Lokal: taruh file logo pada public/images/logo_unijoyo.png --}}
                            <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo Universitas Trunojoyo Madura">
                            <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong>
                            <strong class="line-2">UNIVERSITAS TRUNOJOYO MADURA</strong>
                            <strong class="line-3">FAKULTAS TEKNIK</strong>
                            <div class="address">
                                Sekretariat: Kampus Unijoyo PO Box 2 Telang Kamal Telp 031 7011147 Fax. 031 7011506
                            </div>
                            <div style="clear: both;"></div>
                        </div>

                        {{-- Judul Pratinjau --}}
                        <p class="preview-title">FORM PENGAJUAN SURAT PENGANTAR</p>

                        {{-- Tabel Data Pratinjau --}}
                        <table class="preview-table">
                            {{-- Daftar Mahasiswa (Dinamis) --}}
                            <tr>
                                <td style="vertical-align: top;">Nama</td>
                                <td style="vertical-align: top;">:</td>
                                <td id="preview-mahasiswa-list">
                                    <div class="preview-mahasiswa-item" data-index="0">
                                        <strong>1. <span class="preview-mhs-nama"><span class="preview-placeholder">[Nama Mahasiswa]</span></span></strong><br>
                                        <small>NIM: <span class="preview-mhs-nim"><span class="preview-placeholder">[NIM]</span></span> | Angkatan: <span class="preview-mhs-angkatan"><span class="preview-placeholder">-</span></span></small>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Jurusan</td>
                                <td>:</td>
                                <td id="preview-jurusan-magang"><span class="preview-placeholder">[Jurusan]</span></td>
                            </tr>
                            <tr>
                                <td>Dosen Pembimbing</td>
                                <td>:</td>
                                <td id="preview-dospem1-magang"><span class="preview-placeholder">[Pilih Dosen]</span></td>
                            </tr>
                            {{-- [BARU] Baris Dosen Pembimbing 2 --}}
                            <tr>
                                <td>Dosen Pembimbing 2</td>
                                <td>:</td>
                                <td id="preview-dospem2-magang"><span class="preview-placeholder">[Opsional]</span></td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">Surat Pengantar*</td>
                                <td style="vertical-align: top;">:</td>
                                <td>
                                    1. Pengantar Kerja Praktek<br>
                                    2. Pengantar TA<br>
                                    3. Pengantar Dosen Pembimbing I TA<br>
                                    4. Magang
                                </td>
                            </tr>
                            <tr>
                                <td>Instansi/Perusahaan</td>
                                <td>:</td>
                                <td id="preview-instansi-magang"><span class="preview-placeholder">[Nama Instansi]</span></td>
                            </tr>
                        </table>

                        {{-- Bagian Khusus Magang --}}
                        <div class="preview-magang-section">
                            <strong><u>Isian berikut utk pengantar Magang</u></strong>
                            <table class="preview-table" style="margin-top: 0;">
                                <tr>
                                    <td>Judul Penelitian</td>
                                    <td>:</td>
                                    <td id="preview-judul-magang"><span class="preview-placeholder">[Judul Penelitian]</span></td>
                                </tr>
                                <tr>
                                    <td>Jangka waktu penelitian</td>
                                    <td>:</td>
                                    <td id="preview-jangka-waktu-magang"><span class="preview-placeholder">[Tanggal]</span></td>
                                </tr>
                                <tr>
                                    <td>Identitas Surat Balasan**</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>


                        {{-- Tanda Tangan Pratinjau --}}
                        <div class="preview-signature">
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1">Menyetujui<br>Koordinator KP/TA</p>
                                    <div style="height: 70px;"></div> {{-- Spasi ttd Kaprodi --}}
                                    <p class="mb-0">
                                        ( 
                                        @if($kaprodiName)
                                            {{ $kaprodiName }}
                                        @else
                                            <span class="preview-placeholder">[Nama Kaprodi]</span>
                                        @endif
                                        )
                                    </p>
                                    <p>
                                        NIP. 
                                        @if($kaprodiNIP)
                                            {{ $kaprodiNIP }}
                                        @else
                                            <span class="preview-placeholder">...</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-6 text-center">
                                    <p class="mb-1">Bangkalan, <span id="preview-tanggal-magang"></span></p>
                                    <p class="mb-1">Pemohon</p>
                                    {{-- [BARU] Tempat untuk gambar TTD - diantara nama dan NIM --}}
                                    <img src="" alt="Tanda Tangan" id="preview-ttd-image" style="display:none;">
                                    <p class="mb-0">( <span id="preview-nama-magang-ttd"><span class="preview-placeholder">[Nama Mahasiswa]</span></span> )</p>
                                    <p class="mt-1">NIM. <span id="preview-nim-magang-ttd"><span class="preview-placeholder">[NIM]</span></span></p>
                                </div>
                            </div>
                        </div>
                        <hr style="border-top: 1px dashed #000; margin-top: 15px;">
                        <small style="font-size: 10pt;">
                            Cat: *Tulis alamat Instansi/perusahaan yg dituju<br>
                            **Diisi untuk permohonan kedua dan seterusnya
                        </small>
                    </div>
                </div>
            </div>

            {{-- FORM SPESIFIK: PEMINJAMAN MOBIL DINAS --}}
            <div id="form-mobil-dinas" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Peminjaman Mobil Dinas</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Pemohon</strong></label>
                        <input type="text" class="form-control" value="{{ Auth::user()->Name_User }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Jabatan/Posisi</strong></label>
                        <input type="text" class="form-control" value="{{ Auth::user()->role->Name_Role ?? 'N/A' }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pinjam" class="form-label"><strong>Tanggal Peminjaman</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_pinjam]" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_kembali" class="form-label"><strong>Tanggal Pengembalian</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_kembali]" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tujuan_peminjaman" class="form-label"><strong>Tujuan/Keperluan Peminjaman</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_MobilDinas" rows="3" placeholder="Contoh: Untuk keperluan kunjungan ke instansi..." required></textarea>
                </div>

                <div class="mb-3">
                    <label for="tujuan_lokasi" class="form-label"><strong>Lokasi Tujuan</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[lokasi_tujuan]" placeholder="Contoh: Surabaya, Jawa Timur" required>
                </div>

                <div class="mb-4">
                    <label for="dokumen_surat_tugas" class="form-label"><strong>Unggah Surat Tugas/Undangan (Opsional)</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_mobil_dinas">
                    <div class="form-text">Format: PDF (Maks. 2MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: SURAT KETERANGAN TIDAK MENERIMA BEASISWA --}}
            <div id="form-tidak-beasiswa" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Surat Keterangan Tidak Menerima Beasiswa</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Mahasiswa</strong></label>
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
                        <label for="semester" class="form-label"><strong>Semester Saat Ini</strong></label>
                        <input type="number" class="form-control" name="data_spesifik[semester]" placeholder="Contoh: 5" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keperluan" class="form-label"><strong>Keperluan Surat</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_TidakBeasiswa" rows="3" placeholder="Contoh: Untuk syarat pengajuan beasiswa..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="dokumen_ktp" class="form-label"><strong>Unggah KTP/Identitas</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_tidak_beasiswa" required>
                    <div class="form-text">Format: PDF atau JPG (Maks. 1MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: CEK PLAGIASI (TURNITIN) --}}
            <div id="form-cek-plagiasi" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Permohonan Cek Plagiasi (Turnitin)</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Mahasiswa</strong></label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->Nama_Mahasiswa ?? Auth::user()->Name_User }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>NPM/NIM</strong></label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->NIM ?? 'NIM Tidak Ditemukan' }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="judul_naskah" class="form-label"><strong>Judul Naskah/Skripsi</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[judul_naskah]" placeholder="Judul lengkap naskah..." required>
                </div>

                <div class="mb-3">
                    <label for="jenis_dokumen" class="form-label"><strong>Jenis Dokumen</strong></label>
                    <select class="form-select" name="data_spesifik[jenis_dokumen]" required>
                        <option value="" selected disabled>-- Pilih Jenis Dokumen --</option>
                        <option value="Skripsi">Skripsi</option>
                        <option value="Tugas Akhir">Tugas Akhir</option>
                        <option value="Proposal">Proposal</option>
                        <option value="Jurnal">Jurnal</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label"><strong>Catatan Tambahan</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_CekPlagiasi" rows="3" placeholder="Catatan atau informasi tambahan..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="file_naskah" class="form-label"><strong>Unggah File Naskah</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_cek_plagiasi" required>
                    <div class="form-text">Format: DOCX atau PDF (Maks. 5MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: SURAT DISPENSASI --}}
            <div id="form-dispensasi" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Surat Dispensasi</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Mahasiswa</strong></label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->Nama_Mahasiswa ?? Auth::user()->Name_User }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>NPM/NIM</strong></label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->NIM ?? 'NIM Tidak Ditemukan' }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_mulai_dispensasi" class="form-label"><strong>Tanggal Mulai Dispensasi</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_mulai]" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_selesai_dispensasi" class="form-label"><strong>Tanggal Selesai Dispensasi</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_selesai]" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alasan_dispensasi" class="form-label"><strong>Alasan/Keperluan Dispensasi</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_Dispensasi" rows="3" placeholder="Contoh: Mengikuti lomba/kegiatan..." required></textarea>
                </div>

                <div class="mb-3">
                    <label for="mata_kuliah" class="form-label"><strong>Mata Kuliah yang Tidak Dihadiri</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[mata_kuliah]" placeholder="Contoh: Basis Data, Pemrograman Web" required>
                </div>

                <div class="mb-4">
                    <label for="file_surat_undangan" class="form-label"><strong>Unggah Surat Undangan/Bukti</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_dispensasi" required>
                    <div class="form-text">Format: PDF (Maks. 2MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: SURAT KETERANGAN BERKELAKUAN BAIK --}}
            <div id="form-berkelakuan-baik" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Surat Keterangan Berkelakuan Baik</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Mahasiswa</strong></label>
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
                        <label for="angkatan" class="form-label"><strong>Angkatan</strong></label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->Angkatan ?? 'N/A' }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keperluan_skbb" class="form-label"><strong>Keperluan Surat</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_BerkelakuanBaik" rows="3" placeholder="Contoh: Untuk persyaratan melamar pekerjaan..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="file_ktp_skbb" class="form-label"><strong>Unggah KTP/Identitas</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_berkelakuan_baik" required>
                    <div class="form-text">Format: PDF atau JPG (Maks. 1MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: SURAT TUGAS --}}
            <div id="form-surat-tugas" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Permohonan Surat Tugas</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Pemohon</strong></label>
                        <input type="text" class="form-control" value="{{ Auth::user()->Name_User }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Jabatan/Posisi</strong></label>
                        <input type="text" class="form-control" value="{{ Auth::user()->role->Name_Role ?? 'N/A' }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nama_kegiatan" class="form-label"><strong>Nama Kegiatan</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[nama_kegiatan]" placeholder="Contoh: Seminar Nasional..." required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_kegiatan" class="form-label"><strong>Tanggal Kegiatan</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_kegiatan]" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lokasi_kegiatan" class="form-label"><strong>Lokasi Kegiatan</strong></label>
                        <input type="text" class="form-control" name="data_spesifik[lokasi_kegiatan]" placeholder="Contoh: Jakarta" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tujuan_kegiatan" class="form-label"><strong>Tujuan/Deskripsi Kegiatan</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_SuratTugas" rows="3" placeholder="Jelaskan tujuan dan deskripsi kegiatan..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="file_proposal" class="form-label"><strong>Unggah Proposal/Undangan</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_surat_tugas" required>
                    <div class="form-text">Format: PDF (Maks. 3MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: SURAT REKOMENDASI MBKM --}}
            <div id="form-mbkm" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Surat Rekomendasi MBKM</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Mahasiswa</strong></label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->Nama_Mahasiswa ?? Auth::user()->Name_User }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>NPM/NIM</strong></label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->NIM ?? 'NIM Tidak Ditemukan' }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="program_mbkm" class="form-label"><strong>Program MBKM</strong></label>
                    <select class="form-select" name="data_spesifik[program_mbkm]" required>
                        <option value="" selected disabled>-- Pilih Program MBKM --</option>
                        <option value="Magang/Studi Independen">Magang/Studi Independen</option>
                        <option value="Kampus Mengajar">Kampus Mengajar</option>
                        <option value="Pertukaran Mahasiswa">Pertukaran Mahasiswa</option>
                        <option value="Wirausaha">Wirausaha</option>
                        <option value="Riset/Proyek Kemanusiaan">Riset/Proyek Kemanusiaan</option>
                        <option value="KKN Tematik">KKN Tematik</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nama_mitra" class="form-label"><strong>Nama Mitra/Instansi Tujuan</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[nama_mitra]" placeholder="Contoh: PT. Google Indonesia" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="periode_mulai" class="form-label"><strong>Periode Mulai</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[periode_mulai]" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="periode_selesai" class="form-label"><strong>Periode Selesai</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[periode_selesai]" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi_kegiatan" class="form-label"><strong>Deskripsi Kegiatan MBKM</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_MBKM" rows="3" placeholder="Jelaskan kegiatan MBKM yang akan dilakukan..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="file_proposal_mbkm" class="form-label"><strong>Unggah Proposal MBKM</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_mbkm" required>
                    <div class="form-text">Format: PDF (Maks. 3MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: PEMINJAMAN GEDUNG --}}
            <div id="form-peminjaman-gedung" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Peminjaman Gedung</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Pemohon</strong></label>
                        <input type="text" class="form-control" value="{{ Auth::user()->Name_User }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="organisasi" class="form-label"><strong>Organisasi/Unit</strong></label>
                        <input type="text" class="form-control" name="data_spesifik[organisasi]" placeholder="Contoh: Himpunan Mahasiswa Teknik Informatika" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nama_kegiatan_gedung" class="form-label"><strong>Nama Kegiatan</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[nama_kegiatan]" placeholder="Contoh: Seminar Teknologi" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pinjam_gedung" class="form-label"><strong>Tanggal Penggunaan</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_pinjam]" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="waktu_penggunaan" class="form-label"><strong>Waktu Penggunaan</strong></label>
                        <input type="text" class="form-control" name="data_spesifik[waktu_penggunaan]" placeholder="Contoh: 08.00 - 17.00 WIB" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="gedung_yang_dipinjam" class="form-label"><strong>Gedung/Ruang yang Dipinjam</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[gedung]" placeholder="Contoh: Gedung Serba Guna Lantai 2" required>
                </div>

                <div class="mb-3">
                    <label for="jumlah_peserta" class="form-label"><strong>Estimasi Jumlah Peserta</strong></label>
                    <input type="number" class="form-control" name="data_spesifik[jumlah_peserta]" placeholder="Contoh: 150" required>
                </div>

                <div class="mb-3">
                    <label for="keperluan_gedung" class="form-label"><strong>Keperluan/Deskripsi Acara</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_PeminjamanGedung" rows="3" placeholder="Jelaskan kebutuhan dan deskripsi acara..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="file_proposal_gedung" class="form-label"><strong>Unggah Proposal Kegiatan</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_peminjaman_gedung" required>
                    <div class="form-text">Format: PDF (Maks. 3MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: LEMBUR --}}
            <div id="form-lembur" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Permohonan Lembur</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Pemohon</strong></label>
                        <input type="text" class="form-control" value="{{ Auth::user()->Name_User }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Jabatan/Posisi</strong></label>
                        <input type="text" class="form-control" value="{{ Auth::user()->role->Name_Role ?? 'N/A' }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_lembur" class="form-label"><strong>Tanggal Lembur</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_lembur]" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jam_lembur" class="form-label"><strong>Jam Lembur</strong></label>
                        <input type="text" class="form-control" name="data_spesifik[jam_lembur]" placeholder="Contoh: 18.00 - 22.00 WIB" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="pekerjaan_lembur" class="form-label"><strong>Pekerjaan yang Dilakukan</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_Lembur" rows="3" placeholder="Jelaskan pekerjaan yang akan dilakukan saat lembur..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="file_rab" class="form-label"><strong>Unggah RAB/Dokumen Pendukung (Opsional)</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_lembur">
                    <div class="form-text">Format: PDF (Maks. 2MB).</div>
                </div>
            </div>

            {{-- FORM SPESIFIK: PEMINJAMAN RUANG --}}
            <div id="form-peminjaman-ruang" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Peminjaman Ruang</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Pemohon</strong></label>
                        <input type="text" class="form-control" value="{{ Auth::user()->Name_User }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="organisasi_ruang" class="form-label"><strong>Organisasi/Unit</strong></label>
                        <input type="text" class="form-control" name="data_spesifik[organisasi]" placeholder="Contoh: UKM Robotika" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nama_kegiatan_ruang" class="form-label"><strong>Nama Kegiatan</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[nama_kegiatan]" placeholder="Contoh: Rapat Koordinasi" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pinjam_ruang" class="form-label"><strong>Tanggal Penggunaan</strong></label>
                        <input type="date" class="form-control" name="data_spesifik[tanggal_pinjam]" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="waktu_ruang" class="form-label"><strong>Waktu Penggunaan</strong></label>
                        <input type="text" class="form-control" name="data_spesifik[waktu_penggunaan]" placeholder="Contoh: 13.00 - 15.00 WIB" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="ruang_yang_dipinjam" class="form-label"><strong>Ruang yang Dipinjam</strong></label>
                    <select class="form-select" name="data_spesifik[ruang]" required>
                        <option value="" selected disabled>-- Pilih Ruang --</option>
                        <option value="Lab Komputer 1">Lab Komputer 1</option>
                        <option value="Lab Komputer 2">Lab Komputer 2</option>
                        <option value="Ruang Rapat A">Ruang Rapat A</option>
                        <option value="Ruang Rapat B">Ruang Rapat B</option>
                        <option value="Aula Lantai 3">Aula Lantai 3</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keperluan_ruang" class="form-label"><strong>Keperluan/Deskripsi</strong></label>
                    <textarea class="form-control" name="Deskripsi_Tugas_Surat_PeminjamanRuang" rows="3" placeholder="Jelaskan keperluan peminjaman ruang..." required></textarea>
                </div>

                <div class="mb-4">
                    <label for="file_proposal_ruang" class="form-label"><strong>Unggah Surat Permohonan (Opsional)</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_peminjaman_ruang">
                    <div class="form-text">Format: PDF (Maks. 2MB).</div>
                </div>
            </div>

            {{-- Tombol Aksi (Selalu Terlihat) --}}
            <hr>
            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-secondary me-2">Reset Form</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

{{-- JavaScript-nya sekarang bekerja berdasarkan ID dari Database --}}
@push('scripts')
{{-- Konfigurasi untuk JavaScript --}}
<script>
    // Function untuk select jenis surat dari card
    function selectJenisSurat(idJenis) {
        // Set value dropdown
        document.getElementById('jenisSurat').value = idJenis;
        
        // Trigger change event
        const event = new Event('change');
        document.getElementById('jenisSurat').dispatchEvent(event);
        
        // Show form container & scroll to it
        document.getElementById('form-container').style.display = 'block';
        document.getElementById('form-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Set route configuration for JavaScript module
    window.formIdMap = {
        '3': { // ID untuk "Surat Keterangan Aktif Kuliah"
            formId: 'form-surat-aktif',
            route: "{{ route('mahasiswa.pengajuan.aktif.store') }}"
        },
        '13': { // ID untuk "Surat Pengantar KP/Magang"
            formId: 'form-surat-magang',
            route: "{{ route('mahasiswa.pengajuan.magang.store') }}"
        },
        '15': { // Peminjaman Mobil Dinas
            formId: 'form-mobil-dinas',
            route: "#" // Placeholder route - belum diimplementasi
        },
        '16': { // Tidak Menerima Beasiswa
            formId: 'form-tidak-beasiswa',
            route: "#"
        },
        '17': { // Cek Plagiasi
            formId: 'form-cek-plagiasi',
            route: "#"
        },
        '18': { // Dispensasi
            formId: 'form-dispensasi',
            route: "#"
        },
        '19': { // Berkelakuan Baik
            formId: 'form-berkelakuan-baik',
            route: "#"
        },
        '20': { // Surat Tugas
            formId: 'form-surat-tugas',
            route: "#"
        },
        '21': { // MBKM
            formId: 'form-mbkm',
            route: "#"
        },
        '22': { // Peminjaman Gedung
            formId: 'form-peminjaman-gedung',
            route: "#"
        },
        '23': { // Lembur
            formId: 'form-lembur',
            route: "#"
        },
        '24': { // Peminjaman Ruang
            formId: 'form-peminjaman-ruang',
            route: "#"
        }
    };
    
    // Set mahasiswa search route
    window.mahasiswaSearchRoute = "{{ route('mahasiswa.api.mahasiswa.search') }}";
</script>

{{-- Load JavaScript Module --}}
<script src="{{ asset('js/pengajuan-surat.js') }}"></script>
@endpush