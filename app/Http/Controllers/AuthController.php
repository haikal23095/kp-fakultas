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

        // Ambil statistik surat berdasarkan status
        $permohonanBaru = $baseQuery()->whereIn('Status', ['baru', 'Diterima Admin'])->count();
        $menungguTTE = $baseQuery()->whereIn('Status', ['Disetujui Dekan', 'Menunggu TTE'])->count();
        $suratSelesaiBulanIni = $baseQuery()->where('Status', 'Selesai')
            ->whereMonth('Tanggal_Diselesaikan', date('m'))
            ->whereYear('Tanggal_Diselesaikan', date('Y'))
            ->count();
        $totalArsip = $baseQuery()->where('Status', 'Selesai')->count();

        // Ambil antrian permohonan terbaru (5 terakhir)
        $antrianSurat = $baseQuery()
            ->with(['pemberiTugas.role', 'pemberiTugas.mahasiswa', 'pemberiTugas.dosen', 'pemberiTugas.pegawai', 'jenisSurat'])
            ->whereIn('Status', ['baru', 'Diterima Admin', 'Diproses Admin'])
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


    public function dashboardDekan()
    {
        return view('dashboard.dekan');
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
            ->count();

        // Hitung Jumlah Dosen di prodi ini
        $jumlahDosen = \App\Models\Dosen::where('Id_Prodi', $prodiId)->count();

        // Surat Keluar dan Total Arsip tetap statis dulu
        $suratKeluar = 4;
        $totalArsip = 45;

        return view('dashboard.kaprodi', compact(
            'suratMasuk',
            'suratKeluar',
            'jumlahDosen',
            'totalArsip'
        ));
    }

    public function dashboardDosen()
    {
        return view('dashboard.dosen');
    }

    public function dashboardMahasiswa()
    {
        $user = Auth::user();

        // Ambil semua pengajuan surat mahasiswa dari tabel Tugas_Surat
        $totalPengajuan = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->has('suratMagang')
            ->count();

        // Menunggu Proses (status = 'Diajukan-ke-koordinator' di Surat_Magang)
        $menungguProses = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->whereHas('suratMagang', function ($query) {
                $query->whereIn('Status', ['Diajukan-ke-koordinator', 'Dikerjakan-admin']);
            })
            ->count();

        // Selesai & Dapat Diunduh (status = 'Success' di Surat_Magang)
        $selesai = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->whereHas('suratMagang', function ($query) {
                $query->where('Status', 'Success');
            })
            ->count();

        // Ambil 5 riwayat pengajuan terkini
        $riwayatTerkini = TugasSurat::with(['jenisSurat', 'suratMagang', 'fileArsip'])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->has('suratMagang')
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.mahasiswa', [
            'totalPengajuan' => $totalPengajuan,
            'menungguProses' => $menungguProses,
            'selesai' => $selesai,
            'riwayatTerkini' => $riwayatTerkini
        ]);
    }

    public function dashboardDefault()
    {
        return view('dashboard.default');
    }
}
