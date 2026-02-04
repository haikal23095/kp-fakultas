# Struktur Views Mahasiswa

Struktur folder views mahasiswa telah diorganisir berdasarkan proses bisnis:

## 📁 Struktur Folder

```
mahasiswa/
├── pengajuan/                    # 📝 Form Pengajuan Surat
│   ├── form_surat_aktif.blade.php
│   ├── form_surat_rekomendasi.blade.php
│   ├── form_legalisir.blade.php
│   ├── legalisir.blade.php
│   ├── form_surat_dispensasi.blade.php
│   ├── form_surat_kelakuan_baik.blade.php
│   ├── form_surat_tidak_beasiswa.blade.php
│   ├── form_peminjaman_mobil.blade.php
│   ├── form_izin_malam.blade.php
│   └── form_surat_magang.blade.php
│
├── riwayat/                      # 📋 Riwayat & Status Surat
│   ├── index.blade.php          # Halaman utama riwayat (semua jenis)
│   ├── aktif.blade.php          # Riwayat surat keterangan aktif
│   ├── magang.blade.php         # Riwayat surat magang/KP
│   ├── legalisir.blade.php      # Riwayat legalisir
│   ├── berkelakuan_baik.blade.php
│   ├── dispensasi.blade.php
│   ├── tidak_beasiswa.blade.php
│   ├── mobil_dinas.blade.php
│   └── generic.blade.php        # Template riwayat umum
│
├── magang/                       # 🤝 Fitur Khusus Magang
│   └── ajakan_magang.blade.php  # Undangan magang kelompok
│
├── pdf/                          # 📄 Template PDF
│   └── (template PDF untuk generate surat)
│
└── pilih_jenis_surat.blade.php  # 🏠 Menu Utama - Pilih Jenis Surat
```

## 🔄 Migrasi Path

Jika ada controller yang masih menggunakan path lama, perlu diupdate:

### Path Lama → Path Baru

**Pengajuan:**

- `mahasiswa.form_surat_aktif` → `mahasiswa.pengajuan.form_surat_aktif`
- `mahasiswa.form_surat_magang` → `mahasiswa.pengajuan.form_surat_magang`
- `mahasiswa.form_legalisir` → `mahasiswa.pengajuan.form_legalisir`
- `mahasiswa.form_surat_dispensasi` → `mahasiswa.pengajuan.form_surat_dispensasi`
- `mahasiswa.form_surat_kelakuan_baik` → `mahasiswa.pengajuan.form_surat_kelakuan_baik`
- `mahasiswa.form_surat_tidak_beasiswa` → `mahasiswa.pengajuan.form_surat_tidak_beasiswa`
- `mahasiswa.form_peminjaman_mobil` → `mahasiswa.pengajuan.form_peminjaman_mobil`

**Riwayat:**

- `mahasiswa.riwayat` → `mahasiswa.riwayat.index`
- `mahasiswa.riwayat_aktif` → `mahasiswa.riwayat.aktif`
- `mahasiswa.riwayat_magang` → `mahasiswa.riwayat.magang`
- `mahasiswa.riwayat_legalisir` → `mahasiswa.riwayat.legalisir`
- `mahasiswa.riwayat_berkelakuan_baik` → `mahasiswa.riwayat.berkelakuan_baik`
- `mahasiswa.riwayat_dispensasi` → `mahasiswa.riwayat.dispensasi`
- `mahasiswa.riwayat_tidak_beasiswa` → `mahasiswa.riwayat.tidak_beasiswa`
- `mahasiswa.peminjaman_mobil.riwayat` → `mahasiswa.riwayat.mobil_dinas`

## 📝 Catatan

- Folder `pengajuan-surat/` dan `peminjaman_mobil/` sudah dihapus karena redundan
- Semua form pengajuan sekarang terpusat di folder `pengajuan/`
- Semua halaman riwayat terpusat di folder `riwayat/`
- File `pilih_jenis_surat.blade.php` tetap di root karena merupakan menu utama

## ⚠️ View yang Perlu Dibuat

### Peminjaman Mobil Dinas

1. **Method show() - TIDAK DIPERLUKAN** ✅
    - Method `show()` di PeminjamanMobilController sudah di-comment karena tidak digunakan
    - Detail peminjaman ditampilkan via modal di `mahasiswa/riwayat/mobil_dinas.blade.php`
    - Route `mahasiswa.peminjaman.mobil.show` bisa dihapus dari routes/web.php

2. **Preview Surat - PERLU DIBUAT** ⚠️
    - File: `mahasiswa/pdf/peminjaman_mobil.blade.php`
    - Fungsi: Preview HTML surat peminjaman mobil sebelum download
    - Dipanggil dari: PeminjamanMobilController->previewSurat()
    - Sementara preview langsung download file
