<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AccSKPengujiSkripsi;
use App\Models\ReqSKPengujiSkripsi;
use App\Models\Dosen;
use App\Models\Notifikasi;
use App\Models\User;
use App\Helpers\QrCodeHelper;

class SKPengujiSkripsiController extends Controller
{
    /**
     * Tampilkan daftar SK Penguji Skripsi yang menunggu persetujuan Dekan
     */
    public function index(Request $request)
    {
        // Hanya tampilkan SK yang statusnya Menunggu-Persetujuan-Dekan di tabel utama
        $daftarSK = AccSKPengujiSkripsi::with(['reqSKPengujiSkripsi'])
            ->where('Status', 'Menunggu-Persetujuan-Dekan')
            ->orderBy('Tanggal-Pengajuan', 'desc')
            ->get();

        return view('dekan.sk.penguji-skripsi.index', compact('daftarSK'));
    }

    /**
     * Ambil detail SK Penguji Skripsi untuk preview
     */
    public function detail($id)
    {
        try {
            $sk = AccSKPengujiSkripsi::with('reqSKPengujiSkripsi.prodi.jurusan')->findOrFail($id);

            // Process Data_Penguji_Skripsi to include prodi and jurusan info
            $dataPenguji = $sk->Data_Penguji_Skripsi;
            if (is_array($dataPenguji) && count($sk->reqSKPengujiSkripsi) > 0) {
                foreach ($dataPenguji as &$mhs) {
                    // Find matching request SK to get prodi info
                    $reqSK = $sk->reqSKPengujiSkripsi->firstWhere('Id_Mahasiswa', $mhs['mahasiswa_id'] ?? null);

                    if ($reqSK && $reqSK->prodi) {
                        $mhs['prodi_data'] = [
                            'nama_prodi' => $reqSK->prodi->Nama_Prodi ?? '-',
                            'jurusan' => $reqSK->prodi->jurusan ? [
                                'Nama_Jurusan' => $reqSK->prodi->jurusan->Nama_Jurusan ?? '-'
                            ] : null
                        ];
                    }

                    // Enrich penguji data from Dosen table
                    for ($i = 1; $i <= 3; $i++) {
                        $fieldName = 'penguji_' . $i . '_id';
                        if (isset($mhs[$fieldName]) && $mhs[$fieldName]) {
                            $dosen = Dosen::find($mhs[$fieldName]);
                            if ($dosen) {
                                $mhs['penguji_' . $i] = [
                                    'id' => $dosen->No,
                                    'nama_dosen' => $dosen->Nama_Dosen,
                                    'nip' => $dosen->NIP
                                ];
                            }
                        }
                    }
                }
                $sk->Data_Penguji_Skripsi = $dataPenguji;
            }

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

            Log::info('SK Penguji Skripsi Detail - Dekan found', [
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
            Log::error('Error fetching SK Penguji Skripsi detail', [
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
     * Setujui SK Penguji Skripsi dan generate QR Code
     */
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            Log::info('Starting SK Penguji Skripsi approval', ['sk_id' => $id]);

            $sk = AccSKPengujiSkripsi::findOrFail($id);

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
            $qrCodeAbsolutePath = QrCodeHelper::generateQRCode($qrDataString, 'sk_penguji_skripsi');

            // Convert absolute path to relative path for database storage
            $qrCodePath = str_replace(storage_path('app/public/'), '', $qrCodeAbsolutePath);

            Log::info('QR Code generated', ['qr_path' => $qrCodePath]);

            // Update SK dengan status Selesai dan simpan QR Code
            $sk->Status = 'Selesai';
            $sk->Tanggal_Persetujuan_Dekan = now();
            $sk->Id_Dekan = $dekanId;
            $sk->QR_Code = $qrCodePath;
            $sk->save();

            Log::info('SK updated to Selesai', [
                'no' => $sk->No,
                'qr_code' => $qrCodePath
            ]);

            // Update semua request SK terkait
            ReqSKPengujiSkripsi::where('Id_Acc_SK_Penguji_Skripsi', $sk->No)
                ->update(['Status' => 'Selesai']);

            Log::info('Related requests updated to Selesai');

            // Kirim notifikasi ke Admin Fakultas (Pegawai_Fakultas)
            $adminUsers = User::whereHas('role', function ($q) {
                $q->where('Name_Role', 'Pegawai_Fakultas');
            })->get();

            $adminNotificationsSent = 0;
            foreach ($adminUsers as $adminUser) {
                Notifikasi::create([
                    'Dest_user' => $adminUser->Id_User,
                    'Source_User' => Auth::id(),
                    'Tipe_Notifikasi' => 'Accepted',
                    'Pesan' => 'SK Penguji Skripsi ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' telah ditandatangani oleh Dekan',
                    'Data_Tambahan' => json_encode([
                        'acc_id' => $sk->No,
                        'nomor_surat' => $sk->Nomor_Surat,
                        'semester' => $sk->Semester,
                        'tahun_akademik' => $sk->Tahun_Akademik,
                        'qr_code' => $qrCodePath
                    ]),
                    'Is_Read' => false
                ]);
                $adminNotificationsSent++;
            }

            Log::info('Notifications sent to Admin Fakultas on approval', ['count' => $adminNotificationsSent]);

            // Kirim notifikasi ke Kaprodi terkait
            $reqSKList = ReqSKPengujiSkripsi::where('Id_Acc_SK_Penguji_Skripsi', $sk->No)
                ->with('kaprodi.user')
                ->get();

            $kaprodiNotificationsSent = 0;
            $sentToKaprodiIds = [];

            foreach ($reqSKList as $reqSK) {
                if ($reqSK->kaprodi && $reqSK->kaprodi->user) {
                    $kaprodiUserId = $reqSK->kaprodi->user->Id_User;

                    // Hindari duplikasi notifikasi untuk kaprodi yang sama
                    if (!in_array($kaprodiUserId, $sentToKaprodiIds)) {
                        Notifikasi::create([
                            'Dest_user' => $kaprodiUserId,
                            'Source_User' => Auth::id(),
                            'Tipe_Notifikasi' => 'Accepted',
                            'Pesan' => 'SK Penguji Skripsi ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' yang Anda ajukan telah ditandatangani oleh Dekan',
                            'Data_Tambahan' => json_encode([
                                'req_id' => $reqSK->No,
                                'acc_id' => $sk->No,
                                'semester' => $sk->Semester,
                                'tahun_akademik' => $sk->Tahun_Akademik
                            ]),
                            'Is_Read' => false
                        ]);

                        $sentToKaprodiIds[] = $kaprodiUserId;
                        $kaprodiNotificationsSent++;

                        Log::info('Notification sent to Kaprodi on approval', [
                            'kaprodi_id' => $kaprodiUserId,
                            'req_id' => $reqSK->No
                        ]);
                    }
                }
            }

            Log::info('Total notifications sent to Kaprodi on approval', ['count' => $kaprodiNotificationsSent]);

            // Kirim notifikasi ke semua Dosen yang ada di Data_Penguji_Skripsi
            $dataPenguji = $sk->Data_Penguji_Skripsi;
            if (is_string($dataPenguji)) {
                $dataPenguji = json_decode($dataPenguji, true);
            }

            $dosenNotificationsSent = 0;
            $uniqueDosenIds = [];

            if (is_array($dataPenguji)) {
                foreach ($dataPenguji as $mahasiswa) {
                    // Ambil ID penguji 1, 2, dan 3
                    for ($i = 1; $i <= 3; $i++) {
                        $fieldName = 'penguji_' . $i . '_id';
                        if (isset($mahasiswa[$fieldName]) && $mahasiswa[$fieldName]) {
                            $uniqueDosenIds[] = $mahasiswa[$fieldName];
                        }
                    }
                }

                // Hapus duplikat ID dosen
                $uniqueDosenIds = array_unique($uniqueDosenIds);

                // Kirim notifikasi ke setiap dosen penguji
                foreach ($uniqueDosenIds as $idDosen) {
                    $dosen = Dosen::with('user')->find($idDosen);
                    if ($dosen && $dosen->user) {
                        Notifikasi::create([
                            'Dest_user' => $dosen->user->Id_User,
                            'Source_User' => Auth::id(),
                            'Tipe_Notifikasi' => 'Accepted',
                            'Pesan' => 'Anda telah ditetapkan sebagai Dosen Penguji Skripsi untuk semester ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . '. SK telah ditandatangani oleh Dekan.',
                            'Data_Tambahan' => json_encode([
                                'acc_id' => $sk->No,
                                'nomor_surat' => $sk->Nomor_Surat,
                                'semester' => $sk->Semester,
                                'tahun_akademik' => $sk->Tahun_Akademik,
                                'qr_code' => $qrCodePath
                            ]),
                            'Is_Read' => false
                        ]);
                        $dosenNotificationsSent++;

                        Log::info('Notification sent to Dosen Penguji on approval', [
                            'dosen_id' => $dosen->Id_Dosen,
                            'user_id' => $dosen->user->Id_User,
                            'nama_dosen' => $dosen->Nama_Dosen
                        ]);
                    }
                }
            }

            Log::info('Total notifications sent to Dosen Penguji on approval', ['count' => $dosenNotificationsSent]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SK Penguji Skripsi berhasil disetujui dan ditandatangani',
                'qr_code' => $qrCodePath
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving SK Penguji Skripsi', [
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
     * Tolak SK Penguji Skripsi
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|min:10',
            'target' => 'required|in:admin,kaprodi'
        ]);

        try {
            DB::beginTransaction();

            $sk = AccSKPengujiSkripsi::findOrFail($id);

            if ($sk->Status !== 'Menunggu-Persetujuan-Dekan') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dapat ditolak. Status saat ini: ' . $sk->Status
                ], 400);
            }

            $target = $request->target;

            // Update status SK
            $sk->Status = 'Ditolak-Dekan';
            $sk->{'Alasan-Tolak'} = $request->alasan;
            $sk->save();

            // Update request terkait
            ReqSKPengujiSkripsi::where('Id_Acc_SK_Penguji_Skripsi', $sk->No)
                ->update([
                    'Status' => 'Ditolak-Dekan',
                    'Alasan-Tolak' => $request->alasan
                ]);

            if ($target === 'admin') {
                // Kirim ke semua Admin Fakultas (Pegawai_Fakultas)
                $adminUsers = User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Pegawai_Fakultas');
                })->get();

                $notificationsSent = 0;
                foreach ($adminUsers as $adminUser) {
                    Notifikasi::create([
                        'Dest_user' => $adminUser->Id_User,
                        'Source_User' => Auth::id(),
                        'Tipe_Notifikasi' => 'Rejected',
                        'Pesan' => 'SK Penguji Skripsi ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' ditolak oleh Dekan. Alasan: ' . $request->alasan,
                        'Data_Tambahan' => json_encode([
                            'acc_id' => $sk->No,
                            'nomor_surat' => $sk->Nomor_Surat,
                            'alasan' => $request->alasan,
                            'semester' => $sk->Semester,
                            'tahun_akademik' => $sk->Tahun_Akademik
                        ]),
                        'Is_Read' => false
                    ]);
                    $notificationsSent++;
                }

                Log::info('Notifications sent to Admin Fakultas on rejection', [
                    'count' => $notificationsSent,
                    'sk_no' => $sk->No
                ]);

                $message = 'SK Penguji Skripsi berhasil ditolak dan dikembalikan ke Admin Fakultas. ' . $notificationsSent . ' notifikasi terkirim.';
            } else {
                // Kirim ke Kaprodi
                $reqSKList = ReqSKPengujiSkripsi::where('Id_Acc_SK_Penguji_Skripsi', $sk->No)
                    ->with('kaprodi.user')
                    ->get();

                $notificationsSent = 0;
                $sentToKaprodiIds = [];

                foreach ($reqSKList as $reqSK) {
                    if ($reqSK->kaprodi && $reqSK->kaprodi->user) {
                        $kaprodiUserId = $reqSK->kaprodi->user->Id_User;

                        // Hindari duplikasi notifikasi untuk kaprodi yang sama
                        if (!in_array($kaprodiUserId, $sentToKaprodiIds)) {
                            Notifikasi::create([
                                'Dest_user' => $kaprodiUserId,
                                'Source_User' => Auth::id(),
                                'Tipe_Notifikasi' => 'Rejected',
                                'Pesan' => 'SK Penguji Skripsi ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' ditolak oleh Dekan. Alasan: ' . $request->alasan,
                                'Data_Tambahan' => json_encode([
                                    'req_id' => $reqSK->No,
                                    'acc_id' => $sk->No,
                                    'alasan' => $request->alasan,
                                    'semester' => $sk->Semester,
                                    'tahun_akademik' => $sk->Tahun_Akademik
                                ]),
                                'Is_Read' => false
                            ]);

                            $sentToKaprodiIds[] = $kaprodiUserId;
                            $notificationsSent++;

                            Log::info('Notification sent to Kaprodi on rejection', [
                                'kaprodi_id' => $kaprodiUserId,
                                'req_id' => $reqSK->No
                            ]);
                        }
                    }
                }

                Log::info('Total notifications sent to Kaprodi on rejection', ['count' => $notificationsSent]);

                $message = 'SK Penguji Skripsi berhasil ditolak dan notifikasi dikirim ke ' . $notificationsSent . ' Kaprodi';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting SK Penguji Skripsi', [
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
            $history = AccSKPengujiSkripsi::with(['reqSKPengujiSkripsi', 'dekan'])
                ->whereIn('Status', ['Selesai', 'Ditolak-Dekan'])
                ->orderBy('Tanggal-Pengajuan', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching SK Penguji Skripsi history', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat history: ' . $e->getMessage()
            ], 500);
        }
    }
}
