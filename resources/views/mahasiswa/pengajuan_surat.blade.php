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
        max-height: 60px; /* Batas tinggi ttd */
        display: none; /* Sembunyi sampai ada gambar */
        margin: 5px 0;
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

            {{-- ====================================================== --}}
            {{-- FORM SPESIFIK: SURAT KETERANGAN MAHASISWA AKTIF --}}
            {{-- ====================================================== --}}
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
        
                {{-- Data Mahasiswa (Sekarang terisi otomatis dari Auth) --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Nama Mahasiswa</strong></label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->Nama_Mahasiswa ?? Auth::user()->Name_User }}" readonly 
                               id="input-nama-magang"> {{-- ID untuk JS --}}
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>NPM/NIM</strong></label>
                        <input type="text" class="form-control" value="{{ $mahasiswa->NIM ?? 'NIM Tidak Ditemukan' }}" readonly 
                               id="input-nim-magang"> {{-- ID untuk JS --}}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Jurusan</strong></label>
                    <input type="text" class="form-control" value="{{ $prodi->Nama_Prodi ?? 'Jurusan Tidak Ditemukan' }}" readonly 
                           id="input-jurusan-magang"> {{-- ID untuk JS --}}
                </div>

                {{-- Input Dosen (Sekarang menjadi Dropdown dari tabel 'Dosen') --}}
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
                    <div class="form-text">Wajib diunggah. Tanda tangan di atas kertas putih. Format: PNG, JPG (Maks. 1MB).</div>
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
                            <tr>
                                <td>Nama</td>
                                <td>:</td>
                                <td id="preview-nama-magang"><span class="preview-placeholder">[Nama Mahasiswa]</span></td>
                            </tr>
                            <tr>
                                <td>NIM</td>
                                <td>:</td>
                                <td id="preview-nim-magang"><span class="preview-placeholder">[NIM]</span></td>
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
                                    {{-- [BARU] Tempat untuk gambar TTD --}}
                                    <img src="" alt="Tanda Tangan" id="preview-ttd-image">
                                    <p class="mb-0">( <span id="preview-nama-magang-ttd"><span class="preview-placeholder">[Nama Mahasiswa]</span></span> )</p>
                                    <p>NIM. <span id="preview-nim-magang-ttd"><span class="preview-placeholder">[NIM]</span></span></p>
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
        // === SCRIPT KHUSUS UNTUK PREVIEW SURAT MAGANG === //
        // ====================================================== //

        // Ambil elemen input
        const inputNama = document.getElementById('input-nama-magang');
        const inputNIM = document.getElementById('input-nim-magang');
        const inputJurusan = document.getElementById('input-jurusan-magang');
        const inputDospem1 = document.getElementById('input-dospem1-magang');
        const inputInstansi = document.getElementById('input-instansi-magang');
        const inputJudul = document.getElementById('input-judul-magang'); // [BARU]
        const inputMulai = document.getElementById('input-mulai-magang');
        const inputSelesai = document.getElementById('input-selesai-magang');
        const inputTTD = document.getElementById('input-ttd-magang'); // [BARU]

        // Ambil elemen input dosen 2
        const inputDospem2 = document.getElementById('input-dospem2-magang');

        // Ambil elemen preview dosen 2
        const previewDospem2 = document.getElementById('preview-dospem2-magang');
        const previewDospem2Ttd = document.getElementById('preview-dospem2-magang-ttd');

        // Ambil elemen preview
        const previewNama = document.getElementById('preview-nama-magang');
        const previewNIM = document.getElementById('preview-nim-magang');
        const previewJurusan = document.getElementById('preview-jurusan-magang');
        const previewDospem1 = document.getElementById('preview-dospem1-magang');
        const previewInstansi = document.getElementById('preview-instansi-magang');
        const previewJudul = document.getElementById('preview-judul-magang'); // [BARU]
        const previewJangkaWaktu = document.getElementById('preview-jangka-waktu-magang');
        const previewTanggal = document.getElementById('preview-tanggal-magang');
        const previewNamaTtd = document.getElementById('preview-nama-magang-ttd');
        const previewNIMTtd = document.getElementById('preview-nim-magang-ttd');
        const previewDospem1Ttd = document.getElementById('preview-dospem1-magang-ttd');
        const previewTTDImage = document.getElementById('preview-ttd-image'); // [BARU]

        // Placeholder
        const phNama = '<span class="preview-placeholder">[Nama Mahasiswa]</span>';
        const phNIM = '<span class="preview-placeholder">[NIM]</span>';
        const phJurusan = '<span class="preview-placeholder">[Jurusan]</span>';
        const phDospem = '<span class="preview-placeholder">[Pilih Dosen]</span>';
        const phInstansi = '<span class="preview-placeholder">[Nama Instansi]</span>';
        const phJudul = '<span class="preview-placeholder">[Judul Penelitian]</span>';
        const phTanggal = '<span class="preview-placeholder">[Tanggal]</span>';

        // Helper function to format date
        function formatTanggal(dateStr) {
            if (!dateStr) return null;
            const date = new Date(dateStr);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        // Fungsi untuk update preview
        function updatePreview() {
            // Update Teks Biasa
            previewNama.innerHTML = inputNama.value || phNama;
            previewNIM.innerHTML = inputNIM.value || phNIM;
            previewJurusan.innerHTML = inputJurusan.value || phJurusan;
            previewInstansi.innerHTML = inputInstansi.value || phInstansi;
            previewJudul.innerHTML = inputJudul.value || phJudul; // [BARU]
            
            // Update Dropdown Dosen 1
            previewDospem1.innerHTML = inputDospem1.value ? inputDospem1.value : phDospem;

            // [BARU] Update Dropdown Dosen 2 (opsional)
            previewDospem2.innerHTML = inputDospem2.value ? inputDospem2.value : phDospem2;

            // Update Jangka Waktu
            const mulai = formatTanggal(inputMulai.value);
            const selesai = formatTanggal(inputSelesai.value);
            if (mulai && selesai) {
                previewJangkaWaktu.innerHTML = `${mulai} – ${selesai}`;
            } else if (mulai) {
                previewJangkaWaktu.innerHTML = `${mulai} – ...`;
            } else {
                previewJangkaWaktu.innerHTML = phTanggal;
            }

            // Update Tanda Tangan
            previewNamaTtd.innerHTML = inputNama.value || phNama;
            previewNIMTtd.innerHTML = inputNIM.value || phNIM;
            previewDospem1Ttd.innerHTML = inputDospem1.value ? inputDospem1.value : phDospem;
        }

        // [BARU] Fungsi untuk pratinjau Tanda Tangan
        function previewTandaTangan() {
            const file = inputTTD.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewTTDImage.src = e.target.result;
                    previewTTDImage.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                previewTTDImage.src = '';
                previewTTDImage.style.display = 'none';
            }
        }

        // Fungsi untuk inisialisasi dan pasang listener
        function initMagangPreview() {
            // Set tanggal hari ini
            previewTanggal.textContent = formatTanggal(new Date().toISOString().split('T')[0]);

            // Pasang listener ke input
            inputInstansi.addEventListener('input', updatePreview);
            inputJudul.addEventListener('input', updatePreview); // [BARU]
            inputDospem1.addEventListener('change', updatePreview);
            inputDospem2.addEventListener('change', updatePreview); // [BARU]
            inputMulai.addEventListener('change', updatePreview);
            inputSelesai.addEventListener('change', updatePreview);
            inputTTD.addEventListener('change', previewTandaTangan); // [BARU]

            // Panggil sekali saat load untuk mengisi data readonly
            updatePreview();
            
            // Reset gambar ttd jika form di-reset
            const form = inputTTD.closest('form');
            if (form) {
                form.addEventListener('reset', () => {
                    previewTTDImage.src = '';
                    previewTTDImage.style.display = 'none';
                    // Panggil updatePreview untuk reset placeholder
                    // butuh timeout kecil agar reset-nya selesai dulu
                    setTimeout(updatePreview, 0);
                });
            }
        }

        // Note: initMagangPreview() dipanggil dari event 'change' dropdown jenis surat
    });
</script>
@endpush