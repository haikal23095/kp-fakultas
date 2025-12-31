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

        // Ambil data dari Acc_SK_Dosen_Wali
        $skList = AccDekanDosenWali::where('Status', 'Menunggu-Persetujuan-Wadek-1')
            ->orderBy('Tanggal-Pengajuan', 'desc')
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
}
