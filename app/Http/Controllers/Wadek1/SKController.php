<?php

namespace App\Http\Controllers\Wadek1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SKDosenWali;
use App\Models\AccDekanDosenWali;
use Illuminate\Support\Facades\DB;

class SKController extends Controller
{
    /**
     * Halaman ringkasan SK Dosen (4 card) untuk Wadek 1.
     */
    public function index()
    {
        // Jumlah SK Dosen Wali yang sedang menunggu persetujuan Wadek 1
        $skDosenWaliCount = SKDosenWali::where('Status', 'Menunggu-Persetujuan-Wadek-1')->count();
        $skDosenWaliTotal = SKDosenWali::count();

        // SK lain belum diimplementasikan
        $skBebanMengajarCount = 0;
        $skPembimbingSkripsiCount = 0;
        $skPengujiSkripsiCount = 0;

        return view('wadek1.sk.index', compact(
            'skDosenWaliCount',
            'skDosenWaliTotal',
            'skBebanMengajarCount',
            'skPembimbingSkripsiCount',
            'skPengujiSkripsiCount'
        ));
    }

    /**
     * List SK Dosen Wali dari tabel Acc_SK_Dosen_Wali yang statusnya Menunggu-Persetujuan-Wadek-1.
     */
    public function dosenWaliIndex(Request $request)
    {
        $user = Auth::user()->load('pegawaiFakultas.fakultas', 'dosen.fakultas');
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Jika tidak ada di pegawaiFakultas, coba cari dari dosen (gunakan Id_Fakultas langsung)
        if (!$fakultasId && $user->dosen) {
            $fakultasId = $user->dosen->Id_Fakultas;
        }

        // Ambil data dari Acc_SK_Dosen_Wali - tampilkan semua untuk history
        $query = AccDekanDosenWali::query();

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')
            ->paginate(15);

        // Ambil info dekan untuk preview
        $dekanName = '';
        $dekanNip = '';

        if ($fakultasId) {
            // Cari Dekan berdasarkan Fakultas ID langsung dari kolom Dosen.Id_Fakultas
            $dekan = \App\Models\Dosen::select('Dosen.Nama_Dosen', 'Dosen.NIP')
                ->join('Pejabat', 'Dosen.Id_Pejabat', '=', 'Pejabat.Id_Pejabat')
                ->where('Pejabat.Nama_Jabatan', 'Dekan')
                ->where('Dosen.Id_Fakultas', $fakultasId)
                ->whereNotNull('Dosen.Id_Pejabat')
                ->first();

            if ($dekan) {
                $dekanName = $dekan->Nama_Dosen ?? '';
                $dekanNip = $dekan->NIP ?? '';
            }
        }

        return view('wadek1.sk.dosen-wali.index', compact('skList', 'dekanName', 'dekanNip'));
    }

