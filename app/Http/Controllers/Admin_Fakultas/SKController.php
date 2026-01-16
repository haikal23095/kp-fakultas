<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SKDosenWali;
use App\Models\SKBebanMengajar;
use App\Models\ReqSKPembimbingSkripsi;
use App\Models\ReqSKPengujiSkripsi;
use App\Models\AccDekanDosenWali;
use App\Models\AccSKBebanMengajar;
use App\Models\AccSKPembimbingSkripsi;
use App\Models\AccSKPengujiSkripsi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Dosen;
use App\Models\Pejabat;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\Role;

class SKController extends Controller
{
    /**
     * Display the SK main page with 4 cards showing request counts
     */
    public function index()
    {
        // Get counts for each SK type - only "Dikerjakan admin" status
        $skDosenWaliCount = SKDosenWali::where('Status', 'Dikerjakan admin')->count();
        $skDosenWaliTotal = SKDosenWali::where('Status', 'Dikerjakan admin')->count();

        // Get SK Beban Mengajar count - only "Dikerjakan admin" status
        $skBebanMengajarCount = SKBebanMengajar::where('Status', 'Dikerjakan admin')->count();
        $skBebanMengajarTotal = SKBebanMengajar::where('Status', 'Dikerjakan admin')->count();

        // Get SK Pembimbing Skripsi count - only "Dikerjakan admin" status
        $skPembimbingSkripsiCount = ReqSKPembimbingSkripsi::where('Status', 'Dikerjakan admin')->count();
        $skPembimbingSkripsiTotal = ReqSKPembimbingSkripsi::where('Status', 'Dikerjakan admin')->count();

        // Get SK Penguji Skripsi count - only "Dikerjakan admin" status
        $skPengujiSkripsiCount = ReqSKPengujiSkripsi::where('Status', 'Dikerjakan admin')->count();
        $skPengujiSkripsiTotal = ReqSKPengujiSkripsi::where('Status', 'Dikerjakan admin')->count();

        return view('admin_fakultas.sk.index', compact(
            'skDosenWaliCount',
            'skDosenWaliTotal',
            'skBebanMengajarCount',
            'skBebanMengajarTotal',
            'skPembimbingSkripsiCount',
            'skPembimbingSkripsiTotal',
            'skPengujiSkripsiCount',
            'skPengujiSkripsiTotal'
        ));
    }

