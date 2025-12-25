# TODO: Implementasi Lengkap Sistem Persetujuan Dekan

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

### ✅ 3. Legalisir Ijazah/Transkrip (SUDAH JADI)

-   [x] Tabel database
-   [x] Model
-   [x] Controller method
-   [x] Routes
-   [x] View card
-   [x] View detail

---

### ⏳ 4. Surat Cuti Dosen (TODO)

**Proses Bisnis:**

1. Menerima, mencatat, dan memberi nomor disposisi pada surat masuk
2. Mendisposisikan surat ke Wakil Dekan II
3. Menandatangani Surat Ijin Cuti

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_surat_cuti_dosen_table.php`

    ```php
    Schema::create('surat_cuti_dosen', function (Blueprint $table) {
        $table->id('Id_Surat_Cuti');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('tugas_surat');
        $table->foreignId('Id_Dosen')->references('Id_Dosen')->on('dosen');
        $table->string('Nomor_Disposisi')->nullable();
        $table->date('Tanggal_Mulai_Cuti');
        $table->date('Tanggal_Selesai_Cuti');
        $table->text('Alasan_Cuti');
        $table->enum('Status', ['pending', 'disposisi-wakil', 'menunggu-ttd', 'disetujui', 'ditolak'])->default('pending');
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SuratCutiDosen.php`
-   [ ] **Tambah relasi di Model TugasSurat:**

    ```php
    public function suratCutiDosen() {
        return $this->hasOne(SuratCutiDosen::class, 'Id_Tugas_Surat');
    }
    ```

-   [ ] **Update Controller:** `PersetujuanSuratController::listCutiDosen()`

    -   Ganti `Id_Jenis_Surat` dari 99 ke ID yang benar (misal: 4)
    -   Tambah relasi `'suratCutiDosen'` di eager loading
    -   Update query untuk cek status

-   [ ] **View Detail:** Bisa reuse `dekan/persetujuan_surat.blade.php` atau buat khusus

-   [ ] **Tambah di Seeder:** `database/seeders/JenisSuratSeeder.php`
    ```php
    JenisSurat::create(['Id_Jenis_Surat' => 4, 'Nama_Surat' => 'Surat Cuti Dosen']);
    ```

---

### ⏳ 5. Surat Keterangan Tidak Menerima Beasiswa (TODO)

**Proses Bisnis:**

1. Menerima draft surat dari Admin
2. Memverifikasi dan menandatangani Surat Keterangan

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_surat_tidak_beasiswa_table.php`

    ```php
    Schema::create('surat_tidak_beasiswa', function (Blueprint $table) {
        $table->id('Id_Surat_Tidak_Beasiswa');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('tugas_surat');
        $table->foreignId('Id_Mahasiswa')->references('Id_Mahasiswa')->on('mahasiswa');
        $table->string('Semester');
        $table->year('Tahun_Akademik');
        $table->text('Keterangan')->nullable();
        $table->enum('Status', ['draft', 'menunggu-ttd', 'disetujui', 'ditolak'])->default('draft');
        $table->string('Nomor_Surat')->nullable();
        $table->text('QR_Code_Dekan')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SuratTidakBeasiswa.php`
-   [ ] **Update Controller:** `PersetujuanSuratController::listTidakBeasiswa()`
-   [ ] **Tambah di Seeder:** Id_Jenis_Surat: 5

---

### ⏳ 6. SK Fakultas Teknik (TODO)

**Proses Bisnis:**

1. Mengeluarkan dan menandatangani Surat Keputusan (SK)

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_sk_fakultas_table.php`

    ```php
    Schema::create('sk_fakultas', function (Blueprint $table) {
        $table->id('Id_SK_Fakultas');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('tugas_surat');
        $table->string('Nomor_SK')->nullable();
        $table->string('Perihal');
        $table->text('Isi_SK');
        $table->json('Pihak_Terkait')->nullable(); // Array of users/dosen/pegawai
        $table->date('Tanggal_Berlaku');
        $table->date('Tanggal_Berakhir')->nullable();
        $table->enum('Status', ['draft', 'review', 'menunggu-ttd', 'diterbitkan'])->default('draft');
        $table->text('QR_Code_Dekan')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SKFakultas.php`
-   [ ] **Update Controller:** `PersetujuanSuratController::listSKFakultas()`
-   [ ] **Tambah di Seeder:** Id_Jenis_Surat: 6

---

### ⏳ 7. Surat Tugas (TODO)

**Proses Bisnis:**

1. Mengeluarkan Surat Tugas untuk dosen/pegawai

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_surat_tugas_dosen_table.php`

    ```php
    Schema::create('surat_tugas_dosen', function (Blueprint $table) {
        $table->id('Id_Surat_Tugas_Dosen');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('tugas_surat');
        $table->string('Nomor_Surat')->nullable();
        $table->json('Penerima_Tugas'); // Array of Id_Dosen atau Id_Pegawai
        $table->string('Jenis_Tugas'); // Mengajar, Penelitian, Pengabdian, dll
        $table->text('Deskripsi_Tugas');
        $table->date('Tanggal_Mulai');
        $table->date('Tanggal_Selesai');
        $table->string('Lokasi')->nullable();
        $table->enum('Status', ['draft', 'review', 'menunggu-ttd', 'diterbitkan'])->default('draft');
        $table->text('QR_Code_Dekan')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SuratTugasDosen.php`
-   [ ] **Update Controller:** `PersetujuanSuratController::listSuratTugas()`
-   [ ] **Tambah di Seeder:** Id_Jenis_Surat: 7

---

### ⏳ 8. Surat Rekomendasi MBKM (TODO)

**Proses Bisnis:**

1. Menandatangani Surat Rekomendasi program MBKM

**Yang Perlu Dibuat:**

-   [ ] **Migration:** `create_surat_rekomendasi_mbkm_table.php`

    ```php
    Schema::create('surat_rekomendasi_mbkm', function (Blueprint $table) {
        $table->id('Id_Surat_MBKM');
        $table->foreignId('Id_Tugas_Surat')->references('Id_Tugas_Surat')->on('tugas_surat');
        $table->foreignId('Id_Mahasiswa')->references('Id_Mahasiswa')->on('mahasiswa');
        $table->string('Nomor_Surat')->nullable();
        $table->string('Program_MBKM'); // Magang, Kampus Mengajar, dll
        $table->string('Nama_Mitra');
        $table->text('Alamat_Mitra');
        $table->date('Tanggal_Mulai');
        $table->date('Tanggal_Selesai');
        $table->text('Rekomendasi_Dekan')->nullable();
        $table->enum('Status', ['draft', 'review', 'menunggu-ttd', 'disetujui', 'ditolak'])->default('draft');
        $table->text('QR_Code_Dekan')->nullable();
        $table->timestamps();
    });
    ```

-   [ ] **Model:** `app/Models/SuratRekomendasiMBKM.php`
-   [ ] **Update Controller:** `PersetujuanSuratController::listMBKM()`
-   [ ] **Tambah di Seeder:** Id_Jenis_Surat: 8

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

    - Tambahkan entry baru di `database/seeders/DatabaseSeeder.php` atau buat JenisSuratSeeder

4. **Update Controller**

    - File: `app/Http/Controllers/Dekan/PersetujuanSuratController.php`
    - Ganti Id_Jenis_Surat dari 99 ke ID yang benar
    - Tambahkan eager loading untuk relasi baru
    - Update counting di method `index()`

5. **Update Model TugasSurat**

    - Tambahkan relasi `hasOne` untuk setiap jenis surat baru

6. **Test Routes**
    - Pastikan semua routes berfungsi
    - Test apakah card bisa diklik dan menampilkan data

---

## 📁 File-File yang Sudah Disiapkan

### Views:

-   ✅ `resources/views/dekan/persetujuan_index.blade.php` - Halaman dengan 8 card
-   ✅ `resources/views/dekan/persetujuan_surat.blade.php` - View untuk list surat (reusable)

### Controllers:

-   ✅ `app/Http/Controllers/Dekan/PersetujuanSuratController.php`
    -   Method `index()` - Hitung semua counter
    -   Method `listAktif()` - Sudah jadi
    -   Method `listMagang()` - Sudah jadi
    -   Method `listLegalisir()` - Sudah jadi
    -   Method `listCutiDosen()` - TODO placeholder
    -   Method `listTidakBeasiswa()` - TODO placeholder
    -   Method `listSKFakultas()` - TODO placeholder
    -   Method `listSuratTugas()` - TODO placeholder
    -   Method `listMBKM()` - TODO placeholder

### Routes:

-   ✅ Semua routes sudah didefinisikan di `routes/web.php`

---

## 🎨 Catatan Penting

1. **ID Jenis Surat:**

    - 1 = Surat Keterangan Aktif
    - 2 = Surat Pengantar Magang/KP
    - 3 = Legalisir
    - 4-8 = Untuk jenis surat baru (sesuaikan dengan kebutuhan)

2. **View Reusable:**

    - `persetujuan_surat.blade.php` bisa digunakan untuk semua jenis surat
    - Atau buat view khusus jika butuh tampilan berbeda

3. **Status Flow:**

    - Sesuaikan enum `Status` di migration dengan alur bisnis masing-masing surat

4. **QR Code:**
    - Jangan lupa tambahkan kolom `QR_Code_Dekan` untuk tanda tangan digital

---

## 🚀 Quick Start untuk Implementasi

Contoh lengkap untuk Surat Cuti Dosen:

```bash
# 1. Buat migration
php artisan make:migration create_surat_cuti_dosen_table

# 2. Edit migration file (lihat contoh di atas)

# 3. Buat model
php artisan make:model SuratCutiDosen

# 4. Edit model, tambahkan fillable dan relasi

# 5. Update TugasSurat model
# Tambah: public function suratCutiDosen() { ... }

# 6. Update seeder
# Tambah: JenisSurat::create(['Id_Jenis_Surat' => 4, 'Nama_Surat' => 'Surat Cuti Dosen']);

# 7. Run migration dan seeder
php artisan migrate
php artisan db:seed

# 8. Update PersetujuanSuratController
# Di method index(): tambahkan counting untuk cuti dosen
# Di method listCutiDosen(): ganti Id_Jenis_Surat dari 99 ke 4

# 9. Test di browser
# Kunjungi: /dekan/persetujuan-surat
# Klik card "Surat Cuti Dosen"
```

---

## 💡 Tips

-   Copy-paste struktur dari surat yang sudah jadi (misal: SuratMagang) untuk mempercepat
-   Gunakan migration rollback jika ada kesalahan: `php artisan migrate:rollback`
-   Test setiap jenis surat satu per satu
-   Buat seeder untuk data dummy agar mudah testing

---

**Draft ini sudah 50% jadi!** UI, routing, dan struktur controller sudah siap. Tinggal implementasi database dan logika bisnis untuk 5 jenis surat baru. 🎉
