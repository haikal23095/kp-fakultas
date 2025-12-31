<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\AccDekanDosenWali;
use App\Models\Dosen as ModelDosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SKController extends Controller
{
    /**
     * Display a listing of SK types for Dosen.
     */
    public function index()
    {
        return view('dosen.sk.index');
    }

    /**
     * Display a listing of SK Dosen Wali yang melibatkan dosen login.
     */
    public function indexDosenWali()
    {
        $user = Auth::user();

        // Cari data dosen berdasarkan user login
        $dosen = ModelDosen::where('Id_User', $user->Id_User)->first();

        if (!$dosen) {
            return redirect()->route('dosen.sk.index')->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil semua SK Dosen Wali yang sudah selesai
        $skList = AccDekanDosenWali::where('Status', 'Selesai')
            ->orderBy('Tanggal-Persetujuan-Dekan', 'desc')
            ->get();

        // Filter SK yang mengandung nama dosen ini di Data_Dosen_Wali
        $filteredSK = $skList->filter(function ($sk) use ($dosen) {
            $dataDosenWali = $sk->Data_Dosen_Wali ?? [];

            foreach ($dataDosenWali as $item) {
                // Cek apakah nama dosen ada di array
                if (
                    isset($item['nama_dosen']) &&
                    stripos($item['nama_dosen'], $dosen->Nama_Dosen) !== false
                ) {
                    return true;
                }
            }

            return false;
        });

        return view('dosen.sk.dosen-wali.index', compact('filteredSK', 'dosen'));
    }

    /**
     * Get detail SK Dosen Wali for preview.
     */
    public function detailDosenWali($id)
    {
        try {
            // Ambil data SK dari Acc_SK_Dosen_Wali
            $sk = AccDekanDosenWali::findOrFail($id);

            // Ambil data Dekan
            $dekan = ModelDosen::whereHas('pejabat', function ($query) {
                $query->where('Id_Pejabat', 1); // 1 = Dekan
            })->first();

            $dekanName = $dekan ? $dekan->Nama_Dosen : 'Dekan';
            $dekanNip = $dekan ? $dekan->NIP : '-';

            return response()->json([
                'success' => true,
                'sk' => $sk,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download SK Dosen Wali as PDF.
     */
    public function downloadDosenWali($id)
    {
        try {
            $sk = AccDekanDosenWali::findOrFail($id);

            // Ambil data Dekan
            $dekan = ModelDosen::whereHas('pejabat', function ($query) {
                $query->where('Id_Pejabat', 1);
            })->first();

            $dekanName = $dekan ? $dekan->Nama_Dosen : 'Dekan';
            $dekanNip = $dekan ? $dekan->NIP : '-';

            return view('dosen.sk.dosen-wali.download', compact('sk', 'dekanName', 'dekanNip'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh SK: ' . $e->getMessage());
        }
    }
}
