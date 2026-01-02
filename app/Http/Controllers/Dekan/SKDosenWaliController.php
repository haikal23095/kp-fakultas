<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AccDekanDosenWali;
use App\Models\Dosen;
use App\Helpers\QrCodeHelper;

class SKDosenWaliController extends Controller
{
    /**
     * Tampilkan daftar SK Dosen Wali yang menunggu persetujuan Dekan
     */
    public function index(Request $request)
    {
        // Show all SK dengan optional filter, kecuali yang Ditolak-Wadek1
        $query = AccDekanDosenWali::with(['reqSKDosenWali.prodi', 'reqSKDosenWali.kaprodi.user'])
            ->where('Status', '!=', 'Ditolak-Wadek1');

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        $daftarSK = $query->orderBy('Tanggal-Pengajuan', 'desc')->get();

        return view('dekan.sk.dosen-wali', compact('daftarSK'));
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
        try {
            Log::info('Starting SK approval', ['sk_id' => $id]);

            $sk = AccDekanDosenWali::findOrFail($id);

            Log::info('SK found', [
                'no' => $sk->No,
                'status' => $sk->Status
            ]);

            // Generate QR Code untuk verifikasi
            $qrContent = url("/verify-sk-dosen-wali/{$sk->No}");
            Log::info('Generating QR Code', ['content' => $qrContent]);

            $qrPath = QrCodeHelper::generate($qrContent, 200);

            if (!$qrPath) {
                Log::error('QR Code generation failed - empty path returned');
                throw new \Exception('Gagal generate QR Code - path kosong');
            }

            Log::info('QR Code generated', ['path' => $qrPath]);

            // Get URL untuk ditampilkan di preview
            $qrUrl = asset('storage/' . $qrPath);
            Log::info('QR Code URL', ['url' => $qrUrl]);

            // Update status dan simpan QR code path
            $sk->Status = 'Selesai'; // Status ENUM: Menunggu-Persetujuan-Wadek-1, Menunggu-Persetujuan-Dekan, Selesai, Ditolak
            $sk->QR_Code = $qrPath; // Simpan path relatif ke database
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

            return response()->json([
                'success' => true,
                'message' => 'SK Dosen Wali berhasil disetujui dan ditandatangani',
                'qr_code' => $qrUrl  // Return URL untuk ditampilkan di HTML
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('SK not found', [
                'sk_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'SK tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error approving SK', [
                'sk_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
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
                ->where('Status', 'Selesai')
                ->whereNotNull('QR_Code')
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