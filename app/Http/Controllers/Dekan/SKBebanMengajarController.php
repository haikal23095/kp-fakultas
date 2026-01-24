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
use App\Services\WahaService;
use Illuminate\Support\Facades\DB;

class SKBebanMengajarController extends Controller
{
    protected $waha;

    public function __construct(WahaService $waha)
    {
        $this->waha = $waha;
    }

    public function index(Request $request)
    {
        // Hanya tampilkan SK yang menunggu persetujuan Dekan
        $query = AccSKBebanMengajar::where('Status', 'Menunggu-Persetujuan-Dekan');

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
        // Tingkatkan limit waktu eksekusi karena pengiriman notifikasi WA bisa memakan waktu lama
        set_time_limit(180);

        DB::beginTransaction();
        try {
            $sk = AccSKBebanMengajar::findOrFail($id);

            // Validasi: hanya bisa approve jika status masih Menunggu-Persetujuan-Dekan
            if ($sk->Status !== 'Menunggu-Persetujuan-Dekan') {
                DB::rollBack();
                return redirect()->back()->with('error', 'SK tidak dalam status yang valid untuk disetujui');
            }

            // Ambil data Dekan yang login
            $user = Auth::user();
            $dekan = Dosen::where('Id_User', $user->Id_User)->first();

            if (!$dekan) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Data Dekan tidak ditemukan');
            }

            // Generate QR Code untuk tanda tangan
            $qrData = "SK Beban Mengajar\nNomor: {$sk->Nomor_Surat}\nTahun Akademik: {$sk->Tahun_Akademik}\nSemester: {$sk->Semester}\nDitandatangani oleh: {$dekan->Nama_Dosen}\nNIP: {$dekan->NIP}\nTanggal: " . now()->format('d-m-Y H:i:s');

            $qrCode = QrCodeHelper::generateQrCode($qrData);

            // 1. Update status SK Utama (Acc_SK_Beban_Mengajar)
            $sk->update([
                'Status' => 'Selesai',
                'Id_Dekan' => $dekan->Id_Dosen,
                'Tanggal-Persetujuan-Dekan' => now(),
                'QR_Code' => $qrCode
            ]);

            // 2. Update status semua Req_SK_Beban_Mengajar yang terkait
            // Gunakan ID dari $sk->No untuk memastikan konsistensi
            DB::table('Req_SK_Beban_Mengajar')
                ->where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                ->update([
                    'Status' => 'Selesai',
                    'Nomor_Surat' => $sk->Nomor_Surat
                ]);

            DB::commit();

            // --- PROSES NOTIFIKASI (Diluar Transaksi agar jika gagal WA status tetap Selesai) ---
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

                    if ($adminUser->No_WA) {
                        $pesanWA = "✅ *SK BEBAN MENGAJAR DISETUJUI*\n\nSK Tahun Akademik {$sk->Tahun_Akademik} Semester {$sk->Semester} telah disetujui oleh Dekan.\n\n*Nomor Surat:* {$sk->Nomor_Surat}\n\n_Sistem SIFAKULTAS_";
                        $this->waha->sendMessage($adminUser->No_WA, $pesanWA);
                    }
                } catch (\Exception $e) {
                    Log::error('Notification Admin Error: ' . $e->getMessage());
                }
            }

            // 2. Kirim notifikasi ke Kaprodi
            $allSK = SKBebanMengajar::where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                ->whereNotNull('Id_Dosen_Kaprodi')
                ->with(['kaprodi.user', 'prodi'])
                ->get();

            foreach ($allSK as $itemSK) {
                if ($itemSK->kaprodi && $itemSK->kaprodi->user) {
                    try {
                        Notifikasi::create([
                            'Tipe_Notifikasi' => 'Accepted',
                            'Pesan' => "SK Beban Mengajar {$itemSK->prodi->Nama_Prodi} telah disetujui oleh Dekan.",
                            'Dest_user' => $itemSK->kaprodi->Id_User,
                            'Source_User' => $dekanUserId,
                            'Is_Read' => 0
                        ]);

                        if ($itemSK->kaprodi->user->No_WA) {
                            $pesanWA = "✅ *SK BEBAN MENGAJAR DISETUJUI*\n\nSK Prodi " . ($itemSK->prodi->Nama_Prodi ?? '-') . " telah disetujui oleh Dekan.\n\n*Nomor Surat:* {$sk->Nomor_Surat}";
                            $this->waha->sendMessage($itemSK->kaprodi->user->No_WA, $pesanWA);
                        }
                    } catch (\Exception $e) {
                        Log::error('Notification Kaprodi Error: ' . $e->getMessage());
                    }
                }
            }

            // 3. Notifikasi ke Dosen (Hanya yang ada di JSON)
            $bebanData = $sk->Data_Beban_Mengajar;
            if (is_string($bebanData)) {
                $bebanData = json_decode($bebanData, true);
            }

            if (is_array($bebanData)) {
                $dosenNipProcessed = [];
                foreach ($bebanData as $item) {
                    $nip = $item['nip'] ?? $item['NIP'] ?? null;
                    if ($nip && !in_array($nip, $dosenNipProcessed)) {
                        $dosen = Dosen::with('user')->where('NIP', $nip)->first();
                        if ($dosen && $dosen->user) {
                            try {
                                Notifikasi::create([
                                    'Tipe_Notifikasi' => 'Accepted',
                                    'Pesan' => "SK Beban Mengajar Anda telah disetujui oleh Dekan.",
                                    'Dest_user' => $dosen->Id_User,
                                    'Source_User' => $dekanUserId,
                                    'Is_Read' => 0
                                ]);

                                if ($dosen->user->No_WA) {
                                    $pesanWA = "✅ *SK BEBAN MENGAJAR DISETUJUI*\n\nSK Beban Mengajar Anda Semester {$sk->Semester} telah disetujui oleh Dekan.\n\nNomor: {$sk->Nomor_Surat}";
                                    $this->waha->sendMessage($dosen->user->No_WA, $pesanWA);
                                }
                                $dosenNipProcessed[] = $nip;
                            } catch (\Exception $e) {
                                Log::error('Notification Dosen Error: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }

            return redirect()->route('dekan.sk.beban-mengajar.index')->with('success', 'SK Beban Mengajar berhasil disetujui dan notifikasi sedang dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving SK: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui SK: ' . $e->getMessage());
        }
    }

    /**
     * Tolak SK Beban Mengajar
     */
    public function reject(Request $request, $id)
    {
        // Tingkatkan limit waktu eksekusi
        set_time_limit(180);

        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
            'target' => 'required|in:admin,kaprodi'
        ]);

        DB::beginTransaction();
        try {
            $sk = AccSKBebanMengajar::findOrFail($id);

            // Validasi: hanya bisa reject jika status masih Menunggu-Persetujuan-Dekan
            if ($sk->Status !== 'Menunggu-Persetujuan-Dekan') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dalam status yang valid untuk ditolak'
                ], 400);
            }

            $target = $request->target;
            $alasanPenolakan = $request->alasan_penolakan;
            $newStatus = 'Ditolak-Dekan';

            // 1. Update status SK Utama
            $sk->update([
                'Status' => $newStatus,
                'Alasan-Tolak' => $alasanPenolakan
            ]);

            // 2. Update status semua Req_SK_Beban_Mengajar yang terkait
            DB::table('Req_SK_Beban_Mengajar')
                ->where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                ->update([
                    'Status' => $newStatus,
                    'Alasan-Tolak' => $alasanPenolakan
                ]);

            DB::commit();

            // --- PROSES NOTIFIKASI (Diluar Transaksi) ---
            $dekanUserId = Auth::id();

            if ($target === 'admin') {
                $adminUser = User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Pegawai_Fakultas');
                })->first();

                if ($adminUser) {
                    try {
                        Notifikasi::create([
                            'Tipe_Notifikasi' => 'Rejected',
                            'Pesan' => "SK Beban Mengajar Tahun Akademik {$sk->Tahun_Akademik} Semester {$sk->Semester} ditolak oleh Dekan. Alasan: {$alasanPenolakan}",
                            'Dest_user' => $adminUser->Id_User,
                            'Source_User' => $dekanUserId,
                            'Is_Read' => 0
                        ]);

                        if ($adminUser->No_WA) {
                            $pesanWA = "❌ *SK BEBAN MENGAJAR DITOLAK*\n\nSK Tahun Akademik {$sk->Tahun_Akademik} Semester {$sk->Semester} ditolak oleh Dekan.\n\n*Alasan:* {$alasanPenolakan}\n\n_Sistem SIFAKULTAS_";
                            $this->waha->sendMessage($adminUser->No_WA, $pesanWA);
                        }
                    } catch (\Exception $e) {
                        Log::error('Reject Notification Admin Error: ' . $e->getMessage());
                    }
                }
            } else {
                $allSK = SKBebanMengajar::where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                    ->whereNotNull('Id_Dosen_Kaprodi')
                    ->with(['kaprodi.user', 'prodi'])
                    ->get();

                $kaprodiDosenIds = $allSK->pluck('Id_Dosen_Kaprodi')->unique()->filter();

                foreach ($kaprodiDosenIds as $kaprodiDosenId) {
                    $dosenKaprodi = Dosen::with('user')->find($kaprodiDosenId);
                    if ($dosenKaprodi && $dosenKaprodi->Id_User) {
                        $skWithProdi = $allSK->where('Id_Dosen_Kaprodi', $kaprodiDosenId)->first();
                        $prodiName = $skWithProdi && $skWithProdi->prodi ? $skWithProdi->prodi->Nama_Prodi : '-';

                        try {
                            Notifikasi::create([
                                'Tipe_Notifikasi' => 'Rejected',
                                'Pesan' => "SK Beban Mengajar {$prodiName} ditolak oleh Dekan. Alasan: {$alasanPenolakan}",
                                'Dest_user' => $dosenKaprodi->Id_User,
                                'Source_User' => $dekanUserId,
                                'Is_Read' => 0
                            ]);

                            if ($dosenKaprodi->user && $dosenKaprodi->user->No_WA) {
                                $pesanWA = "❌ *SK BEBAN MENGAJAR DITOLAK*\n\nSK Beban Mengajar Prodi {$prodiName} ditolak oleh Dekan.\n\n*Alasan:* {$alasanPenolakan}\n\n_Sistem SIFAKULTAS_";
                                $this->waha->sendMessage($dosenKaprodi->user->No_WA, $pesanWA);
                            }
                        } catch (\Exception $e) {
                            Log::error('Reject Notification Kaprodi Error: ' . $e->getMessage());
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'SK Beban Mengajar berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting SK: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menolak SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan history SK Beban Mengajar yang sudah diproses
     */
    public function history()
    {
        try {
            $history = AccSKBebanMengajar::whereIn('Status', ['Selesai', 'Ditolak-Dekan'])
                ->orderBy('Tanggal-Persetujuan-Dekan', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching SK Beban Mengajar history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat history: ' . $e->getMessage()
            ], 500);
        }
    }
}
