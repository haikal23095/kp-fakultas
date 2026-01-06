<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AccSKBebanMengajar;
use App\Models\SKBebanMengajar;
use App\Models\Dosen;
use App\Models\Notifikasi;
use App\Models\User;
use App\Models\Prodi;
use App\Helpers\QrCodeHelper;

class SKBebanMengajarController extends Controller
{
    /**
     * Tampilkan daftar SK Beban Mengajar yang menunggu persetujuan Dekan
     */
    public function index(Request $request)
    {
        // Show all SK dengan optional filter, kecuali yang ditolak Wadek1
        $query = AccSKBebanMengajar::where('Status', '!=', 'Ditolak-Wadek1');

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        // Filter berdasarkan semester jika ada
        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        $daftarSK = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(10);

        return view('dekan.sk.beban-mengajar.index', compact('daftarSK'));
    }

    /**
     * Ambil detail SK Beban Mengajar untuk preview
     */
    public function detail($id)
    {
        try {
            $sk = AccSKBebanMengajar::findOrFail($id);

            // Convert QR_Code path to base64 jika ada
            $qrCodeBase64 = null;
            if ($sk->QR_Code && file_exists($sk->QR_Code)) {
                $qrCodeBase64 = base64_encode(file_get_contents($sk->QR_Code));
            }

            Log::info('SK Detail fetched', [
                'id' => $sk->No,
                'has_qr_path' => !empty($sk->QR_Code),
                'qr_file_exists' => $sk->QR_Code ? file_exists($sk->QR_Code) : false,
                'has_qr_base64' => !empty($qrCodeBase64)
            ]);

            // Ambil data Dekan dari tabel Dosen dengan Id_Pejabat = 1 (Dekan)
            $dekan = Dosen::where('Id_Pejabat', 1)->first();

            if (!$dekan) {
                Log::error('Dekan tidak ditemukan di tabel Dosen dengan Id_Pejabat=1');
                return response()->json([
                    'success' => false,
                    'message' => 'Data Dekan tidak ditemukan'
                ], 404);
            }

            $dekanName = $dekan->Nama_Dosen ?? 'Nama Dekan';
            $dekanNip = $dekan->NIP ?? '-';

            // Prepare SK data dengan QR Code base64
            $skData = $sk->toArray();
            if ($qrCodeBase64) {
                $skData['QR_Code'] = $qrCodeBase64;
            }

            return response()->json([
                'success' => true,
                'sk' => $skData,
                'dekan' => [
                    'nama' => $dekanName,
                    'nip' => $dekanNip
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching SK detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data SK'
            ], 500);
        }
    }

    /**
     * Setujui SK Beban Mengajar dan tandatangani dengan QR Code
     */
    public function approve(Request $request, $id)
    {
        try {
            $sk = AccSKBebanMengajar::findOrFail($id);

            // Validasi: hanya bisa approve jika status masih Menunggu-Persetujuan-Dekan
            if ($sk->Status !== 'Menunggu-Persetujuan-Dekan') {
                return redirect()->back()->with('error', 'SK tidak dalam status yang valid untuk disetujui');
            }

            // Ambil data Dekan yang login
            $user = Auth::user();
            $dekan = Dosen::where('Id_User', $user->Id_User)->first();

            if (!$dekan) {
                return redirect()->back()->with('error', 'Data Dekan tidak ditemukan');
            }

            // Generate QR Code untuk tanda tangan
            $qrData = "SK Beban Mengajar\nNomor: {$sk->Nomor_Surat}\nTahun Akademik: {$sk->Tahun_Akademik}\nSemester: {$sk->Semester}\nDitandatangani oleh: {$dekan->Nama_Dosen}\nNIP: {$dekan->NIP}\nTanggal: " . now()->format('d-m-Y H:i:s');

            $qrCode = QrCodeHelper::generateQrCode($qrData);

            // Update status SK
            $sk->update([
                'Status' => 'Selesai',
                'Id_Dekan' => $dekan->Id_Dosen,
                'Tanggal-Persetujuan-Dekan' => now(),
                'QR_Code' => $qrCode
            ]);

            // Update status semua Req_SK_Beban_Mengajar yang terkait
            SKBebanMengajar::where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                ->update([
                    'Status' => 'Selesai',
                    'Nomor_Surat' => $sk->Nomor_Surat,
                    'QR_Code' => $qrCode,  // Simpan QR Code juga ke Req_SK_Beban_Mengajar
                    'Tanggal-Persetujuan-Dekan' => now()
                ]);

            $dekanUserId = $user->Id_User;

            // 1. Kirim notifikasi ke Admin Fakultas
            $adminUser = User::whereHas('role', function ($q) {
                $q->where('Name_Role', 'Pegawai_Fakultas');
            })->first();

            if ($adminUser) {
                try {
                    Notifikasi::create([
                        'Tipe_Notifikasi' => 'Accepted',
                        'Pesan' => "SK Beban Mengajar Tahun Akademik {$sk->Tahun_Akademik} Semester {$sk->Semester} telah disetujui dan ditandatangani oleh Dekan dengan Nomor Surat: {$sk->Nomor_Surat}",
                        'Dest_user' => $adminUser->Id_User,
                        'Source_User' => $dekanUserId,
                        'Is_Read' => 0,
                        'Data_Tambahan' => json_encode([
                            'id_acc_sk' => $sk->No,
                            'nomor_surat' => $sk->Nomor_Surat,
                            'tahun_akademik' => $sk->Tahun_Akademik,
                            'semester' => $sk->Semester
                        ])
                    ]);
                    Log::info('Notifikasi berhasil dibuat untuk Admin Fakultas', ['admin_user_id' => $adminUser->Id_User]);
                } catch (\Exception $e) {
                    Log::error('Error creating notification for admin: ' . $e->getMessage());
                }
            }

            // 2. Kirim notifikasi ke semua Kaprodi yang terlibat
            $allSK = SKBebanMengajar::where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                ->whereNotNull('Id_Dosen_Kaprodi')
                ->with(['kaprodi.user', 'prodi'])
                ->get();

            $kaprodiDosenIds = $allSK->pluck('Id_Dosen_Kaprodi')->unique()->filter();
            $notifKaprodiCount = 0;

            foreach ($kaprodiDosenIds as $kaprodiDosenId) {
                $dosenKaprodi = Dosen::with('user')->find($kaprodiDosenId);

                if ($dosenKaprodi && $dosenKaprodi->Id_User) {
                    $skWithProdi = $allSK->where('Id_Dosen_Kaprodi', $kaprodiDosenId)->first();
                    $prodiInfo = $skWithProdi && $skWithProdi->prodi ? $skWithProdi->prodi : null;

                    try {
                        Notifikasi::create([
                            'Tipe_Notifikasi' => 'Accepted',
                            'Pesan' => "SK Beban Mengajar Tahun Akademik {$sk->Tahun_Akademik} Semester {$sk->Semester} telah disetujui dan ditandatangani oleh Dekan dengan Nomor Surat: {$sk->Nomor_Surat}",
                            'Dest_user' => $dosenKaprodi->Id_User,
                            'Source_User' => $dekanUserId,
                            'Is_Read' => 0,
                            'Data_Tambahan' => json_encode([
                                'id_acc_sk' => $sk->No,
                                'nomor_surat' => $sk->Nomor_Surat,
                                'tahun_akademik' => $sk->Tahun_Akademik,
                                'semester' => $sk->Semester,
                                'id_prodi' => $prodiInfo ? $prodiInfo->Id_Prodi : null,
                                'nama_prodi' => $prodiInfo ? $prodiInfo->Nama_Prodi : null
                            ])
                        ]);
                        $notifKaprodiCount++;
                        Log::info('Notifikasi berhasil dibuat untuk Kaprodi', [
                            'kaprodi_name' => $dosenKaprodi->Nama_Dosen,
                            'user_id' => $dosenKaprodi->Id_User
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error creating notification for kaprodi: ' . $e->getMessage());
                    }
                }
            }

            // 3. Kirim notifikasi ke semua dosen yang ada di Data_Beban_Mengajar
            $bebanData = $sk->Data_Beban_Mengajar;
            if (is_string($bebanData)) {
                $bebanData = json_decode($bebanData, true);
            }

            $notifDosenCount = 0;
            $dosenNipProcessed = []; // Track untuk avoid duplicate notifikasi ke dosen yang sama

            if (is_array($bebanData)) {
                foreach ($bebanData as $item) {
                    $nip = $item['nip'] ?? $item['NIP'] ?? null;

                    if ($nip && !in_array($nip, $dosenNipProcessed)) {
                        $dosenMengajar = Dosen::where('NIP', $nip)->first();

                        if ($dosenMengajar && $dosenMengajar->Id_User) {
                            try {
                                Notifikasi::create([
                                    'Tipe_Notifikasi' => 'Accepted',
                                    'Pesan' => "SK Beban Mengajar Anda untuk Tahun Akademik {$sk->Tahun_Akademik} Semester {$sk->Semester} telah disetujui dan ditandatangani oleh Dekan dengan Nomor Surat: {$sk->Nomor_Surat}",
                                    'Dest_user' => $dosenMengajar->Id_User,
                                    'Source_User' => $dekanUserId,
                                    'Is_Read' => 0,
                                    'Data_Tambahan' => json_encode([
                                        'id_acc_sk' => $sk->No,
                                        'nomor_surat' => $sk->Nomor_Surat,
                                        'tahun_akademik' => $sk->Tahun_Akademik,
                                        'semester' => $sk->Semester,
                                        'nip_dosen' => $nip,
                                        'nama_dosen' => $item['nama_dosen'] ?? $item['Nama_Dosen'] ?? null
                                    ])
                                ]);
                                $notifDosenCount++;
                                $dosenNipProcessed[] = $nip;
                                Log::info('Notifikasi berhasil dibuat untuk Dosen Mengajar', [
                                    'nip' => $nip,
                                    'user_id' => $dosenMengajar->Id_User
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Error creating notification for dosen: ' . $e->getMessage(), ['nip' => $nip]);
                            }
                        }
                    }
                }
            }

            Log::info('SK Beban Mengajar disetujui', [
                'id_acc_sk' => $sk->No,
                'id_dekan' => $dekan->Id_Dosen,
                'nomor_surat' => $sk->Nomor_Surat,
                'notif_admin' => $adminUser ? 1 : 0,
                'notif_kaprodi' => $notifKaprodiCount,
                'notif_dosen' => $notifDosenCount
            ]);

            return redirect()->route('dekan.sk.beban-mengajar.index')->with('success', 'SK Beban Mengajar berhasil disetujui dan ditandatangani');
        } catch (\Exception $e) {
            Log::error('Error approving SK: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui SK');
        }
    }

    /**
     * Tolak SK Beban Mengajar
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
            'target' => 'required|in:admin,kaprodi'
        ]);

        try {
            $sk = AccSKBebanMengajar::with(['skBebanMengajar.kaprodi.user'])->findOrFail($id);

            // Validasi: hanya bisa reject jika status masih Menunggu-Persetujuan-Dekan
            if ($sk->Status !== 'Menunggu-Persetujuan-Dekan') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dalam status yang valid untuk ditolak'
                ], 400);
            }

            $target = $request->target;
            $alasanPenolakan = $request->alasan_penolakan;

            // Status berubah menjadi 'Ditolak-Dekan'
            $newStatus = 'Ditolak-Dekan';

            // Update status SK
            $sk->update([
                'Status' => $newStatus,
                'Alasan-Tolak' => $alasanPenolakan
            ]);

            // Update status semua Req_SK_Beban_Mengajar yang terkait
            SKBebanMengajar::where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                ->update([
                    'Status' => $newStatus,
                    'Alasan-Tolak' => $alasanPenolakan
                ]);

            // Kirim notifikasi berdasarkan target
            $dekanUserId = Auth::id();

            if ($target === 'admin') {
                // Kirim ke Admin Fakultas - cari user dengan role Pegawai_Fakultas
                $adminUser = User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Pegawai_Fakultas');
                })->first();

                Log::info('Searching admin user', [
                    'found' => $adminUser ? 'yes' : 'no',
                    'dekan_user_id' => $dekanUserId
                ]);

                if ($adminUser) {
                    try {
                        $notif = Notifikasi::create([
                            'Tipe_Notifikasi' => 'Rejected',
                            'Pesan' => "SK Beban Mengajar Tahun Akademik {$sk->Tahun_Akademik} Semester {$sk->Semester} ditolak oleh Dekan. Alasan: {$alasanPenolakan}",
                            'Dest_user' => $adminUser->Id_User,
                            'Source_User' => $dekanUserId,
                            'Is_Read' => 0,
                            'Data_Tambahan' => json_encode([
                                'id_acc_sk' => $sk->No,
                                'tahun_akademik' => $sk->Tahun_Akademik,
                                'semester' => $sk->Semester
                            ])
                        ]);
                        Log::info('Notifikasi berhasil dibuat untuk Admin', ['notif_id' => $notif->Id_Notifikasi]);
                    } catch (\Exception $e) {
                        Log::error('Error creating notification for admin: ' . $e->getMessage());
                    }
                } else {
                    Log::warning('Admin Fakultas user not found');
                }
            } else {
                // Kirim ke SEMUA Kaprodi yang terlibat - ambil semua SK yang terkait dengan relasi ke Dosen Kaprodi
                $allSK = SKBebanMengajar::where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                    ->whereNotNull('Id_Dosen_Kaprodi')
                    ->with(['kaprodi.user', 'prodi'])
                    ->get();

                // Ambil ID Dosen Kaprodi yang unik
                $kaprodiDosenIds = $allSK->pluck('Id_Dosen_Kaprodi')->unique()->filter();

                Log::info('Searching all kaprodi', [
                    'total_sk' => $allSK->count(),
                    'unique_kaprodi_dosen_ids' => $kaprodiDosenIds->toArray(),
                    'dekan_user_id' => $dekanUserId
                ]);

                $notifCount = 0;
                foreach ($kaprodiDosenIds as $kaprodiDosenId) {
                    // Cari dosen kaprodi
                    $dosenKaprodi = Dosen::with('user')->find($kaprodiDosenId);

                    if ($dosenKaprodi && $dosenKaprodi->Id_User) {
                        // Dapatkan info prodi dari SK
                        $skWithProdi = $allSK->where('Id_Dosen_Kaprodi', $kaprodiDosenId)->first();
                        $prodiInfo = $skWithProdi && $skWithProdi->prodi ? $skWithProdi->prodi : null;

                        try {
                            $notif = Notifikasi::create([
                                'Tipe_Notifikasi' => 'Rejected',
                                'Pesan' => "SK Beban Mengajar Tahun Akademik {$sk->Tahun_Akademik} Semester {$sk->Semester} ditolak oleh Dekan. Alasan: {$alasanPenolakan}",
                                'Dest_user' => $dosenKaprodi->Id_User,
                                'Source_User' => $dekanUserId,
                                'Is_Read' => 0,
                                'Data_Tambahan' => json_encode([
                                    'id_acc_sk' => $sk->No,
                                    'tahun_akademik' => $sk->Tahun_Akademik,
                                    'semester' => $sk->Semester,
                                    'id_dosen_kaprodi' => $dosenKaprodi->Id_Dosen,
                                    'nama_kaprodi' => $dosenKaprodi->Nama_Dosen,
                                    'id_prodi' => $prodiInfo ? $prodiInfo->Id_Prodi : null,
                                    'nama_prodi' => $prodiInfo ? $prodiInfo->Nama_Prodi : null
                                ])
                            ]);
                            $notifCount++;
                            Log::info('Notifikasi berhasil dibuat untuk Kaprodi', [
                                'notif_id' => $notif->Id_Notifikasi,
                                'kaprodi_dosen_id' => $dosenKaprodi->Id_Dosen,
                                'kaprodi_name' => $dosenKaprodi->Nama_Dosen,
                                'user_id' => $dosenKaprodi->Id_User,
                                'prodi_name' => $prodiInfo ? $prodiInfo->Nama_Prodi : 'N/A'
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error creating notification for kaprodi: ' . $e->getMessage(), [
                                'kaprodi_dosen_id' => $dosenKaprodi->Id_Dosen,
                                'user_id' => $dosenKaprodi->Id_User
                            ]);
                        }
                    } else {
                        Log::warning('Kaprodi dosen not found or has no Id_User', [
                            'kaprodi_dosen_id' => $kaprodiDosenId,
                            'dosen_found' => $dosenKaprodi ? 'yes' : 'no',
                            'has_user' => $dosenKaprodi && $dosenKaprodi->Id_User ? 'yes' : 'no'
                        ]);
                    }
                }

                Log::info('Total notifikasi dibuat untuk Kaprodi', ['count' => $notifCount]);
            }

            Log::info('SK Beban Mengajar ditolak', [
                'id_acc_sk' => $sk->No,
                'target' => $target,
                'new_status' => $newStatus
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SK Beban Mengajar berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting SK: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menolak SK'
            ], 500);
        }
    }

    /**
     * Tampilkan history SK Beban Mengajar yang sudah diproses
     */
    public function history(Request $request)
    {
        $query = AccSKBebanMengajar::whereIn('Status', ['Selesai', 'Ditolak-Dekan']);

        // Filter berdasarkan semester jika ada
        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        $daftarSK = $query->orderBy('Tanggal-Persetujuan-Dekan', 'desc')->paginate(10);

        return view('dekan.sk.beban-mengajar.history', compact('daftarSK'));
    }
}
