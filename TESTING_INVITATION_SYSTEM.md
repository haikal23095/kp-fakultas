# Testing Checklist - Invitation System

## Persiapan Testing

### 1. Akun Testing

Butuh minimal 2 akun mahasiswa seprodi:

-   **Mahasiswa A**: Yang membuat pengajuan dan mengundang
-   **Mahasiswa B**: Yang diundang

### 2. Browser Developer Tools

Buka Console (F12) untuk melihat debug log:

```
[DEBUG] Loading draft...
[DEBUG] Saving draft...
[DEBUG] Response status: 200
```

## Test Case 1: Create Draft & Send Invitation

### Langkah Testing:

1. **Login sebagai Mahasiswa A**
2. Buka: `/mahasiswa/pengajuan-surat/magang`
3. **Buka Browser Console** (F12 → Console tab)
4. Isi form:
    - Nama Instansi: `PT. Test Indonesia`
    - Tanggal Mulai: `2025-01-01`
    - Tanggal Selesai: `2025-03-01`
5. Klik tombol **"Tambah Mahasiswa"**
6. Ketik nama Mahasiswa B di autocomplete
7. **Pilih Mahasiswa B dari dropdown**

### Expected Result:

✅ **Console log muncul:**

```
[DEBUG] Saving draft... mahasiswa_id: 123
[DEBUG] Save response status: 200
[DEBUG] Save response data: {success: true, draft_id: 1, message: "..."}
[DEBUG] Draft saved successfully, ID: 1
```

✅ **UI Changes:**

-   Badge kuning **"Menunggu Persetujuan"** muncul di card Mahasiswa B
-   Card opacity 70% (sedikit transparan)
-   Mahasiswa B tidak bisa diedit (disabled)

✅ **Database Check:**

```sql
-- Cek draft tersimpan
SELECT * FROM Surat_Magang_Draft ORDER BY id_draft DESC LIMIT 1;

-- Cek invitation terkirim
SELECT * FROM Surat_Magang_Invitations ORDER BY id_invitation DESC LIMIT 1;
-- Expected: status = 'pending'

-- Cek notifikasi terkirim
SELECT * FROM Notifikasi ORDER BY Id_Notifikasi DESC LIMIT 1;
-- Expected: Tipe_Notifikasi = 'Invitation'
```

### Debugging Jika Gagal:

**A. Invitation tidak terkirim (No notification)**
Cek console error:

-   `CSRF token not found` → Pastikan `<meta name="csrf-token">` ada di layout
-   `500 error` → Cek Laravel log: `storage/logs/laravel.log`

**B. Draft tidak tersimpan**

```bash
# Cek database connection
php artisan tinker
>>> DB::table('Surat_Magang_Draft')->count()
```

**C. Notifikasi kosong**

```sql
-- Cek kolom Data_Tambahan ada
DESCRIBE Notifikasi;
-- Harus ada kolom: Data_Tambahan (json)
```

---

## Test Case 2: Refresh Page (Data Persistence)

### Langkah Testing:

1. **Tetap login sebagai Mahasiswa A**
2. Setelah invite Mahasiswa B (test case 1)
3. **Refresh halaman** (F5 / Ctrl+R)
4. **Buka Console untuk lihat debug log**

### Expected Result:

✅ **Console log muncul:**

```
[DEBUG] Loading draft...
[DEBUG] Response status: 200
[DEBUG] Draft data: {draft: {...}, mahasiswa_pending: [...], ...}
[DEBUG] Draft loaded with ID: 1
[DEBUG] Restoring pending mahasiswa: [{id: 123, nama: "...", status: "pending"}]
```

✅ **UI Restored:**

-   Form fields terisi (Nama Instansi, Tanggal, dll)
-   Mahasiswa B muncul kembali dengan badge **"Menunggu Persetujuan"**
-   Data tidak hilang!

### Debugging Jika Gagal:

**A. Data hilang setelah refresh**

```
[DEBUG] No draft found
```

Kemungkinan:

-   Draft tidak tersimpan (cek test case 1)
-   Query `WHERE Id_Mahasiswa_Pembuat` salah (cek ID mahasiswa login)

**B. Mahasiswa B tidak muncul**

```
[DEBUG] Restoring pending mahasiswa: []
```

Cek database:

```sql
SELECT Data_Mahasiswa_Pending FROM Surat_Magang_Draft
WHERE id_draft = 1;
-- Harus berisi JSON array dengan mahasiswa B
```

---

## Test Case 3: Accept Invitation

### Langkah Testing:

1. **Logout dari Mahasiswa A**
2. **Login sebagai Mahasiswa B**
3. Klik menu **Notifikasi** (icon bell di navbar)
4. Lihat notifikasi undangan dari Mahasiswa A
5. Klik tombol **"Terima"** (hijau)

### Expected Result:

✅ **Alert sukses:**

```
"Undangan berhasil diterima!"
```

✅ **Database Changes:**

```sql
-- Invitation status = 'accepted'
SELECT status FROM Surat_Magang_Invitations WHERE id_invitation = 1;
-- Expected: 'accepted'

-- Mahasiswa B pindah ke Data_Mahasiswa_Confirmed
SELECT Data_Mahasiswa_Confirmed, Data_Mahasiswa_Pending
FROM Surat_Magang_Draft WHERE id_draft = 1;
-- Confirmed: berisi Mahasiswa A + B
-- Pending: kosong []
```

✅ **Notifikasi ke Mahasiswa A:**

```sql
-- Notifikasi baru terkirim
SELECT * FROM Notifikasi
WHERE Dest_User = (ID_USER_MAHASISWA_A)
ORDER BY Id_Notifikasi DESC LIMIT 1;
-- Pesan: "Mahasiswa B menerima undangan magang Anda"
```

---

