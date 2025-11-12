<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DetailSuratController;

// Import Controller untuk Pengajuan Surat (Modular)
use App\Http\Controllers\PengajuanSurat\SuratKeteranganAktifController;
use App\Http\Controllers\PengajuanSurat\SuratPengantarMagangController;

// Impor Model untuk route pengajuan
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\JenisSurat; // Pastikan ini ada
use App\Models\TugasSurat;
use App\Models\Role;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes untuk autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route untuk logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// =========================================================================
// AREA PENGGUNA YANG SUDAH LOGIN
// =========================================================================
Route::middleware('auth')->group(function () {

    // DASHBOARD UTAMA & ROLE
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/admin', [AuthController::class, 'dashboardAdmin'])->name('dashboard.admin');
    Route::get('/dashboard/dekan', [AuthController::class, 'dashboardDekan'])->name('dashboard.dekan');
    Route::get('/dashboard/kajur', [AuthController::class, 'dashboardKajur'])->name('dashboard.kajur');
    Route::get('/dashboard/kaprodi', [AuthController::class, 'dashboardKaprodi'])->name('dashboard.kaprodi');
    Route::get('/dashboard/dosen', [AuthController::class, 'dashboardDosen'])->name('dashboard.dosen');
    Route::get('/dashboard/mahasiswa', [AuthController::class, 'dashboardMahasiswa'])->name('dashboard.mahasiswa');
    Route::get('/dashboard/default', [AuthController::class, 'dashboardDefault'])->name('dashboard.default');

    // FITUR ADMIN
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/kelola-pengguna', function () {
            return view('admin.kelola_pengguna');
        })->name('users.index');

        // ... (route /kelola-pengguna) ...

        Route::get('/manajemen-surat', function () {

            // ======================================================
            // UBAH BARIS DI BAWAH INI
            // ======================================================

            // KODE LAMA ANDA:
            // $daftarTugas = TugasSurat::with(['pemberiTugas', 'jenisSurat']) 

            // KODE BARU:
            // 1) Tandai tugas yang melewati tenggat sebagai 'Terlambat' (kecuali yang sudah Selesai/Terlambat)
            TugasSurat::whereNotIn('Status', ['Selesai', 'Terlambat'])
                ->whereDate('Tanggal_Tenggat_Tugas_Surat', '<', Carbon::now()->toDateString())
                ->update(['Status' => 'Terlambat']);

            // 2) Ambil Id_Prodi dari user yang login (semua user pasti punya prodi)
            $user = Auth::user()->load(['dosen', 'mahasiswa', 'pegawai']);
            $prodiId = $user->dosen?->Id_Prodi ?? $user->mahasiswa?->Id_Prodi ?? $user->pegawai?->Id_Prodi;

            // 3) Ambil daftar tugas yang terfilter berdasarkan prodi (filter berdasarkan PEMBERI tugas = yang mengajukan)
            $daftarTugas = TugasSurat::with(['pemberiTugas.role', 'pemberiTugas.mahasiswa', 'pemberiTugas.dosen', 'pemberiTugas.pegawai', 'jenisSurat'])
                ->where(function ($q) use ($prodiId) {
                    // Filter surat yang diajukan oleh mahasiswa dari prodi yang sama
                    $q->whereHas('pemberiTugas.mahasiswa', function ($subQ) use ($prodiId) {
                        $subQ->where('Id_Prodi', $prodiId);
                    })
                        // ATAU filter surat yang diajukan oleh dosen dari prodi yang sama
                        ->orWhereHas('pemberiTugas.dosen', function ($subQ) use ($prodiId) {
                        $subQ->where('Id_Prodi', $prodiId);
                    })
                        // ATAU filter surat yang diajukan oleh pegawai dari prodi yang sama
                        ->orWhereHas('pemberiTugas.pegawai', function ($subQ) use ($prodiId) {
                        $subQ->where('Id_Prodi', $prodiId);
                    });
                })
                ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
                ->get();

            // 4) Ambil daftar role untuk dropdown pengiriman (admin)
            $roles = Role::orderBy('Name_Role')->get();

            return view('admin.manajemen_surat', [
                'daftarTugas' => $daftarTugas,
                'roles' => $roles,
            ]);

        })->name('surat.manage');

        // Detail surat (lihat detail berdasarkan Id_Tugas_Surat)
        Route::get('/surat/{id}/detail', [DetailSuratController::class, 'show'])->name('surat.detail');
        // Download dokumen pendukung (admin)
        Route::get('/surat/{id}/download', [DetailSuratController::class, 'downloadPendukung'])->name('surat.download');
        // Proses upload draft final / ajukan ke Dekan
        Route::post('/surat/{id}/process-draft', [DetailSuratController::class, 'processDraft'])->name('surat.process_draft');

        // Route: update status tugas (hanya admin)
        Route::post('/manajemen-surat/{id}/update-status', function (Request $request, $id) {
            $user = Auth::user();
            if (!$user || $user->Id_Role != 1) {
                abort(403);
            }

            $validated = $request->validate([
                'status' => 'required|in:Belum,Proses,Selesai'
            ]);

            $tugas = \App\Models\TugasSurat::find($id);
            if (!$tugas) {
                return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
            }

            $tugas->Status = $validated['status'];
            if (strtolower($validated['status']) === 'selesai') {
                $tugas->Tanggal_Diselesaikan = Carbon::now();
            } else {
                // Jika status bukan selesai, pastikan Tanggal_Diselesaikan dikosongkan
                $tugas->Tanggal_Diselesaikan = null;
            }
            $tugas->save();

            return redirect()->back()->with('success', 'Status berhasil diperbarui.');
        })->name('surat.updateStatus');

        // Route: assign tugas ke sebuah role dan upload file opsional (hanya admin)
        Route::post('/manajemen-surat/{id}/assign', function (Request $request, $id) {
            $user = Auth::user();
            if (!$user || $user->Id_Role != 1) {
                abort(403);
            }

            $validated = $request->validate([
                'role_id' => 'required|numeric|exists:Roles,Id_Role',
                'file' => 'nullable|file|max:10240', // max 10MB
            ]);

            $tugas = \App\Models\TugasSurat::find($id);
            if (!$tugas) {
                return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
            }

            // Simpan file jika ada
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $path = $request->file('file')->store('uploads/admin', 'public');
                $tugas->File_Surat = $path;
            }

            // Cari user pertama yang memiliki role yang dipilih (simple routing ke role)
            $receiver = \DB::table('Users')->where('Id_Role', $validated['role_id'])->first();
            if ($receiver) {
                $tugas->Id_Penerima_Tugas_Surat = $receiver->Id_User;
            }

            // Set status menjadi 'Proses' saat dikirim (admin hanya memicu pengiriman)
            $tugas->Status = 'Proses';

            // Atur tenggat maksimal 3 hari dari sekarang
            $tugas->Tanggal_Tenggat_Tugas_Surat = Carbon::now()->addDays(3);

            $tugas->save();

            return redirect()->back()->with('success', 'Tugas berhasil dikirim ke role yang dipilih.');
        })->name('surat.assign');

        // ... (route /arsip-surat) ...

        Route::get('/arsip-surat', function () {
            // Ambil tugas yang sudah selesai (arsip)
            $arsipTugas = TugasSurat::with(['pemberiTugas.role', 'jenisSurat'])
                ->whereRaw("LOWER(TRIM(Status)) = 'selesai'")
                ->orderBy('Tanggal_Diselesaikan', 'desc')
                ->get();

            return view('admin.arsip_surat', [
                'arsipTugas' => $arsipTugas
            ]);
        })->name('surat.archive');

        Route::get('/pengaturan', function () {
            return view('admin.pengaturan');
        })->name('settings.index');
    });

    // FITUR DEKAN
    Route::prefix('dekan')->name('dekan.')->group(function () {
        Route::get('/persetujuan-surat', function () {
            return view('dekan.persetujuan_surat');
        })->name('persetujuan.index');
        Route::get('/arsip-surat', function () {
            return view('dekan.arsip_surat');
        })->name('arsip.index');
    });

    // FITUR DOSEN
    Route::prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/pengajuan', function () {
            return view('dosen.pengajuan');
        })->name('pengajuan.index');
        // PENANDA: Rute yang hilang ditambahkan di sini
        Route::get('/riwayat', function () {
            return view('dosen.riwayat');
        })->name('riwayat.index');
        Route::get('/input-nilai', function () {
            return view('dosen.input_nilai');
        })->name('nilai.index');
        Route::get('/bimbingan', function () {
            return view('dosen.bimbingan_akademik');
        })->name('bimbingan.index');
    });

    // FITUR KAJUR
    Route::prefix('kajur')->name('kajur.')->group(function () {
        Route::get('/verifikasi-rps', function () {
            return view('kajur.verifikasi_rps');
        })->name('rps.index');
        Route::get('/laporan', function () {
            return view('kajur.laporan_jurusan');
        })->name('laporan.index');
        Route::get('/persetujuan-surat', function () {
            return view('kajur.persetujuan-surat');
        })->name('persetujuan.index');
    });

    // FITUR KAPRODI
    Route::prefix('kaprodi')->name('kaprodi.')->group(function () {
        Route::get('/kurikulum', function () {
            return view('kaprodi.kurikulum');
        })->name('kurikulum.index');
        Route::get('/jadwal-kuliah', function () {
            return view('kaprodi.jadwal_kuliah');
        })->name('jadwal.index');
    });

    // FITUR MAHASISWA
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {

        // --- ROUTE GET: FORM PENGAJUAN SURAT ---
        Route::get('/pengajuan-surat', function () {

            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

            $prodi = null;
            if ($mahasiswa && $mahasiswa->Id_Prodi) {
                $prodi = Prodi::find($mahasiswa->Id_Prodi);
            }

            $dosens = Dosen::orderBy('Nama_Dosen', 'asc')->get();

            // Definisikan surat apa saja yang boleh diajukan Mahasiswa
            $namaSuratMahasiswa = [
                'Surat Keterangan Aktif Kuliah',
                'Surat Rekomendasi',
                'Surat Pengantar KP/Magang'
            ];

            // Ambil dari DB HANYA surat-surat yang ada di daftar statis
            $jenis_surats = JenisSurat::whereIn('Nama_Surat', $namaSuratMahasiswa)
                ->orderBy('Nama_Surat', 'asc')
                ->get();

            return view('mahasiswa.pengajuan_surat', [
                'mahasiswa' => $mahasiswa,
                'prodi' => $prodi,
                'dosens' => $dosens,
                'jenis_surats' => $jenis_surats
            ]);

        })->name('pengajuan.create');

        // --- ROUTE POST: MENYIMPAN PENGAJUAN SURAT (MODULAR) ---
        // Route untuk Surat Keterangan Mahasiswa Aktif
        Route::post('/pengajuan-surat/aktif', [SuratKeteranganAktifController::class, 'store'])
            ->name('pengajuan.aktif.store');

        // Route untuk Surat Pengantar Magang/KP
        Route::post('/pengajuan-surat/magang', [SuratPengantarMagangController::class, 'store'])
            ->name('pengajuan.magang.store');

        // Rute lainnya
        Route::get('/riwayat', function () {
            return view('mahasiswa.riwayat');
        })->name('riwayat.index');
        Route::get('/legalisir', function () {
            return view('mahasiswa.legalisir');
        })->name('legalisir.create');
    });

});
