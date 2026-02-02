<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AccDekanDosenWali;
use App\Models\Dosen;
use App\Models\Notifikasi;
use App\Models\User;
use App\Helpers\QrCodeHelper;
use App\Services\WahaService;

class SKDosenWaliController extends Controller
{
    protected $waha;

    public function __construct(WahaService $waha)
    {
        $this->waha = $waha;
    }

    /**
     * Tampilkan daftar SK Dosen Wali yang menunggu persetujuan Dekan
     */
    public function index(Request $request)
    {
        // Show all SK dengan optional filter, kecuali yang Ditolak-Wadek1 dan Menunggu-Persetujuan-Wadek-1
        $query = AccDekanDosenWali::with(['reqSKDosenWali.prodi', 'reqSKDosenWali.kaprodi.user'])
            ->whereNotIn('Status', ['Ditolak-Wadek1', 'Menunggu-Persetujuan-Wadek-1']);

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        $daftarSK = $query->orderBy('Tanggal-Pengajuan', 'desc')->get();

        return view('dekan.sk.dosen-wali.index', compact('daftarSK'));
    }

    /**
     * Ambil detail SK Dosen Wali untuk preview
     */
    public function detail($id)
    {
        try {
            $sk = AccDekanDosenWali::with(['reqSKDosenWali.prodi'])
                ->findOrFail($id);

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

            Log::info('SK Detail - Dekan found', [
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
            Log::error('Error fetching SK detail', [
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
     * Setujui SK Dosen Wali dan generate QR Code
     */
    public function approve($id)
    {
        set_time_limit(180);

        try {
            DB::beginTransaction();

            Log::info('Starting SK Dosen Wali approval', ['sk_id' => $id]);

            $sk = AccDekanDosenWali::findOrFail($id);

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
                'dekan_name' => $dekanName
            ]);

            // Generate QR Code untuk verifikasi
            $qrContent = url("/verify-sk-dosen-wali/{$sk->No}");
            Log::info('Generating QR Code', ['content' => $qrContent]);

            $qrPath = QrCodeHelper::generate($qrContent, 10);

            if (!$qrPath) {
                Log::error('QR Code generation failed - empty path returned');
                throw new \Exception('Gagal generate QR Code - path kosong');
            }

            Log::info('QR Code generated', ['relative_path' => $qrPath]);

            // Update status dan simpan QR code path relatif
            $sk->Status = 'Selesai';
            $sk->QR_Code = $qrPath;
            $sk->{'Tanggal-Persetujuan-Dekan'} = now();
            $sk->Id_Dekan = Auth::user()->Id_User;
            $sk->save();

            Log::info('SK updated successfully', [
                'status' => $sk->Status,
                'qr_path' => $sk->QR_Code
            ]);

            // Update status di tabel Req_SK_Dosen_Wali yang terhubung dengan SK ini
            $updatedCount = \App\Models\SKDosenWali::where('Id_Acc_SK_Dosen_Wali', $sk->No)
                ->update(['Status' => 'Selesai']);

            Log::info('Updated Req_SK_Dosen_Wali status', [
                'acc_sk_id' => $sk->No,
                'updated_count' => $updatedCount
            ]);

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
                        'Pesan' => 'SK Dosen Wali ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' telah ditandatangani oleh Dekan',
                        'Data_Tambahan' => json_encode([
                            'acc_id' => $sk->No,
                            'nomor_surat' => $sk->Nomor_Surat,
                            'semester' => $sk->Semester,
                            'tahun_akademik' => $sk->Tahun_Akademik,
                            'qr_code' => $qrPath
                        ]),
                        'Is_Read' => false
                    ]);

                    if ($adminUser->No_WA) {
                        $pesanWA = "✅ *SK DOSEN WALI DISETUJUI*\n\nSK Dosen Wali Semester {$sk->Semester} Tahun Akademik {$sk->Tahun_Akademik} telah ditandatangani oleh Dekan.\n\n*Nomor Surat:* {$sk->Nomor_Surat}\n\n_Sistem SIFAKULTAS_";
                        $this->waha->sendMessage($adminUser->No_WA, $pesanWA);
                    }
                    $adminNotificationsSent++;
                } catch (\Exception $e) {
                    Log::error('Error sending admin notification: ' . $e->getMessage());
                }
            }

            Log::info('Notifications sent to Admin Fakultas on approval', ['count' => $adminNotificationsSent]);

            // 2. Kirim notifikasi ke Kaprodi yang mengajukan
            $reqSKList = $sk->reqSKDosenWali()->with('kaprodi.user')->get();
            $kaprodiNotificationsSent = 0;
            $sentToKaprodiIds = [];

            foreach ($reqSKList as $reqSK) {
                if ($reqSK->kaprodi && $reqSK->kaprodi->user) {
                    $kaprodiUserId = $reqSK->kaprodi->user->Id_User;

                    // Hindari duplikasi notifikasi untuk Kaprodi yang sama
                    if (!in_array($kaprodiUserId, $sentToKaprodiIds)) {
                        try {
                            Notifikasi::create([
                                'Dest_user' => $kaprodiUserId,
                                'Source_User' => Auth::id(),
                                'Tipe_Notifikasi' => 'Accepted',
                                'Pesan' => 'SK Dosen Wali ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' yang Anda ajukan telah ditandatangani oleh Dekan.',
                                'Data_Tambahan' => json_encode([
                                    'acc_id' => $sk->No,
                                    'nomor_surat' => $sk->Nomor_Surat,
                                    'semester' => $sk->Semester,
                                    'tahun_akademik' => $sk->Tahun_Akademik,
                                    'qr_code' => $qrPath
                                ]),
                                'Is_Read' => false
                            ]);

                            if ($reqSK->kaprodi->user->No_WA) {
                                $pesanWA = "✅ *SK DOSEN WALI DISETUJUI*\n\nSK Dosen Wali Semester {$sk->Semester} {$sk->Tahun_Akademik} yang Anda ajukan telah ditandatangani oleh Dekan.\n\n*Nomor SK:* {$sk->Nomor_Surat}\n\n_Sistem SIFAKULTAS_";
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
                            'sk_no' => $sk->No
                        ]);
                    }
                }
            }

            Log::info('Total notifications sent to Kaprodi on approval', ['count' => $kaprodiNotificationsSent]);

            // 3. Kirim notifikasi ke semua Dosen yang ada di Data_Dosen_Wali
            $dataDosenWali = $sk->Data_Dosen_Wali;
            if (is_string($dataDosenWali)) {
                $dataDosenWali = json_decode($dataDosenWali, true);
            }

            $dosenNotificationsSent = 0;
            $allDosenNips = [];
            $allDosenIds = [];

            if (is_array($dataDosenWali)) {
                foreach ($dataDosenWali as $dosenData) {
                    // Extract NIP dan ID Dosen dari berbagai kemungkinan struktur data
                    if (!empty($dosenData['nip']))
                        $allDosenNips[] = $dosenData['nip'];
                    if (!empty($dosenData['NIP']))
                        $allDosenNips[] = $dosenData['NIP'];
                    if (!empty($dosenData['id_dosen']))
                        $allDosenIds[] = $dosenData['id_dosen'];
                    if (!empty($dosenData['Id_Dosen']))
                        $allDosenIds[] = $dosenData['Id_Dosen'];
                }

                $uniqueDosenNips = array_unique(array_filter($allDosenNips));
                $uniqueDosenIds = array_unique(array_filter($allDosenIds));

                Log::info('Dosen to notify from Data_Dosen_Wali', [
                    'nips' => $uniqueDosenNips,
                    'ids' => $uniqueDosenIds
                ]);

                // Ambil data dosen berdasarkan NIP atau ID Dosen
                $dosensToNotify = Dosen::with(['user'])
                    ->where(function ($query) use ($uniqueDosenNips, $uniqueDosenIds) {
                        if (!empty($uniqueDosenNips)) {
                            $query->whereIn('NIP', $uniqueDosenNips);
                        }
                        if (!empty($uniqueDosenIds)) {
                            $query->orWhereIn('Id_Dosen', $uniqueDosenIds);
                        }
                    })
                    ->get();

                foreach ($dosensToNotify as $dosen) {
                    if ($dosen && $dosen->user) {
                        try {
                            Notifikasi::create([
                                'Dest_user' => $dosen->user->Id_User,
                                'Source_User' => Auth::id(),
                                'Tipe_Notifikasi' => 'Accepted',
                                'Pesan' => 'Anda telah ditetapkan sebagai Dosen Wali untuk semester ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . '. SK telah ditandatangani oleh Dekan.',
                                'Data_Tambahan' => json_encode([
                                    'acc_id' => $sk->No,
                                    'nomor_surat' => $sk->Nomor_Surat,
                                    'semester' => $sk->Semester,
                                    'tahun_akademik' => $sk->Tahun_Akademik,
                                    'qr_code' => $qrPath
                                ]),
                                'Is_Read' => false
                            ]);

                            if ($dosen->user->No_WA) {
                                $pesanWA = "✅ *SK PENETAPAN DOSEN WALI*\n\nAnda telah ditetapkan sebagai Dosen Wali untuk Semester {$sk->Semester} {$sk->Tahun_Akademik}.\n\nSK telah ditandatangani oleh Dekan.\n\n*Nomor SK:* {$sk->Nomor_Surat}\n\n_Sistem SIFAKULTAS_";
                                $this->waha->sendMessage($dosen->user->No_WA, $pesanWA);
                                Log::info('WhatsApp notification sent to Dosen Wali', ['no_wa' => $dosen->user->No_WA, 'dosen_name' => $dosen->Nama_Dosen]);
                            }
                            $dosenNotificationsSent++;

                            Log::info('Internal notification sent to Dosen Wali on approval', [
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

            Log::info('Total notifications sent to Dosen Wali on approval', ['count' => $dosenNotificationsSent]);

            // Build URL untuk ditampilkan di preview
            $qrUrl = asset('storage/' . $qrPath);

            return response()->json([
                'success' => true,
                'message' => 'SK Dosen Wali berhasil disetujui dan ditandatangani. Notifikasi telah dikirim.',
                'qr_code' => $qrUrl
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('SK not found', [
                'sk_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'SK tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error approving SK Dosen Wali', [
                'sk_id' => $id,
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
     * Tampilkan history SK Dosen Wali yang sudah selesai ditandatangani
     */
    public function history()
    {
        try {
            $history = AccDekanDosenWali::with(['reqSKDosenWali.prodi', 'reqSKDosenWali.kaprodi.user'])
                ->whereIn('Status', ['Selesai', 'Ditolak-Dekan'])
                ->orderBy('Tanggal-Persetujuan-Dekan', 'desc')
                ->get();

            // Get Dekan name for display
            $dekan = Dosen::where('Id_Pejabat', 1)->first();
            $dekanName = $dekan ? $dekan->Nama_Dosen : 'Dr. Budi Hartono, S.Kom., M.Kom.';

            // Add dekan name to each SK
            $history->transform(function ($sk) use ($dekanName) {
                $sk->dekan_name = $dekanName;
                return $sk;
            });

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching history', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tolak SK Dosen Wali dengan pilihan target (admin atau kaprodi)
     */
    public function reject(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'alasan' => 'required|string|min:10',
                'target' => 'required|in:admin,kaprodi'
            ], [
                'alasan.required' => 'Alasan penolakan wajib diisi',
                'alasan.min' => 'Alasan penolakan minimal 10 karakter',
                'target.required' => 'Target penolakan wajib dipilih',
                'target.in' => 'Target penolakan tidak valid'
            ]);

            Log::info('Dekan Reject - Starting', [
                'sk_id' => $id,
                'target' => $validated['target'],
                'alasan' => $validated['alasan']
            ]);

            $sk = AccDekanDosenWali::with(['reqSKDosenWali.prodi', 'reqSKDosenWali.kaprodi.user'])
                ->findOrFail($id);

            // Update status dan alasan penolakan
            $sk->Status = 'Ditolak-Dekan';
            $sk->{'Alasan-Tolak'} = $validated['alasan'];
            $sk->save();

            Log::info('Dekan Reject - SK updated', [
                'sk_no' => $sk->No,
                'status' => $sk->Status
            ]);

            // Update status di tabel Req_SK_Dosen_Wali
            \App\Models\SKDosenWali::where('Id_Acc_SK_Dosen_Wali', $sk->No)
                ->update(['Status' => 'Ditolak-Dekan']);

            // Kirim notifikasi berdasarkan target
            if ($validated['target'] === 'admin') {
                // Kirim notifikasi ke Admin Fakultas (Pegawai_Fakultas)
                Log::info('Dekan Reject - Searching for Admin Fakultas user with role Pegawai_Fakultas');

                $adminFakultasUser = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Pegawai_Fakultas');
                })->first();

                if (!$adminFakultasUser) {
                    Log::warning('Dekan Reject - Admin Fakultas user NOT FOUND with role Pegawai_Fakultas');
                }

                Log::info('Dekan Reject - Admin Fakultas User Found', [
                    'admin_id' => $adminFakultasUser->Id_User,
                    'admin_name' => $adminFakultasUser->name
                ]);

                if ($adminFakultasUser) {
                    $notification = \App\Models\Notifikasi::create([
                        'Dest_user' => $adminFakultasUser->Id_User,
                        'Source_User' => auth()->id(),
                        'Tipe_Notifikasi' => 'Rejected',
                        'Pesan' => "SK Dosen Wali No. {$sk->Nomor_Surat} ditolak oleh Dekan. Alasan: {$validated['alasan']}",
                        'Is_Read' => false,
                        'Data_Tambahan' => [
                            'judul' => 'SK Dosen Wali Ditolak oleh Dekan',
                            'link' => route('admin_fakultas.sk.dosen-wali'),
                            'sk_id' => $sk->No,
                            'nomor_surat' => $sk->Nomor_Surat,
                            'alasan' => $validated['alasan']
                        ]
                    ]);

                    Log::info('Dekan Reject - Notification Created Successfully', [
                        'notification_id' => $notification->Id_Notifikasi,
                        'admin_id' => $adminFakultasUser->Id_User,
                        'admin_name' => $adminFakultasUser->name
                    ]);
                } else {
                    Log::warning('Dekan Reject - Admin Fakultas user is null after query');
                }
            } else {
                // Kirim notifikasi ke Kaprodi
                $firstReq = $sk->reqSKDosenWali->first();
                if ($firstReq && $firstReq->kaprodi && $firstReq->kaprodi->user) {
                    \App\Models\Notifikasi::create([
                        'Dest_user' => $firstReq->kaprodi->user->Id_User,
                        'Source_User' => auth()->id(),
                        'Tipe_Notifikasi' => 'Rejected',
                        'Pesan' => "SK Dosen Wali No. {$sk->Nomor_Surat} ditolak oleh Dekan. Alasan: {$validated['alasan']}",
                        'Is_Read' => false,
                        'Data_Tambahan' => [
                            'judul' => 'SK Dosen Wali Ditolak oleh Dekan',
                            'link' => route('kaprodi.sk.dosen-wali.index'),
                            'sk_id' => $sk->No,
                            'nomor_surat' => $sk->Nomor_Surat,
                            'alasan' => $validated['alasan']
                        ]
                    ]);

                    Log::info('Dekan Reject - Notification sent to Kaprodi', [
                        'kaprodi_id' => $firstReq->kaprodi->user->Id_User
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'SK Dosen Wali berhasil ditolak dan notifikasi telah dikirim'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Dekan Reject - SK not found', [
                'sk_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'SK tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Dekan Reject - Error', [
                'sk_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak SK: ' . $e->getMessage()
            ], 500);
        }
    }
}