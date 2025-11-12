@extends('layouts.mahasiswa')

@section('title', 'Pengajuan Surat Baru')

@section('content')

{{-- Menampilkan pesan sukses setelah submit --}}
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- ====================================================== --}}
{{-- 				INI PERBAIKANNYA 				--}}
{{-- ====================================================== --}}
{{-- Menampilkan pesan error dari Controller (try...catch) --}}
@if (session('error'))
    <div class="alert alert-danger">
        <strong>Terjadi Kesalahan:</strong> {{ session('error') }}
    </div>
@endif
{{-- ====================================================== --}}
{{-- 			  AKHIR DARI PERBAIKAN 				--}}
{{-- ====================================================== --}}


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
            <div id="form-surat-magang" class="dynamic-form" style="display: none;">
                <h5 class="mb-3">Formulir Surat Pengantar Magang/KP</h5>
                
                {{-- Data Mahasiswa (Sekarang terisi otomatis dari Auth) --}}
                <div class="row">
                    <div class="col-md-6 mb-3"><label><strong>Nama Mahasiswa</strong></label><input type="text" class="form-control" value="{{ $mahasiswa->Nama_Mahasiswa ?? Auth::user()->Name_User }}" readonly></div>
                    <div class="col-md-6 mb-3"><label><strong>NPM/NIM</strong></label><input type="text" class="form-control" value="{{ $mahasiswa->NIM ?? 'NIM Tidak Ditemukan' }}" readonly></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Jurusan</strong></label>
                    <input type="text" class="form-control" value="{{ $prodi->Nama_Prodi ?? 'Jurusan Tidak Ditemukan' }}" readonly>
                </div>

                {{-- Input Dosen (Sekarang menjadi Dropdown dari tabel 'Dosen') --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="dospem1" class="form-label"><strong>Dosen Pembimbing 1</strong></label>
                        <select class="form-select" name="data_spesifik[dosen_pembimbing_1]" required>
                            <option value="" selected disabled>-- Pilih Dosen Pembimbing 1 --</option>
                            @foreach ($dosens as $dosen)
                                <option value="{{ $dosen->Nama_Dosen }}">{{ $dosen->Nama_Dosen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="dospem2" class="form-label"><strong>Dosen Pembimbing 2</strong></label>
                        <select class="form-select" name="data_spesifik[dosen_pembimbing_2]">
                            <option value="" selected>-- Pilih Dosen Pembimbing 2 --</option>
                            @foreach ($dosens as $dosen)
                                <option value="{{ $dosen->Nama_Dosen }}">{{ $dosen->Nama_Dosen }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Ini semua adalah data yang akan masuk ke 'data_spesifik' (JSON) --}}
                <div class="mb-3">
                    <label for="nama_instansi" class="form-label"><strong>Nama Instansi/Perusahaan Tujuan</strong></label>
                    <input type="text" class="form-control" name="data_spesifik[nama_instansi]" placeholder="Contoh: PT. Google Indonesia" required>
                </div>
                 <div class="mb-3">
                    <label for="alamat_instansi" class="form-label"><strong>Alamat Lengkap Instansi</strong></label>
                    <textarea class="form-control" name="data_spesifik[alamat_instansi]" rows="3" placeholder="Contoh: Jl. Jenderal Sudirman Kav. 52-53, Jakarta Selatan" required></textarea>
                </div>
                 
                {{-- Ini adalah 'file_pendukung' yang akan disimpan ke 'File_Arsip' --}}
                <div class="mb-4">
                    <label for="dokumen_form_kp" class="form-label"><strong>Unggah Form Pengajuan KP</strong></label>
                    <input class="form-control" type="file" name="file_pendukung_magang" required>
                    <div class="form-text">Wajib mengunggah Form Pengajuan yang sudah ditandatangani. Format: PDF (Maks. 2MB).</div>
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
            '3': {
                formId: 'form-surat-aktif',
                route: "{{ route('mahasiswa.pengajuan.aktif.store') }}"
            },
            '6': {
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
                }
            } else {
                // Jika jenis surat tidak ada mapping, kosongkan action
                formPengajuan.action = '';
                alert('Jenis surat ini belum tersedia. Silakan pilih jenis surat lain.');
            }
        });

        // Sembunyikan semua saat halaman baru dimuat
        hideAllDynamicForms();
    });
</script>
@endpush