    /**
     * Display list of SK Dosen Wali requests untuk diproses
     */
    public function dosenWali(Request $request)
    {
        $query = SKDosenWali::with('prodi');

        // Hanya tampilkan SK dengan status "Dikerjakan admin"
        $query->where('Status', 'Dikerjakan admin');

        // Apply filters
        if ($request->filled('prodi')) {
            $query->where('Id_Prodi', $request->prodi);
        }

        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        // Get prodi list for filter
        $prodiList = \App\Models\Prodi::orderBy('Nama_Prodi')->get();

        // Ambil data dekan berdasarkan fakultas admin yang login
        $user = Auth::user()->load('pegawaiFakultas.fakultas');
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $dekan = null;
        if ($fakultasId) {
            $dekan = Dosen::with(['pejabat', 'prodi.fakultas'])
                ->whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
                    $q->where('Id_Fakultas', $fakultasId);
                })
                ->whereHas('pejabat', function ($q) {
                    $q->where('Nama_Jabatan', 'like', 'DEKAN%');
                })
                ->first();
        }

        $dekanName = $dekan->Nama_Dosen ?? 'FAIKUL UMAM';
        $dekanNip = $dekan->NIP ?? '198301182008121001';

        return view('admin_fakultas.sk.dosen-wali.index', compact('skList', 'prodiList', 'dekanName', 'dekanNip'));
    }

    /**
     * Display history of SK Dosen Wali from Acc_SK_Dosen_Wali
     */
    public function dosenWaliHistory(Request $request)
    {
        $query = AccDekanDosenWali::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        return view('admin_fakultas.sk.dosen-wali.history', compact('skList'));
    }

    /**
     * Get detail history SK for modal
     */
    public function dosenWaliDetailHistory($id)
    {
        try {
            $sk = AccDekanDosenWali::findOrFail($id);

            return response()->json([
                'success' => true,
                'sk' => $sk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SK tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Download SK Dosen Wali
     */
    public function downloadDosenWali($id)
    {
        // TODO: Implement PDF download
        return response()->json([
            'success' => false,
            'message' => 'Fitur download belum diimplementasikan'
        ]);
    }

    /**
     * Show detail of SK Dosen Wali
     */
    public function dosenWaliDetail($id)
    {
        $sk = SKDosenWali::with('prodi')->findOrFail($id);

        return view('admin_fakultas.sk.dosen-wali.detail', compact('sk'));
    }

    /**
     * Process SK Dosen Wali (approve or reject)
     */
    public function dosenWaliProcess(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string'
        ]);

        $sk = SKDosenWali::findOrFail($id);

        if ($request->action === 'approve') {
            $sk->Status = 'Menunggu-Persetujuan-Dekan';
            $message = 'SK Dosen Wali berhasil disetujui dan diteruskan ke Dekan!';
        } else {
            $sk->Status = 'Ditolak';
            $message = 'SK Dosen Wali ditolak!';
        }

        $sk->save();

        return redirect()->route('admin_fakultas.sk.dosen-wali')
            ->with('success', $message);
    }

    /**
     * Submit multiple SK Dosen Wali to Wadek 1
     */
    public function submitToWadek(Request $request)
    {
        $request->validate([
            'sk_ids' => 'required|array|min:1',
            'sk_ids.*' => 'exists:Req_SK_Dosen_Wali,No',
            'nomor_surat' => 'required|string|max:100'
        ]);

        try {
            DB::beginTransaction();

            // Ambil semua SK yang akan diajukan (termasuk yang ditolak untuk resubmit)
            $skItems = SKDosenWali::whereIn('No', $request->sk_ids)
                ->whereIn('Status', ['Dikerjakan admin', 'Ditolak-Wadek1', 'Ditolak-Dekan'])
                ->get();

            if ($skItems->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada SK yang dapat diproses. Pastikan SK memiliki status "Dikerjakan admin", "Ditolak-Wadek1", atau "Ditolak-Dekan"'
                ], 400);
            }

            // Gabungkan semua data dosen wali dari SK yang dipilih
            $mergedDosenWali = [];
            $tenggatTerdekat = null;

            // Cek apakah ada SK yang ditolak Dekan (untuk langsung skip ke Dekan)
            $hasDitolakDekan = $skItems->contains(function ($sk) {
                return $sk->Status === 'Ditolak-Dekan';
            });

            // Tentukan status target berdasarkan kondisi
            $targetStatus = $hasDitolakDekan ? 'Menunggu-Persetujuan-Dekan' : 'Menunggu-Persetujuan-Wadek-1';

            foreach ($skItems as $sk) {
                // Ambil dan decode data dosen wali (antisipasi data lama yang double-encoded)
                $dosenData = $sk->Data_Dosen_Wali;
                if (is_string($dosenData)) {
                    $dosenData = json_decode($dosenData, true);
                }

                if (is_array($dosenData)) {
                    foreach ($dosenData as $dosen) {
                        $mergedDosenWali[] = array_merge($dosen, [
                            'prodi' => $sk->prodi->Nama_Prodi ?? '-',
                            'Id_Prodi' => $sk->Id_Prodi,
                        ]);
                    }
                }

                // Hitung tenggat terdekat dari semua SK yang diajukan
                $tanggalTenggatSk = $sk->{'Tanggal-Tenggat'};
                if ($tanggalTenggatSk) {
                    if ($tenggatTerdekat === null || $tanggalTenggatSk->lessThan($tenggatTerdekat)) {
                        $tenggatTerdekat = $tanggalTenggatSk;
                    }
                }

                // Update status di tabel request sesuai kondisi
                $sk->Status = $targetStatus;
                $sk->Nomor_Surat = $request->nomor_surat;
                $sk->save();
            }

            // Buat satu entri gabungan di ACC_Dekan_Dosen_Wali
            $firstSk = $skItems->first();

            // Cek apakah SK yang ditolak Dekan sudah punya entry di Acc table
            $existingAccId = null;
            if ($hasDitolakDekan) {
                // Cari Id_Acc_SK_Dosen_Wali dari SK yang ditolak Dekan
                $ditolakDekanSk = $skItems->first(function ($sk) {
                    return $sk->Status === 'Ditolak-Dekan' && $sk->Id_Acc_SK_Dosen_Wali;
                });

                if ($ditolakDekanSk && $ditolakDekanSk->Id_Acc_SK_Dosen_Wali) {
                    $existingAccId = $ditolakDekanSk->Id_Acc_SK_Dosen_Wali;
                }
            }

            if ($existingAccId) {
                // Update existing Acc entry untuk SK yang ditolak Dekan
                $accSK = AccDekanDosenWali::find($existingAccId);
                $accSK->Data_Dosen_Wali = $mergedDosenWali;
                $accSK->Nomor_Surat = $request->nomor_surat;
                $accSK->Status = $targetStatus;
                $accSK->{'Alasan-Tolak'} = null; // Clear rejection reason
                $accSK->save();
            } else {
                // Create new Acc entry untuk SK baru atau yang ditolak Wadek1
                $accSK = AccDekanDosenWali::create([
                    'Semester' => $firstSk->Semester,
                    'Tahun_Akademik' => $firstSk->Tahun_Akademik,
                    'Data_Dosen_Wali' => $mergedDosenWali,
                    'Nomor_Surat' => $request->nomor_surat,
                    'Status' => $targetStatus,
                    'Tanggal-Pengajuan' => now(),
                    'Tanggal-Tenggat' => $tenggatTerdekat ?? now()->addDays(3),
                ]);
            }

            // Update Id_Acc_SK_Dosen_Wali di setiap record Req_SK_Dosen_Wali yang dipilih
            SKDosenWali::whereIn('No', $request->sk_ids)
                ->whereIn('Status', ['Menunggu-Persetujuan-Wadek-1', 'Menunggu-Persetujuan-Dekan'])
                ->update(['Id_Acc_SK_Dosen_Wali' => $accSK->No]);

            // Kirim notifikasi sesuai target
            if ($hasDitolakDekan) {
                // Kirim notifikasi ke Dekan jika langsung ke Dekan
                $dekanUser = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Dekan');
                })->first();

                if ($dekanUser) {
                    \App\Models\Notifikasi::create([
                        'Dest_user' => $dekanUser->Id_User,
                        'Source_User' => auth()->id(),
                        'Tipe_Notifikasi' => 'Accepted',
                        'Pesan' => "SK Dosen Wali No. {$accSK->Nomor_Surat} telah diperbaiki dan menunggu persetujuan Anda.",
                        'Is_Read' => false,
                        'Data_Tambahan' => [
                            'judul' => 'SK Dosen Wali Menunggu Persetujuan (Resubmit)',
                            'link' => route('dekan.persetujuan.sk_dosen_wali'),
                            'sk_id' => $accSK->No,
                            'nomor_surat' => $accSK->Nomor_Surat
                        ]
                    ]);
                }
            } else {
                // Kirim notifikasi ke Wadek 1 untuk SK baru
                $wadek1User = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Wadek1');
                })->first();

                if ($wadek1User) {
                    \App\Models\Notifikasi::create([
                        'Dest_user' => $wadek1User->Id_User,
                        'Source_User' => auth()->id(),
                        'Tipe_Notifikasi' => 'Accepted',
                        'Pesan' => "SK Dosen Wali No. {$accSK->Nomor_Surat} menunggu persetujuan Anda.",
                        'Is_Read' => false,
                        'Data_Tambahan' => [
                            'judul' => 'SK Dosen Wali Menunggu Persetujuan',
                            'link' => route('wadek1.sk.dosen-wali.index'),
                            'sk_id' => $accSK->No,
                            'nomor_surat' => $accSK->Nomor_Surat
                        ]
                    ]);
                }
            }

            DB::commit();

            $jumlah = $skItems->count();

            // Pesan sukses berbeda tergantung target
            $targetName = $hasDitolakDekan ? 'Dekan' : 'Wadek 1';

            // Flash pesan sukses untuk ditampilkan sebagai alert hijau setelah reload
            session()->flash('success', "Berhasil mengajukan {$jumlah} SK Dosen Wali ke {$targetName}");

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengajukan {$jumlah} SK Dosen Wali ke {$targetName}"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview PDF template for SK Dosen Wali
     */
    public function previewPDF(Request $request)
    {
        $pdfPath = resource_path('views/admin_fakultas/sk/dosen-wali/SK Dosen Wali FT Ganjil 2023-2024.pdf');

        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'PDF template tidak ditemukan'], 404);
        }

        return response()->file($pdfPath);
    }

    /**
     * Get details of selected SK for preview
     */
    public function getDetails(Request $request)
    {
        $request->validate([
            'sk_ids' => 'required|array'
        ]);

        try {
            $skList = SKDosenWali::with('prodi')
                ->whereIn('No', $request->sk_ids)
                ->get();

            $dosenList = [];

            foreach ($skList as $sk) {
                $dosenData = $sk->Data_Dosen_Wali;

                // Handle double-encoded JSON
                if (is_string($dosenData)) {
                    $dosenData = json_decode($dosenData, true);
                }

                if (is_array($dosenData)) {
                    foreach ($dosenData as $dosen) {
                        $dosenList[] = [
                            'nama_dosen' => $dosen['nama_dosen'] ?? '-',
                            'nip' => $dosen['nip'] ?? '-',
                            'prodi' => $sk->prodi->Nama_Prodi ?? '-',
                            'jumlah_anak_wali' => $dosen['jumlah_anak_wali'] ?? 0
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'dosen_list' => $dosenList
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject SK Dosen Wali request
     */
    public function rejectDosenWali(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'sk_id' => 'required|exists:Req_SK_Dosen_Wali,No',
                'alasan' => 'required|string|min:10'
            ], [
                'sk_id.required' => 'ID SK harus diisi',
                'sk_id.exists' => 'SK tidak ditemukan',
                'alasan.required' => 'Alasan penolakan harus diisi',
                'alasan.min' => 'Alasan penolakan minimal 10 karakter'
            ]);

            // Get SK
            $sk = SKDosenWali::findOrFail($request->sk_id);

            // Check if SK can be rejected (only if status is 'Dikerjakan admin')
            if ($sk->Status !== 'Dikerjakan admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dapat ditolak karena sudah diproses'
                ], 400);
            }

            // Get admin user
            $adminUser = Auth::user();

            // Get kaprodi user (the one who submitted the SK)
            $kaprodiDosen = Dosen::find($sk->Id_Dosen_Kaprodi);
            $kaprodiUser = $kaprodiDosen ? $kaprodiDosen->user : null;

            if (!$kaprodiUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Kaprodi tidak ditemukan'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Update SK status to 'Ditolak-Admin'
                $sk->Status = 'Ditolak-Admin';
                $sk->{'Alasan-Tolak'} = $request->alasan;
                $sk->save();

                // Create notification to Kaprodi
                \App\Models\Notifikasi::create([
                    'Source_User' => $adminUser->Id_User,
                    'Dest_user' => $kaprodiUser->Id_User,
                    'Tipe_Notifikasi' => 'Rejected',
                    'Pesan' => 'SK Dosen Wali untuk ' .
                        ($sk->prodi->Nama_Prodi ?? 'Prodi') .
                        ' Semester ' . $sk->Semester .
                        ' TA ' . $sk->Tahun_Akademik .
                        ' telah ditolak. Alasan: ' . $request->alasan,
                    'Data_Tambahan' => json_encode([
                        'url' => route('kaprodi.sk.dosen-wali.index')
                    ]),
                    'Is_Read' => false
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'SK berhasil ditolak dan notifikasi telah dikirim ke Kaprodi'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak SK: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== SK BEBAN MENGAJAR METHODS ====================

    /**
     * Display list of SK Beban Mengajar requests untuk diproses
     */
    public function bebanMengajar(Request $request)
    {
        $query = SKBebanMengajar::with('prodi');

        // Hanya tampilkan SK dengan status "Dikerjakan admin"
        $query->where('Status', 'Dikerjakan admin');

        // Apply filters
        if ($request->filled('prodi')) {
            $query->where('Id_Prodi', $request->prodi);
        }

        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        // Get prodi list for filter
        $prodiList = \App\Models\Prodi::orderBy('Nama_Prodi')->get();

        // Ambil data dekan berdasarkan fakultas admin yang login
        $user = Auth::user()->load('pegawaiFakultas.fakultas');
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $dekan = null;
        if ($fakultasId) {
            $dekan = Dosen::with(['pejabat', 'prodi.fakultas'])
                ->whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
                    $q->where('Id_Fakultas', $fakultasId);
                })
                ->whereHas('pejabat', function ($q) {
                    $q->where('Nama_Jabatan', 'like', 'DEKAN%');
                })
                ->first();
        }

        $dekanName = $dekan->Nama_Dosen ?? 'FAIKUL UMAM';
        $dekanNip = $dekan->NIP ?? '198301182008121001';

        return view('admin_fakultas.sk.beban-mengajar.index', compact('skList', 'prodiList', 'dekanName', 'dekanNip'));
    }

    /**
     * Display history of SK Beban Mengajar
     */
    public function bebanMengajarHistory(Request $request)
    {
        $query = SKBebanMengajar::with('prodi');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        // Exclude only 'Dikerjakan admin' status from history
        $query->where('Status', '!=', 'Dikerjakan admin');

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        return view('admin_fakultas.sk.beban-mengajar.history', compact('skList'));
    }

    /**
     * Get detail history SK Beban Mengajar for modal
     */
    public function bebanMengajarDetailHistory($id)
    {
        try {
            $sk = SKBebanMengajar::with('prodi')->findOrFail($id);

            return response()->json([
                'success' => true,
                'sk' => $sk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SK tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Download SK Beban Mengajar
     */
    public function downloadBebanMengajar($id)
    {
        // TODO: Implement PDF download
        return response()->json([
            'success' => false,
            'message' => 'Fitur download belum diimplementasikan'
        ]);
    }

    /**
     * Show detail of SK Beban Mengajar
     */
    public function bebanMengajarDetail($id)
    {
        $sk = SKBebanMengajar::with('prodi')->findOrFail($id);

        return view('admin_fakultas.sk.beban-mengajar.detail', compact('sk'));
    }

    /**
     * Process SK Beban Mengajar (approve or reject)
     */
    public function bebanMengajarProcess(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string'
        ]);

        $sk = SKBebanMengajar::findOrFail($id);

        if ($request->action === 'approve') {
            $sk->Status = 'Menunggu-Persetujuan-Dekan';
            $message = 'SK Beban Mengajar berhasil disetujui dan diteruskan ke Dekan!';
        } else {
            $sk->Status = 'Ditolak';
            $message = 'SK Beban Mengajar ditolak!';
        }

        $sk->save();

        return redirect()->route('admin_fakultas.sk.beban-mengajar')
            ->with('success', $message);
    }

    /**
     * Submit multiple SK Beban Mengajar to Wadek 1
     */
    public function submitToWadekBebanMengajar(Request $request)
    {
        $request->validate([
            'sk_ids' => 'required|array|min:1',
            'sk_ids.*' => 'exists:Req_SK_Beban_Mengajar,No',
            'nomor_surat' => 'required|string|max:100'
        ]);

        try {
            DB::beginTransaction();

            // Ambil semua SK yang akan diajukan
            $skItems = SKBebanMengajar::whereIn('No', $request->sk_ids)
                ->whereIn('Status', ['Dikerjakan admin', 'Ditolak-Wadek1', 'Ditolak-Dekan'])
                ->get();

            if ($skItems->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada SK yang dapat diproses. Pastikan SK memiliki status "Dikerjakan admin", "Ditolak-Wadek1", atau "Ditolak-Dekan"'
                ], 400);
            }

            // Cek apakah ada SK yang ditolak Dekan (untuk langsung skip ke Dekan)
            $hasDitolakDekan = $skItems->contains(function ($sk) {
                return $sk->Status === 'Ditolak-Dekan';
            });

            // Tentukan status target berdasarkan kondisi
            $targetStatus = $hasDitolakDekan ? 'Menunggu-Persetujuan-Dekan' : 'Menunggu-Persetujuan-Wadek-1';

            // Gabungkan semua data beban mengajar dari SK yang dipilih
            $gabunganBebanMengajar = [];
            $semester = $skItems->first()->Semester;
            $tahunAkademik = $skItems->first()->Tahun_Akademik;
            $tanggalPengajuan = $skItems->first()->{'Tanggal-Pengajuan'};
            $tanggalTenggat = $skItems->first()->{'Tanggal-Tenggat'};

            foreach ($skItems as $sk) {
                $bebanData = $sk->Data_Beban_Mengajar;

                // Handle double-encoded JSON
                if (is_string($bebanData)) {
                    $bebanData = json_decode($bebanData, true);
                }

                if (is_array($bebanData)) {
                    // Tambahkan info prodi ke setiap item
                    foreach ($bebanData as $item) {
                        $item['Id_Prodi'] = $sk->Id_Prodi;
                        $item['Nama_Prodi'] = $sk->prodi->Nama_Prodi ?? '-';
                        $gabunganBebanMengajar[] = $item;
                    }
                }
            }

            // Buat entry baru di tabel Acc_SK_Beban_Mengajar
            $accSK = AccSKBebanMengajar::create([
                'Semester' => $semester,
                'Tahun_Akademik' => $tahunAkademik,
                'Data_Beban_Mengajar' => $gabunganBebanMengajar,
                'Nomor_Surat' => $request->nomor_surat,
                'Status' => $targetStatus,
                'Tanggal-Pengajuan' => $tanggalPengajuan,
                'Tanggal-Tenggat' => $tanggalTenggat,
            ]);

            // Update status dan link ke Acc_SK di tabel Req_SK_Beban_Mengajar
            foreach ($skItems as $sk) {
                $sk->Status = $targetStatus;
                $sk->Nomor_Surat = $request->nomor_surat;
                $sk->Id_Acc_SK_Beban_Mengajar = $accSK->No;
                $sk->save();
            }

            // Kirim notifikasi sesuai target
            if ($hasDitolakDekan) {
                // Kirim notifikasi ke Dekan jika langsung ke Dekan
                $dekanUser = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Dekan');
                })->first();

                if ($dekanUser) {
                    \App\Models\Notifikasi::create([
                        'Dest_user' => $dekanUser->Id_User,
                        'Source_User' => auth()->id(),
                        'Tipe_Notifikasi' => 'Accepted',
                        'Pesan' => "SK Beban Mengajar No. {$request->nomor_surat} telah diperbaiki dan menunggu persetujuan Anda.",
                        'Is_Read' => false,
                        'Data_Tambahan' => [
                            'judul' => 'SK Beban Mengajar Menunggu Persetujuan (Resubmit)',
                            'link' => route('admin_fakultas.sk.beban-mengajar'),
                            'nomor_surat' => $request->nomor_surat
                        ]
                    ]);
                }
            } else {
                // Kirim notifikasi ke Wadek 1 untuk SK baru
                $wadek1User = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Wadek1');
                })->first();

                if ($wadek1User) {
                    \App\Models\Notifikasi::create([
                        'Dest_user' => $wadek1User->Id_User,
                        'Source_User' => auth()->id(),
                        'Tipe_Notifikasi' => 'Accepted',
                        'Pesan' => "SK Beban Mengajar No. {$request->nomor_surat} menunggu persetujuan Anda.",
                        'Is_Read' => false,
                        'Data_Tambahan' => [
                            'judul' => 'SK Beban Mengajar Menunggu Persetujuan',
                            'link' => route('admin_fakultas.sk.beban-mengajar'),
                            'nomor_surat' => $request->nomor_surat
                        ]
                    ]);
                }
            }

            DB::commit();

            $jumlah = $skItems->count();
            $targetName = $hasDitolakDekan ? 'Dekan' : 'Wadek 1';

            session()->flash('success', "Berhasil mengajukan {$jumlah} SK Beban Mengajar ke {$targetName}");

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengajukan {$jumlah} SK Beban Mengajar ke {$targetName}"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview PDF template for SK Beban Mengajar
     */
    public function previewPDFBebanMengajar(Request $request)
    {
        // TODO: Update path to actual SK Beban Mengajar PDF template
        $pdfPath = resource_path('views/admin_fakultas/sk/beban-mengajar/template.pdf');

        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'PDF template tidak ditemukan'], 404);
        }

        return response()->file($pdfPath);
    }

    /**
     * Get details of selected SK Beban Mengajar for preview
     */
    public function getDetailsBebanMengajar(Request $request)
    {
        $request->validate([
            'sk_ids' => 'required|array'
        ]);

        try {
            $skList = SKBebanMengajar::with('prodi')
                ->whereIn('No', $request->sk_ids)
                ->get();

            $bebanMengajarList = [];

            foreach ($skList as $sk) {
                $bebanData = $sk->Data_Beban_Mengajar;

                // Handle double-encoded JSON
                if (is_string($bebanData)) {
                    $bebanData = json_decode($bebanData, true);
                }

                if (is_array($bebanData)) {
                    foreach ($bebanData as $item) {
                        $bebanMengajarList[] = [
                            'nama_dosen' => $item['nama_dosen'] ?? '-',
                            'nip' => $item['nip'] ?? '-',
                            'prodi' => $sk->prodi->Nama_Prodi ?? '-',
                            'mata_kuliah' => $item['nama_mata_kuliah'] ?? $item['mata_kuliah'] ?? '-',
                            'nama_mata_kuliah' => $item['nama_mata_kuliah'] ?? $item['mata_kuliah'] ?? '-',
                            'kelas' => $item['kelas'] ?? '',
                            'sks' => $item['sks'] ?? 0
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'beban_mengajar_list' => $bebanMengajarList
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject SK Beban Mengajar request
     */
    public function rejectBebanMengajar(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'sk_id' => 'required|exists:Req_SK_Beban_Mengajar,No',
                'alasan' => 'required|string|min:10'
            ], [
                'sk_id.required' => 'ID SK harus diisi',
                'sk_id.exists' => 'SK tidak ditemukan',
                'alasan.required' => 'Alasan penolakan harus diisi',
                'alasan.min' => 'Alasan penolakan minimal 10 karakter'
            ]);

            // Get SK
            $sk = SKBebanMengajar::findOrFail($request->sk_id);

            // Check if SK can be rejected (only if status is 'Dikerjakan admin')
            if ($sk->Status !== 'Dikerjakan admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dapat ditolak karena sudah diproses'
                ], 400);
            }

            // Get admin user
            $adminUser = Auth::user();

            // Get kaprodi user (the one who submitted the SK)
            $kaprodiDosen = Dosen::find($sk->Id_Dosen_Kaprodi);
            $kaprodiUser = $kaprodiDosen ? $kaprodiDosen->user : null;

            if (!$kaprodiUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Kaprodi tidak ditemukan'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Update SK status to 'Ditolak-Admin'
                $sk->Status = 'Ditolak-Admin';
                $sk->{'Alasan-Tolak'} = $request->alasan;
                $sk->save();

                // Create notification to Kaprodi
                \App\Models\Notifikasi::create([
                    'Source_User' => $adminUser->Id_User,
                    'Dest_user' => $kaprodiUser->Id_User,
                    'Tipe_Notifikasi' => 'Rejected',
                    'Pesan' => 'SK Beban Mengajar untuk ' .
                        ($sk->prodi->Nama_Prodi ?? 'Prodi') .
                        ' Semester ' . $sk->Semester .
                        ' TA ' . $sk->Tahun_Akademik .
                        ' telah ditolak. Alasan: ' . $request->alasan,
                    'Data_Tambahan' => json_encode([
                        'url' => route('kaprodi.sk.beban-mengajar.index')
                    ]),
                    'Is_Read' => false
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'SK berhasil ditolak dan notifikasi telah dikirim ke Kaprodi'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display list of SK Pembimbing Skripsi requests
     */
    public function pembimbingSkripsi(Request $request)
    {
        $query = ReqSKPembimbingSkripsi::with(['prodi', 'kaprodi']);

        // Hanya tampilkan SK dengan status "Dikerjakan admin"
        $query->where('Status', 'Dikerjakan admin');

        // Apply filters
        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        if ($request->filled('tahun_akademik')) {
            $query->where('Tahun_Akademik', $request->tahun_akademik);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        // Get Dekan information (same approach as dosenWali method)
        $user = Auth::user()->load('pegawaiFakultas.fakultas');
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $dekan = null;
        if ($fakultasId) {
            $dekan = Dosen::with(['pejabat', 'prodi.fakultas'])
                ->whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
                    $q->where('Id_Fakultas', $fakultasId);
                })
                ->whereHas('pejabat', function ($q) {
                    $q->where('Nama_Jabatan', 'like', 'DEKAN%');
                })
                ->first();
        }

        $dekanName = $dekan->Nama_Dosen ?? 'FAIKUL UMAM';
        $dekanNip = $dekan->NIP ?? '198301182008121001';

        return view('admin_fakultas.sk.pembimbing-skripsi.index', compact('skList', 'dekanName', 'dekanNip'));
    }

    /**
     * Display detail of specific SK Pembimbing Skripsi
     */
    public function pembimbingSkripsiDetail($id)
    {
        $sk = ReqSKPembimbingSkripsi::with(['prodi', 'kaprodi.user', 'approval'])->findOrFail($id);

        // Decode JSON data if needed
        $dataPembimbing = $sk->Data_Pembimbing_Skripsi;

        // Handle if it's still a string (double-encoded JSON)
        if (is_string($dataPembimbing)) {
            $dataPembimbing = json_decode($dataPembimbing, true);
        }

        // Ensure it's an array
        if (!is_array($dataPembimbing)) {
            $dataPembimbing = [];
        }

        return view('admin_fakultas.sk.pembimbing-skripsi.detail', compact('sk', 'dataPembimbing'));
    }

    /**
     * Reject SK Pembimbing Skripsi request
     */
    public function rejectPembimbingSkripsi(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'sk_id' => 'required|exists:Req_SK_Pembimbing_Skripsi,No',
                'alasan' => 'required|string|min:10|max:1000'
            ], [
                'sk_id.required' => 'ID SK harus diisi',
                'sk_id.exists' => 'SK tidak ditemukan',
                'alasan.required' => 'Alasan penolakan harus diisi',
                'alasan.min' => 'Alasan penolakan minimal 10 karakter',
                'alasan.max' => 'Alasan penolakan maksimal 1000 karakter'
            ]);

            // Get SK with relationships
            $sk = ReqSKPembimbingSkripsi::with(['prodi', 'kaprodi.user'])->findOrFail($request->sk_id);

            // Check if SK can be rejected (only if status is 'Dikerjakan admin')
            if ($sk->Status !== 'Dikerjakan admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dapat ditolak karena sudah diproses'
                ], 400);
            }

            // Get admin user (Pegawai Fakultas/Admin Fakultas)
            $adminUser = Auth::user();

            // Get kaprodi user (the one who submitted the SK)
            $kaprodiUser = $sk->kaprodi->user ?? null;

            if (!$kaprodiUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Kaprodi tidak ditemukan'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Update SK status to 'Ditolak-Admin'
                $sk->Status = 'Ditolak-Admin';
                $sk->{'Alasan-Tolak'} = $request->alasan;
                $sk->save();

                // Create notification to Kaprodi
                \App\Models\Notifikasi::create([
                    'Source_User' => $adminUser->Id_User,
                    'Dest_user' => $kaprodiUser->Id_User,
                    'Tipe_Notifikasi' => 'Rejected',
                    'Pesan' => 'SK Pembimbing Skripsi untuk ' .
                        ($sk->prodi->Nama_Prodi ?? 'Prodi') .
                        ' Semester ' . $sk->Semester .
                        ' TA ' . $sk->Tahun_Akademik .
                        ' telah ditolak oleh Admin Fakultas. Alasan: ' . $request->alasan,
                    'Data_Tambahan' => json_encode([
                        'url' => route('kaprodi.sk.pembimbing-skripsi.create'),
                        'sk_id' => $sk->No
                    ]),
                    'Is_Read' => false
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'SK berhasil ditolak dan notifikasi telah dikirim ke Kaprodi'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of multiple SK Pembimbing Skripsi for preview
     */
    public function getPembimbingSkripsiDetails(Request $request)
    {
        try {
            $skIds = $request->sk_ids;

            if (empty($skIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No SK IDs provided'
                ], 400);
            }

            $skList = ReqSKPembimbingSkripsi::with(['prodi'])
                ->whereIn('No', $skIds)
                ->get();

            $mahasiswaList = [];

            foreach ($skList as $sk) {
                $dataPembimbing = $sk->Data_Pembimbing_Skripsi;

                // Decode if string
                if (is_string($dataPembimbing)) {
                    $dataPembimbing = json_decode($dataPembimbing, true);
                }

                if (is_array($dataPembimbing)) {
                    foreach ($dataPembimbing as $data) {
                        $mahasiswaList[] = [
                            'prodi' => $sk->prodi->Nama_Prodi ?? '-',
                            'nim' => $data['nim'] ?? '-',
                            'nama_mahasiswa' => $data['nama_mahasiswa'] ?? '-',
                            'judul_skripsi' => $data['judul_skripsi'] ?? '-',
                            'pembimbing_1' => $data['pembimbing_1'] ?? null,
                            'pembimbing_2' => $data['pembimbing_2'] ?? null,
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'mahasiswa_list' => $mahasiswaList
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit multiple SK Pembimbing Skripsi to Wadek 1
     */
    public function submitPembimbingSkripsiToWadek(Request $request)
    {
        $request->validate([
            'sk_ids' => 'required|array|min:1',
            'sk_ids.*' => 'exists:Req_SK_Pembimbing_Skripsi,No',
            'nomor_surat' => 'required|string|max:100',
            'tahun_akademik' => 'required|string|max:20'
        ]);

        try {
            DB::beginTransaction();

            $skItems = ReqSKPembimbingSkripsi::with('prodi.jurusan')
                ->whereIn('No', $request->sk_ids)
                ->whereIn('Status', ['Dikerjakan admin', 'Ditolak-Wadek1', 'Ditolak-Dekan'])
                ->get();

            if ($skItems->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada SK yang dapat diproses'
                ], 400);
            }

            // Check if any SK was rejected by Dekan
            $hasDitolakDekan = $skItems->contains(function ($sk) {
                return $sk->Status === 'Ditolak-Dekan';
            });

            // Merge all data with prodi and jurusan info
            $mergedMahasiswa = [];

            foreach ($skItems as $sk) {
                $dataPembimbing = $sk->Data_Pembimbing_Skripsi;

                if (is_string($dataPembimbing)) {
                    $dataPembimbing = json_decode($dataPembimbing, true);
                }

                if (is_array($dataPembimbing)) {
                    // Enrich each mahasiswa with prodi and jurusan data
                    foreach ($dataPembimbing as $mhs) {
                        if ($sk->prodi) {
                            $mhs['prodi_data'] = [
                                'nama_prodi' => $sk->prodi->Nama_Prodi,
                                'jurusan' => $sk->prodi->jurusan ? [
                                    'Nama_Jurusan' => $sk->prodi->jurusan->Nama_Jurusan
                                ] : null
                            ];
                        }
                        $mergedMahasiswa[] = $mhs;
                    }
                }
            }

            // Create Acc record with Tanggal_Pengajuan and Tanggal_Tenggat
            $tanggalPengajuan = now();
            $tanggalTenggat = now()->addDays(3);

            $accSK = AccSKPembimbingSkripsi::create([
                'Nomor_Surat' => $request->nomor_surat,
                'Semester' => $skItems->first()->Semester,
                'Tahun_Akademik' => $request->tahun_akademik,
                'Data_Pembimbing_Skripsi' => json_encode($mergedMahasiswa),
                'Tanggal-Pengajuan' => $tanggalPengajuan,
                'Tanggal-Tenggat' => $tanggalTenggat,
                'Status' => $hasDitolakDekan ? 'Menunggu-Persetujuan-Dekan' : 'Menunggu-Persetujuan-Wadek-1',
            ]);

            // Update all request records with acc_id and new status
            foreach ($skItems as $sk) {
                $sk->Id_Acc_SK_Pembimbing_Skripsi = $accSK->No;
                $sk->Status = $hasDitolakDekan ? 'Menunggu-Persetujuan-Dekan' : 'Menunggu-Persetujuan-Wadek-1';
                $sk->save();
            }

            // Send notification to Wadek 1 or Dekan
            $targetRoleName = $hasDitolakDekan ? 'Dekan' : 'Wadek1';
            $targetUsers = User::whereHas('role', function ($q) use ($targetRoleName) {
                $q->where('Name_Role', $targetRoleName);
            })->get();

            $targetRoleDisplay = $hasDitolakDekan ? 'Dekan' : 'Wadek 1';

            foreach ($targetUsers as $targetUser) {
                Notifikasi::create([
                    'Dest_user' => $targetUser->Id_User,
                    'Source_User' => Auth::id(),
                    'Tipe_Notifikasi' => 'Accepted',
                    'Pesan' => 'SK Pembimbing Skripsi baru menunggu persetujuan Anda. Semester ' .
                        $skItems->first()->Semester . ' TA ' . $request->tahun_akademik,
                    'Data_Tambahan' => json_encode([
                        'acc_id' => $accSK->No,
                        'nomor_surat' => $request->nomor_surat
                    ]),
                    'Is_Read' => false
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SK berhasil diajukan ke ' . $targetRoleDisplay
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan history SK Pembimbing Skripsi
     */
    public function pembimbingSkripsiHistory(Request $request)
    {
        $query = AccSKPembimbingSkripsi::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        return view('admin_fakultas.sk.pembimbing-skripsi.history', compact('skList'));
    }

    /**
     * Detail history SK Pembimbing Skripsi untuk modal
     */
    public function pembimbingSkripsiDetailHistory($id)
    {
        try {
            $sk = AccSKPembimbingSkripsi::with(['reqSKPembimbingSkripsi'])->findOrFail($id);

            // Parse Data_Pembimbing_Skripsi
            $dataPembimbing = $sk->Data_Pembimbing_Skripsi;
            if (is_string($dataPembimbing)) {
                $dataPembimbing = json_decode($dataPembimbing, true);
            }
            $sk->Data_Pembimbing_Skripsi = $dataPembimbing;

            return response()->json([
                'success' => true,
                'sk' => $sk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download SK Pembimbing Skripsi yang sudah selesai
     */
    public function downloadPembimbingSkripsi($id)
    {
        try {
            $sk = AccSKPembimbingSkripsi::findOrFail($id);

            if ($sk->Status !== 'Selesai' || !$sk->QR_Code) {
                return redirect()->back()->with('error', 'SK belum selesai atau QR Code belum tersedia');
            }

            // TODO: Generate PDF dengan QR Code
            // Untuk sementara redirect ke halaman detail
            return redirect()->route('admin_fakultas.sk.pembimbing-skripsi.detail', $id)
                ->with('info', 'Fitur download PDF sedang dalam pengembangan');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendownload SK: ' . $e->getMessage());
        }
    }

    /**
     * Display list of SK Penguji Skripsi requests
     */
    public function pengujiSkripsi(Request $request)
    {
        $query = ReqSKPengujiSkripsi::with(['prodi', 'kaprodi']);

        // Hanya tampilkan SK dengan status "Dikerjakan admin"
        $query->where('Status', 'Dikerjakan admin');

        // Apply filters
        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        if ($request->filled('tahun_akademik')) {
            $query->where('Tahun_Akademik', $request->tahun_akademik);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        // Get Dekan information
        $user = Auth::user()->load('pegawaiFakultas.fakultas');
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $dekan = null;
        if ($fakultasId) {
            $dekan = Dosen::with(['pejabat', 'prodi.fakultas'])
                ->whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
                    $q->where('Id_Fakultas', $fakultasId);
                })
                ->whereHas('pejabat', function ($q) {
                    $q->where('Nama_Jabatan', 'like', 'DEKAN%');
                })
                ->first();
        }

        $dekanName = $dekan->Nama_Dosen ?? 'FAIKUL UMAM';
        $dekanNip = $dekan->NIP ?? '198301182008121001';

        return view('admin_fakultas.sk.penguji-skripsi.index', compact('skList', 'dekanName', 'dekanNip'));
    }

    /**
     * Display detail of specific SK Penguji Skripsi
     */
    public function pengujiSkripsiDetail($id)
    {
        $sk = ReqSKPengujiSkripsi::with(['prodi', 'kaprodi.user', 'approval'])->findOrFail($id);

        $dataPenguji = $sk->Data_Penguji_Skripsi;

        // Handle if it's still a string (double-encoded JSON)
        if (is_string($dataPenguji)) {
            $dataPenguji = json_decode($dataPenguji, true);
        }

        return view('admin_fakultas.sk.penguji-skripsi.detail', compact('sk', 'dataPenguji'));
    }

    /**
     * Reject SK Penguji Skripsi request
     */
    public function rejectPengujiSkripsi(Request $request)
    {
        try {
            $request->validate([
                'sk_id' => 'required|exists:Req_SK_Penguji_Skripsi,No',
                'alasan' => 'required|string|min:10|max:1000'
            ], [
                'sk_id.required' => 'ID SK harus diisi',
                'sk_id.exists' => 'SK tidak ditemukan',
                'alasan.required' => 'Alasan penolakan harus diisi',
                'alasan.min' => 'Alasan penolakan minimal 10 karakter',
            ]);

            $sk = ReqSKPengujiSkripsi::with(['prodi', 'kaprodi.user'])->findOrFail($request->sk_id);

            if ($sk->Status !== 'Dikerjakan admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dapat ditolak karena sudah diproses'
                ], 400);
            }

            DB::beginTransaction();

            try {
                $sk->Status = 'Ditolak-Admin';
                $sk->{'Alasan-Tolak'} = $request->alasan;
                $sk->save();

                // Send notification to Kaprodi if user exists
                $adminUser = Auth::user();
                if ($sk->kaprodi && $sk->kaprodi->user) {
                    \App\Models\Notifikasi::create([
                        'Source_User' => $adminUser->Id_User,
                        'Dest_user' => $sk->kaprodi->user->Id_User,
                        'Tipe_Notifikasi' => 'Rejected',
                        'Pesan' => 'SK Penguji Skripsi untuk ' . ($sk->prodi->Nama_Prodi ?? 'Prodi') . ' periode ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' ditolak oleh Admin Fakultas. Alasan: ' . $request->alasan,
                        'Data_Tambahan' => json_encode([
                            'url' => route('kaprodi.sk.penguji-skripsi.history'),
                            'sk_id' => $sk->No
                        ]),
                        'Is_Read' => false,
                        'created_at' => now()
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'SK Penguji Skripsi berhasil ditolak'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Penguji Skripsi details for modal
     */
    public function getPengujiSkripsiDetails(Request $request)
    {
        try {
            $sk = ReqSKPengujiSkripsi::with(['prodi', 'kaprodi'])->findOrFail($request->sk_id);

            return response()->json([
                'success' => true,
                'data_penguji' => $sk->Data_Penguji_Skripsi,
                'prodi' => $sk->prodi->Nama_Prodi ?? '-',
                'semester' => $sk->Semester . ' ' . $sk->Tahun_Akademik
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit SK Penguji Skripsi to Wadek (Dekan side essentially)
     */
    public function submitPengujiSkripsiToWadek(Request $request)
    {
        $request->validate([
            'sk_id' => 'required|exists:Req_SK_Penguji_Skripsi,No',
            'nomor_surat' => 'required|string|max:100',
            'tahun_akademik' => 'required|string|max:20'
        ]);

        try {
            DB::beginTransaction();

            $sk = ReqSKPengujiSkripsi::with('prodi.jurusan')
                ->findOrFail($request->sk_id);

            // Check if status is valid for processing
            if (!in_array($sk->Status, ['Dikerjakan admin', 'Ditolak-Wadek1', 'Ditolak-Dekan'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dapat diproses dengan status saat ini'
                ], 400);
            }

            // Check if any SK was rejected by Dekan
            $hasDitolakDekan = $sk->Status === 'Ditolak-Dekan';

            // Find Dekan based on Fakultas
            $user = Auth::user()->load('pegawaiFakultas.fakultas');
            $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

            $dekan = null;
            if ($fakultasId) {
                $dekan = Dosen::with(['pejabat', 'prodi.fakultas'])
                    ->whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
                        $q->where('Id_Fakultas', $fakultasId);
                    })
                    ->whereHas('pejabat', function ($q) {
                        $q->where('Nama_Jabatan', 'like', 'DEKAN%');
                    })
                    ->first();
            }

            // Prepare data with prodi and jurusan info
            $dataPenguji = $sk->Data_Penguji_Skripsi;

            if (is_string($dataPenguji)) {
                $dataPenguji = json_decode($dataPenguji, true);
            }

            $enrichedData = [];
            if (is_array($dataPenguji)) {
                foreach ($dataPenguji as $mhs) {
                    if ($sk->prodi) {
                        $mhs['prodi_data'] = [
                            'nama_prodi' => $sk->prodi->Nama_Prodi,
                            'jurusan' => $sk->prodi->jurusan ? [
                                'Nama_Jurusan' => $sk->prodi->jurusan->Nama_Jurusan
                            ] : null
                        ];
                    }
                    $enrichedData[] = $mhs;
                }
            }

            // Create Acc record with Tanggal-Pengajuan and Tanggal-Tenggat
            $tanggalPengajuan = $sk->{'Tanggal-Pengajuan'} ?? now();
            $tanggalTenggat = $sk->{'Tanggal-Tenggat'} ?? now()->addDays(3);

            $accSK = AccSKPengujiSkripsi::create([
                'Nomor_Surat' => $request->nomor_surat,
                'Semester' => $sk->Semester,
                'Tahun_Akademik' => $request->tahun_akademik,
                'Data_Penguji_Skripsi' => $enrichedData, // Already array, no need to json_encode
                'Tanggal-Pengajuan' => $tanggalPengajuan,
                'Tanggal-Tenggat' => $tanggalTenggat,
                'Status' => $hasDitolakDekan ? 'Menunggu-Persetujuan-Dekan' : 'Menunggu-Persetujuan-Wadek-1',
                'Id_Dekan' => $dekan ? $dekan->Id_Dosen : null,
            ]);

            // Update request record with acc_id, nomor_surat, and new status
            $sk->Id_Acc_SK_Penguji_Skripsi = $accSK->No;
            $sk->Nomor_Surat = $request->nomor_surat;
            $sk->Status = $hasDitolakDekan ? 'Menunggu-Persetujuan-Dekan' : 'Menunggu-Persetujuan-Wadek-1';
            $sk->save();

            $targetRoleDisplay = $hasDitolakDekan ? 'Dekan' : 'Wadek 1';

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SK berhasil diajukan ke ' . $targetRoleDisplay
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in submitPengujiSkripsiToWadek: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pengujiSkripsiHistory(Request $request)
    {
        $query = AccSKPengujiSkripsi::with(['requestSK.prodi', 'requestSK.kaprodi', 'dekan']);

        $skList = $query->orderBy('No', 'desc')->paginate(15);

        return view('admin_fakultas.sk.penguji-skripsi.history', compact('skList'));
    }

    public function pengujiSkripsiDetailHistory($id)
    {
        $acc = AccSKPengujiSkripsi::with(['requestSK.prodi', 'requestSK.kaprodi.user', 'dekan'])->findOrFail($id);
        $sk = $acc->requestSK;
        $dataPenguji = $acc->Data_Penguji_Skripsi;

        // Handle if it's still a string (double-encoded JSON)
        if (is_string($dataPenguji)) {
            $dataPenguji = json_decode($dataPenguji, true);
        }

        return view('admin_fakultas.sk.penguji-skripsi.detail-history', compact('acc', 'sk', 'dataPenguji'));
    }

    public function downloadPengujiSkripsi($id)
    {
        try {
            $sk = AccSKPengujiSkripsi::findOrFail($id);

            if ($sk->Status !== 'Selesai' || !$sk->QR_Code) {
                return redirect()->back()->with('error', 'SK belum selesai atau QR Code belum tersedia');
            }

            return redirect()->route('admin_fakultas.sk.penguji-skripsi.detail', $id)
                ->with('info', 'Fitur download PDF sedang dalam pengembangan');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendownload SK: ' . $e->getMessage());
        }
    }
}