## Test Case 4: UI Update After Accept

### Langkah Testing:

1. **Logout dari Mahasiswa B**
2. **Login kembali sebagai Mahasiswa A**
3. Buka form magang lagi
4. **Buka Console**
5. Tunggu draft load

### Expected Result:

✅ **Console log:**

```
[DEBUG] Restoring confirmed mahasiswa (skipping first)
```

✅ **UI Update:**

-   Badge berubah dari kuning **"Menunggu Persetujuan"**
-   Menjadi hijau **"Terkonfirmasi"**
-   Card opacity normal (tidak transparan)

---

## Test Case 5: Reject Invitation

### Langkah Testing:

1. **Login sebagai Mahasiswa C** (buat test lagi dengan mahasiswa ketiga)
2. Mahasiswa A invite Mahasiswa C
3. Login sebagai Mahasiswa C
4. Buka notifikasi
5. Klik tombol **"Tolak"** (merah)
6. Isi alasan: `"Saya sudah magang di tempat lain"`
7. Klik **"Tolak Undangan"**

### Expected Result:

✅ **Alert sukses:**

```
"Undangan ditolak"
```

✅ **Database:**

```sql
-- Invitation status = 'rejected'
SELECT status, keterangan FROM Surat_Magang_Invitations
WHERE Id_Mahasiswa_Diundang = (ID_MAHASISWA_C);
-- status: 'rejected'
-- keterangan: "Saya sudah magang di tempat lain"

-- Mahasiswa C dihapus dari pending
SELECT Data_Mahasiswa_Pending FROM Surat_Magang_Draft WHERE id_draft = 1;
-- Tidak ada Mahasiswa C
```

✅ **Notifikasi ke Mahasiswa A:**

```
"Mahasiswa C menolak undangan magang: Saya sudah magang di tempat lain"
```

---

## Test Case 6: Auto-Save Form

### Langkah Testing:

1. Login sebagai Mahasiswa A
2. Buka form magang
3. **Buka Console**
4. Isi field "Nama Instansi"
5. **Tunggu 3 detik** (jangan ketik apa-apa)
6. Lihat console

### Expected Result:

✅ **Console log:**

```
[DEBUG] Saving draft... mahasiswa_id: null
[DEBUG] Save response status: 200
[DEBUG] Draft saved successfully, ID: 1
```

✅ **Setelah 3 detik idle, draft auto-save**

---

## Common Issues & Solutions

### Issue 1: "CSRF token not found"

**Solution:**

```blade
{{-- Pastikan ada di layout --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Issue 2: "Use of unknown class: SuratMagangDraft"

**Solution:**

```bash
composer dump-autoload
php artisan optimize:clear
```

### Issue 3: Draft tidak load setelah refresh

**Debug:**

```bash
php artisan tinker
>>> $draft = \App\Models\SuratMagangDraft::latest()->first();
>>> $draft->Data_Mahasiswa_Pending
>>> $draft->Data_Mahasiswa_Confirmed
```

### Issue 4: Notifikasi tidak muncul

**Check:**

```sql
-- Cek Id_User mahasiswa B
SELECT Id_User FROM Mahasiswa WHERE Id_Mahasiswa = 123;

-- Cek notifikasi
SELECT * FROM Notifikasi WHERE Dest_User = (ID_USER_DARI_QUERY_ATAS);
```

---

## Quick Database Queries

```sql
-- Reset testing (hapus semua draft dan invitation)
DELETE FROM Surat_Magang_Invitations;
DELETE FROM Surat_Magang_Draft;
DELETE FROM Notifikasi WHERE Tipe_Notifikasi = 'Invitation';

-- Lihat semua draft
SELECT
    id_draft,
    Id_Mahasiswa_Pembuat,
    Nama_Instansi,
    Data_Mahasiswa_Pending,
    Data_Mahasiswa_Confirmed,
    created_at
FROM Surat_Magang_Draft
ORDER BY id_draft DESC;

-- Lihat semua invitation
SELECT
    i.id_invitation,
    i.status,
    m1.Nama_Mahasiswa as Pengundang,
    m2.Nama_Mahasiswa as Diundang,
    i.invited_at,
    i.responded_at
FROM Surat_Magang_Invitations i
JOIN Mahasiswa m1 ON i.Id_Mahasiswa_Pengundang = m1.Id_Mahasiswa
JOIN Mahasiswa m2 ON i.Id_Mahasiswa_Diundang = m2.Id_Mahasiswa
ORDER BY i.id_invitation DESC;

-- Lihat notifikasi invitation
SELECT
    n.Id_Notifikasi,
    n.Tipe_Notifikasi,
    n.Pesan,
    u1.Name_User as Pengirim,
    u2.Name_User as Penerima,
    n.Is_Read,
    n.Data_Tambahan,
    n.created_at
FROM Notifikasi n
JOIN User u1 ON n.Source_User = u1.Id_User
JOIN User u2 ON n.Dest_User = u2.Id_User
WHERE n.Tipe_Notifikasi = 'Invitation'
ORDER BY n.Id_Notifikasi DESC;
```

---

## Success Criteria

✅ **Sistem berfungsi jika:**

1. Invite mahasiswa → Notifikasi terkirim ✓
2. Refresh page → Data tidak hilang ✓
3. Accept invitation → Badge berubah hijau ✓
4. Reject invitation → Mahasiswa dihapus dari list ✓
5. Auto-save berfungsi setiap 3 detik ✓
6. Console log menunjukkan semua proses ✓

---

## Next Step After Testing

Jika semua test pass:

1. Hapus console.log yang tidak perlu (untuk production)
2. Tambah loading indicator (spinner saat save)
3. Tambah toast notification ("Draft tersimpan")
4. Implement submit final (convert draft ke Surat_Magang)
