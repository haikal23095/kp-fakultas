@extends('layouts.mahasiswa')

@section('title', 'Pengajuan Surat Baru')

{{-- CSS Khusus untuk Pratinjau --}}
@push('styles')
<style>
    /* Style untuk pratinjau agar mirip dokumen */
    .preview-document-wrapper {
        /* Hapus position: sticky untuk layout atas-bawah */
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
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- Menampilkan pesan error dari Controller (try...catch) --}}
@if (session('error'))
    <div class="alert alert-danger">
        <strong>Terjadi Kesalahan:</strong> {{ session('error') }}
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


<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Pengajuan</h6>
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

            {{-- ====================================================== --}}
            {{-- FORM SPESIFIK: SURAT PENGANTAR MAGANG/KP --}}
            {{-- ====================================================== --}}
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
                                <label class="form-label"><strong>Semester</strong></label>
                                <select class="form-select mahasiswa-semester" name="mahasiswa[0][semester]" required data-index="0">
                                    <option value="">--Pilih--</option>
                                    @for($i = 1; $i <= 14; $i++)
                                        <option value="{{ $i }}" {{ $i == 6 ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label"><strong>Jurusan</strong></label>
                                <input type="text" class="form-control mahasiswa-jurusan" 
                                       name="mahasiswa[0][jurusan]"
                                       value="{{ $prodi->Nama_Prodi ?? 'Jurusan Tidak Ditemukan' }}" 
                                       readonly
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
                                <label class="form-label"><strong>Semester</strong></label>
                                <select class="form-select mahasiswa-semester" name="" required data-index="">
                                    <option value="">--Pilih--</option>
                                    @for($i = 1; $i <= 14; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label"><strong>Jurusan</strong></label>
                                <input type="text" class="form-control mahasiswa-jurusan" 
                                       name=""
                                       placeholder="Jurusan otomatis terisi"
                                       readonly
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
                    <h6 class="mb-3 text-center"><i class="fas fa-eye me-1"></i> Pratinjau Form Pengajuan</h6>
                    
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
                                        <small>NIM: <span class="preview-mhs-nim"><span class="preview-placeholder">[NIM]</span></span> | Semester: <span class="preview-mhs-semester"><span class="preview-placeholder">-</span></span></small>
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
                                    <div style="height: 70px;"></div> {{-- Spasi ttd Dosen --}}
                                    <p class="mb-0">( <span id="preview-dospem1-magang-ttd"><span class="preview-placeholder">[Nama Dosen]</span></span> )</p>
                                    <p>NIP. ...</p>
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

            {{-- ... (Form dinamis lainnya akan Anda tambahkan di sini) ... --}}

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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const jenisSuratSelect = document.getElementById('jenisSurat');
        const dynamicForms = document.querySelectorAll('.dynamic-form');
        const formPengajuan = document.getElementById('formPengajuan');

        // --- PENTING: PETA ID DARI DATABASE KE ID FORM & ROUTE ---
        const formIdMap = {
            '3': { // Ganti '3' dengan Id_Jenis_Surat Anda untuk "Surat Keterangan Aktif"
                formId: 'form-surat-aktif',
                route: "{{ route('mahasiswa.pengajuan.aktif.store') }}"
            },
            '6': { // Ganti '6' dengan Id_Jenis_Surat Anda untuk "Surat Pengantar KP/Magang"
                formId: 'form-surat-magang',
                route: "{{ route('mahasiswa.pengajuan.magang.store') }}"
            }
        };
        // --- Akhir Peta ---

        function hideAllDynamicForms() {
            dynamicForms.forEach(function(form) {
                form.style.display = 'none';
                
                // Simpan status required asli dan nonaktifkan validasi
                form.querySelectorAll('input, select, textarea').forEach(function(input) {
                    if (input.required) {
                        input.setAttribute('data-was-required', 'true');
                        input.required = false;
                    }
                    // Disable input agar tidak tersubmit jika kosong
                    input.disabled = true;
                });
            });
        }

        jenisSuratSelect.addEventListener('change', function() {
            hideAllDynamicForms();

            const selectedValue = this.value; // Ini adalah ID, misal '3'
            const formConfig = formIdMap[selectedValue];
            
            if (formConfig) {
                // Set action form sesuai jenis surat
                formPengajuan.action = formConfig.route;
                
                const targetForm = document.getElementById(formConfig.formId);
                if (targetForm) {
                    targetForm.style.display = 'block';
                    
                    // Aktifkan kembali input dan restore status required
                    targetForm.querySelectorAll('input, select, textarea').forEach(function(input) {
                        input.disabled = false;
                        if (input.getAttribute('data-was-required') === 'true') {
                            input.required = true;
                        }
                    });

                    // === [BARU] Jika form magang, inisialisasi preview ===
                    if (formConfig.formId === 'form-surat-magang') {
                        initMagangPreview();
                    }
                }
            } else {
                // Jika jenis surat tidak ada mapping, kosongkan action
                formPengajuan.action = '';
                alert('Jenis surat ini belum tersedia. Silakan pilih jenis surat lain.');
            }
        });

        // Sembunyikan semua saat halaman baru dimuat
        hideAllDynamicForms();

        // ====================================================== //
        // === SCRIPT KHUSUS UNTUK SURAT MAGANG === //
        // ====================================================== //
        
        let mahasiswaIndex = 1; // Mulai dari 1 karena 0 sudah ada (mahasiswa yang login)
        let autocompleteTimeout = null;

        // Fungsi untuk inisialisasi form magang
        function initMagangPreview() {
            // Set tanggal hari ini di preview
            const previewTanggal = document.getElementById('preview-tanggal-magang');
            if (previewTanggal) {
                previewTanggal.textContent = formatTanggal(new Date().toISOString().split('T')[0]);
            }

            // Tambah event listener untuk tombol tambah mahasiswa
            document.getElementById('btn-tambah-mahasiswa').addEventListener('click', tambahMahasiswa);

            // Event listener untuk mahasiswa pertama (yang sudah ada) - dengan multiple approach
            const firstMhsNama = document.querySelector('.mahasiswa-nama[data-index="0"]');
            const firstMhsNim = document.querySelector('.mahasiswa-nim[data-index="0"]');
            const firstMhsSemester = document.querySelector('.mahasiswa-semester[data-index="0"]');
            
            if (firstMhsNama) {
                firstMhsNama.addEventListener('input', updateMahasiswaPreviewList);
                firstMhsNama.addEventListener('change', updateMahasiswaPreviewList);
            }
            if (firstMhsNim) {
                firstMhsNim.addEventListener('input', updateMahasiswaPreviewList);
                firstMhsNim.addEventListener('change', updateMahasiswaPreviewList);
            }
            if (firstMhsSemester) {
                firstMhsSemester.addEventListener('change', updateMahasiswaPreviewList);
                firstMhsSemester.addEventListener('input', updateMahasiswaPreviewList);
            }

            // Event delegation untuk semua input mahasiswa (termasuk yang ditambah nanti)
            document.addEventListener('input', function(e) {
                if (e.target.matches('.mahasiswa-nama, .mahasiswa-nim, .mahasiswa-semester')) {
                    updateMahasiswaPreviewList();
                }
            });
            
            document.addEventListener('change', function(e) {
                if (e.target.matches('.mahasiswa-semester')) {
                    updateMahasiswaPreviewList();
                }
            });

            // Update preview dari input yang sudah ada
            setupPreviewListeners();
            
            // Initial update preview mahasiswa - dipanggil multiple kali untuk memastikan
            updateMahasiswaPreviewList();
            setTimeout(() => {
                updateMahasiswaPreviewList();
            }, 100);
            setTimeout(() => {
                updateMahasiswaPreviewList();
            }, 300);
        }

        // Helper function to format date
        function formatTanggal(dateStr) {
            if (!dateStr) return null;
            const date = new Date(dateStr);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        // Setup listeners untuk update preview
        function setupPreviewListeners() {
            const inputInstansi = document.getElementById('input-instansi-magang');
            const inputJudul = document.getElementById('input-judul-magang');
            const inputDospem1 = document.getElementById('input-dospem1-magang');
            const inputDospem2 = document.getElementById('input-dospem2-magang');
            const inputMulai = document.getElementById('input-mulai-magang');
            const inputSelesai = document.getElementById('input-selesai-magang');

            if (inputInstansi) inputInstansi.addEventListener('input', updatePreview);
            if (inputJudul) inputJudul.addEventListener('input', updatePreview);
            if (inputDospem1) inputDospem1.addEventListener('change', updatePreview);
            if (inputDospem2) inputDospem2.addEventListener('change', updatePreview);
            if (inputMulai) inputMulai.addEventListener('change', updatePreview);
            if (inputSelesai) inputSelesai.addEventListener('change', updatePreview);

            // Event listener untuk upload TTD dengan background removal
            const inputTTD = document.getElementById('input-ttd-magang');
            if (inputTTD) {
                inputTTD.addEventListener('change', handleTTDUpload);
            }

            // Initial preview
            updatePreview();
        }

        // Fungsi untuk update preview
        function updatePreview() {
            const inputInstansi = document.getElementById('input-instansi-magang');
            const inputJudul = document.getElementById('input-judul-magang');
            const inputDospem1 = document.getElementById('input-dospem1-magang');
            const inputDospem2 = document.getElementById('input-dospem2-magang');
            const inputMulai = document.getElementById('input-mulai-magang');
            const inputSelesai = document.getElementById('input-selesai-magang');

            const previewInstansi = document.getElementById('preview-instansi-magang');
            const previewJudul = document.getElementById('preview-judul-magang');
            const previewDospem1 = document.getElementById('preview-dospem1-magang');
            const previewDospem2 = document.getElementById('preview-dospem2-magang');
            const previewJangkaWaktu = document.getElementById('preview-jangka-waktu-magang');
            const previewDospem1Ttd = document.getElementById('preview-dospem1-magang-ttd');

            if (previewInstansi) {
                previewInstansi.innerHTML = inputInstansi?.value || '<span class="preview-placeholder">[Nama Instansi]</span>';
            }
            if (previewJudul) {
                previewJudul.innerHTML = inputJudul?.value || '<span class="preview-placeholder">[Judul Penelitian]</span>';
            }
            if (previewDospem1) {
                previewDospem1.innerHTML = inputDospem1?.value || '<span class="preview-placeholder">[Pilih Dosen]</span>';
            }
            if (previewDospem2) {
                previewDospem2.innerHTML = inputDospem2?.value || '<span class="preview-placeholder">[Opsional]</span>';
            }
            if (previewDospem1Ttd) {
                previewDospem1Ttd.innerHTML = inputDospem1?.value || '<span class="preview-placeholder">[Nama Dosen]</span>';
            }
            if (previewJangkaWaktu) {
                const mulai = formatTanggal(inputMulai?.value);
                const selesai = formatTanggal(inputSelesai?.value);
                if (mulai && selesai) {
                    previewJangkaWaktu.innerHTML = `${mulai} – ${selesai}`;
                } else if (mulai) {
                    previewJangkaWaktu.innerHTML = `${mulai} – ...`;
                } else {
                    previewJangkaWaktu.innerHTML = '<span class="preview-placeholder">[Tanggal]</span>';
                }
            }

            // Update daftar mahasiswa di preview
            updateMahasiswaPreviewList();

            // Update jurusan di preview (ambil dari mahasiswa pertama)
            const firstMhsJurusan = document.querySelector('.mahasiswa-jurusan[data-index="0"]');
            const previewJurusan = document.getElementById('preview-jurusan-magang');
            if (previewJurusan && firstMhsJurusan) {
                previewJurusan.innerHTML = firstMhsJurusan.value || '<span class="preview-placeholder">[Jurusan]</span>';
            }

            // Update nama dan NIM di bagian tanda tangan (mahasiswa pertama)
            const firstMhsNama = document.querySelector('.mahasiswa-nama[data-index="0"]');
            const firstMhsNim = document.querySelector('.mahasiswa-nim[data-index="0"]');
            const previewNamaTtd = document.getElementById('preview-nama-magang-ttd');
            const previewNIMTtd = document.getElementById('preview-nim-magang-ttd');
            
            if (previewNamaTtd && firstMhsNama) {
                previewNamaTtd.innerHTML = firstMhsNama.value || '<span class="preview-placeholder">[Nama Mahasiswa]</span>';
            }
            if (previewNIMTtd && firstMhsNim) {
                previewNIMTtd.innerHTML = firstMhsNim.value || '<span class="preview-placeholder">[NIM]</span>';
            }
        }

        // Fungsi untuk update daftar mahasiswa di preview
        function updateMahasiswaPreviewList() {
            const previewList = document.getElementById('preview-mahasiswa-list');
            if (!previewList) {
                console.log('Preview list element not found');
                return;
            }

            previewList.innerHTML = '';

            // Ambil semua mahasiswa item
            const mahasiswaItems = document.querySelectorAll('.mahasiswa-item');
            console.log('Found mahasiswa items:', mahasiswaItems.length);
            
            mahasiswaItems.forEach((item, index) => {
                const dataIndex = item.getAttribute('data-index');
                const namaInput = item.querySelector(`.mahasiswa-nama[data-index="${dataIndex}"]`);
                const nimInput = item.querySelector(`.mahasiswa-nim[data-index="${dataIndex}"]`);
                const semesterInput = item.querySelector(`.mahasiswa-semester[data-index="${dataIndex}"]`);
                
                const nama = namaInput?.value || '';
                const nim = nimInput?.value || '';
                const semester = semesterInput?.value || '';

                console.log(`Mahasiswa ${index}:`, {nama, nim, semester, dataIndex});

                const previewItem = document.createElement('div');
                previewItem.className = 'preview-mahasiswa-item';
                previewItem.setAttribute('data-preview-index', dataIndex);
                
                const namaDisplay = nama ? nama : '<span class="preview-placeholder">[Nama Mahasiswa]</span>';
                const nimDisplay = nim ? nim : '<span class="preview-placeholder">[NIM]</span>';
                const semesterDisplay = semester ? semester : '<span class="preview-placeholder">-</span>';
                
                previewItem.innerHTML = `
                    <strong>${index + 1}. <span class="preview-mhs-nama">${namaDisplay}</span></strong><br>
                    <small>NIM: <span class="preview-mhs-nim">${nimDisplay}</span> | Semester: <span class="preview-mhs-semester">${semesterDisplay}</span></small>
                `;
                
                previewList.appendChild(previewItem);
            });
        }

        // Fungsi tambah mahasiswa
        function tambahMahasiswa() {
            const container = document.getElementById('mahasiswa-container');
            const template = document.getElementById('mahasiswa-template');
            const clone = template.content.cloneNode(true);

            // Update index
            const item = clone.querySelector('.mahasiswa-item');
            item.setAttribute('data-index', mahasiswaIndex);
            
            // Update nomor item
            clone.querySelector('.item-number').textContent = mahasiswaIndex + 1;

            // Update name attributes
            clone.querySelector('.mahasiswa-nama').setAttribute('name', `mahasiswa[${mahasiswaIndex}][nama]`);
            clone.querySelector('.mahasiswa-nama').setAttribute('data-index', mahasiswaIndex);
            
            clone.querySelector('.mahasiswa-nim').setAttribute('name', `mahasiswa[${mahasiswaIndex}][nim]`);
            clone.querySelector('.mahasiswa-nim').setAttribute('data-index', mahasiswaIndex);
            
            clone.querySelector('.mahasiswa-jurusan').setAttribute('name', `mahasiswa[${mahasiswaIndex}][jurusan]`);
            clone.querySelector('.mahasiswa-jurusan').setAttribute('data-index', mahasiswaIndex);
            
            clone.querySelector('.mahasiswa-semester').setAttribute('name', `mahasiswa[${mahasiswaIndex}][semester]`);
            clone.querySelector('.mahasiswa-semester').setAttribute('data-index', mahasiswaIndex);

            // Event listener untuk tombol hapus
            clone.querySelector('.btn-hapus-mahasiswa').addEventListener('click', function() {
                item.remove();
                updateItemNumbers();
                updateMahasiswaPreviewList(); // Update preview setelah hapus
            });

            // Event listener untuk autocomplete
            const inputNama = clone.querySelector('.autocomplete-mahasiswa');
            inputNama.addEventListener('input', function(e) {
                handleAutocomplete(e.target);
            });

            // Event listener untuk update preview tidak perlu karena sudah pakai delegation

            container.appendChild(clone);
            mahasiswaIndex++;
            
            // Update preview setelah menambah mahasiswa baru
            setTimeout(updateMahasiswaPreviewList, 50);
        }

        // Fungsi hapus dan update nomor urut
        function updateItemNumbers() {
            document.querySelectorAll('.mahasiswa-item').forEach((item, index) => {
                const number = item.querySelector('.item-number');
                if (number) {
                    number.textContent = index + 1;
                }
            });
        }

        // Fungsi autocomplete
        function handleAutocomplete(input) {
            clearTimeout(autocompleteTimeout);
            
            const query = input.value.trim();
            const resultsDiv = input.nextElementSibling;

            if (query.length < 2) {
                resultsDiv.style.display = 'none';
                return;
            }

            autocompleteTimeout = setTimeout(() => {
                fetch(`{{ route('mahasiswa.api.mahasiswa.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            resultsDiv.innerHTML = '<div class="autocomplete-item"><small>Tidak ada hasil</small></div>';
                            resultsDiv.style.display = 'block';
                            return;
                        }

                        resultsDiv.innerHTML = '';
                        data.forEach(mhs => {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-item';
                            div.innerHTML = `<strong>${mhs.nama}</strong><br><small>NIM: ${mhs.nim} - ${mhs.jurusan}</small>`;
                            div.addEventListener('click', () => {
                                selectMahasiswa(input, mhs);
                                resultsDiv.style.display = 'none';
                            });
                            resultsDiv.appendChild(div);
                        });
                        resultsDiv.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching mahasiswa:', error);
                    });
            }, 300);
        }

        // Fungsi select mahasiswa dari autocomplete
        function selectMahasiswa(input, mhs) {
            const index = input.getAttribute('data-index');
            
            // Set nilai
            input.value = mhs.nama;
            const nimInput = document.querySelector(`.mahasiswa-nim[data-index="${index}"]`);
            const jurusanInput = document.querySelector(`.mahasiswa-jurusan[data-index="${index}"]`);
            
            if (nimInput) nimInput.value = mhs.nim;
            if (jurusanInput) jurusanInput.value = mhs.jurusan;
            
            // Tutup autocomplete
            input.nextElementSibling.style.display = 'none';
            
            // Update preview setelah select
            updateMahasiswaPreviewList();
        }

        // Close autocomplete ketika klik di luar
        document.addEventListener('click', function(e) {
            if (!e.target.classList.contains('autocomplete-mahasiswa')) {
                document.querySelectorAll('.autocomplete-results').forEach(div => {
                    div.style.display = 'none';
                });
            }
        });

        // Fungsi untuk handle upload TTD dan preview
        function handleTTDUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const previewImg = document.getElementById('preview-ttd-image');
            const processingDiv = document.getElementById('ttd-processing');

            // Show processing indicator
            if (processingDiv) processingDiv.style.display = 'block';

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    // Create canvas for background removal
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    canvas.width = img.width;
                    canvas.height = img.height;
                    
                    // Draw image
                    ctx.drawImage(img, 0, 0);
                    
                    // Get image data
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const data = imageData.data;
                    
                    // Simple background removal - remove white/light colors
                    for (let i = 0; i < data.length; i += 4) {
                        const red = data[i];
                        const green = data[i + 1];
                        const blue = data[i + 2];
                        
                        // If pixel is mostly white/light (threshold = 200)
                        if (red > 200 && green > 200 && blue > 200) {
                            data[i + 3] = 0; // Make transparent
                        }
                    }
                    
                    ctx.putImageData(imageData, 0, 0);
                    
                    // Set preview image
                    const processedImage = canvas.toDataURL('image/png');
                    previewImg.src = processedImage;
                    previewImg.style.display = 'block';
                    previewImg.style.maxHeight = '80px';
                    previewImg.style.margin = '10px auto';
                    
                    // Hide processing indicator
                    if (processingDiv) processingDiv.style.display = 'none';
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Note: initMagangPreview() dipanggil dari event 'change' dropdown jenis surat
    });
</script>
@endpush