<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TugasSurat;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Check if password is hashed or plain text
            $passwordMatches = false;

            if (str_starts_with($user->password, '$2y$')) {
                // Password is hashed, use normal check
                $passwordMatches = Hash::check($request->password, $user->password);
            } else {
                // Password is plain text, compare directly
                $passwordMatches = ($request->password === $user->password);
            }

            if ($passwordMatches) {
                Auth::login($user, $request->filled('remember'));
                $request->session()->regenerate();

                // Redirect berdasarkan role
                return $this->redirectToRoleDashboard($user->Id_Role);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Menampilkan dashboard setelah login
     */
    public function dashboard()
    {
        $user = Auth::user();
        return $this->redirectToRoleDashboard($user->Id_Role);
    }

    /**
     * Redirect ke dashboard berdasarkan role
     */
    private function redirectToRoleDashboard($roleId)
    {
        switch ($roleId) {
            case 1:
                return redirect()->route('dashboard.admin_prodi');
            case 2:
                return redirect()->route('dashboard.dekan');
            case 3:
                return redirect()->route('dashboard.kajur');
            case 4:
                return redirect()->route('dashboard.kaprodi');
            case 5:
                return redirect()->route('dashboard.dosen');
            case 6:
                return redirect()->route('dashboard.mahasiswa');
            case 7:
                return redirect()->route('dashboard.admin_fakultas');
            case 8:
                return redirect()->route('dashboard.wadek1');
            default:
                return redirect()->route('dashboard.default');
        }
    }

    // Dashboard untuk setiap role
    public function dashboardAdmin()
    {
        // Ambil Id_Prodi dari user yang login (semua user pasti punya prodi)
        $user = Auth::user()->load(['dosen', 'mahasiswa', 'pegawai.prodi']);

        // Ambil nama prodi (untuk tampilan dashboard)
        $namaProdi = $user->dosen?->prodi?->Nama_Prodi ??
            $user->mahasiswa?->prodi?->Nama_Prodi ??
            $user->pegawai?->prodi?->Nama_Prodi ??
            'Fakultas Teknik'; // Default jika Admin Fakultas (tidak terikat prodi)

        // NOTE: Untuk Admin Fakultas (Role 1), kita asumsikan mereka bisa melihat SEMUA surat dalam fakultas.
        // Jadi kita TIDAK memfilter berdasarkan Prodi spesifik admin tersebut.
        // Jika nanti ada kebutuhan "Admin Prodi", logika ini bisa disesuaikan.

        // Base query TANPA filter prodi (Menampilkan semua surat di Fakultas Teknik)
        $baseQuery = function () {
            return \App\Models\TugasSurat::query();
        };

        // Ambil statistik surat berdasarkan status dengan filter prodi
        // Status sekarang ada di tabel spesifik (Surat_Magang)
        $permohonanBaru = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->whereIn('Status', ['Diajukan-ke-koordinator', 'Dikerjakan-admin']);
            })->count();
        $menungguTTE = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->where('Status', 'Diajukan-ke-dekan');
            })->count();
        $suratSelesaiBulanIni = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->where('Status', 'Success');
            })
            ->whereMonth('Tanggal_Diselesaikan', date('m'))
            ->whereYear('Tanggal_Diselesaikan', date('Y'))
            ->count();
        $totalArsip = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->where('Status', 'Success');
            })->count();

        // Ambil antrian permohonan terbaru (5 terakhir)
        $antrianSurat = $baseQuery()
            ->with(['pemberiTugas.role', 'pemberiTugas.mahasiswa', 'pemberiTugas.dosen', 'pemberiTugas.pegawai', 'jenisSurat', 'suratMagang'])
            ->whereHas('suratMagang', function ($q) {
                $q->whereIn('Status', ['Diajukan-ke-koordinator', 'Dikerjakan-admin']);
            })
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.admin_prodi', compact(
            'permohonanBaru',
            'menungguTTE',
            'suratSelesaiBulanIni',
            'totalArsip',
            'antrianSurat',
            'namaProdi'
        ));
    }

    public function dashboardAdminFakultas()
    {
        // Ambil Id_Fakultas dari user yang login
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Ambil nama fakultas
        $namaFakultas = $user->pegawaiFakultas?->fakultas?->Nama_Fakultas ?? 'Fakultas';

        // Base query dengan filter fakultas (filter berdasarkan PEMBERI tugas = yang mengajukan)
        $baseQuery = function () use ($fakultasId) {
            return \App\Models\TugasSurat::query()
                ->where(function ($q) use ($fakultasId) {
                    $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    })
                        ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                            $subQ->where('Id_Fakultas', $fakultasId);
                        })
                        ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                            $subQ->where('Id_Fakultas', $fakultasId);
                        });
                });
        };

        // Ambil statistik surat berdasarkan status dengan filter fakultas
        // Status sekarang ada di tabel spesifik (Surat_Magang)
        $permohonanBaru = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->whereIn('Status', ['Diajukan-ke-koordinator', 'Dikerjakan-admin']);
            })->count();
        $menungguTTE = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->where('Status', 'Diajukan-ke-dekan');
            })->count();
        $suratSelesaiBulanIni = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->where('Status', 'Success');
            })
            ->whereMonth('Tanggal_Diselesaikan', date('m'))
            ->whereYear('Tanggal_Diselesaikan', date('Y'))
            ->count();
        $totalArsip = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->where('Status', 'Success');
            })->count();

        // Ambil antrian permohonan terbaru (5 terakhir) dengan filter fakultas
        $antrianSurat = $baseQuery()
            ->with(['pemberiTugas.role', 'pemberiTugas.mahasiswa', 'pemberiTugas.dosen', 'pemberiTugas.pegawai', 'jenisSurat', 'suratMagang'])
            ->whereHas('suratMagang', function ($q) {
                $q->whereIn('Status', ['Diajukan-ke-koordinator', 'Dikerjakan-admin']);
            })
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->take(5)
            ->get();

        // Get SK Dosen Wali pending count
        $skDosenWaliPending = \App\Models\SKDosenWali::where('Status', 'Dikerjakan admin')->count();

        return view('dashboard.admin_fakultas', compact(
            'permohonanBaru',
            'menungguTTE',
            'suratSelesaiBulanIni',
            'totalArsip',
            'antrianSurat',
            'namaFakultas',
            'skDosenWaliPending'
        ));
    }

    public function dashboardDekan()
    {
        return view('dashboard.dekan');
    }

    public function dashboardWadek1()
    {
        return view('dashboard.wadek1');
    }

    public function dashboardKajur()
    {
        return view('dashboard.kajur');
    }

    public function dashboardKaprodi()
    {
        $user = Auth::user();

        // Ambil data Kaprodi (bisa dari Dosen atau Pegawai)
        $kaprodiDosen = $user->dosen;
        $kaprodiPegawai = $user->pegawai;

        // Ambil Id_Prodi dari Kaprodi
        $prodiId = $kaprodiDosen?->Id_Prodi ?? $kaprodiPegawai?->Id_Prodi;

        if (!$prodiId) {
            return view('dashboard.kaprodi', [
                'suratMasuk' => 0,
                'suratKeluar' => 4, // statis
                'jumlahDosen' => 0,
                'totalArsip' => 45, // statis
            ]);
        }

        // Hitung Surat Masuk: Surat Magang dengan Acc_Koordinator = false dari mahasiswa di prodi ini
        $suratMasuk = \App\Models\SuratMagang::query()
            ->whereHas('tugasSurat.pemberiTugas.mahasiswa', function ($q) use ($prodiId) {
                $q->where('Id_Prodi', $prodiId);
            })
            ->where('Acc_Koordinator', false)
            ->where('Status', 'Diajukan-ke-koordinator')
            ->count();

        // Ambil data antrian surat magang untuk ditampilkan di tabel
        $antrianSurat = \App\Models\SuratMagang::query()
            ->with(['tugasSurat.pemberiTugas.mahasiswa'])
            ->whereHas('tugasSurat.pemberiTugas.mahasiswa', function ($q) use ($prodiId) {
                $q->where('Id_Prodi', $prodiId);
            })
            ->where('Acc_Koordinator', false)
            ->where('Status', 'Diajukan-ke-koordinator')
            ->orderBy('id_no', 'desc')
            ->limit(3)
            ->get();

        // Hitung Jumlah Dosen di prodi ini
        $jumlahDosen = \App\Models\Dosen::where('Id_Prodi', $prodiId)->count();

        // Surat Keluar dan Total Arsip tetap statis dulu
        $suratKeluar = 4;
        $totalArsip = 45;

        return view('dashboard.kaprodi', compact(
            'suratMasuk',
            'suratKeluar',
            'jumlahDosen',
            'totalArsip',
            'antrianSurat'
        ));
    }

    public function dashboardDosen()
    {
        return view('dashboard.dosen');
    }

    public function dashboardMahasiswa()
    {
        $user = Auth::user();

        // Ambil statistik dari tabel Surat_Magang berdasarkan Id_Pemberi_Tugas_Surat mahasiswa
        $ditolak = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->whereHas('suratMagang', function ($query) {
                $query->where('Status', 'Ditolak');
            })
            ->count();

        // Diterima (status = 'Success' di Surat_Magang)
        $diterima = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->whereHas('suratMagang', function ($query) {
                $query->where('Status', 'Success');
            })
            ->count();

        // Total Pengajuan
        $totalPengajuan = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->has('suratMagang')
            ->count();

        // Ambil 5 riwayat pengajuan terkini dari Surat_Magang
        $riwayatTerkini = \App\Models\SuratMagang::whereHas('tugasSurat', function ($query) use ($user) {
            $query->where('Id_Pemberi_Tugas_Surat', $user->Id_User);
        })
            ->with(['tugasSurat.jenisSurat'])
            ->orderBy('id_no', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.mahasiswa', [
            'ditolak' => $ditolak,
            'diterima' => $diterima,
            'totalPengajuan' => $totalPengajuan,
            'riwayatTerkini' => $riwayatTerkini
        ]);
    }

    public function dashboardDefault()
    {
        return view('dashboard.default');
    }
}
