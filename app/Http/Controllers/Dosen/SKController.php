<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\AccDekanDosenWali;
use App\Models\AccSKBebanMengajar;
use App\Models\AccSKPembimbingSkripsi;
use App\Models\ReqSKPembimbingSkripsi;
use App\Models\AccSKPengujiSkripsi;
use App\Models\ReqSKPengujiSkripsi;
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

    /**
     * Display SK Beban Mengajar yang melibatkan dosen login.
     */
    public function indexBebanMengajar()
    {
        $user = Auth::user();
        $dosen = ModelDosen::where('Id_User', $user->Id_User)->first();

        if (!$dosen) {
            return redirect()->route('dosen.sk.index')->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil SK yang sudah disetujui Dekan
        $skList = AccSKBebanMengajar::where('Status', 'Selesai')
            ->with(['dekan'])
            ->orderBy('Tanggal-Persetujuan-Dekan', 'desc')
            ->get();

        // Filter SK yang melibatkan dosen ini
        $filteredSK = $skList->filter(function ($sk) use ($dosen) {
            $dataBeban = $sk->Data_Beban_Mengajar ?? [];

            foreach ($dataBeban as $item) {
                if (
                    isset($item['nama_dosen']) &&
                    stripos($item['nama_dosen'], $dosen->Nama_Dosen) !== false
                ) {
                    return true;
                }
            }
            return false;
        });

        return view('dosen.sk.beban-mengajar.index', compact('filteredSK', 'dosen'));
    }

    /**
     * Get detail SK Beban Mengajar for preview.
     */
    public function detailBebanMengajar($id)
    {
        try {
            $sk = AccSKBebanMengajar::with(['dekan'])->findOrFail($id);

            $dekanName = $sk->dekan ? $sk->dekan->Nama_Dosen : 'Dekan';
            $dekanNip = $sk->dekan ? $sk->dekan->NIP : '-';

            // Get QR Code path
            $qrCodePath = null;
            if ($sk->QR_Code) {
                $qrCodePath = asset('storage/' . $sk->QR_Code);
            }

            return response()->json([
                'success' => true,
                'sk' => $sk,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip,
                'qrCodePath' => $qrCodePath
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download SK Beban Mengajar as PDF.
     */
    public function downloadBebanMengajar($id)
    {
        try {
            $sk = AccSKBebanMengajar::with(['dekan'])->findOrFail($id);

            $dekanName = $sk->dekan ? $sk->dekan->Nama_Dosen : 'Dekan';
            $dekanNip = $sk->dekan ? $sk->dekan->NIP : '-';

            return view('dosen.sk.beban-mengajar.download', compact('sk', 'dekanName', 'dekanNip'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh SK: ' . $e->getMessage());
        }
    }

    /**
     * Display SK Pembimbing Skripsi yang melibatkan dosen login.
     */
    public function indexPembimbingSkripsi()
    {
        $user = Auth::user();
        $dosen = ModelDosen::where('Id_User', $user->Id_User)->first();

        if (!$dosen) {
            return redirect()->route('dosen.sk.index')->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil SK yang sudah disetujui Dekan melalui tabel ReqSKPembimbingSkripsi
        $skList = ReqSKPembimbingSkripsi::where('Status', 'Selesai')
            ->with(['accSKPembimbingSkripsi.dekan', 'prodi'])
            ->orderBy('No', 'desc')
            ->get();

        // Filter SK yang melibatkan dosen ini sebagai pembimbing
        $filteredSK = $skList->filter(function ($sk) use ($dosen) {
            $dataPembimbing = is_string($sk->Data_Pembimbing_Skripsi)
                ? json_decode($sk->Data_Pembimbing_Skripsi, true)
                : $sk->Data_Pembimbing_Skripsi;

            if (!is_array($dataPembimbing)) {
                return false;
            }

            foreach ($dataPembimbing as $mhs) {
                // Cek pembimbing 1
                if (
                    isset($mhs['pembimbing_1']['nama_dosen']) &&
                    stripos($mhs['pembimbing_1']['nama_dosen'], $dosen->Nama_Dosen) !== false
                ) {
                    return true;
                }
                // Cek pembimbing 2
                if (
                    isset($mhs['pembimbing_2']['nama_dosen']) &&
                    stripos($mhs['pembimbing_2']['nama_dosen'], $dosen->Nama_Dosen) !== false
                ) {
                    return true;
                }
            }
            return false;
        });

        // Pre-calculate mahasiswa counts for the view
        foreach ($filteredSK as $sk) {
            $pembimbingData = is_string($sk->Data_Pembimbing_Skripsi)
                ? json_decode($sk->Data_Pembimbing_Skripsi, true)
                : $sk->Data_Pembimbing_Skripsi;

            $sk->totalMahasiswa = is_array($pembimbingData) ? count($pembimbingData) : 0;

            $sk->myMahasiswa = collect($pembimbingData)->filter(function ($mhs) use ($dosen) {
                // Check pembimbing 1
                $isP1 = isset($mhs['pembimbing_1']['nama_dosen']) &&
                    stripos($mhs['pembimbing_1']['nama_dosen'], $dosen->Nama_Dosen) !== false;
                // Check pembimbing 2
                $isP2 = isset($mhs['pembimbing_2']['nama_dosen']) &&
                    stripos($mhs['pembimbing_2']['nama_dosen'], $dosen->Nama_Dosen) !== false;

                return $isP1 || $isP2;
            })->count();
        }

        return view('dosen.sk.pembimbing-skripsi.index', compact('filteredSK', 'dosen'));
    }

    /**
     * Download SK Pembimbing Skripsi as PDF.
     */
    public function downloadPembimbingSkripsi($id)
    {
        try {
            $sk = ReqSKPembimbingSkripsi::with(['accSKPembimbingSkripsi.dekan', 'prodi'])->findOrFail($id);

            $accSK = $sk->accSKPembimbingSkripsi;
            if (!$accSK || !$accSK->Nomor_Surat) {
                return redirect()->back()->with('error', 'Nomor surat belum tersedia');
            }

            $dekanName = $accSK->dekan ? $accSK->dekan->Nama_Dosen : 'Dekan';
            $dekanNip = $accSK->dekan ? $accSK->dekan->NIP : '-';

            // Get QR Code path
            $qrCodePath = null;
            if ($accSK->QR_Code) {
                $fullPath = storage_path('app/public/' . $accSK->QR_Code);
                if (file_exists($fullPath)) {
                    $qrCodePath = asset('storage/' . $accSK->QR_Code);
                }
            }

            // Prepare data for view - gunakan data dari ReqSK tapi dengan nomor surat dari AccSK
            $skData = $accSK;
            $skData->Data_Pembimbing_Skripsi = $sk->Data_Pembimbing_Skripsi;
            $skData->Semester = $sk->Semester;
            $skData->Tahun_Akademik = $sk->Tahun_Akademik;
            $skData->prodi = $sk->prodi;

            return view('dosen.sk.pembimbing-skripsi.download', [
                'sk' => $skData,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip,
                'qrCodePath' => $qrCodePath
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh SK: ' . $e->getMessage());
        }
    }

    /**
     * Get detail SK Pembimbing Skripsi for preview.
     */
    public function detailPembimbingSkripsi($id)
    {
        try {
            $sk = ReqSKPembimbingSkripsi::with(['accSKPembimbingSkripsi.dekan', 'prodi'])->findOrFail($id);

            $accSK = $sk->accSKPembimbingSkripsi;
            if (!$accSK) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail persetujuan SK belum tersedia'
                ], 404);
            }

            $dekanName = $accSK->dekan ? $accSK->dekan->Nama_Dosen : 'Dekan';
            $dekanNip = $accSK->dekan ? $accSK->dekan->NIP : '-';

            // Get QR Code path
            $qrCodePath = null;
            if ($accSK->QR_Code) {
                $qrCodePath = asset('storage/' . $accSK->QR_Code);
            }

            // Prepare data
            $skData = $accSK->toArray();
            $skData['Data_Pembimbing_Skripsi'] = is_string($sk->Data_Pembimbing_Skripsi)
                ? json_decode($sk->Data_Pembimbing_Skripsi, true)
                : $sk->Data_Pembimbing_Skripsi;
            $skData['Semester'] = $sk->Semester;
            $skData['Tahun_Akademik'] = $sk->Tahun_Akademik;
            $skData['prodi'] = $sk->prodi;

            return response()->json([
                'success' => true,
                'sk' => $skData,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip,
                'qrCodePath' => $qrCodePath
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display SK Penguji Skripsi yang melibatkan dosen login.
     */
    public function indexPengujiSkripsi()
    {
        $user = Auth::user();
        $dosen = ModelDosen::where('Id_User', $user->Id_User)->first();

        if (!$dosen) {
            return redirect()->route('dosen.sk.index')->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil SK yang sudah disetujui Dekan
        $skList = ReqSKPengujiSkripsi::where('Status', 'Selesai')
            ->with(['accSKPengujiSkripsi.dekan', 'prodi'])
            ->orderBy('No', 'desc')
            ->get();

        // Filter SK yang melibatkan dosen ini sebagai penguji
        $filteredSK = $skList->filter(function ($sk) use ($dosen) {
            $dataPenguji = is_string($sk->Data_Penguji_Skripsi)
                ? json_decode($sk->Data_Penguji_Skripsi, true)
                : $sk->Data_Penguji_Skripsi;

            if (!is_array($dataPenguji)) {
                return false;
            }

            foreach ($dataPenguji as $mhs) {
                if (
                    (isset($mhs['nama_penguji_1']) && stripos($mhs['nama_penguji_1'], $dosen->Nama_Dosen) !== false) ||
                    (isset($mhs['nama_penguji_2']) && stripos($mhs['nama_penguji_2'], $dosen->Nama_Dosen) !== false) ||
                    (isset($mhs['nama_penguji_3']) && stripos($mhs['nama_penguji_3'], $dosen->Nama_Dosen) !== false)
                ) {
                    return true;
                }
            }
            return false;
        });

        // Pre-calculate mahasiswa counts for the view
        foreach ($filteredSK as $sk) {
            $pengujiData = is_string($sk->Data_Penguji_Skripsi)
                ? json_decode($sk->Data_Penguji_Skripsi, true)
                : $sk->Data_Penguji_Skripsi;

            $sk->totalMahasiswa = is_array($pengujiData) ? count($pengujiData) : 0;
            $sk->myMahasiswa = collect($pengujiData)->filter(function ($mhs) use ($dosen) {
                return (isset($mhs['nama_penguji_1']) && stripos($mhs['nama_penguji_1'], $dosen->Nama_Dosen) !== false) ||
                    (isset($mhs['nama_penguji_2']) && stripos($mhs['nama_penguji_2'], $dosen->Nama_Dosen) !== false) ||
                    (isset($mhs['nama_penguji_3']) && stripos($mhs['nama_penguji_3'], $dosen->Nama_Dosen) !== false);
            })->count();
        }

        return view('dosen.sk.penguji-skripsi.index', compact('filteredSK', 'dosen'));
    }

    /**
     * Get detail SK Penguji Skripsi for preview.
     */
    public function detailPengujiSkripsi($id)
    {
        try {
            $sk = ReqSKPengujiSkripsi::with(['accSKPengujiSkripsi.dekan', 'prodi'])->findOrFail($id);
            $accSK = $sk->accSKPengujiSkripsi;

            if (!$accSK) {
                return response()->json(['success' => false, 'message' => 'Detail persetujuan SK belum tersedia'], 404);
            }

            $dekanName = $accSK->dekan ? $accSK->dekan->Nama_Dosen : 'Dekan';
            $dekanNip = $accSK->dekan ? $accSK->dekan->NIP : '-';
            $qrCodePath = $accSK->QR_Code ? asset('storage/' . $accSK->QR_Code) : null;

            $skData = $accSK->toArray();
            $skData['Data_Penguji_Skripsi'] = is_string($sk->Data_Penguji_Skripsi)
                ? json_decode($sk->Data_Penguji_Skripsi, true)
                : $sk->Data_Penguji_Skripsi;
            $skData['Semester'] = $sk->Semester;
            $skData['Tahun_Akademik'] = $sk->Tahun_Akademik;
            $skData['prodi'] = $sk->prodi;

            return response()->json([
                'success' => true,
                'sk' => $skData,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip,
                'qrCodePath' => $qrCodePath
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil detail SK: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download SK Penguji Skripsi as PDF.
     */
    public function downloadPengujiSkripsi($id)
    {
        try {
            $sk = ReqSKPengujiSkripsi::with(['accSKPengujiSkripsi.dekan', 'prodi'])->findOrFail($id);
            $accSK = $sk->accSKPengujiSkripsi;

            if (!$accSK || !$accSK->Nomor_Surat) {
                return redirect()->back()->with('error', 'Nomor surat belum tersedia');
            }

            $dekanName = $accSK->dekan ? $accSK->dekan->Nama_Dosen : 'Dekan';
            $dekanNip = $accSK->dekan ? $accSK->dekan->NIP : '-';
            $qrCodePath = $accSK->QR_Code ? asset('storage/' . $accSK->QR_Code) : null;

            $skData = $accSK;
            $skData->Data_Penguji_Skripsi = $sk->Data_Penguji_Skripsi;
            $skData->Semester = $sk->Semester;
            $skData->Tahun_Akademik = $sk->Tahun_Akademik;
            $skData->prodi = $sk->prodi;

            return view('dosen.sk.penguji-skripsi.download', [
                'sk' => $skData,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip,
                'qrCodePath' => $qrCodePath
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh SK: ' . $e->getMessage());
        }
    }
}
