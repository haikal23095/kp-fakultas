<?php

namespace App\Http\Controllers\Wadek1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SKDosenWali;
use App\Models\SKBebanMengajar;
use App\Models\AccDekanDosenWali;
use App\Models\AccSKBebanMengajar;
use App\Models\AccSKPembimbingSkripsi;
use App\Models\ReqSKPembimbingSkripsi;
use App\Models\AccSKPengujiSkripsi;
use App\Models\ReqSKPengujiSkripsi;
use App\Models\Dosen;
use App\Models\Pejabat;
use App\Models\User;
use App\Models\Notifikasi;
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

        // Jumlah SK Beban Mengajar yang menunggu persetujuan Wadek 1
        $skBebanMengajarCount = \App\Models\AccSKBebanMengajar::where('Status', 'Menunggu-Persetujuan-Wadek-1')->count();

        // Jumlah SK Pembimbing Skripsi yang menunggu persetujuan Wadek 1
        $skPembimbingSkripsiCount = AccSKPembimbingSkripsi::where('Status', 'Menunggu-Persetujuan-Wadek-1')->count();

        // Jumlah SK Penguji Skripsi yang menunggu persetujuan Wadek 1
        $skPengujiSkripsiCount = AccSKPengujiSkripsi::where('Status', 'Menunggu-Persetujuan-Wadek-1')->count();

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

    /**
     * List SK Beban Mengajar dari tabel Acc_SK_Beban_Mengajar untuk Wadek 1.
     */
    public function bebanMengajarIndex(Request $request)
    {
        $user = Auth::user()->load('pegawaiFakultas.fakultas', 'dosen.fakultas');
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Jika tidak ada di pegawaiFakultas, coba cari dari dosen
        if (!$fakultasId && $user->dosen) {
            $fakultasId = $user->dosen->Id_Fakultas;
        }

        // Ambil data dari Acc_SK_Beban_Mengajar
        $query = AccSKBebanMengajar::query();

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        // Filter berdasarkan semester
        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')
            ->paginate(15);

        // Ambil info dekan untuk preview
        $dekanName = '';
        $dekanNip = '';

        if ($fakultasId) {
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

        return view('wadek1.sk.beban-mengajar.index', compact('skList', 'dekanName', 'dekanNip'));
    }

    /**
     * Detail SK Beban Mengajar dari Acc_SK_Beban_Mengajar
     */
    public function bebanMengajarDetail($id)
    {
        $sk = AccSKBebanMengajar::findOrFail($id);

        // Ambil info dekan
        $user = Auth::user()->load('pegawaiFakultas.fakultas', 'dosen.fakultas');
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        if (!$fakultasId && $user->dosen) {
            $fakultasId = $user->dosen->Id_Fakultas;
        }

        $dekanName = '';
        $dekanNip = '';

        if ($fakultasId) {
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

        return response()->json([
            'success' => true,
            'sk' => $sk,
            'dekanName' => $dekanName,
            'dekanNip' => $dekanNip
        ]);
    }

    /**
     * Setujui SK Beban Mengajar (ubah status ke Menunggu-Persetujuan-Dekan)
     */
    public function bebanMengajarApprove(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $sk = AccSKBebanMengajar::findOrFail($id);

            if ($sk->Status !== 'Menunggu-Persetujuan-Wadek-1') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK ini tidak dapat disetujui karena statusnya bukan Menunggu-Persetujuan-Wadek-1'
                ], 400);
            }

            // Update status di tabel Acc_SK_Beban_Mengajar
            $sk->Status = 'Menunggu-Persetujuan-Dekan';
            $sk->save();

            // Update status di tabel Req_SK_Beban_Mengajar yang terhubung
            SKBebanMengajar::where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                ->where('Status', 'Menunggu-Persetujuan-Wadek-1')
                ->update(['Status' => 'Menunggu-Persetujuan-Dekan']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SK Beban Mengajar berhasil disetujui dan diteruskan ke Dekan'
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
     * Tolak SK Beban Mengajar
     */
    public function bebanMengajarReject(Request $request, $id)
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

            $sk = AccSKBebanMengajar::findOrFail($id);
            $target = $request->target; // 'admin' atau 'kaprodi'

            \Log::info('Wadek1 Reject SK Beban Mengajar - SK Data:', [
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

            // Update status di tabel Acc_SK_Beban_Mengajar
            $sk->Status = 'Ditolak-Wadek1';
            $sk->{'Alasan-Tolak'} = $request->alasan;
            $sk->save();

            \Log::info('Wadek1 Reject SK Beban Mengajar - SK Updated:', ['no' => $sk->No, 'new_status' => 'Ditolak-Wadek1']);

            // Update status di tabel Req_SK_Beban_Mengajar yang terhubung
            $updatedCount = SKBebanMengajar::where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                ->where('Status', 'Menunggu-Persetujuan-Wadek-1')
                ->update([
                    'Status' => 'Ditolak-Wadek1',
                    'Alasan-Tolak' => $request->alasan
                ]);

            \Log::info('Wadek1 Reject SK Beban Mengajar - Req Updated:', ['count' => $updatedCount]);

            // Get Wadek 1 user (yang login)
            $wadekUser = Auth::user();

            \Log::info('Wadek1 Reject SK Beban Mengajar - Wadek User:', ['id' => $wadekUser->Id_User]);

            // Kirim notifikasi berdasarkan target
            if ($target === 'admin') {
                // Kirim ke Admin Fakultas (Pegawai_Fakultas)
                $adminFakultasUser = \App\Models\User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Pegawai_Fakultas');
                })->first();

                \Log::info('Wadek1 Reject SK Beban Mengajar - Admin Found:', [
                    'found' => $adminFakultasUser ? 'Yes' : 'No',
                    'id' => $adminFakultasUser ? $adminFakultasUser->Id_User : null
                ]);

                if ($adminFakultasUser) {
                    $notif = \App\Models\Notifikasi::create([
                        'Source_User' => $wadekUser->Id_User,
                        'Dest_user' => $adminFakultasUser->Id_User,
                        'Tipe_Notifikasi' => 'Rejected',
                        'Pesan' => 'SK Beban Mengajar ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' (Nomor: ' . ($sk->Nomor_Surat ?? '-') . ') dikembalikan oleh Wadek 1 untuk revisi. Alasan: ' . $request->alasan,
                        'Data_Tambahan' => json_encode(['url' => route('admin_fakultas.sk.beban-mengajar')]),
                        'Is_Read' => false
                    ]);

                    \Log::info('Wadek1 Reject SK Beban Mengajar - Notifikasi to Admin Created:', ['id' => $notif->Id_Notifikasi]);
                }
            } else {
                // Kirim ke Kaprodi - ambil SEMUA request SK yang terhubung dengan eager loading
                $allReqSKs = SKBebanMengajar::where('Id_Acc_SK_Beban_Mengajar', $sk->No)
                    ->with('kaprodi.user') // Eager load kaprodi dan user-nya
                    ->get();

                \Log::info('Wadek1 Reject SK Beban Mengajar - Found Requests:', ['count' => $allReqSKs->count()]);

                $sentCount = 0;
                $kaprodiIds = []; // Track unique kaprodi IDs

                foreach ($allReqSKs as $reqSK) {
                    if ($reqSK->Id_Dosen_Kaprodi && !in_array($reqSK->Id_Dosen_Kaprodi, $kaprodiIds)) {
                        // Gunakan relasi Eloquent
                        $kaprodiUser = null;
                        if ($reqSK->kaprodi && $reqSK->kaprodi->user) {
                            $kaprodiUser = $reqSK->kaprodi->user;
                        }

                        \Log::info('Wadek1 Reject SK Beban Mengajar - Processing Kaprodi:', [
                            'Id_Dosen_Kaprodi' => $reqSK->Id_Dosen_Kaprodi,
                            'found' => $kaprodiUser ? 'Yes' : 'No',
                            'Id_User' => $kaprodiUser ? $kaprodiUser->Id_User : null
                        ]);

                        if ($kaprodiUser) {
                            $notif = \App\Models\Notifikasi::create([
                                'Source_User' => $wadekUser->Id_User,
                                'Dest_user' => $kaprodiUser->Id_User,
                                'Tipe_Notifikasi' => 'Rejected',
                                'Pesan' => 'Pengajuan SK Beban Mengajar ' . $sk->Semester . ' ' . $sk->Tahun_Akademik . ' telah ditolak oleh Wadek 1. Alasan: ' . $request->alasan,
                                'Data_Tambahan' => json_encode(['url' => route('kaprodi.sk.beban-mengajar.index')]),
                                'Is_Read' => false
                            ]);

                            $kaprodiIds[] = $reqSK->Id_Dosen_Kaprodi; // Mark as sent
                            $sentCount++;

                            \Log::info('Wadek1 Reject SK Beban Mengajar - Notifikasi to Kaprodi Created:', [
                                'Id_Notifikasi' => $notif->Id_Notifikasi,
                                'Id_Dosen_Kaprodi' => $reqSK->Id_Dosen_Kaprodi,
                                'Id_User' => $kaprodiUser->Id_User
                            ]);
                        }
                    }
                }

                \Log::info('Wadek1 Reject SK Beban Mengajar - Total Notifications Sent:', ['count' => $sentCount]);
            }

            DB::commit();

            $message = $target === 'admin'
                ? 'SK Beban Mengajar berhasil dikembalikan ke Admin Fakultas untuk revisi'
                : 'SK Beban Mengajar berhasil ditolak dan notifikasi dikirim ke Kaprodi';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Wadek1 Reject SK Beban Mengajar - Error:', [
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

    /**
     * List SK Pembimbing Skripsi dari tabel Acc_SK_Pembimbing_Skripsi
     */
    public function pembimbingSkripsiIndex(Request $request)
    {
        $query = AccSKPembimbingSkripsi::with('reqSKPembimbingSkripsi.prodi');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        } else {
            // Default: tampilkan semua status yang relevan untuk Wadek 1
            $query->whereIn('Status', [
                'Menunggu-Persetujuan-Wadek-1',
                'Menunggu-Persetujuan-Dekan',
                'Ditolak-Wadek1',
                'Selesai'
            ]);
        }

        $skList = $query->orderBy('No', 'desc')->paginate(15);

        // Get dekan info
        $dekan = Dosen::with(['pejabat'])
            ->whereHas('pejabat', function ($q) {
                $q->where('Nama_Jabatan', 'like', 'DEKAN%');
            })
            ->first();

        $dekanName = $dekan ? $dekan->Nama_Dosen : '';
        $dekanNip = $dekan ? $dekan->NIP : '';

        return view('wadek1.sk.pembimbing-skripsi.index', compact('skList', 'dekanName', 'dekanNip'));
    }

    /**
     * Detail SK Pembimbing Skripsi
     */
    public function pembimbingSkripsiDetail($id)
    {
        try {
            $sk = AccSKPembimbingSkripsi::with('reqSKPembimbingSkripsi.prodi.jurusan')->findOrFail($id);

            // Process Data_Pembimbing_Skripsi to include prodi and jurusan info
            $dataPembimbing = $sk->Data_Pembimbing_Skripsi;
            if (is_array($dataPembimbing) && count($sk->reqSKPembimbingSkripsi) > 0) {
                // Get prodi info from first reqSK (assuming all have same prodi)
                $reqSK = $sk->reqSKPembimbingSkripsi->first();
                if ($reqSK && $reqSK->prodi) {
                    // Add prodi and jurusan data to each mahasiswa
                    foreach ($dataPembimbing as &$mhs) {
                        $mhs['prodi_data'] = [
                            'nama_prodi' => $reqSK->prodi->Nama_Prodi,
                            'jurusan' => $reqSK->prodi->jurusan ? [
                                'Nama_Jurusan' => $reqSK->prodi->jurusan->Nama_Jurusan
                            ] : null
                        ];
                    }
                    $sk->Data_Pembimbing_Skripsi = $dataPembimbing;
                }
            }

            // Get dekan info
            $dekan = Dosen::with(['pejabat'])
                ->whereHas('pejabat', function ($q) {
                    $q->where('Nama_Jabatan', 'like', 'DEKAN%');
                })
                ->first();

            $dekanName = $dekan ? $dekan->Nama_Dosen : '';
            $dekanNip = $dekan ? $dekan->NIP : '';

            return response()->json([
                'success' => true,
                'sk' => $sk,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading SK Pembimbing Skripsi detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve SK Pembimbing Skripsi (teruskan ke Dekan)
     */
    public function pembimbingSkripsiApprove($id)
    {
        try {
            DB::beginTransaction();

            $accSK = AccSKPembimbingSkripsi::findOrFail($id);

            if ($accSK->Status !== 'Menunggu-Persetujuan-Wadek-1') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dalam status menunggu persetujuan Wadek 1'
                ], 400);
            }

            // Update status ke Menunggu-Persetujuan-Dekan
            $accSK->update([
                'Status' => 'Menunggu-Persetujuan-Dekan',
                'Tanggal-Persetujuan-Wadek-1' => now()
            ]);

            // Update semua request SK yang terkait
            ReqSKPembimbingSkripsi::where('Id_Acc_SK_Pembimbing_Skripsi', $accSK->No)
                ->update(['Status' => 'Menunggu-Persetujuan-Dekan']);

            // Kirim notifikasi ke Dekan
            $dekanUser = User::whereHas('role', function ($q) {
                $q->where('Name_Role', 'Dekan');
            })->first();

            if ($dekanUser) {
                Notifikasi::create([
                    'Dest_user' => $dekanUser->Id_User,
                    'Source_User' => Auth::id(),
                    'Tipe_Notifikasi' => 'Accepted',
                    'Pesan' => 'SK Pembimbing Skripsi telah disetujui Wadek 1 dan menunggu persetujuan Anda. Semester ' .
                        $accSK->Semester . ' TA ' . $accSK->Tahun_Akademik,
                    'Data_Tambahan' => json_encode([
                        'acc_id' => $accSK->No,
                        'nomor_surat' => $accSK->{'Nomor-SK'}
                    ]),
                    'Is_Read' => false
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SK Pembimbing Skripsi berhasil disetujui dan diteruskan ke Dekan'
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
     * Reject SK Pembimbing Skripsi
     */
    public function pembimbingSkripsiReject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string',
            'target' => 'required|in:admin,kaprodi'
        ]);

        try {
            DB::beginTransaction();

            $accSK = AccSKPembimbingSkripsi::findOrFail($id);

            if ($accSK->Status !== 'Menunggu-Persetujuan-Wadek-1') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dalam status menunggu persetujuan Wadek 1'
                ], 400);
            }

            $target = $request->target;

            if ($target === 'admin') {
                // Kembalikan ke Admin Fakultas
                $accSK->update([
                    'Status' => 'Ditolak-Wadek1',
                    'Alasan_Tolak' => $request->alasan
                ]);

                ReqSKPembimbingSkripsi::where('Id_Acc_SK_Pembimbing_Skripsi', $accSK->No)
                    ->update([
                        'Status' => 'Ditolak-Wadek1',
                        'Alasan-Tolak' => $request->alasan
                    ]);

                // Kirim notifikasi ke Admin Fakultas
                $adminUser = User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Admin Fakultas');
                })->first();

                if ($adminUser) {
                    Notifikasi::create([
                        'Dest_user' => $adminUser->Id_User,
                        'Source_User' => Auth::id(),
                        'Tipe_Notifikasi' => 'Rejected',
                        'Pesan' => 'SK Pembimbing Skripsi dikembalikan untuk revisi. Alasan: ' . $request->alasan,
                        'Data_Tambahan' => json_encode([
                            'acc_id' => $accSK->No,
                            'nomor_surat' => $accSK->{'Nomor-SK'},
                            'alasan' => $request->alasan
                        ]),
                        'Is_Read' => false
                    ]);
                }

                $message = 'SK Pembimbing Skripsi berhasil dikembalikan ke Admin Fakultas untuk revisi';

            } else {
                // Tolak dan kirim notifikasi ke Kaprodi
                $accSK->update([
                    'Status' => 'Ditolak-Wadek1',
                    'Alasan_Tolak' => $request->alasan
                ]);

                ReqSKPembimbingSkripsi::where('Id_Acc_SK_Pembimbing_Skripsi', $accSK->No)
                    ->update([
                        'Status' => 'Ditolak-Wadek1',
                        'Alasan-Tolak' => $request->alasan
                    ]);

                // Kirim notifikasi ke semua Kaprodi yang terkait
                $reqSKList = ReqSKPembimbingSkripsi::where('Id_Acc_SK_Pembimbing_Skripsi', $accSK->No)
                    ->with('kaprodi.user')
                    ->get();

                foreach ($reqSKList as $reqSK) {
                    if ($reqSK->kaprodi && $reqSK->kaprodi->user) {
                        Notifikasi::create([
                            'Dest_user' => $reqSK->kaprodi->user->Id_User,
                            'Source_User' => Auth::id(),
                            'Tipe_Notifikasi' => 'Rejected',
                            'Pesan' => 'SK Pembimbing Skripsi Anda ditolak oleh Wadek 1. Alasan: ' . $request->alasan,
                            'Data_Tambahan' => json_encode([
                                'req_id' => $reqSK->No,
                                'acc_id' => $accSK->No,
                                'alasan' => $request->alasan
                            ]),
                            'Is_Read' => false
                        ]);
                    }
                }

                $message = 'SK Pembimbing Skripsi berhasil ditolak dan notifikasi dikirim ke Kaprodi';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
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
     * List SK Penguji Skripsi dari tabel Acc_SK_Penguji_Skripsi
     */
    public function pengujiSkripsiIndex(Request $request)
    {
        $query = AccSKPengujiSkripsi::with('reqSKPengujiSkripsi.prodi');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        } else {
            // Default: tampilkan semua status yang relevan untuk Wadek 1
            $query->whereIn('Status', [
                'Menunggu-Persetujuan-Wadek-1',
                'Menunggu-Persetujuan-Dekan',
                'Ditolak-Wadek1',
                'Selesai'
            ]);
        }

        $skList = $query->orderBy('No', 'desc')->paginate(15);

        // Get dekan info
        $dekan = Dosen::with(['pejabat'])
            ->whereHas('pejabat', function ($q) {
                $q->where('Nama_Jabatan', 'like', 'DEKAN%');
            })
            ->first();

        $dekanName = $dekan ? $dekan->Nama_Dosen : '';
        $dekanNip = $dekan ? $dekan->NIP : '';

        return view('wadek1.sk.penguji-skripsi.index', compact('skList', 'dekanName', 'dekanNip'));
    }

    /**
     * Detail SK Penguji Skripsi
     */
    public function pengujiSkripsiDetail($id)
    {
        try {
            $sk = AccSKPengujiSkripsi::with('reqSKPengujiSkripsi.prodi.jurusan')->findOrFail($id);

            // Process Data_Penguji_Skripsi to include prodi and jurusan info
            $dataPenguji = $sk->Data_Penguji_Skripsi;
            if (is_array($dataPenguji) && count($sk->reqSKPengujiSkripsi) > 0) {
                // Get prodi info from first reqSK (assuming all have same prodi)
                $reqSK = $sk->reqSKPengujiSkripsi->first();
                if ($reqSK && $reqSK->prodi) {
                    // Add prodi and jurusan data to each mahasiswa
                    foreach ($dataPenguji as &$mhs) {
                        $mhs['prodi_data'] = [
                            'nama_prodi' => $reqSK->prodi->Nama_Prodi,
                            'jurusan' => $reqSK->prodi->jurusan ? [
                                'Nama_Jurusan' => $reqSK->prodi->jurusan->Nama_Jurusan
                            ] : null
                        ];

                        // Enrich penguji data - fetch dosen info for each penguji
                        for ($i = 1; $i <= 3; $i++) {
                            $pengujiKey = 'penguji_' . $i;
                            $fieldName = 'penguji_' . $i . '_id'; // The actual field name in JSON

                            if (isset($mhs[$fieldName]) && !empty($mhs[$fieldName])) {
                                $dosen = Dosen::find($mhs[$fieldName]);
                                if ($dosen) {
                                    $mhs[$pengujiKey] = [
                                        'id' => $dosen->No,
                                        'nama_dosen' => $dosen->Nama_Dosen,
                                        'nip' => $dosen->NIP
                                    ];
                                } else {
                                    $mhs[$pengujiKey] = null;
                                }
                            } else {
                                $mhs[$pengujiKey] = null;
                            }
                        }
                    }
                    $sk->Data_Penguji_Skripsi = $dataPenguji;
                }
            }

            // Get dekan info
            $dekan = Dosen::with(['pejabat'])
                ->whereHas('pejabat', function ($q) {
                    $q->where('Nama_Jabatan', 'like', 'DEKAN%');
                })
                ->first();

            $dekanName = $dekan ? $dekan->Nama_Dosen : '';
            $dekanNip = $dekan ? $dekan->NIP : '';

            return response()->json([
                'success' => true,
                'sk' => $sk,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading SK Penguji Skripsi detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve SK Penguji Skripsi (teruskan ke Dekan)
     */
    public function pengujiSkripsiApprove($id)
    {
        try {
            DB::beginTransaction();

            $accSK = AccSKPengujiSkripsi::findOrFail($id);

            if ($accSK->Status !== 'Menunggu-Persetujuan-Wadek-1') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dalam status menunggu persetujuan Wadek 1'
                ], 400);
            }

            // Update status ke Menunggu-Persetujuan-Dekan
            $accSK->Status = 'Menunggu-Persetujuan-Dekan';
            $accSK->save();

            // Update semua request SK yang terkait
            ReqSKPengujiSkripsi::where('Id_Acc_SK_Penguji_Skripsi', $accSK->No)
                ->update(['Status' => 'Menunggu-Persetujuan-Dekan']);

            // Kirim notifikasi ke Dekan
            $dekanUser = User::whereHas('role', function ($q) {
                $q->where('Name_Role', 'Dekan');
            })->first();

            if ($dekanUser) {
                Notifikasi::create([
                    'Dest_user' => $dekanUser->Id_User,
                    'Source_User' => Auth::id(),
                    'Tipe_Notifikasi' => 'Accepted',
                    'Pesan' => 'SK Penguji Skripsi telah disetujui Wadek 1 dan menunggu persetujuan Anda. Semester ' .
                        $accSK->Semester . ' TA ' . $accSK->Tahun_Akademik,
                    'Data_Tambahan' => json_encode([
                        'acc_id' => $accSK->No,
                        'nomor_surat' => $accSK->Nomor_Surat
                    ]),
                    'Is_Read' => false
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'SK Penguji Skripsi berhasil disetujui dan diteruskan ke Dekan'
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
     * Reject SK Penguji Skripsi
     */
    public function pengujiSkripsiReject(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string',
            'target' => 'required|in:admin,kaprodi'
        ]);

        try {
            DB::beginTransaction();

            $accSK = AccSKPengujiSkripsi::findOrFail($id);

            if ($accSK->Status !== 'Menunggu-Persetujuan-Wadek-1') {
                return response()->json([
                    'success' => false,
                    'message' => 'SK tidak dalam status menunggu persetujuan Wadek 1'
                ], 400);
            }

            $target = $request->target;

            if ($target === 'admin') {
                // Kembalikan ke Admin Fakultas
                $accSK->update([
                    'Status' => 'Ditolak-Wadek1',
                    'Alasan-Tolak' => $request->alasan
                ]);

                ReqSKPengujiSkripsi::where('Id_Acc_SK_Penguji_Skripsi', $accSK->No)
                    ->update([
                        'Status' => 'Ditolak-Wadek1',
                        'Alasan-Tolak' => $request->alasan
                    ]);

                // Kirim notifikasi ke Admin Fakultas (Pegawai_Fakultas)
                $adminUsers = User::whereHas('role', function ($q) {
                    $q->where('Name_Role', 'Pegawai_Fakultas');
                })->get();

                \Log::info('Admin users found: ' . $adminUsers->count());

                $notificationCount = 0;
                foreach ($adminUsers as $adminUser) {
                    Notifikasi::create([
                        'Dest_user' => $adminUser->Id_User,
                        'Source_User' => Auth::id(),
                        'Tipe_Notifikasi' => 'Rejected',
                        'Pesan' => 'SK Penguji Skripsi dikembalikan untuk revisi oleh Wadek 1. Alasan: ' . $request->alasan,
                        'Data_Tambahan' => json_encode([
                            'acc_id' => $accSK->No,
                            'nomor_surat' => $accSK->Nomor_Surat,
                            'semester' => $accSK->Semester,
                            'tahun_akademik' => $accSK->Tahun_Akademik,
                            'alasan' => $request->alasan
                        ]),
                        'Is_Read' => false
                    ]);
                    $notificationCount++;
                }

                \Log::info('Notifications sent: ' . $notificationCount);

                $message = 'SK Penguji Skripsi berhasil dikembalikan ke Admin Fakultas untuk revisi. ' . $notificationCount . ' notifikasi terkirim.';

            } else {
                // Tolak dan kirim notifikasi ke Kaprodi
                $accSK->update([
                    'Status' => 'Ditolak-Wadek1',
                    'Alasan-Tolak' => $request->alasan
                ]);

                ReqSKPengujiSkripsi::where('Id_Acc_SK_Penguji_Skripsi', $accSK->No)
                    ->update([
                        'Status' => 'Ditolak-Wadek1',
                        'Alasan-Tolak' => $request->alasan
                    ]);

                // Kirim notifikasi ke semua Kaprodi yang terkait
                $reqSKList = ReqSKPengujiSkripsi::where('Id_Acc_SK_Penguji_Skripsi', $accSK->No)
                    ->with('kaprodi.user')
                    ->get();

                foreach ($reqSKList as $reqSK) {
                    if ($reqSK->kaprodi && $reqSK->kaprodi->user) {
                        Notifikasi::create([
                            'Dest_user' => $reqSK->kaprodi->user->Id_User,
                            'Source_User' => Auth::id(),
                            'Tipe_Notifikasi' => 'Rejected',
                            'Pesan' => 'SK Penguji Skripsi Anda ditolak oleh Wadek 1. Alasan: ' . $request->alasan,
                            'Data_Tambahan' => json_encode([
                                'req_id' => $reqSK->No,
                                'acc_id' => $accSK->No,
                                'alasan' => $request->alasan
                            ]),
                            'Is_Read' => false
                        ]);
                    }
                }

                $message = 'SK Penguji Skripsi berhasil ditolak dan notifikasi dikirim ke Kaprodi';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