    /**
     * Detail SK Dosen Wali dari Acc_SK_Dosen_Wali untuk preview
     */
    public function dosenWaliDetail($id)
    {
        $sk = AccDekanDosenWali::findOrFail($id);

        // Ambil info dekan
        $user = Auth::user()->load('pegawaiFakultas.fakultas', 'dosen.fakultas');
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Jika tidak ada di pegawaiFakultas, coba cari dari dosen (gunakan Id_Fakultas langsung)
        if (!$fakultasId && $user->dosen) {
            $fakultasId = $user->dosen->Id_Fakultas;
        }

        $dekanName = '';
        $dekanNip = '';

        \Log::info('Wadek1 Detail SK - Fakultas ID: ' . $fakultasId);
        \Log::info('User Info:', [
            'user_id' => $user->Id_User,
            'has_pegawai_fakultas' => $user->pegawaiFakultas ? 'Yes' : 'No',
            'has_dosen' => $user->dosen ? 'Yes' : 'No',
            'fakultas_id' => $fakultasId
        ]);

        if ($fakultasId) {
            // Cari Dekan berdasarkan Fakultas ID langsung dari kolom Dosen.Id_Fakultas
            $dekan = \App\Models\Dosen::select('Dosen.Nama_Dosen', 'Dosen.NIP', 'Dosen.Id_Dosen', 'Dosen.Id_Fakultas')
                ->join('Pejabat', 'Dosen.Id_Pejabat', '=', 'Pejabat.Id_Pejabat')
                ->where('Pejabat.Nama_Jabatan', 'Dekan')
                ->where('Dosen.Id_Fakultas', $fakultasId)
                ->whereNotNull('Dosen.Id_Pejabat')
                ->first();

            \Log::info('Query Dekan Result: ', [
                'found' => $dekan ? 'Yes' : 'No',
                'dekan' => $dekan ? $dekan->toArray() : 'null'
            ]);

            if ($dekan) {
                $dekanName = $dekan->Nama_Dosen ?? '';
                $dekanNip = $dekan->NIP ?? '';
            }
        }

        \Log::info('Final Dekan Data: ', [
            'dekanName' => $dekanName,
            'dekanNip' => $dekanNip
        ]);

        return response()->json([
            'success' => true,
            'sk' => $sk,
            'dekanName' => $dekanName,
            'dekanNip' => $dekanNip,
            'debug' => [
                'fakultasId' => $fakultasId,
                'dekanFound' => !empty($dekanName)
            ]
        ]);
    }

