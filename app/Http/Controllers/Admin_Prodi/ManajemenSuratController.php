<?php

namespace App\Http\Controllers\Admin_Prodi;


use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratMagang;


class ManajemenSuratController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil Id_Prodi admin yang login
        $adminProdi = $user->pegawai ?? $user->dosen;
        $prodiId = $adminProdi?->Id_Prodi;

        if (!$prodiId) {
            return view('admin_prodi.manajemen_surat', [
                'suratMagangPending' => collect([]),
                'suratAktifPending' => collect([]),
                'suratMagangSemua' => collect([]),
                'suratAktifSemua' => collect([]),
            ]);
        }

        // ===== SURAT MAGANG =====
        // Query surat magang yang sudah ACC Kaprodi (Status = 'Dikerjakan-admin')
        $suratMagang = SuratMagang::with([
            'pemberiTugas.mahasiswa.prodi',
            'koordinator'
        ])
            ->whereHas('pemberiTugas.mahasiswa', function ($q) use ($prodiId) {
                $q->where('Id_Prodi', $prodiId); // Filter by prodi admin
            })
            ->where('Acc_Koordinator', true) // Sudah disetujui Kaprodi
            ->orderBy('id_no', 'desc')
            ->get();

        // Pisahkan pending vs semua
        $suratMagangPending = $suratMagang->filter(function ($surat) {
            $status = strtolower(trim($surat->Status ?? ''));
            return $status === 'dikerjakan-admin';
        });

        $suratMagangSemua = $suratMagang->filter(function ($surat) {
            $status = strtolower(trim($surat->Status ?? ''));
            return !in_array($status, ['success', 'ditolak']);
        });

        // ===== SURAT KETERANGAN AKTIF =====
        // Query surat aktif yang perlu diproses admin
        $suratAktif = SuratKetAktif::with([
            'pemberiTugas.mahasiswa.prodi'
        ])
            ->whereHas('pemberiTugas.mahasiswa', function ($q) use ($prodiId) {
                $q->where('Id_Prodi', $prodiId); // Filter by prodi admin
            })
            ->orderBy('id_no', 'desc')
            ->get();

        // Pisahkan pending vs semua
        $suratAktifPending = $suratAktif->filter(function ($surat) {
            $status = strtolower(trim($surat->Status ?? ''));
            return in_array($status, ['diterima admin', 'baru', 'diajukan-ke-koordinator']);
        });

        $suratAktifSemua = $suratAktif->filter(function ($surat) {
            $status = strtolower(trim($surat->Status ?? ''));
            return !in_array($status, ['success', 'ditolak']);
        });

        return view('admin_prodi.manajemen_surat', [
            'suratMagangPending' => $suratMagangPending,
            'suratAktifPending' => $suratAktifPending,
            'suratMagangSemua' => $suratMagangSemua,
            'suratAktifSemua' => $suratAktifSemua,
        ]);
    }

    /**
     * Preview Surat Pengantar Magang on standalone page
     */
    public function previewMagang($idNo)
    {
        $surat = SuratMagang::with([
            'pemberiTugas.mahasiswa.prodi',
            'koordinator'
        ])->where('id_no', $idNo)->firstOrFail();

        $mahasiswa = $surat->pemberiTugas->mahasiswa ?? null;
        $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
        $dosenPembimbing = is_array($surat->Data_Dosen_pembiming) ? $surat->Data_Dosen_pembiming : json_decode($surat->Data_Dosen_pembiming, true);

        return view('admin_prodi.preview_surat_magang', compact('surat', 'mahasiswa', 'dataMahasiswa', 'dosenPembimbing'));
    }

    /**
     * Update status tugas surat
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi user adalah admin
        $user = Auth::user();
        if (!$user || ($user->Id_Role != 1 && $user->Id_Role != 7)) { // Role 1 Admin Prodi, Role 7 Admin Fakultas
            abort(403, 'Unauthorized action.');
        }

        // Validasi input
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        // Cari record
        $surat = SuratMagang::find($id);
        if (!$surat) {
            $surat = SuratKetAktif::find($id);
        }

        if (!$surat) {
            return redirect()->back()->with('error', 'Surat tidak ditemukan.');
        }

        $surat->Status = $validated['status'];
        $surat->save();

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }

    /**
     * Tampilkan halaman arsip surat
     */
    public function archive()
    {
        // Ambil arsip dari kedua tabel yang statusnya Success
        $arsipMagang = SuratMagang::where('Status', 'Success')->get();
        $arsipAktif = SuratKetAktif::where('Status', 'Success')->get();

        // Gabungkan untuk tampilan (jika diperlukan satu list)
        $arsipTugas = $arsipMagang->concat($arsipAktif)->sortByDesc('Tanggal_Diselesaikan');

        return view('admin_prodi.arsip_surat', [
            'arsipTugas' => $arsipTugas
        ]);
    }

    /**
     * Preview dokumen pendukung (PDF) untuk surat
     */
    public function previewDokumen($id)
    {
        // Cek Magang
        $surat = SuratMagang::find($id);
        $dokumenPath = null;

        if ($surat && $surat->Dokumen_Proposal) {
            $dokumenPath = $surat->Dokumen_Proposal;
        } else {
            // Cek Aktif
            $surat = SuratKetAktif::find($id);
            if ($surat && $surat->KRS) {
                $dokumenPath = $surat->KRS;
            }
        }

        if (!$dokumenPath || !Storage::disk('public')->exists($dokumenPath)) {
            abort(404, 'Dokumen tidak ditemukan');
        }

        return Storage::disk('public')->response($dokumenPath, null, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($dokumenPath) . '"'
        ]);
    }


    /**
     * Tambah nomor surat dan teruskan ke Dekan
     */
    public function addNomorSurat(Request $request, $id)
    {
        $validated = $request->validate([
            'nomor_surat' => 'required|string|max:100',
        ]);

        // Cari jenis surat 
        $surat = SuratMagang::find($id);

        if (!$surat) {
            $surat = SuratKetAktif::find($id);
        }

        if (!$surat) {
            abort(404, 'Surat tidak ditemukan');
        }

        // Simpan nomor surat
        $surat->Nomor_Surat = $validated['nomor_surat'];
        $surat->Status = 'Diajukan-ke-dekan';
        $surat->save();

        // Kirim notifikasi ke Mahasiswa
        if ($surat->Id_Pemberi_Tugas) {
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => 'Nomor surat telah ditetapkan: ' . $validated['nomor_surat'] . '. Surat Anda sedang diproses untuk tanda tangan Dekan.',
                'Dest_user' => $surat->Id_Pemberi_Tugas,
                'Source_User' => Auth::id(),
                'Is_Read' => false,
                'created_at' => now(),
            ]);
        }

        // Cari Dekan untuk notifikasi
        $dekan = User::where('Id_Role', 2)->first();

        if ($dekan) {
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => 'Surat baru menunggu tanda tangan: ' . $validated['nomor_surat'],
                'Dest_user' => $dekan->Id_User,
                'Source_User' => Auth::id(),
                'Is_Read' => false,
                'created_at' => now(),
            ]);

            // Update penerima tugas ke Dekan 
            if (isset($surat->Id_Penerima_Tugas)) {
                $surat->Id_Penerima_Tugas = $dekan->Id_User;
                $surat->save();
            }
        }

        return redirect()->route('admin_prodi.surat.manage')
            ->with('success', 'Nomor surat berhasil ditambahkan, mahasiswa diberi notifikasi, dan surat diteruskan ke Dekan!');
    }
}