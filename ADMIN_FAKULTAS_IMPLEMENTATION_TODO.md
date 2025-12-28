# TODO: Implementasi Lengkap Sistem Manajemen Surat Admin Fakultas

## Status: Draft Setengah Jadi ✅

UI dan struktur routing sudah dibuat. Tinggal implementasi database dan logika bisnis.

---

## 📋 Checklist Implementasi Per Jenis Surat

### ✅ 1. Surat Keterangan Aktif (SUDAH JADI)

-   [x] Tabel database
-   [x] Model
-   [x] Controller method
-   [x] Routes
-   [x] View card
-   [x] View detail

### ✅ 2. Surat Pengantar KP/Magang (SUDAH JADI)

-   [x] Tabel database
-   [x] Model
-   [x] Controller method
-   [x] Routes
-   [x] View card
-   [x] View detail

### ✅ 3. Legalisir Ijazah/Transkrip (SUDAH JADI - JANGAN DISENTUH)

**NOTE: Teman yang handle, jangan diubah!**

-   [x] Tabel database
-   [x] Model
-   [x] Controller method
-   [x] Routes
-   [x] View card
-   [x] View detail

---

### ⏳ 4. Peminjaman Mobil Dinas (TODO)

**Proses Bisnis:**

1. Memeriksa jadwal & kesiapan mobil dinas
2. Menganalisis kebutuhan & memberi rekomendasi
3. Membuat surat peminjaman

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_peminjaman_mobil_dinas_table.php`

    ```php
    Schema::create('peminjaman_mobil_dinas', function (Blueprint $table) {
        $table->id('Id_Peminjaman_Mobil');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('Tugas_Surat');
        $table->foreignId('Id_Pemohon')->references('Id_User')->on('Users'); // Dosen/Pegawai
        $table->string('Tujuan_Peminjaman');
        $table->text('Keperluan');
        $table->date('Tanggal_Mulai');
        $table->date('Tanggal_Selesai');
        $table->time('Jam_Berangkat');
        $table->time('Jam_Kembali')->nullable();
        $table->string('Tujuan_Lokasi');
        $table->integer('Jumlah_Penumpang')->default(1);
        $table->string('Nomor_Polisi_Mobil')->nullable();
        $table->string('Driver')->nullable();
        $table->enum('Status', ['pending', 'cek_jadwal', 'tersedia', 'disetujui', 'digunakan', 'selesai', 'ditolak'])->default('pending');
        $table->text('Catatan_Admin')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/PeminjamanMobilDinas.php`
-   [ ] **Update Controller:** `ManajemenSuratController::listMobilDinas()`
-   [ ] **Tambah di Seeder:** Id_Jenis_Surat baru

---

### ⏳ 5. Surat Cuti (TODO)

**Proses Bisnis:**

1. Menerima, mencatat, dan memberi nomor/memo disposisi
2. Menyerahkan surat ke Wakil Dekan II

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_surat_cuti_table.php`

    ```php
    Schema::create('surat_cuti', function (Blueprint $table) {
        $table->id('Id_Surat_Cuti');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('Tugas_Surat');
        $table->foreignId('Id_Pemohon')->references('Id_User')->on('Users'); // Dosen/Pegawai
        $table->enum('Jenis_Cuti', ['Tahunan', 'Sakit', 'Besar', 'Melahirkan', 'Alasan Penting', 'Lainnya']);
        $table->date('Tanggal_Mulai_Cuti');
        $table->date('Tanggal_Selesai_Cuti');
        $table->integer('Lama_Cuti'); // dalam hari
        $table->text('Alasan_Cuti');
        $table->string('Nomor_Disposisi')->nullable();
        $table->date('Tanggal_Disposisi')->nullable();
        $table->enum('Status', ['pending', 'dicatat', 'disposisi', 'ke_wadek2', 'disetujui', 'ditolak'])->default('pending');
        $table->text('Catatan')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SuratCuti.php`
-   [ ] **Update Controller:** `ManajemenSuratController::listCuti()`
-   [ ] **Tambah di Seeder:** Id_Jenis_Surat baru

---

### ⏳ 6. Surat Keterangan Tidak Menerima Beasiswa (TODO)

**Proses Bisnis:**

1. Menerima form pengajuan
2. Memproses data, membuat, dan mengarsipkan Surat Keterangan
3. Mengajukan draft surat ke Dekan

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_surat_tidak_beasiswa_table.php`

    ```php
    Schema::create('surat_tidak_beasiswa', function (Blueprint $table) {
        $table->id('Id_Surat_Tidak_Beasiswa');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('Tugas_Surat');
        $table->foreignId('Id_Mahasiswa')->references('Id_Mahasiswa')->on('Mahasiswa');
        $table->string('Semester_Sekarang');
        $table->year('Tahun_Akademik');
        $table->text('Keterangan_Tambahan')->nullable();
        $table->string('Nomor_Surat')->nullable();
        $table->enum('Status', ['pending', 'diproses', 'draft', 'ke_dekan', 'disetujui', 'selesai', 'ditolak'])->default('pending');
        $table->text('Path_File_Draft')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SuratTidakBeasiswa.php`
-   [ ] **Update Controller:** `ManajemenSuratController::listTidakBeasiswa()`

---

### ⏳ 7. Surat Dispensasi (TODO)

**Proses Bisnis:**

1. Memproses & memverifikasi berkas permohonan
2. Memberi nomor surat, stempel, & mengarsipkan
3. Menyerahkan Surat Dispensasi ke Mahasiswa

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_surat_dispensasi_table.php`

    ```php
    Schema::create('surat_dispensasi', function (Blueprint $table) {
        $table->id('Id_Surat_Dispensasi');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('Tugas_Surat');
        $table->foreignId('Id_Mahasiswa')->references('Id_Mahasiswa')->on('Mahasiswa');
        $table->string('Keperluan_Dispensasi'); // Sakit, Kepentingan Keluarga, dll
        $table->date('Tanggal_Mulai');
        $table->date('Tanggal_Selesai');
        $table->text('Alasan_Detail');
        $table->string('Path_Surat_Dokter')->nullable(); // Jika sakit
        $table->string('Nomor_Surat')->nullable();
        $table->enum('Status', ['pending', 'verifikasi', 'diproses', 'siap_diambil', 'selesai', 'ditolak'])->default('pending');
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SuratDispensasi.php`
-   [ ] **Update Controller:** `ManajemenSuratController::listDispensasi()`

---

### ⏳ 8. Surat Keterangan Berkelakuan Baik (TODO)

**Proses Bisnis:**

1. Memproses permohonan dari mahasiswa
2. Menyerahkan surat kepada mahasiswa

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_surat_berkelakuan_baik_table.php`

    ```php
    Schema::create('surat_berkelakuan_baik', function (Blueprint $table) {
        $table->id('Id_Surat_Berkelakuan');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('Tugas_Surat');
        $table->foreignId('Id_Mahasiswa')->references('Id_Mahasiswa')->on('Mahasiswa');
        $table->string('Keperluan'); // Beasiswa, Magang, Pekerjaan, dll
        $table->text('Keterangan_Tambahan')->nullable();
        $table->string('Nomor_Surat')->nullable();
        $table->enum('Status', ['pending', 'diproses', 'siap_diambil', 'selesai', 'ditolak'])->default('pending');
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SuratBerkelakuanBaik.php`
-   [ ] **Update Controller:** `ManajemenSuratController::listBerkelakuanBaik()`

---

### ⏳ 9. SK Fakultas Teknik (TODO)

**Proses Bisnis:**

1. Memeriksa, menganalisis maksud/tujuan, & memproses surat
2. Menyerahkan rekomendasi ke pimpinan
3. Menerima SK final & mengarsipkan

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_sk_fakultas_table.php`

    ```php
    Schema::create('sk_fakultas', function (Blueprint $table) {
        $table->id('Id_SK_Fakultas');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('Tugas_Surat');
        $table->string('Nomor_SK')->nullable();
        $table->string('Perihal');
        $table->text('Maksud_Tujuan');
        $table->text('Isi_SK')->nullable();
        $table->json('Pihak_Terkait')->nullable(); // Daftar yang terlibat
        $table->date('Tanggal_Berlaku');
        $table->date('Tanggal_Berakhir')->nullable();
        $table->enum('Status', ['pending', 'analisis', 'rekomendasi', 'ke_pimpinan', 'diterbitkan', 'arsip', 'ditolak'])->default('pending');
        $table->text('Catatan_Admin')->nullable();
        $table->string('Path_File_SK')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SKFakultas.php`
-   [ ] **Update Controller:** `ManajemenSuratController::listSKFakultas()`

---

### ⏳ 10. Peminjaman Gedung & Ruang (TODO)

**Proses Bisnis:**

1. Menyediakan informasi jadwal peminjaman gedung
2. Memproses rekomendasi peminjaman ke BUK
3. Meneruskan surat ke BUK atau PD II
4. Menerima, mencatat & memberi lembar disposisi

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_peminjaman_gedung_table.php`

    ```php
    Schema::create('peminjaman_gedung', function (Blueprint $table) {
        $table->id('Id_Peminjaman_Gedung');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('Tugas_Surat');
        $table->foreignId('Id_Pemohon')->references('Id_User')->on('Users');
        $table->string('Nama_Gedung_Ruang');
        $table->string('Keperluan_Acara');
        $table->date('Tanggal_Mulai');
        $table->date('Tanggal_Selesai');
        $table->time('Jam_Mulai');
        $table->time('Jam_Selesai');
        $table->integer('Perkiraan_Peserta')->nullable();
        $table->text('Detail_Kebutuhan')->nullable();
        $table->string('Nomor_Disposisi')->nullable();
        $table->enum('Status', ['pending', 'cek_jadwal', 'tersedia', 'rekomendasi_buk', 'ke_pd2', 'disetujui', 'ditolak'])->default('pending');
        $table->text('Catatan')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/PeminjamanGedung.php`
-   [ ] **Update Controller:** `ManajemenSuratController::listPeminjamanGedung()`

---

### ⏳ 11. Lembur Jam Malam & Hari Libur (TODO)

**Proses Bisnis:**

1. Menerima, mencatat & memberi disposisi
2. Menyerahkan surat ke Wakil Dekan II

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_surat_lembur_table.php`

    ```php
    Schema::create('surat_lembur', function (Blueprint $table) {
        $table->id('Id_Surat_Lembur');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('Tugas_Surat');
        $table->json('Daftar_Pegawai'); // Array Id_User pegawai yang lembur
        $table->date('Tanggal_Lembur');
        $table->time('Jam_Mulai');
        $table->time('Jam_Selesai');
        $table->enum('Jenis_Lembur', ['Jam Malam', 'Hari Libur', 'Hari Libur Nasional']);
        $table->text('Keperluan_Pekerjaan');
        $table->string('Nomor_Disposisi')->nullable();
        $table->enum('Status', ['pending', 'dicatat', 'disposisi', 'ke_wadek2', 'disetujui', 'ditolak'])->default('pending');
        $table->text('Catatan')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SuratLembur.php`
-   [ ] **Update Controller:** `ManajemenSuratController::listLembur()`

---

## 🔧 Langkah-Langkah Implementasi

### Untuk Setiap Jenis Surat Baru:

1. **Buat Migration**

    ```bash
    php artisan make:migration create_[nama_tabel]_table
    ```

2. **Buat Model**

    ```bash
    php artisan make:model [NamaModel]
    ```

3. **Update Seeder JenisSurat**

    - Tambahkan entry baru untuk jenis surat

4. **Update Controller**

    - File: `app/Http/Controllers/Admin_Fakultas/ManajemenSuratController.php`
    - Update method `index()` untuk counting
    - Update method `list[NamaSurat]()` dengan query yang benar

5. **Update Model TugasSurat**

    - Tambahkan relasi `hasOne` untuk setiap jenis surat baru

6. **Test Routes**
    - Pastikan semua routes berfungsi
    - Test apakah card bisa diklik

---

## 📁 File-File yang Sudah Disiapkan

### Views:

-   ✅ `resources/views/admin_fakultas/manajemen_surat_index.blade.php` - Halaman dengan 11 card
-   ✅ `resources/views/admin_fakultas/list_surat_general.blade.php` - View untuk list surat (reusable)

### Controllers:

-   ✅ `app/Http/Controllers/Admin_Fakultas/ManajemenSuratController.php`
    -   Method `index()` - Hitung semua counter
    -   Method `listMobilDinas()` - TODO placeholder
    -   Method `listCuti()` - TODO placeholder
    -   Method `listTidakBeasiswa()` - TODO placeholder
    -   Method `listDispensasi()` - TODO placeholder
    -   Method `listBerkelakuanBaik()` - TODO placeholder
    -   Method `listSKFakultas()` - TODO placeholder
    -   Method `listPeminjamanGedung()` - TODO placeholder
    -   Method `listLembur()` - TODO placeholder

### Routes:

-   ✅ Semua routes sudah didefinisikan di `routes/web.php`

---

## 🎨 Catatan Penting

1. **Legalisir JANGAN DISENTUH!** ✋

    - Teman yang handle
    - Sudah ada route, controller, view sendiri
    - Tetap di card tapi jangan ubah apapun

2. **ID Jenis Surat:**

    - 1 = Surat Keterangan Aktif
    - 2 = Surat Pengantar Magang/KP
    - 14 = Legalisir
    - Untuk jenis baru, tentukan ID yang belum dipakai

3. **View Reusable:**

    - `list_surat_general.blade.php` bisa digunakan untuk semua jenis surat
    - Atau buat view khusus jika butuh tampilan berbeda

4. **Status Flow:**
    - Sesuaikan enum `Status` di migration dengan alur bisnis masing-masing surat

---

**Draft ini sudah 50% jadi!** UI, routing, dan struktur controller sudah siap. Tinggal implementasi database dan logika bisnis untuk 8 jenis surat baru. 🎉
