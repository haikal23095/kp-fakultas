<?php

namespace App\Http\Controllers\Admin_Prodi;


use App\Http\Controllers\Controller;
use App\Models\TugasSurat;
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
        $suratMagang = \App\Models\SuratMagang::with([
                'tugasSurat.pemberiTugas.mahasiswa.prodi',
                'tugasSurat.jenisSurat',
                'koordinator'
            ])
            ->whereHas('tugasSurat.pemberiTugas.mahasiswa', function($q) use ($prodiId) {
                $q->where('Id_Prodi', $prodiId); // Filter by prodi admin
            })
            ->where('Acc_Koordinator', true) // Sudah disetujui Kaprodi
            ->orderBy('id_no', 'desc')
            ->get();

        // Pisahkan pending vs semua
        $suratMagangPending = $suratMagang->filter(function($surat) {
            $status = strtolower(trim($surat->Status ?? ''));
            return $status === 'dikerjakan-admin';
        });

        $suratMagangSemua = $suratMagang->filter(function($surat) {
            $status = strtolower(trim($surat->Status ?? ''));
            return $status !== 'selesai' && $status !== 'ditolak';
        });

        // ===== SURAT KETERANGAN AKTIF =====
        // Query surat aktif yang perlu diproses admin
        $suratAktif = TugasSurat::with([
                'pemberiTugas.mahasiswa.prodi',
                'jenisSurat',
                'suratKetAktif'
            ])
            ->whereHas('pemberiTugas.mahasiswa', function($q) use ($prodiId) {
                $q->where('Id_Prodi', $prodiId); // Filter by prodi admin
            })
            ->whereHas('jenisSurat', function($q) {
                $q->where('Nama_Surat', 'LIKE', '%Keterangan Aktif%');
            })
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        // Pisahkan pending vs semua
        $suratAktifPending = $suratAktif->filter(function($tugas) {
            $status = strtolower(trim($tugas->Status ?? ''));
            return $status === 'diterima admin' || $status === 'baru';
        });

        $suratAktifSemua = $suratAktif->filter(function($tugas) {
            $status = strtolower(trim($tugas->Status ?? ''));
            return $status !== 'selesai' && $status !== 'ditolak';
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
            'tugasSurat.pemberiTugas.mahasiswa.prodi',
            'tugasSurat.jenisSurat',
            'koordinator'
        ])->where('id_no', $idNo)->firstOrFail();

        $mahasiswa = $surat->tugasSurat->pemberiTugas->mahasiswa ?? null;
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
        if (!$user || $user->Id_Role != 1) {
            abort(403, 'Unauthorized action.');
        }

        // Validasi input
        $validated = $request->validate([
            'status' => 'required|in:Belum,Proses,Selesai'
        ]);

        // Update status (delegasi ke model)
        $tugas = TugasSurat::updateStatusById($id, $validated['status']);

        if (!$tugas) {
            return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
        }

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }

    /**
     * Tampilkan halaman arsip surat
     */
    public function archive()
    {
        // Ambil arsip tugas yang sudah selesai (delegasi ke model)
        $arsipTugas = TugasSurat::getArsipSelesai();

        return view('admin_prodi.arsip_surat', [
            'arsipTugas' => $arsipTugas
        ]);
    }

    /**
     * Preview dokumen pendukung (PDF) untuk surat magang
     * Menampilkan file dalam iframe/embed
     */
    public function previewDokumen($id)
    {
        $tugasSurat = TugasSurat::with(['suratMagang'])->findOrFail($id);
        
        $dokumenPath = null;

        // 1. Cek di tabel Surat_Magang (Prioritas Utama)
        if ($tugasSurat->suratMagang && $tugasSurat->suratMagang->Dokumen_Proposal) {
            $dokumenPath = $tugasSurat->suratMagang->Dokumen_Proposal;
        }

        // 2. Jika tidak ada, cek di data_spesifik (Fallback / Surat Aktif)
        if (!$dokumenPath) {
            $dataSpesifik = $tugasSurat->data_spesifik;
            $dokumenPath = $dataSpesifik['dokumen_pendukung'] ?? null;
        }
        
        if (!$dokumenPath || !\Storage::disk('public')->exists($dokumenPath)) {
            abort(404, 'Dokumen tidak ditemukan');
        }
        
        // Return file untuk preview (dengan header inline)
        return \Storage::disk('public')->response($dokumenPath, null, [
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

        $tugasSurat = TugasSurat::with(['suratMagang', 'suratKetAktif'])->findOrFail($id);
        
        // Simpan nomor surat (parent status pakai label aplikasi: 'menunggu-ttd')
        $tugasSurat->Nomor_Surat = $validated['nomor_surat'];
        $tugasSurat->Status = 'menunggu-ttd';
        $tugasSurat->save();

        // Jika surat magang: sinkronkan status child ke enum valid ('Diajukan-ke-dekan')
        if ($tugasSurat->suratMagang) {
            try {
                // Update child hanya dengan nilai enum yang valid (hindari warning truncation)
                $tugasSurat->suratMagang->Status = 'Diajukan-ke-dekan';
                $tugasSurat->suratMagang->save();
            } catch (\Throwable $e) {
                // Lewati jika terjadi masalah; jangan blokir alur utama
            }

            // Kirim notifikasi ke Mahasiswa: nomor surat telah diberikan
            try {
                $mahasiswaUserId = $tugasSurat->Id_Pemberi_Tugas_Surat; // Pemberi tugas adalah mahasiswa
                if ($mahasiswaUserId) {
                    \App\Models\Notifikasi::create([
                        'Tipe_Notifikasi' => 'surat',
                        'Pesan' => 'Nomor surat KP/Magang telah ditetapkan: ' . $validated['nomor_surat'] . '. Surat Anda akan diproses untuk tanda tangan Dekan.',
                        'Dest_user' => $mahasiswaUserId,
                        'Source_User' => Auth::id(),
                        'Is_Read' => false,
                        'created_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // Biarkan lanjut tanpa mengganggu alur utama
            }
        }
        // Kirim notifikasi ke Dekan
        $dekan = \App\Models\User::whereHas('role', function($q) {
            $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
        })->first();

        if ($dekan) {
            \App\Models\Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => 'Surat baru menunggu tanda tangan: ' . $validated['nomor_surat'],
                'Dest_user' => $dekan->Id_User,
                'Source_User' => Auth::id(),
                'Is_Read' => false,
                'created_at' => now(),
            ]);

            // Update penerima tugas ke Dekan
            $tugasSurat->Id_Penerima_Tugas_Surat = $dekan->Id_User;
            $tugasSurat->save();
        }

        // Buat arsip awal (entry) untuk tracking setelah nomor dibuat
        try {
            \App\Models\FileArsip::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_Pemberi_Tugas_Surat' => $tugasSurat->Id_Pemberi_Tugas_Surat,
                'Id_Penerima_Tugas_Surat' => $tugasSurat->Id_Penerima_Tugas_Surat,
                'Keterangan' => 'Nomor surat ditetapkan: ' . $validated['nomor_surat'] . '. Surat diteruskan untuk tanda tangan Dekan.'
            ]);
        } catch (\Throwable $e) {
            // Lewati jika tabel/model belum tersedia; tidak blokir alur utama
        }

        return redirect()->route('admin_prodi.surat.manage')
            ->with('success', 'Nomor surat berhasil ditambahkan, mahasiswa diberi notifikasi, dan surat diteruskan ke Dekan!');
    }

    /**
     * Helper: Ambil Id_Prodi dari user yang login
     */
    private function getProdiIdFromUser()
    {
        $user = Auth::user()->load(['dosen', 'mahasiswa', 'pegawai']);

        return $user->dosen?->Id_Prodi
            ?? $user->mahasiswa?->Id_Prodi
            ?? $user->pegawai?->Id_Prodi;
    }
}