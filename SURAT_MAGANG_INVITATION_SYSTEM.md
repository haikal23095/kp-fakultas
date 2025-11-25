# Sistem Invitation untuk Surat Magang/KP

## Overview

Sistem ini memungkinkan mahasiswa untuk mengundang teman seprodi untuk bergabung dalam pengajuan surat magang/KP. **Undangan terkirim otomatis saat memilih mahasiswa dari autocomplete**. Data disimpan sebagai draft dan persisten saat refresh.

## Flow Sistem

### 1. Mahasiswa Membuat Pengajuan & Menambah Tim

````
1. Mahasiswa A buka form magang
2. Klik tombol "Tambah Mahasiswa"
3. Ketik nama/NIM di autocomplete → Pilih Mahasiswa B
4. ✅ OTOMATIS: Draft tersimpan + Invitation terkirim (status: pending)
5. Notifikasi muncul di akun Mahasiswa B
6. Mahasiswa B terlihat dengan badge "Menunggu Persetujuan" (kuning)

## Database Schema

### 1. Tabel `Surat_Magang_Draft`

Menyimpan draft sementara pengajuan surat magang.

```sql
CREATE TABLE Surat_Magang_Draft (
    id_draft BIGINT PRIMARY KEY AUTO_INCREMENT,
    Id_Mahasiswa_Pembuat INT NOT NULL,
    Id_Jenis_Surat INT NOT NULL,
    Nama_Instansi VARCHAR(255),
    Alamat_Instansi TEXT,
    Tanggal_Mulai DATE,
    Tanggal_Selesai DATE,
    Judul_Penelitian VARCHAR(500),
    Dosen_Pembimbing_1 VARCHAR(255),
    Dosen_Pembimbing_2 VARCHAR(255),
    File_Proposal VARCHAR(500),
    File_TTD VARCHAR(500),
    Data_Mahasiswa_Confirmed JSON,  -- Mahasiswa yang sudah accept
    Data_Mahasiswa_Pending JSON,    -- Mahasiswa yang masih pending
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (Id_Mahasiswa_Pembuat) REFERENCES Mahasiswa(Id_Mahasiswa),
    FOREIGN KEY (Id_Jenis_Surat) REFERENCES Jenis_Surat(Id_Jenis_Surat)
);
````

### 2. Tabel `Surat_Magang_Invitations`

Tracking undangan ke mahasiswa lain.

```sql
CREATE TABLE Surat_Magang_Invitations (
    id_invitation BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_draft BIGINT NOT NULL,
    Id_Mahasiswa_Diundang INT NOT NULL,
    Id_Mahasiswa_Pengundang INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    keterangan TEXT,  -- Alasan jika reject
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    FOREIGN KEY (id_draft) REFERENCES Surat_Magang_Draft(id_draft) ON DELETE CASCADE,
    FOREIGN KEY (Id_Mahasiswa_Diundang) REFERENCES Mahasiswa(Id_Mahasiswa),
    FOREIGN KEY (Id_Mahasiswa_Pengundang) REFERENCES Mahasiswa(Id_Mahasiswa),
    INDEX idx_mahasiswa_status (Id_Mahasiswa_Diundang, status)
);
```

## Flow Sistem

### 1. Membuat Draft dan Mengundang Mahasiswa

```
Mahasiswa A mengisi form → Click "Tambah Mahasiswa" →
Pilih Mahasiswa B dari autocomplete →
Sistem save draft + create invitation (status: pending) →
Notifikasi ke Mahasiswa B
```

**Backend (Controller):**

```php
public function saveDraft(Request $request)
{
    DB::beginTransaction();

    try {
        // 1. Save draft
        $draft = SuratMagangDraft::updateOrCreate(
            [
                'Id_Mahasiswa_Pembuat' => auth()->user()->mahasiswa->Id_Mahasiswa,
                'id_draft' => $request->draft_id // null untuk draft baru
            ],
            [
                'Id_Jenis_Surat' => $request->Id_Jenis_Surat,
                'Nama_Instansi' => $request->nama_instansi,
                // ... data lainnya
                'Data_Mahasiswa_Confirmed' => [
                    [
                        'id' => auth()->user()->mahasiswa->Id_Mahasiswa,
                        'nama' => auth()->user()->mahasiswa->Nama_Mahasiswa,
                        'nim' => auth()->user()->mahasiswa->NIM,
                        'angkatan' => auth()->user()->mahasiswa->Angkatan,
                        'status' => 'confirmed'
                    ]
                ],
                'Data_Mahasiswa_Pending' => $request->mahasiswa_pending // dari form
            ]
        );

        // 2. Create invitations untuk mahasiswa yang diundang
        foreach ($request->mahasiswa_pending as $mhs) {
            $invitation = SuratMagangInvitation::create([
                'id_draft' => $draft->id_draft,
                'Id_Mahasiswa_Diundang' => $mhs['id'],
                'Id_Mahasiswa_Pengundang' => auth()->user()->mahasiswa->Id_Mahasiswa,
                'status' => 'pending'
            ]);

            // 3. Create notification
            Notifikasi::create([
                'Tipe_Notifikasi' => 'invitation',
                'Pesan' => auth()->user()->mahasiswa->Nama_Mahasiswa .
                          ' mengundang Anda untuk bergabung dalam pengajuan magang ke ' .
                          $request->nama_instansi,
                'Dest_User' => $mhs['id_user'],
                'Source_User' => auth()->user()->Id_User,
                'Is_Read' => false
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'draft_id' => $draft->id_draft,
            'message' => 'Draft berhasil disimpan dan undangan terkirim'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
```

### 2. Load Draft Saat Refresh

**Backend:**

```php
public function getDraft($draftId)
{
    $draft = SuratMagangDraft::with(['invitations.mahasiswaDiundang'])
        ->where('id_draft', $draftId)
        ->where('Id_Mahasiswa_Pembuat', auth()->user()->mahasiswa->Id_Mahasiswa)
        ->first();

    if (!$draft) {
        return response()->json(['error' => 'Draft not found'], 404);
    }

    return response()->json([
        'draft' => $draft,
        'mahasiswa_confirmed' => $draft->Data_Mahasiswa_Confirmed,
        'mahasiswa_pending' => $draft->Data_Mahasiswa_Pending,
        'invitations' => $draft->invitations
    ]);
}
```

**Frontend (JavaScript):**

```javascript
// Saat page load, cek apakah ada draft_id di localStorage
document.addEventListener("DOMContentLoaded", function () {
    const draftId = localStorage.getItem("current_draft_id");

    if (draftId) {
        loadDraft(draftId);
    }
});

function loadDraft(draftId) {
    fetch(`/mahasiswa/api/draft/${draftId}`)
        .then((response) => response.json())
        .then((data) => {
            // Populate form fields
            document.getElementById("input-instansi-magang").value =
                data.draft.Nama_Instansi;
            document.getElementById("input-judul-magang").value =
                data.draft.Judul_Penelitian;
            // ... populate semua fields

            // Load mahasiswa confirmed
            data.mahasiswa_confirmed.forEach((mhs, index) => {
                if (index === 0) return; // Skip pembuat (sudah ada)
                addMahasiswaToForm(mhs, "confirmed");
            });

            // Load mahasiswa pending dengan badge
            data.mahasiswa_pending.forEach((mhs) => {
                addMahasiswaToForm(mhs, "pending");
            });
        });
}

function addMahasiswaToForm(mhs, status) {
    // Clone template
    const template = document.getElementById("mahasiswa-template");
    const clone = template.content.cloneNode(true);

    // Fill data
    clone.querySelector(".mahasiswa-nama").value = mhs.nama;
    clone.querySelector(".mahasiswa-nim").value = mhs.nim;
    clone.querySelector(".mahasiswa-angkatan").value = mhs.angkatan;

    // Add status badge
    if (status === "pending") {
        const badge = document.createElement("span");
        badge.className = "badge bg-warning text-dark ms-2";
        badge.textContent = "Menunggu Persetujuan";
        clone.querySelector(".mahasiswa-nama").parentElement.appendChild(badge);

        // Disable inputs
        clone
            .querySelector(".mahasiswa-item")
            .classList.add("pending-invitation");
        clone
            .querySelectorAll("input")
            .forEach((input) => (input.disabled = true));
    }

    document.getElementById("mahasiswa-container").appendChild(clone);
}
```

### 3. Mahasiswa Menerima/Menolak Undangan

**Notifikasi dengan Action Buttons:**

```blade
{{-- resources/views/notifikasi/index.blade.php --}}
@if($notif->Tipe_Notifikasi == 'invitation')
    @php
        $invitationData = json_decode($notif->Data_Tambahan ?? '{}');
        $invitation = \App\Models\SuratMagangInvitation::find($invitationData->invitation_id ?? 0);
    @endphp

    @if($invitation && $invitation->status === 'pending')
        <div class="mt-2">
            <form action="{{ route('mahasiswa.invitation.accept', $invitation->id_invitation) }}"
                  method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-check"></i> Terima
                </button>
            </form>

            <button type="button" class="btn btn-sm btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectModal{{ $invitation->id_invitation }}">
                <i class="fas fa-times"></i> Tolak
            </button>
        </div>
    @elseif($invitation)
        <span class="badge bg-{{ $invitation->status === 'accepted' ? 'success' : 'danger' }}">
            {{ $invitation->status === 'accepted' ? 'Diterima' : 'Ditolak' }}
        </span>
    @endif
@endif
```

**Backend:**

```php
public function acceptInvitation($invitationId)
{
    DB::beginTransaction();

    try {
        $invitation = SuratMagangInvitation::findOrFail($invitationId);

        // Validasi: hanya yang diundang yang bisa accept
        if ($invitation->Id_Mahasiswa_Diundang != auth()->user()->mahasiswa->Id_Mahasiswa) {
            abort(403);
        }

        // Update invitation status
        $invitation->status = 'accepted';
        $invitation->responded_at = now();
        $invitation->save();

        // Update draft: move dari pending ke confirmed
        $draft = $invitation->draft;
        $pendingList = $draft->Data_Mahasiswa_Pending ?? [];
        $confirmedList = $draft->Data_Mahasiswa_Confirmed ?? [];

        // Find and move mahasiswa
        foreach ($pendingList as $key => $mhs) {
            if ($mhs['id'] == $invitation->Id_Mahasiswa_Diundang) {
                $mhs['status'] = 'confirmed';
                $confirmedList[] = $mhs;
                unset($pendingList[$key]);
                break;
            }
        }

        $draft->Data_Mahasiswa_Pending = array_values($pendingList);
        $draft->Data_Mahasiswa_Confirmed = $confirmedList;
        $draft->save();

        // Notify pembuat
        Notifikasi::create([
            'Tipe_Notifikasi' => 'invitation_accepted',
            'Pesan' => auth()->user()->mahasiswa->Nama_Mahasiswa .
                      ' menerima undangan magang Anda',
            'Dest_User' => $invitation->mahasiswaPengundang->Id_User,
            'Source_User' => auth()->user()->Id_User,
            'Is_Read' => false
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Undangan diterima');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}

public function rejectInvitation(Request $request, $invitationId)
{
    $request->validate([
        'keterangan' => 'required|min:10'
    ]);

    DB::beginTransaction();

    try {
        $invitation = SuratMagangInvitation::findOrFail($invitationId);

        $invitation->status = 'rejected';
        $invitation->keterangan = $request->keterangan;
        $invitation->responded_at = now();
        $invitation->save();

        // Update draft: remove dari pending
        $draft = $invitation->draft;
        $pendingList = $draft->Data_Mahasiswa_Pending ?? [];

        foreach ($pendingList as $key => $mhs) {
            if ($mhs['id'] == $invitation->Id_Mahasiswa_Diundang) {
                unset($pendingList[$key]);
                break;
            }
        }

        $draft->Data_Mahasiswa_Pending = array_values($pendingList);
        $draft->save();

        // Notify pembuat
        Notifikasi::create([
            'Tipe_Notifikasi' => 'invitation_rejected',
            'Pesan' => auth()->user()->mahasiswa->Nama_Mahasiswa .
                      ' menolak undangan magang: ' . $request->keterangan,
            'Dest_User' => $invitation->mahasiswaPengundang->Id_User,
            'Source_User' => auth()->user()->Id_User,
            'Is_Read' => false
        ]);

        DB::commit();

        return redirect()->back()->with('success', 'Undangan ditolak');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}
```

### 4. Submit Final (Dari Draft ke Surat Final)

```php
public function submitDraft($draftId)
{
    DB::beginTransaction();

    try {
        $draft = SuratMagangDraft::with('invitations')->findOrFail($draftId);

        // Validasi: hanya pembuat yang bisa submit
        if ($draft->Id_Mahasiswa_Pembuat != auth()->user()->mahasiswa->Id_Mahasiswa) {
            abort(403);
        }

        // Validasi: tidak ada pending invitations
        if ($draft->pendingInvitations()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Masih ada undangan yang belum dijawab');
        }

        // Create Tugas_Surat (logic dari SuratPengantarMagangController)
        $tugasSurat = new TugasSurat();
        // ... isi data
        $tugasSurat->save();

        // Create Surat_Magang
        $suratMagang = new SuratMagang();
        $suratMagang->Id_Tugas_Surat = $tugasSurat->Id_Tugas_Surat;
        $suratMagang->Data_Mahasiswa = $draft->Data_Mahasiswa_Confirmed;
        // ... copy data lainnya dari draft
        $suratMagang->save();

        // Delete draft dan invitations (cascade)
        $draft->delete();

        // Clear localStorage
        // (frontend akan handle ini)

        DB::commit();

        return redirect()->route('mahasiswa.riwayat')
            ->with('success', 'Pengajuan berhasil dikirim!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
}
```

## Routes

```php
// routes/web.php - Mahasiswa Routes
Route::prefix('mahasiswa')->middleware(['auth', 'mahasiswa'])->group(function() {

    // Draft Management
    Route::post('/api/draft/save', [SuratPengantarMagangController::class, 'saveDraft'])
        ->name('mahasiswa.draft.save');
    Route::get('/api/draft/{id}', [SuratPengantarMagangController::class, 'getDraft'])
        ->name('mahasiswa.draft.get');
    Route::delete('/api/draft/{id}', [SuratPengantarMagangController::class, 'deleteDraft'])
        ->name('mahasiswa.draft.delete');

    // Invitation Actions
    Route::post('/invitation/{id}/accept', [SuratPengantarMagangController::class, 'acceptInvitation'])
        ->name('mahasiswa.invitation.accept');
    Route::post('/invitation/{id}/reject', [SuratPengantarMagangController::class, 'rejectInvitation'])
        ->name('mahasiswa.invitation.reject');

    // Submit Final
    Route::post('/draft/{id}/submit', [SuratPengantarMagangController::class, 'submitDraft'])
        ->name('mahasiswa.draft.submit');
});
```

## Frontend Implementation

### Auto-save Draft

```javascript
// Debounced auto-save setiap input berubah
let autoSaveTimeout;
const AUTOSAVE_DELAY = 2000; // 2 detik

document.querySelectorAll("input, select, textarea").forEach((element) => {
    element.addEventListener("input", function () {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(saveDraft, AUTOSAVE_DELAY);
    });
});

function saveDraft() {
    const formData = new FormData(document.querySelector("form"));
    const draftId = localStorage.getItem("current_draft_id");

    if (draftId) {
        formData.append("draft_id", draftId);
    }

    fetch("/mahasiswa/api/draft/save", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                localStorage.setItem("current_draft_id", data.draft_id);
                showToast("Draft tersimpan", "success");
            }
        })
        .catch((error) => console.error("Auto-save failed:", error));
}
```

## Testing

```bash
# 1. Create draft
POST /mahasiswa/api/draft/save
Body: { form data + mahasiswa yang diundang }
Expected: Draft created, invitations sent, notifications created

# 2. Load draft
GET /mahasiswa/api/draft/{id}
Expected: Full draft data with invitations status

# 3. Accept invitation
POST /mahasiswa/invitation/{id}/accept
Expected: Invitation status = accepted, moved to confirmed list

# 4. Reject invitation
POST /mahasiswa/invitation/{id}/reject
Body: { keterangan: "Alasan..." }
Expected: Invitation status = rejected, removed from list

# 5. Submit final
POST /mahasiswa/draft/{id}/submit
Expected: Tugas_Surat & Surat_Magang created, draft deleted
```

## Keuntungan Sistem Ini

✅ **Data Persistence**: Draft tersimpan, tidak hilang saat refresh  
✅ **Collaborative**: Mahasiswa lain harus approve dulu  
✅ **Transparent**: Status invitation jelas (pending/accepted/rejected)  
✅ **Notifikasi**: Real-time notification system  
✅ **Undo-able**: Draft bisa dihapus sebelum submit final  
✅ **Audit Trail**: Ada history siapa invite siapa dan kapan

## Next Steps

1. Implement Controller methods
2. Update Frontend form dengan auto-save
3. Add invitation action buttons di notifikasi
4. Test flow lengkap
5. Add validation rules
6. Add UI indicators (badges, tooltips)