    /**
     * Setujui SK Dosen Wali (ubah status dari Menunggu-Persetujuan-Wadek-1 ke Menunggu-Persetujuan-Dekan)
     */
    public function dosenWaliApprove(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $sk = AccDekanDosenWali::findOrFail($id);

            if ($sk->Status !== 'Menunggu-Persetujuan-Wadek-1') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK ini tidak dapat disetujui karena statusnya bukan Menunggu-Persetujuan-Wadek-1'
                ], 400);
            }

            // Update status di tabel Acc_SK_Dosen_Wali ke Menunggu-Persetujuan-Dekan
            $sk->Status = 'Menunggu-Persetujuan-Dekan';
            $sk->save();

            // Update status di tabel Req_SK_Dosen_Wali yang terhubung dengan SK ini (melalui Id_Acc_SK_Dosen_Wali)
            SKDosenWali::where('Id_Acc_SK_Dosen_Wali', $sk->No)
                ->where('Status', 'Menunggu-Persetujuan-Wadek-1')
                ->update(['Status' => 'Menunggu-Persetujuan-Dekan']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SK Dosen Wali berhasil disetujui dan diteruskan ke Dekan'
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
     * Tolak SK Dosen Wali (ubah status ke Ditolak-Wadek1 dan kirim notifikasi ke Admin Fakultas)
     */
    public function dosenWaliReject(Request $request, $id)
    {
        try {
            $request->validate([
                'alasan' => 'required|string|min:10',
                'target' => 'required|in:admin,kaprodi'
            ], [
                'alasan.required' => 'Alasan penolakan harus diisi',
                'alasan.min' => 'Alasan penolakan minimal 10 karakter',
                'target.required' => 'Tujuan penolakan harus dipilih',
                'target.in' => 'Tujuan penolakan tidak valid'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()['alasan'] ?? ['Unknown error'])
            ], 422);
        }

        try {
            DB::beginTransaction();

            $sk = AccDekanDosenWali::findOrFail($id);
            $target = $request->target; // 'admin' atau 'kaprodi'

            \Log::info('Wadek1 Reject SK - SK Data:', [
                'id' => $id,
                'status' => $sk->Status,
                'semester' => $sk->Semester,
                'tahun' => $sk->Tahun_Akademik,
                'target' => $target
            ]);

            if ($sk->Status !== 'Menunggu-Persetujuan-Wadek-1') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'SK ini tidak dapat ditolak karena statusnya bukan Menunggu-Persetujuan-Wadek-1'
                ], 400);
            }

            // Update status di tabel Acc_SK_Dosen_Wali (gunakan status ENUM yang valid)
            $sk->Status = 'Ditolak-Wadek1';
            $sk->{'Alasan-Tolak'} = $request->alasan;
            $sk->save();

            \Log::info('Wadek1 Reject SK - SK Updated:', ['no' => $sk->No, 'new_status' => 'Ditolak-Wadek1']);

            // Update status di tabel Req_SK_Dosen_Wali yang terhubung
            $updatedCount = SKDosenWali::where('Id_Acc_SK_Dosen_Wali', $sk->No)
                ->where('Status', 'Menunggu-Persetujuan-Wadek-1')
                ->update([
                    'Status' => 'Ditolak-Wadek1',
                    'Alasan-Tolak' => $request->alasan
                ]);

            \Log::info('Wadek1 Reject SK - Req Updated:', ['count' => $updatedCount]);

            // Get Wadek 1 user (yang login)
            $wadekUser = Auth::user();

            \Log::info('Wadek1 Reject SK - Wadek User:', ['id' => $wadekUser->Id_User]);

            // Kirim notifikasi berdasarkan target
            if ($target === 'admin') {
                // Kirim ke Admin Fakultas (Pegawai_Fakultas)
                $adminFakultasUser = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Pegawai_Fakultas');
                })->first();

                \Log::info('Wadek1 Reject SK - Admin Found:', [
                    'found' => $adminFakultasUser ? 'Yes' : 'No',
                    'id' => $adminFakultasUser ? $adminFakultasUser->Id_User : null
                ]);

                if ($adminFakultasUser) {
                    $notif = \App\Models\Notifikasi::create([
                        'Source_User' => $wadekUser->Id_User,
                        'Dest_user' => $adminFakultasUser->Id_User,
                        'Tipe_Notifikasi' => 'Rejected',
                        'Pesan' => 'SK Dosen Wali ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' (Nomor: ' . ($sk->Nomor_Surat ?? '-') . ') dikembalikan oleh Wadek 1 untuk revisi. Alasan: ' . $request->alasan,
                        'Data_Tambahan' => json_encode(['url' => route('admin_fakultas.sk.dosen-wali.history')]),
                        'Is_Read' => false
                    ]);

                    \Log::info('Wadek1 Reject SK - Notifikasi to Admin Created:', ['id' => $notif->Id_Notifikasi]);
                }
            } else {
                // Kirim ke Kaprodi - cari dari request SK yang pertama
                $firstReqSK = SKDosenWali::where('Id_Acc_SK_Dosen_Wali', $sk->No)->first();

                if ($firstReqSK && $firstReqSK->Id_Kaprodi) {
                    $kaprodiUser = \App\Models\User::where('Id_Dosen', $firstReqSK->Id_Kaprodi)->first();

                    \Log::info('Wadek1 Reject SK - Kaprodi Found:', [
                        'found' => $kaprodiUser ? 'Yes' : 'No',
                        'id' => $kaprodiUser ? $kaprodiUser->Id_User : null
                    ]);

                    if ($kaprodiUser) {
                        $notif = \App\Models\Notifikasi::create([
                            'Source_User' => $wadekUser->Id_User,
                            'Dest_user' => $kaprodiUser->Id_User,
                            'Tipe_Notifikasi' => 'Rejected',
                            'Pesan' => 'Pengajuan SK Dosen Wali ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' telah ditolak oleh Wadek 1. Alasan: ' . $request->alasan,
                            'Data_Tambahan' => json_encode(['url' => route('kaprodi.sk.dosen-wali.index')]),
                            'Is_Read' => false
                        ]);

                        \Log::info('Wadek1 Reject SK - Notifikasi to Kaprodi Created:', ['id' => $notif->Id_Notifikasi]);
                    }
                }
            }

            DB::commit();

            $message = $target === 'admin'
                ? 'SK Dosen Wali berhasil dikembalikan ke Admin Fakultas untuk revisi'
                : 'SK Dosen Wali berhasil ditolak dan notifikasi dikirim ke Kaprodi';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Wadek1 Reject SK - Error:', [
                'message' => $e->getMessage(),
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
}
