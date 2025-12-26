<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SKDosenWali;
use Illuminate\Support\Facades\Auth;

class SKController extends Controller
{
    /**
     * Display the SK main page with 4 cards showing request counts
     */
    public function index()
    {
        // Get counts for each SK type
        // For now, we only have SK Dosen Wali implemented
        $skDosenWaliCount = SKDosenWali::where('Status', '!=', 'Selesai')
            ->where('Status', '!=', 'Ditolak')
            ->count();

        $skDosenWaliTotal = SKDosenWali::count();

        // TODO: Add counts for other SK types when implemented
        $skBebanMengajarCount = 0;
        $skPembimbingSkripsiCount = 0;
        $skPengujiSkripsiCount = 0;

        return view('admin_fakultas.sk.index', compact(
            'skDosenWaliCount',
            'skDosenWaliTotal',
            'skBebanMengajarCount',
            'skPembimbingSkripsiCount',
            'skPengujiSkripsiCount'
        ));
    }

    /**
     * Display list of SK Dosen Wali requests
     */
    public function dosenWali(Request $request)
    {
        $query = SKDosenWali::with('prodi');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->filled('prodi')) {
            $query->where('Prodi', $request->prodi);
        }

        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        // Get prodi list for filter
        $prodiList = \App\Models\Prodi::orderBy('Nama_Prodi')->get();

        return view('admin_fakultas.sk.dosen-wali.index', compact('skList', 'prodiList'));
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
            'sk_ids.*' => 'exists:SK_Dosen_Wali,No',
            'nomor_surat' => 'required|string|max:100'
        ]);

        try {
            // Update all selected SK items
            $updated = SKDosenWali::whereIn('No', $request->sk_ids)
                ->where('Status', 'Dikerjakan admin')
                ->update([
                    'Status' => 'Menunggu-Persetujuan-Wadek-1',
                    'Nomor_Surat' => $request->nomor_surat
                ]);

            if ($updated === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada SK yang dapat diproses. Pastikan SK memiliki status "Dikerjakan admin"'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengajukan {$updated} SK Dosen Wali ke Wadek 1"
            ]);

        } catch (\Exception $e) {
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
}
