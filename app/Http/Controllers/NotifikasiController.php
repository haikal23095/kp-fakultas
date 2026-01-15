<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan halaman daftar notifikasi
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil semua notifikasi untuk user yang sedang login
        $notifikasis = Notifikasi::forUser($user->Id_User)
            ->with(['sourceUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Hitung jumlah notifikasi yang belum dibaca
        $unreadCount = Notifikasi::forUser($user->Id_User)
            ->unread()
            ->count();

        // Determine the layout based on user role
        $roleId = $user->Id_Role;

        // Debug: Log role information
        \Log::info('NotifikasiController - User ID: ' . $user->Id_User . ', Role ID: ' . $roleId);

        $layout = match ($roleId) {
            1 => 'admin_prodi',
            2 => 'dekan',
            3 => 'kajur',
            4 => 'kaprodi',
            5 => 'dosen',
            6 => 'mahasiswa',
            7 => 'admin_fakultas',
            8 => 'wadek1',
            default => 'mahasiswa',
        };

        \Log::info('NotifikasiController - Layout selected: ' . $layout);

        return view('notifikasi.index', compact('notifikasis', 'unreadCount', 'layout'));
    }

    /**
     * Menampilkan dropdown notifikasi (AJAX)
     */
    public function getRecent()
    {
        $user = Auth::user();

        // Ambil 5 notifikasi terbaru
        $notifikasis = Notifikasi::forUser($user->Id_User)
            ->with(['sourceUser'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Hitung jumlah notifikasi yang belum dibaca
        $unreadCount = Notifikasi::forUser($user->Id_User)
            ->unread()
            ->count();

        return response()->json([
            'notifikasis' => $notifikasis,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notifikasi sebagai sudah dibaca dan redirect ke detail
     */
    public function markAsReadAndRedirect($id)
    {
        $user = Auth::user();

        $notifikasi = Notifikasi::where('Id_Notifikasi', $id)
            ->where('Dest_User', $user->Id_User)
            ->firstOrFail();

        // Mark as read
        $notifikasi->markAsRead();

        // Get redirect URL from Data_Tambahan
        $dataTambahan = is_array($notifikasi->Data_Tambahan) 
            ? $notifikasi->Data_Tambahan 
            : json_decode($notifikasi->Data_Tambahan ?? '{}', true);
        
        $redirectUrl = $dataTambahan['action_url'] ?? null;

        // Jika tidak ada action_url, coba tentukan berdasarkan data notifikasi
        if (!$redirectUrl) {
            $redirectUrl = $this->determineRedirectUrl($notifikasi, $dataTambahan, $user);
        }

        return redirect($redirectUrl);
    }

    /**
     * Tentukan redirect URL berdasarkan data notifikasi dan role user
     */
    private function determineRedirectUrl($notifikasi, $dataTambahan, $user)
    {
        $roleId = $user->Id_Role;
        $tipeNotif = strtolower($notifikasi->Tipe_Notifikasi ?? '');
        
        // Cek apakah ada id_tugas_surat atau jenis_surat di data tambahan
        $idTugasSurat = $dataTambahan['id_tugas_surat'] ?? $dataTambahan['id_tugas'] ?? null;
        $jenisSurat = $dataTambahan['jenis_surat'] ?? $dataTambahan['entity'] ?? null;

        // Jika jenis_surat tidak ada, coba deteksi dari pesan notifikasi atau dari database
        if (!$jenisSurat) {
            $jenisSurat = $this->detectJenisSuratFromNotification($notifikasi, $idTugasSurat);
        }

        // MAHASISWA (Id_Role = 6)
        if ($roleId == 6) {
            // Jika ada jenis surat spesifik, arahkan ke riwayat jenis surat tersebut
            if ($jenisSurat) {
                return $this->getMahasiswaRiwayatRoute($jenisSurat);
            }
            
            // Cek tipe notifikasi untuk invitation
            if ($tipeNotif == 'invitation' && isset($dataTambahan['invitation_id'])) {
                return route('mahasiswa.riwayat.magang');
            }
            
            // Default ke halaman riwayat
            return route('mahasiswa.riwayat');
        }

        // ADMIN FAKULTAS (Id_Role = 7)
        if ($roleId == 7) {
            if ($jenisSurat) {
                return match($jenisSurat) {
                    'mobil_dinas', 'peminjaman_mobil' => route('admin_fakultas.surat.mobil_dinas'),
                    'legalisir', 'surat_legalisir' => route('admin_fakultas.legalisir.index'),
                    default => route('admin_fakultas.surat.kelola'),
                };
            }
            
            // Berdasarkan tipe notifikasi - default ke kelola surat bukan dashboard
            return route('admin_fakultas.surat.kelola');
        }

        // ADMIN PRODI (Id_Role = 1)
        if ($roleId == 1) {
            // Selalu ke manajemen surat, bukan dashboard
            return route('admin_prodi.surat.manage');
        }

        // DEKAN (Id_Role = 2)
        if ($roleId == 2) {
            if ($jenisSurat) {
                return match($jenisSurat) {
                    'legalisir', 'surat_legalisir' => route('dekan.legalisir.index'),
                    'sk_dosen_wali' => route('dekan.sk_dosen_wali.index'),
                    'magang', 'surat_magang' => route('dekan.surat.pending'),
                    default => route('dekan.surat.pending'),
                };
            }
            // Default ke pending surat, bukan dashboard
            return route('dekan.surat.pending');
        }

        // WADEK1 (Id_Role = 8)
        if ($roleId == 8) {
            if ($jenisSurat) {
                return match($jenisSurat) {
                    'legalisir', 'surat_legalisir' => route('wadek1.legalisir.index'),
                    'sk' => route('wadek1.sk.index'),
                    default => route('wadek1.surat.pending'),
                };
            }
            // Default ke pending surat, bukan dashboard
            return route('wadek1.surat.pending');
        }

        // WADEK3 - Kemahasiswaan
        if ($roleId == 9 || $roleId == 10) { // Sesuaikan ID role Wadek3
            if ($jenisSurat) {
                return match($jenisSurat) {
                    'berkelakuan_baik', 'kelakuan_baik' => route('wadek3.kelakuan_baik.index'),
                    'dispensasi' => route('wadek3.kemahasiswaan.index'),
                    default => route('wadek3.kemahasiswaan.index'),
                };
            }
            // Default ke kemahasiswaan, bukan dashboard
            return route('wadek3.kemahasiswaan.index');
        }

        // DOSEN (Id_Role = 5)
        if ($roleId == 5) {
            // Cek apakah notifikasi terkait SK Dosen Wali
            $pesan = strtolower($notifikasi->Pesan ?? '');
            if (str_contains($pesan, 'dosen wali') || str_contains($pesan, 'sk dosen')) {
                return route('dosen.sk.dosen-wali.index');
            }
            
            // Cek apakah notifikasi terkait SK
            if (str_contains($pesan, 'sk') || str_contains($pesan, 'surat keputusan')) {
                return route('dosen.sk.index');
            }
            
            // Jika ada id_tugas_surat atau notifikasi terkait surat, ke riwayat
            if ($idTugasSurat || in_array($tipeNotif, ['surat', 'approval', 'invitation'])) {
                return route('dosen.riwayat.index');
            }
            
            // Default ke riwayat (bukan dashboard) kalau ada notifikasi
            if ($notifikasi->Pesan) {
                return route('dosen.riwayat.index');
            }
            
            // Fallback ke dashboard
            return route('dashboard.dosen');
        }

        // KAJUR (Id_Role = 3)
        if ($roleId == 3) {
            // Cek apakah notifikasi terkait persetujuan surat
            $pesan = strtolower($notifikasi->Pesan ?? '');
            if (str_contains($pesan, 'surat') || str_contains($pesan, 'persetujuan') || str_contains($pesan, 'pengajuan')) {
                return route('kajur.persetujuan.index');
            }
            
            // Default ke persetujuan surat jika ada notifikasi approval/surat
            if (in_array($tipeNotif, ['surat', 'approval', 'invitation'])) {
                return route('kajur.persetujuan.index');
            }
            
            return route('dashboard.kajur');
        }

        // KAPRODI (Id_Role = 4)
        if ($roleId == 4) {
            // Cek apakah notifikasi terkait surat atau SK
            $pesan = strtolower($notifikasi->Pesan ?? '');
            
            if (str_contains($pesan, 'magang') || str_contains($pesan, 'kp') || str_contains($pesan, 'permintaan')) {
                return route('kaprodi.surat.index');
            }
            
            if (str_contains($pesan, 'dosen wali') || str_contains($pesan, 'sk dosen')) {
                return route('kaprodi.sk.dosen-wali.index');
            }
            
            if (str_contains($pesan, 'sk') || str_contains($pesan, 'surat keputusan')) {
                return route('kaprodi.sk.index');
            }
            
            // Default ke permintaan surat jika ada notifikasi approval/surat
            if (in_array($tipeNotif, ['surat', 'approval', 'invitation']) || $idTugasSurat) {
                return route('kaprodi.surat.index');
            }
            
            return route('dashboard.kaprodi');
        }

        // Fallback ke dashboard masing-masing role
        return match ($roleId) {
            1 => route('dashboard.admin_prodi'),
            2 => route('dashboard.dekan'),
            3 => route('dashboard.kajur'),
            4 => route('dashboard.kaprodi'),
            5 => route('dashboard.dosen'),
            6 => route('dashboard.mahasiswa'),
            7 => route('dashboard.admin_fakultas'),
            8 => route('dashboard.wadek1'),
            default => route('dashboard.mahasiswa'),
        };
    }

    /**
     * Deteksi jenis surat dari notifikasi atau database
     */
    private function detectJenisSuratFromNotification($notifikasi, $idTugasSurat)
    {
        // 1. Coba deteksi dari pesan notifikasi
        $pesan = strtolower($notifikasi->Pesan ?? '');
        
        if (str_contains($pesan, 'aktif')) return 'aktif';
        if (str_contains($pesan, 'magang') || str_contains($pesan, 'kp')) return 'magang';
        if (str_contains($pesan, 'legalisir')) return 'legalisir';
        if (str_contains($pesan, 'mobil dinas') || str_contains($pesan, 'peminjaman mobil')) return 'mobil_dinas';
        if (str_contains($pesan, 'beasiswa')) return 'tidak_beasiswa';
        if (str_contains($pesan, 'dispensasi')) return 'dispensasi';
        if (str_contains($pesan, 'berkelakuan baik') || str_contains($pesan, 'kelakuan baik')) return 'berkelakuan_baik';
        if (str_contains($pesan, 'surat tugas')) return 'surat_tugas';

        // 2. Jika ada id_tugas_surat, coba ambil dari database
        if ($idTugasSurat) {
            try {
                $tugasSurat = \App\Models\TugasSurat::with([
                    'suratKetAktif', 
                    'suratMagang', 
                    'suratLegalisir',
                    'suratTidakBeasiswa',
                    'suratDispensasi',
                    'suratKelakuanBaik',
                    'jenisSurat'
                ])->find($idTugasSurat);

                if ($tugasSurat) {
                    // Deteksi dari relasi
                    if ($tugasSurat->suratKetAktif) return 'aktif';
                    if ($tugasSurat->suratMagang) return 'magang';
                    if ($tugasSurat->suratLegalisir) return 'legalisir';
                    if ($tugasSurat->suratTidakBeasiswa) return 'tidak_beasiswa';
                    if ($tugasSurat->suratDispensasi) return 'dispensasi';
                    if ($tugasSurat->suratKelakuanBaik) return 'berkelakuan_baik';

                    // Deteksi dari Id_Jenis_Surat
                    if ($tugasSurat->Id_Jenis_Surat == 1) return 'aktif';
                    if ($tugasSurat->Id_Jenis_Surat == 2) return 'magang';
                    if ($tugasSurat->Id_Jenis_Surat == 4) return 'mobil_dinas';
                    if ($tugasSurat->Id_Jenis_Surat == 6) return 'tidak_beasiswa';
                    if ($tugasSurat->Id_Jenis_Surat == 18) return 'dispensasi';

                    // Deteksi dari nama jenis surat
                    if ($tugasSurat->jenisSurat) {
                        $namaSurat = strtolower($tugasSurat->jenisSurat->Nama_Surat ?? '');
                        if (str_contains($namaSurat, 'aktif')) return 'aktif';
                        if (str_contains($namaSurat, 'magang')) return 'magang';
                        if (str_contains($namaSurat, 'legalisir')) return 'legalisir';
                        if (str_contains($namaSurat, 'mobil')) return 'mobil_dinas';
                        if (str_contains($namaSurat, 'beasiswa')) return 'tidak_beasiswa';
                        if (str_contains($namaSurat, 'dispensasi')) return 'dispensasi';
                        if (str_contains($namaSurat, 'berkelakuan baik')) return 'berkelakuan_baik';
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Error detecting jenis surat from TugasSurat: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Get route mahasiswa riwayat berdasarkan jenis surat
     */
    private function getMahasiswaRiwayatRoute($jenisSurat)
    {
        return match($jenisSurat) {
            'aktif', 'surat_aktif', 'keterangan aktif' => route('mahasiswa.riwayat.aktif'),
            'magang', 'surat_magang', 'kp' => route('mahasiswa.riwayat.magang'),
            'legalisir', 'surat_legalisir' => route('mahasiswa.riwayat.legalisir'),
            'mobil_dinas', 'peminjaman_mobil' => route('mahasiswa.riwayat.mobil_dinas'),
            'tidak_beasiswa', 'beasiswa' => route('mahasiswa.riwayat.tidak_beasiswa'),
            'dispensasi' => route('mahasiswa.riwayat.dispensasi'),
            'berkelakuan_baik', 'kelakuan_baik' => route('mahasiswa.riwayat.berkelakuan_baik'),
            'surat_tugas' => route('mahasiswa.riwayat.surat_tugas'),
            default => route('mahasiswa.riwayat'),
        };
    }

    /**
     * Mark notifikasi sebagai sudah dibaca (AJAX only)
     */
    public function markAsRead($id)
    {
        $user = Auth::user();

        $notifikasi = Notifikasi::where('Id_Notifikasi', $id)
            ->where('Dest_User', $user->Id_User)
            ->firstOrFail();

        $notifikasi->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca');
    }

    /**
     * Mark semua notifikasi sebagai sudah dibaca
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        Notifikasi::forUser($user->Id_User)
            ->unread()
            ->update(['Is_Read' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }

    /**
     * Hapus notifikasi
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $notifikasi = Notifikasi::where('Id_Notifikasi', $id)
            ->where('Dest_User', $user->Id_User)
            ->firstOrFail();

        $notifikasi->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus');
    }

    /**
     * Hapus semua notifikasi user
     */
    public function deleteAll()
    {
        $user = Auth::user();

        $deletedCount = Notifikasi::where('Dest_User', $user->Id_User)->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'deleted_count' => $deletedCount
            ]);
        }

        return redirect()->back()->with('success', "Berhasil menghapus {$deletedCount} notifikasi");
    }
}
