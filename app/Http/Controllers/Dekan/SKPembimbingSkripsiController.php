<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AccSKPembimbingSkripsi;
use App\Models\ReqSKPembimbingSkripsi;
use App\Models\Dosen;
use App\Models\Notifikasi;
use App\Models\User;
use App\Helpers\QrCodeHelper;
use App\Services\WahaService;

class SKPembimbingSkripsiController extends Controller
{
    protected $waha;

    public function __construct(WahaService $waha)
    {
        $this->waha = $waha;
    }

    /**
     * Tampilkan daftar SK Pembimbing Skripsi yang menunggu persetujuan Dekan
     */
    public function index(Request $request)
    {
        // Hanya tampilkan SK yang statusnya Menunggu-Persetujuan-Dekan
        $daftarSK = AccSKPembimbingSkripsi::with(['reqSKPembimbingSkripsi'])
            ->where('Status', 'Menunggu-Persetujuan-Dekan')
            ->orderBy('Tanggal-Pengajuan', 'desc')
            ->get();

        return view('dekan.sk.pembimbing-skripsi.index', compact('daftarSK'));
    }

    /**
     * Ambil detail SK Pembimbing Skripsi untuk preview
     */
    public function detail($id)
    {
        try {
            $sk = AccSKPembimbingSkripsi::findOrFail($id);

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

            Log::info('SK Pembimbing Skripsi Detail - Dekan found', [
                'dekan_name' => $dekanName,
                'dekan_nip' => $dekanNip,
                'id_pejabat' => $dekan->Id_Pejabat
            ]);

            return response()->json([
                'success' => true,
                'sk' => $sk,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching SK Pembimbing Skripsi detail', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Setujui SK Pembimbing Skripsi dan generate QR Code
     */
    public function approve($id)
    {
        set_time_limit(180);
        try {
            DB::beginTransaction();

            Log::info('Starting SK Pembimbing Skripsi approval', ['sk_id' => $id]);

            $sk = AccSKPembimbingSkripsi::findOrFail($id);

            Log::info('SK found', [
                'no' => $sk->No,
                'status' => $sk->Status
            ]);

            // Validasi status - hanya bisa approve jika statusnya Menunggu-Persetujuan-Dekan
            if ($sk->Status !== 'Menunggu-Persetujuan-Dekan') {
                throw new \Exception('SK tidak dapat disetujui. Status saat ini: ' . $sk->Status);
            }

            // Ambil data Dekan
            $dekan = Dosen::where('Id_Pejabat', 1)->first();
            if (!$dekan) {
                throw new \Exception('Data Dekan tidak ditemukan');
            }

            $dekanId = $dekan->Id_Dosen;
            $dekanName = $dekan->Nama_Dosen;
            $dekanNip = $dekan->NIP;

            Log::info('Dekan data retrieved', [
                'dekan_id' => $dekanId,
                'dekan_name' => $dekanName,
                'dekan_nip' => $dekanNip
            ]);

            // Generate QR Code
            $qrData = [
                'nomor_sk' => $sk->Nomor_Surat,
                'semester' => $sk->Semester,
                'tahun_akademik' => $sk->Tahun_Akademik,
                'dekan_name' => $dekanName,
                'dekan_nip' => $dekanNip,
                'tanggal_ttd' => now()->format('Y-m-d H:i:s')
            ];

            // Convert array to JSON string for QR Code
            $qrDataString = json_encode($qrData);
            $qrCodeAbsolutePath = QrCodeHelper::generateQRCode($qrDataString, 'sk_pembimbing_skripsi');

            // Convert absolute path to relative path for database storage
            // Path format: storage/app/public/qr-codes/sk_pembimbing_skripsi_xxx.png
            // Database needs: qr-codes/sk_pembimbing_skripsi_xxx.png
            $qrCodePath = str_replace(storage_path('app/public/'), '', $qrCodeAbsolutePath);

            Log::info('QR Code generated', ['qr_path' => $qrCodePath]);

            // Update SK dengan status Selesai dan simpan QR Code
            $sk->update([
                'Status' => 'Selesai',
                'Tanggal_Persetujuan_Dekan' => now(),
                'Id_Dekan' => $dekanId,
                'QR_Code' => $qrCodePath
            ]);

            Log::info('SK updated to Selesai', [
                'no' => $sk->No,
                'qr_code' => $qrCodePath
            ]);

            // Update semua request SK terkait
            ReqSKPembimbingSkripsi::where('Id_Acc_SK_Pembimbing_Skripsi', $sk->No)
                ->update(['Status' => 'Selesai']);

            Log::info('Related requests updated to Selesai');

            DB::commit();

            // --- PROSES NOTIFIKASI (DI LUAR TRANSAKSI) ---

            // 1. Kirim notifikasi ke Admin Fakultas (Pegawai_Fakultas)
            $adminUsers = User::whereHas('role', function ($q) {
                $q->where('Name_Role', 'Pegawai_Fakultas');
            })->get();

            $adminNotificationsSent = 0;
            foreach ($adminUsers as $adminUser) {
                try {
                    Notifikasi::create([
                        'Dest_user' => $adminUser->Id_User,
                        'Source_User' => Auth::id(),
                        'Tipe_Notifikasi' => 'Accepted',
                        'Pesan' => 'SK Pembimbing Skripsi ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' telah ditandatangani oleh Dekan',
                        'Data_Tambahan' => json_encode([
                            'acc_id' => $sk->No,
                            'nomor_surat' => $sk->Nomor_Surat,
                            'semester' => $sk->Semester,
                            'tahun_akademik' => $sk->Tahun_Akademik,
                            'qr_code' => $qrCodePath
                        ]),
                        'Is_Read' => false
                    ]);

                    if ($adminUser->No_WA) {
                        $pesanWA = "✅ *SK PEMBIMBING SKRIPSI DISETUJUI*\n\nSK Pembimbing Skripsi Semester {$sk->Semester} Tahun Akademik {$sk->Tahun_Akademik} telah disetujui dan ditandatangani oleh Dekan.\n\n*Nomor Surat:* {$sk->Nomor_Surat}\n\n_Sistem SIFAKULTAS_";
                        $this->waha->sendMessage($adminUser->No_WA, $pesanWA);
                    }
                    $adminNotificationsSent++;
                } catch (\Exception $e) {
                    Log::error('Error sending admin notification: ' . $e->getMessage());
                }
            }

            Log::info('Notifications sent to Admin Fakultas on approval', ['count' => $adminNotificationsSent]);

            // 2. Kirim notifikasi ke Kaprodi terkait
            $reqSKList = ReqSKPembimbingSkripsi::where('Id_Acc_SK_Pembimbing_Skripsi', $sk->No)
                ->with(['kaprodi.user'])
                ->get();

            $kaprodiNotificationsSent = 0;
            $sentToKaprodiIds = [];
            foreach ($reqSKList as $reqSK) {
                if ($reqSK->kaprodi && $reqSK->kaprodi->user) {
                    $kaprodiUserId = $reqSK->kaprodi->user->Id_User;

                    // Hindari duplikasi notifikasi untuk kaprodi yang sama
                    if (!in_array($kaprodiUserId, $sentToKaprodiIds)) {
                        try {
                            Notifikasi::create([
                                'Dest_user' => $kaprodiUserId,
                                'Source_User' => Auth::id(),
                                'Tipe_Notifikasi' => 'Accepted',
                                'Pesan' => 'SK Pembimbing Skripsi ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' yang Anda ajukan telah ditandatangani oleh Dekan.',
                                'Data_Tambahan' => json_encode([
                                    'req_id' => $reqSK->No,
                                    'acc_id' => $sk->No,
                                    'semester' => $sk->Semester,
                                    'tahun_akademik' => $sk->Tahun_Akademik,
                                    'qr_code' => $qrCodePath
                                ]),
                                'Is_Read' => false
                            ]);

                            if ($reqSK->kaprodi->user->No_WA) {
                                $pesanWA = "✅ *SK PEMBIMBING SKRIPSI DISETUJUI*\n\nSK Pembimbing Skripsi Semester {$sk->Semester} {$sk->Tahun_Akademik} yang Anda ajukan telah ditandatangani oleh Dekan.\n\n*Nomor SK:* {$sk->Nomor_Surat}\n\n_Sistem SIFAKULTAS_";
                                $this->waha->sendMessage($reqSK->kaprodi->user->No_WA, $pesanWA);
                                Log::info('WhatsApp notification sent to Kaprodi', ['no_wa' => $reqSK->kaprodi->user->No_WA]);
                            }

                            $sentToKaprodiIds[] = $kaprodiUserId;
                            $kaprodiNotificationsSent++;
                        } catch (\Exception $e) {
                            Log::error('Error sending kaprodi notification: ' . $e->getMessage());
                        }

                        Log::info('Internal notification sent to Kaprodi on approval', [
                            'kaprodi_id' => $kaprodiUserId,
                            'req_id' => $reqSK->No
                        ]);
                    }
                }
            }

            Log::info('Total notifications sent to Kaprodi on approval', ['count' => $kaprodiNotificationsSent]);

            // 3. Kirim notifikasi ke semua Dosen yang ada di Data_Pembimbing_Skripsi
            $dataPembimbing = $sk->Data_Pembimbing_Skripsi;
            if (is_string($dataPembimbing)) {
                $dataPembimbing = json_decode($dataPembimbing, true);
            }

            $dosenNotificationsSent = 0;
            $allDosenNips = [];
            $allDosenIds = [];

            if (is_array($dataPembimbing)) {
                foreach ($dataPembimbing as $mahasiswa) {
                    foreach (['pembimbing_1', 'pembimbing_2'] as $pKey) {
                        if (isset($mahasiswa[$pKey])) {
                            $p = $mahasiswa[$pKey];
                            if (is_array($p)) {
                                if (!empty($p['nip']))
                                    $allDosenNips[] = $p['nip'];
                                if (!empty($p['id_dosen']))
                                    $allDosenIds[] = $p['id_dosen'];
                            } elseif (!empty($p)) {
                                $allDosenIds[] = $p;
                            }
                        }
                    }
                }

                $uniqueDosenNips = array_unique(array_filter($allDosenNips));
                $uniqueDosenIds = array_unique(array_filter($allDosenIds));

                // Ambil data dosen berdasarkan NIP atau ID Dosen (Id_User via Dosen -> Users)
                $dosensToNotify = Dosen::with(['user'])
                    ->whereIn('NIP', $uniqueDosenNips)
                    ->orWhereIn('Id_Dosen', $uniqueDosenIds)
                    ->get();

                foreach ($dosensToNotify as $dosen) {
                    if ($dosen && $dosen->user) {
                        try {
                            Notifikasi::create([
                                'Dest_user' => $dosen->user->Id_User,
                                'Source_User' => Auth::id(),
                                'Tipe_Notifikasi' => 'Accepted',
                                'Pesan' => 'Anda telah ditetapkan sebagai Dosen Pembimbing Skripsi untuk semester ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . '. SK telah ditandatangani oleh Dekan.',
                                'Data_Tambahan' => json_encode([
                                    'acc_id' => $sk->No,
                                    'nomor_surat' => $sk->Nomor_Surat,
                                    'semester' => $sk->Semester,
                                    'tahun_akademik' => $sk->Tahun_Akademik,
                                    'qr_code' => $qrCodePath
                                ]),
                                'Is_Read' => false
                            ]);

                            if ($dosen->user->No_WA) {
                                $pesanWA = "✅ *SK PENETAPAN DOSEN PEMBIMBING*\n\nAnda telah ditetapkan sebagai Dosen Pembimbing Skripsi untuk Semester {$sk->Semester} {$sk->Tahun_Akademik}.\n\nSK telah ditandatangani oleh Dekan.\n\n*Nomor SK:* {$sk->Nomor_Surat}\n\n_Sistem SIFAKULTAS_";
                                $this->waha->sendMessage($dosen->user->No_WA, $pesanWA);
                                Log::info('WhatsApp notification sent to Dosen Pembimbing', ['no_wa' => $dosen->user->No_WA, 'dosen_name' => $dosen->Nama_Dosen]);
                            }
                            $dosenNotificationsSent++;

                            Log::info('Internal notification sent to Dosen Pembimbing on approval', [
                                'dosen_id' => $dosen->Id_Dosen,
                                'user_id' => $dosen->user->Id_User,
                                'nama_dosen' => $dosen->Nama_Dosen
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error sending dosen notification: ' . $e->getMessage());
                        }
                    }
                }
            }

            Log::info('Total notifications sent to Dosen Pembimbing on approval', ['count' => $dosenNotificationsSent]);

            return response()->json([
                'success' => true,
                'message' => 'SK Pembimbing Skripsi berhasil disetujui dan ditandatangani',
                'qr_code' => $qrCodePath
            ]);

        } catch (\Exception $e) {
            // Jika dalam transaksi, rollback
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error approving SK Pembimbing Skripsi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tolak SK Pembimbing Skripsi
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|min:10',
            'target' => 'required|in:admin,kaprodi'
        ]);

        try {
            DB::beginTransaction();

            $sk = AccSKPembimbingSkripsi::findOrFail($id);

            if ($sk->Status !== 'Menunggu-Persetujuan-Dekan') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dapat ditolak. Status saat ini: ' . $sk->Status
                ], 400);
            }

            $target = $request->target;

            // Update status SK
            $sk->update([
                'Status' => 'Ditolak-Dekan',
                'Alasan_Tolak' => $request->alasan
            ]);

            // Update request terkait
            ReqSKPembimbingSkripsi::where('Id_Acc_SK_Pembimbing_Skripsi', $sk->No)
                ->update([
                    'Status' => 'Ditolak-Dekan',
                    'Alasan-Tolak' => $request->alasan
                ]);

            if ($target === 'admin') {
                // Kirim ke Admin Fakultas (Pegawai_Fakultas)
                $adminUser = User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Pegawai_Fakultas');
                })->first();

                if ($adminUser) {
                    Notifikasi::create([
                        'Dest_user' => $adminUser->Id_User,
                        'Source_User' => Auth::id(),
                        'Tipe_Notifikasi' => 'Rejected',
                        'Pesan' => 'SK Pembimbing Skripsi ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' ditolak oleh Dekan. Alasan: ' . $request->alasan,
                        'Data_Tambahan' => json_encode([
                            'acc_id' => $sk->No,
                            'nomor_surat' => $sk->Nomor_Surat,
                            'alasan' => $request->alasan,
                            'semester' => $sk->Semester,
                            'tahun_akademik' => $sk->Tahun_Akademik
                        ]),
                        'Is_Read' => false
                    ]);

                    Log::info('Notification sent to Admin Fakultas on rejection', [
                        'admin_id' => $adminUser->Id_User,
                        'sk_no' => $sk->No
                    ]);
                } else {
                    Log::warning('Admin Fakultas user not found for rejection notification');
                }

                $message = 'SK Pembimbing Skripsi berhasil ditolak dan dikembalikan ke Admin Fakultas';
            } else {
                // Kirim ke Kaprodi
                $reqSKList = ReqSKPembimbingSkripsi::where('Id_Acc_SK_Pembimbing_Skripsi', $sk->No)
                    ->with('kaprodi.user')
                    ->get();

                $notificationsSent = 0;
                foreach ($reqSKList as $reqSK) {
                    if ($reqSK->kaprodi && $reqSK->kaprodi->user) {
                        $kaprodiUser = $reqSK->kaprodi->user;
                        $notifPesan = 'SK Pembimbing Skripsi ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' ditolak oleh Dekan. Alasan: ' . $request->alasan;

                        Notifikasi::create([
                            'Dest_user' => $kaprodiUser->Id_User,
                            'Source_User' => Auth::id(),
                            'Tipe_Notifikasi' => 'Rejected',
                            'Pesan' => $notifPesan,
                            'Data_Tambahan' => json_encode([
                                'req_id' => $reqSK->No,
                                'acc_id' => $sk->No,
                                'alasan' => $request->alasan,
                                'semester' => $sk->Semester,
                                'tahun_akademik' => $sk->Tahun_Akademik
                            ]),
                            'Is_Read' => false
                        ]);

                        // Kirim WhatsApp (WAHA)
                        if ($kaprodiUser->No_WA) {
                            try {
                                $this->waha->sendMessage($kaprodiUser->No_WA, $notifPesan);
                            } catch (\Exception $e) {
                                Log::error('Dekan Reject - WA Error: ' . $e->getMessage());
                            }
                        }

                        $notificationsSent++;

                        Log::info('Notification sent to Kaprodi on rejection', [
                            'kaprodi_id' => $kaprodiUser->Id_User,
                            'req_id' => $reqSK->No
                        ]);
                    }
                }

                Log::info('Total notifications sent to Kaprodi', ['count' => $notificationsSent]);

                $message = 'SK Pembimbing Skripsi berhasil ditolak dan notifikasi dikirim ke ' . $notificationsSent . ' Kaprodi';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting SK Pembimbing Skripsi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan history SK yang sudah ditandatangani
     */
    public function history()
    {
        try {
            $history = AccSKPembimbingSkripsi::with(['reqSKPembimbingSkripsi', 'dekan'])
                ->whereIn('Status', ['Selesai', 'Ditolak-Dekan'])
                ->orderBy('No', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching SK Pembimbing Skripsi history', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat history: ' . $e->getMessage()
            ], 500);
        }
    }
}
