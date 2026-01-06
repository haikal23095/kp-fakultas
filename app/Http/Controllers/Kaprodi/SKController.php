<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prodi;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\SKDosenWali;
use App\Models\SKBebanMengajar;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SKController extends Controller
{
    /**
     * Display the SK main page with 4 cards
     */
    public function index()
    {
        return view('kaprodi.sk.index');
    }

    /**
     * Display list of submitted SK Beban Mengajar
     */
    public function indexBebanMengajar()
    {
        // TODO: Get data from database when model is ready
        return view('kaprodi.sk.beban-mengajar.index');
    }

    /**
     * Display history of SK Beban Mengajar
     */
    public function historyBebanMengajar(Request $request)
    {
        // Get logged in user's dosen ID
        $user = Auth::user();
        $dosenId = $user->dosen ? $user->dosen->Id_Dosen : null;

        if (!$dosenId) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan');
        }

        // Query SK Beban Mengajar
        $query = SKBebanMengajar::with(['prodi', 'kaprodi'])
            ->where('Id_Dosen_Kaprodi', $dosenId);

        // Apply filters
        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        if ($request->filled('tahun_akademik')) {
            $query->where('Tahun_Akademik', $request->tahun_akademik);
        }

        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        // Order by newest first
        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        return view('kaprodi.sk.beban-mengajar.history', compact('skList'));
    }

    /**
     * Show the form for creating SK Beban Mengajar
     */
    public function createBebanMengajar()
    {
        // Get user info
        $user = Auth::user();

        // Get prodi from logged in kaprodi
        $prodi = null;
        if ($user->dosen) {
            $prodi = $user->dosen->prodi;
        } elseif ($user->pegawai) {
            $prodi = Prodi::find($user->pegawai->Id_Prodi);
        }

        // Get all prodi for dropdown
        $prodis = Prodi::orderBy('Nama_Prodi', 'asc')->get();

        // Get all dosen from the same prodi
        $dosens = Dosen::when($prodi, function ($query) use ($prodi) {
            return $query->where('Id_Prodi', $prodi->Id_Prodi);
        })
            ->orderBy('Nama_Dosen', 'asc')
            ->get();

        // Get mata kuliah (unique names) for the prodi
        $mataKuliahList = MataKuliah::when($prodi, function ($query) use ($prodi) {
            return $query->where('Id_Prodi', $prodi->Id_Prodi);
        })
            ->selectRaw('MIN(Nomor) as Nomor, Nama_Matakuliah, MAX(SKS) as SKS, Id_Prodi')
            ->groupBy('Nama_Matakuliah', 'Id_Prodi')
            ->orderBy('Nama_Matakuliah', 'asc')
            ->get();

        // Get all mata kuliah with classes for JavaScript usage
        $allMataKuliah = MataKuliah::when($prodi, function ($query) use ($prodi) {
            return $query->where('Id_Prodi', $prodi->Id_Prodi);
        })
            ->select('Nomor', 'Nama_Matakuliah', 'Kelas', 'SKS', 'Id_Prodi')
            ->orderBy('Nama_Matakuliah', 'asc')
            ->orderBy('Kelas', 'asc')
            ->get();

        return view('kaprodi.sk.beban-mengajar.create', compact('prodis', 'dosens', 'prodi', 'mataKuliahList', 'allMataKuliah'));
    }

    /**
     * Store SK Beban Mengajar
     */
    public function storeBebanMengajar(Request $request)
    {
        // Validate request
        $request->validate([
            'prodi_id' => 'required|exists:Prodi,Id_Prodi',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_akademik' => 'required|string',
            'beban' => 'required|array|min:1',
            'beban.*.dosen_id' => 'required|exists:Dosen,Id_Dosen',
            'beban.*.mata_kuliah_id' => 'required|exists:Matakuliah,Nomor',
            'beban.*.sks' => 'required|integer|min:1|max:6',
        ], [
            'prodi_id.required' => 'Program studi harus dipilih',
            'semester.required' => 'Semester harus dipilih',
            'tahun_akademik.required' => 'Tahun akademik harus diisi',
            'beban.required' => 'Minimal harus ada 1 beban mengajar',
            'beban.min' => 'Minimal harus ada 1 beban mengajar',
        ]);

        try {
            // Get kaprodi's Id_Dosen
            $user = Auth::user();
            $idDosenKaprodi = null;

            if ($user->dosen) {
                $idDosenKaprodi = $user->dosen->Id_Dosen;
            }

            // Prepare data beban mengajar untuk disimpan ke JSON
            $dataBeban = [];
            foreach ($request->beban as $beban) {
                // Get data dosen
                $dosenInfo = Dosen::find($beban['dosen_id']);

                // Get data mata kuliah (dari Nomor yang dipilih di kelas)
                $mataKuliahInfo = MataKuliah::find($beban['mata_kuliah_id']);

                $dataBeban[] = [
                    'id_dosen' => $beban['dosen_id'],
                    'nama_dosen' => $dosenInfo->Nama_Dosen,
                    'nip' => $dosenInfo->NIP,
                    'id_mata_kuliah' => $beban['mata_kuliah_id'],
                    'nama_mata_kuliah' => $mataKuliahInfo->Nama_Matakuliah,
                    'kelas' => $mataKuliahInfo->Kelas,
                    'sks' => $beban['sks']
                ];
            }

            // Hitung tanggal tenggat (3 hari dari sekarang)
            $tanggalPengajuan = Carbon::now();
            $tanggalTenggat = Carbon::now()->addDays(3);

            // Simpan ke database
            SKBebanMengajar::create([
                'Id_Prodi' => $request->prodi_id,
                'Semester' => $request->semester,
                'Tahun_Akademik' => $request->tahun_akademik,
                'Data_Beban_Mengajar' => $dataBeban,
                'Nomor_Surat' => null, // Will be filled by admin
                'Id_Acc_SK_Beban_Mengajar' => null, // Will be filled when approved by admin
                'Tanggal-Pengajuan' => $tanggalPengajuan,
                'Tanggal-Tenggat' => $tanggalTenggat,
                'Id_Dosen_Kaprodi' => $idDosenKaprodi,
                'Status' => 'Dikerjakan admin'
            ]);

            return redirect()->route('kaprodi.sk.index')
                ->with('success', 'SK Beban Mengajar berhasil diajukan! Tanggal tenggat: ' . $tanggalTenggat->format('d M Y H:i'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengajukan SK Beban Mengajar: ' . $e->getMessage());
        }
    }

    /**
     * Get detail SK Beban Mengajar for preview
     */
    public function detailBebanMengajar($id)
    {
        try {
            // Get SK from Req_SK_Beban_Mengajar dengan relasi ke Acc_SK_Beban_Mengajar
            $sk = SKBebanMengajar::with(['prodi', 'accSKBebanMengajar'])->findOrFail($id);

            // Format tanggal pengajuan
            if ($sk->{'Tanggal-Pengajuan'}) {
                $sk->setAttribute('Tanggal-Pengajuan', Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y H:i'));
            }

            if ($sk->{'Tanggal-Tenggat'}) {
                $sk->setAttribute('Tanggal-Tenggat', Carbon::parse($sk->{'Tanggal-Tenggat'})->format('d M Y H:i'));
            }

            // Get Dekan info
            $dekan = Dosen::where('Id_Pejabat', 1)->first();

            // Convert QR Code path to base64 if exists
            // QR Code ada di tabel Acc_SK_Beban_Mengajar
            $qrCodeBase64 = null;
            $qrCodePath = $sk->accSKBebanMengajar->QR_Code ?? null;

            \Log::info('QR Code Path from Acc_SK: ' . ($qrCodePath ?? 'NULL'));

            if ($qrCodePath) {
                // Cek beberapa kemungkinan path
                $possiblePaths = [
                    $qrCodePath, // Path asli dari database
                    storage_path('app/public/' . $qrCodePath), // Path di storage
                    public_path('storage/' . $qrCodePath), // Path di public
                    public_path($qrCodePath), // Path relatif dari public
                ];

                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        \Log::info('QR Code file found at: ' . $path);
                        $qrCodeBase64 = base64_encode(file_get_contents($path));
                        \Log::info('QR Code converted to base64, length: ' . strlen($qrCodeBase64));
                        break;
                    }
                }

                if (!$qrCodeBase64) {
                    \Log::info('QR Code file not found in any path. Tried: ' . implode(', ', $possiblePaths));
                }
            } else {
                \Log::info('QR Code path is null or empty in Acc_SK_Beban_Mengajar');
            }

            // Prepare response
            $response = [
                'success' => true,
                'sk' => array_merge($sk->toArray(), [
                    'QR_Code' => $qrCodeBase64,
                    'Tanggal-Persetujuan-Dekan' => $sk->accSKBebanMengajar->{'Tanggal-Persetujuan-Dekan'} ?? null,
                    'dekan' => $dekan ? [
                        'Nama_Dosen' => $dekan->Nama_Dosen,
                        'NIP' => $dekan->NIP
                    ] : null
                ])
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in detailBebanMengajar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download SK Beban Mengajar as PDF
     */
    public function downloadBebanMengajar($id)
    {
        try {
            // Get SK from Req_SK_Beban_Mengajar
            $sk = SKBebanMengajar::with('prodi')->findOrFail($id);

            // Check if SK is completed
            if ($sk->Status !== 'Selesai') {
                return redirect()->back()->with('error', 'SK belum selesai, tidak dapat diunduh');
            }

            // Check if Nomor_Surat is available
            if (!$sk->Nomor_Surat) {
                return redirect()->back()->with('error', 'Nomor surat belum tersedia');
            }

            // Get Dekan info
            $dekan = Dosen::where('Id_Pejabat', 1)->first();
            $dekanName = $dekan ? $dekan->Nama_Dosen : 'Dr. Budi Hartono, S.Kom., M.Kom.';
            $dekanNip = $dekan ? $dekan->NIP : '198503152010121001';

            // Return view for print/download
            return view('kaprodi.sk.beban-mengajar.download', [
                'sk' => $sk,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendownload SK: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating SK Dosen Wali
     */
    public function createDosenWali()
    {
        // Get user info
        $user = Auth::user();

        // Get prodi from logged in kaprodi
        $prodi = null;
        if ($user->dosen) {
            $prodi = $user->dosen->prodi;
        } elseif ($user->pegawai) {
            $prodi = Prodi::find($user->pegawai->Id_Prodi);
        }

        // Get all prodi for dropdown
        $prodis = Prodi::orderBy('Nama_Prodi', 'asc')->get();

        // Get all dosen from the same prodi
        $dosens = Dosen::when($prodi, function ($query) use ($prodi) {
            return $query->where('Id_Prodi', $prodi->Id_Prodi);
        })
            ->orderBy('Nama_Dosen', 'asc')
            ->get();

        return view('kaprodi.sk.dosen-wali.create', compact('prodis', 'dosens', 'prodi'));
    }

    /**
     * Store SK Dosen Wali
     */
    public function storeDosenWali(Request $request)
    {
        // Validate request
        $request->validate([
            'id_prodi' => 'required|exists:Prodi,Id_Prodi',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_akademik' => 'required|string',
            'dosen' => 'required|array|min:1',
            'dosen.*.id_dosen' => 'required|exists:Dosen,Id_Dosen',
            'dosen.*.jumlah_anak_wali' => 'required|integer|min:0',
        ], [
            'id_prodi.required' => 'Program studi harus dipilih',
            'semester.required' => 'Semester harus dipilih',
            'tahun_akademik.required' => 'Tahun akademik harus diisi',
            'dosen.required' => 'Minimal harus ada 1 dosen wali',
            'dosen.min' => 'Minimal harus ada 1 dosen wali',
        ]);

        try {
            // Get kaprodi's Id_Dosen
            $user = Auth::user();
            $idDosenKaprodi = null;

            if ($user->dosen) {
                $idDosenKaprodi = $user->dosen->Id_Dosen;
            }

            // Prepare data dosen wali untuk disimpan ke JSON
            $dataDosen = [];
            foreach ($request->dosen as $dosen) {
                $dosenInfo = Dosen::find($dosen['id_dosen']);
                $dataDosen[] = [
                    'id_dosen' => $dosen['id_dosen'],
                    'nama_dosen' => $dosenInfo->Nama_Dosen,
                    'nip' => $dosenInfo->NIP,
                    'jumlah_anak_wali' => $dosen['jumlah_anak_wali']
                ];
            }

            // Hitung tanggal tenggat (3 hari dari sekarang)
            $tanggalPengajuan = Carbon::now();
            $tanggalTenggat = Carbon::now()->addDays(3);

            // Simpan ke database
            SKDosenWali::create([
                'Id_Prodi' => $request->id_prodi,
                'Semester' => $request->semester,
                'Tahun_Akademik' => $request->tahun_akademik,
                'Data_Dosen_Wali' => $dataDosen, // Laravel akan auto-encode karena ada casting di model
                'Status' => 'Dikerjakan admin',
                'Tanggal-Pengajuan' => $tanggalPengajuan,
                'Tanggal-Tenggat' => $tanggalTenggat,
                'Id_Dosen_Kaprodi' => $idDosenKaprodi
            ]);

            return redirect()->route('kaprodi.sk.index')
                ->with('success', 'SK Dosen Wali berhasil diajukan! Tanggal tenggat: ' . $tanggalTenggat->format('d M Y H:i'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengajukan SK Dosen Wali: ' . $e->getMessage());
        }
    }

    /**
     * Display list of submitted SK Dosen Wali
     */
    public function indexDosenWali()
    {
        // Get user info
        $user = Auth::user();

        // Get prodi from logged in kaprodi
        $idProdi = null;
        if ($user->dosen) {
            $idProdi = $user->dosen->Id_Prodi;
        } elseif ($user->pegawai) {
            $idProdi = $user->pegawai->Id_Prodi;
        }

        // Get SK Dosen Wali data for this prodi
        $skList = SKDosenWali::with('prodi')
            ->when($idProdi, function ($query) use ($idProdi) {
                return $query->where('Id_Prodi', $idProdi);
            })
            ->orderBy('Tanggal-Pengajuan', 'desc')
            ->get()
            ->map(function ($sk) {
                // Ensure Alasan-Tolak is properly loaded
                $sk->setAttribute('Alasan-Tolak', $sk->{'Alasan-Tolak'});
                return $sk;
            });

        return view('kaprodi.sk.dosen-wali.index', compact('skList'));
    }

    /**
     * Get detail SK Dosen Wali for preview
     */
    public function detailDosenWali($id)
    {
        try {
            // Get SK from Req_SK_Dosen_Wali
            $sk = SKDosenWali::with('prodi')->findOrFail($id);

            // Get Acc_SK if exists to get the final data
            $accSK = null;
            if ($sk->Id_Acc_SK_Dosen_Wali) {
                $accSK = \App\Models\AccDekanDosenWali::find($sk->Id_Acc_SK_Dosen_Wali);
            }

            // Get Dekan info
            $dekan = Dosen::where('Id_Pejabat', 1)->first();
            $dekanName = $dekan ? $dekan->Nama_Dosen : 'Dr. Budi Hartono, S.Kom., M.Kom.';
            $dekanNip = $dekan ? $dekan->NIP : '198503152010121001';

            // If SK is completed and has Acc_SK, use data from Acc_SK
            if ($sk->Status === 'Selesai' && $accSK) {
                return response()->json([
                    'success' => true,
                    'sk' => $accSK, // Use Acc_SK data instead
                    'accSK' => $accSK,
                    'dekanName' => $dekanName,
                    'dekanNip' => $dekanNip,
                    'isFromAcc' => true // Flag to indicate data is from Acc_SK
                ]);
            }

            // Otherwise return original SK data
            return response()->json([
                'success' => true,
                'sk' => $sk,
                'accSK' => $accSK,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip,
                'isFromAcc' => false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download SK Dosen Wali as PDF
     */
    public function downloadDosenWali($id)
    {
        try {
            // Get SK from Req_SK_Dosen_Wali
            $sk = SKDosenWali::with('prodi')->findOrFail($id);

            // Check if SK is completed
            if ($sk->Status !== 'Selesai') {
                return redirect()->back()->with('error', 'SK belum selesai, tidak dapat diunduh');
            }

            // Get Acc_SK for QR code
            $accSK = null;
            if ($sk->Id_Acc_SK_Dosen_Wali) {
                $accSK = \App\Models\AccDekanDosenWali::find($sk->Id_Acc_SK_Dosen_Wali);
            }

            if (!$accSK || !$accSK->QR_Code) {
                return redirect()->back()->with('error', 'QR Code tidak ditemukan');
            }

            // Get Dekan info
            $dekan = Dosen::where('Id_Pejabat', 1)->first();
            $dekanName = $dekan ? $dekan->Nama_Dosen : 'Dr. Budi Hartono, S.Kom., M.Kom.';
            $dekanNip = $dekan ? $dekan->NIP : '198503152010121001';

            // Return view for print/download
            return view('kaprodi.sk.dosen-wali.download', [
                'sk' => $sk,
                'accSK' => $accSK,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendownload SK: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating SK Pembimbing Skripsi
     */
    public function createPembimbingSkripsi()
    {
        // TODO: Implement form for SK Pembimbing Skripsi
        return view('kaprodi.sk.pembimbing-skripsi.create');
    }

    /**
     * Show the form for creating SK Penguji Skripsi
     */
    public function createPengujiSkripsi()
    {
        // TODO: Implement form for SK Penguji Skripsi
        return view('kaprodi.sk.penguji-skripsi.create');
    }
}
