<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prodi;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Mahasiswa;
use App\Models\SKDosenWali;
use App\Models\SKBebanMengajar;
use App\Models\ReqSKPembimbingSkripsi;
use App\Models\AccSKPembimbingSkripsi;
use App\Models\ReqSKPengujiSkripsi;
use App\Models\AccSKPengujiSkripsi;
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

        return view('kaprodi.sk.dosen-wali.history', compact('skList'));
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
     * Display history of SK Pembimbing Skripsi
     */
    public function historyPembimbingSkripsi(Request $request)
    {
        // Get logged in user's dosen ID
        $user = Auth::user();
        $dosenId = $user->dosen ? $user->dosen->Id_Dosen : null;

        if (!$dosenId) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan');
        }

        // Query SK Pembimbing Skripsi with AccSK relation for QR Code
        $query = ReqSKPembimbingSkripsi::with(['prodi', 'kaprodi', 'accSKPembimbingSkripsi.dekan'])
            ->where('Id_Dosen_Kaprodi', $dosenId);

        // Debug: Check if relation is loaded - UNCOMMENT TO DEBUG
        // $testSK = $query->first();
        // dd([
        //     'sk' => $testSK,
        //     'accSK_object' => $testSK->accSKPembimbingSkripsi,
        //     'toArray' => $testSK->toArray(),
        //     'relationLoaded' => $testSK->relationLoaded('accSKPembimbingSkripsi'),
        // ]);

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

        return view('kaprodi.sk.pembimbing-skripsi.history', compact('skList'));
    }

    /**
     * Show the form for creating SK Pembimbing Skripsi
     */
    public function createPembimbingSkripsi()
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

        // Get all mahasiswa from the same prodi
        $mahasiswas = Mahasiswa::when($prodi, function ($query) use ($prodi) {
            return $query->where('Id_Prodi', $prodi->Id_Prodi);
        })
            ->orderBy('Nama_Mahasiswa', 'asc')
            ->get();

        return view('kaprodi.sk.pembimbing-skripsi.create', compact('prodis', 'dosens', 'mahasiswas', 'prodi'));
    }

    /**
     * Store SK Pembimbing Skripsi
     */
    public function storePembimbingSkripsi(Request $request)
    {
        // Validate request
        $request->validate([
            'prodi_id' => 'required|exists:Prodi,Id_Prodi',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_akademik' => 'required|string',
            'pembimbing' => 'required|array|min:1',
            'pembimbing.*.mahasiswa_id' => 'required|exists:Mahasiswa,Id_Mahasiswa',
            'pembimbing.*.judul_skripsi' => 'required|string|max:500',
            'pembimbing.*.pembimbing_1' => 'required|exists:Dosen,Id_Dosen',
            'pembimbing.*.pembimbing_2' => 'required|exists:Dosen,Id_Dosen',
        ], [
            'prodi_id.required' => 'Program studi harus dipilih',
            'semester.required' => 'Semester harus dipilih',
            'tahun_akademik.required' => 'Tahun akademik harus diisi',
            'pembimbing.required' => 'Minimal harus ada 1 mahasiswa',
            'pembimbing.min' => 'Minimal harus ada 1 mahasiswa',
            'pembimbing.*.judul_skripsi.required' => 'Judul skripsi harus diisi',
            'pembimbing.*.judul_skripsi.max' => 'Judul skripsi maksimal 500 karakter',
        ]);

        try {
            // Get kaprodi's Id_Dosen
            $user = Auth::user();
            $idDosenKaprodi = null;

            if ($user->dosen) {
                $idDosenKaprodi = $user->dosen->Id_Dosen;
            }

            // Prepare data pembimbing untuk disimpan ke JSON
            $dataPembimbing = [];
            foreach ($request->pembimbing as $pembimbing) {
                // Get data mahasiswa
                $mahasiswaInfo = Mahasiswa::find($pembimbing['mahasiswa_id']);

                // Get data dosen pembimbing 1
                $dosen1Info = Dosen::find($pembimbing['pembimbing_1']);

                // Get data dosen pembimbing 2
                $dosen2Info = Dosen::find($pembimbing['pembimbing_2']);

                $dataPembimbing[] = [
                    'id_mahasiswa' => $pembimbing['mahasiswa_id'],
                    'nama_mahasiswa' => $mahasiswaInfo->Nama_Mahasiswa,
                    'nim' => $mahasiswaInfo->NIM,
                    'judul_skripsi' => $pembimbing['judul_skripsi'],
                    'pembimbing_1' => [
                        'id_dosen' => $pembimbing['pembimbing_1'],
                        'nama_dosen' => $dosen1Info->Nama_Dosen,
                        'nip' => $dosen1Info->NIP,
                    ],
                    'pembimbing_2' => [
                        'id_dosen' => $pembimbing['pembimbing_2'],
                        'nama_dosen' => $dosen2Info->Nama_Dosen,
                        'nip' => $dosen2Info->NIP,
                    ],
                ];
            }

            // Hitung tanggal tenggat (3 hari dari sekarang)
            $tanggalPengajuan = Carbon::now();
            $tanggalTenggat = Carbon::now()->addDays(3);

            // Simpan ke database tabel Req_SK_Pembimbing_Skripsi
            ReqSKPembimbingSkripsi::create([
                'Id_Prodi' => $request->prodi_id,
                'Semester' => $request->semester,
                'Tahun_Akademik' => $request->tahun_akademik,
                'Data_Pembimbing_Skripsi' => json_encode($dataPembimbing), // Convert array to JSON string
                'Id_Dosen_Kaprodi' => $idDosenKaprodi,
                'Nomor_Surat' => null, // Will be filled by admin
                'Status' => 'Dikerjakan admin',
                'Id_Acc_SK_Pembimbing_Skripsi' => null, // Will be filled when approved
                'Alasan-Tolak' => null,
                'Tanggal-Pengajuan' => $tanggalPengajuan,
                'Tanggal-Tenggat' => $tanggalTenggat,
            ]);

            return redirect()->route('kaprodi.sk.index')
                ->with('success', 'SK Pembimbing Skripsi berhasil diajukan! Tanggal tenggat: ' . $tanggalTenggat->format('d M Y H:i'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengajukan SK Pembimbing Skripsi: ' . $e->getMessage());
        }
    }

    /**
     * Download SK Pembimbing Skripsi as PDF
     */
    public function downloadPembimbingSkripsi($id)
    {
        try {
            $user = Auth::user();
            $dosenId = $user->dosen ? $user->dosen->Id_Dosen : null;

            if (!$dosenId) {
                return redirect()->back()->with('error', 'Data dosen tidak ditemukan');
            }

            // Get SK data with related models including AccSK
            $sk = ReqSKPembimbingSkripsi::with(['prodi.jurusan', 'kaprodi', 'accSKPembimbingSkripsi.dekan'])
                ->where('No', $id)
                ->where('Id_Dosen_Kaprodi', $dosenId)
                ->firstOrFail();

            // Only allow download if status is Selesai
            if ($sk->Status !== 'Selesai') {
                return redirect()->back()->with('error', 'SK belum selesai ditandatangani');
            }

            // Get AccSKPembimbingSkripsi for Dekan info, QR Code, and Nomor_Surat
            $accSK = $sk->accSKPembimbingSkripsi;

            if (!$accSK) {
                return redirect()->back()->with('error', 'Data persetujuan SK tidak ditemukan');
            }

            // Check if Nomor_Surat is available (from AccSK, not from Req)
            if (!$accSK->Nomor_Surat) {
                return redirect()->back()->with('error', 'Nomor surat belum tersedia');
            }

            // Get Dekan info
            $dekanName = $accSK->dekan ? $accSK->dekan->Nama_Dosen : 'Dekan Fakultas Teknik';
            $dekanNip = $accSK->dekan ? $accSK->dekan->NIP : '-';

            // Return view for print/download
            return view('kaprodi.sk.pembimbing-skripsi.download', [
                'sk' => $accSK,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip,
                'qrCodePath' => $accSK->QR_Code ? asset('storage/' . $accSK->QR_Code) : null
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendownload SK: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating SK Penguji Skripsi
     */
    public function createPengujiSkripsi()
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

        // Get all mahasiswa from the same prodi
        $mahasiswas = Mahasiswa::when($prodi, function ($query) use ($prodi) {
            return $query->where('Id_Prodi', $prodi->Id_Prodi);
        })
            ->orderBy('Nama_Mahasiswa', 'asc')
            ->get();

        return view('kaprodi.sk.penguji-skripsi.create', compact('prodis', 'dosens', 'mahasiswas', 'prodi'));
    }

    /**
     * Store SK Penguji Skripsi
     */
    public function storePengujiSkripsi(Request $request)
    {
        $request->validate([
            'prodi_id' => 'required|exists:Prodi,Id_Prodi',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_akademik' => 'required',
            'penguji' => 'required|array|min:1',
        ]);

        try {
            $user = Auth::user();
            $dosenId = $user->dosen ? $user->dosen->Id_Dosen : null;

            if (!$dosenId) {
                return redirect()->back()->with('error', 'Data dosen tidak ditemukan');
            }

            // Process data penguji
            $dataPenguji = [];
            foreach ($request->penguji as $item) {
                $mahasiswa = Mahasiswa::find($item['mahasiswa_id']);
                $p1 = Dosen::find($item['penguji_1']);
                $p2 = Dosen::find($item['penguji_2']);
                $p3 = Dosen::find($item['penguji_3']);

                $dataPenguji[] = [
                    'mahasiswa_id' => $item['mahasiswa_id'],
                    'nama_mahasiswa' => $mahasiswa->Nama_Mahasiswa,
                    'nim' => $mahasiswa->NIM,
                    'judul_skripsi' => $item['judul_skripsi'],
                    'penguji_1_id' => $item['penguji_1'],
                    'nama_penguji_1' => $p1->Nama_Dosen,
                    'penguji_2_id' => $item['penguji_2'],
                    'nama_penguji_2' => $p2->Nama_Dosen,
                    'penguji_3_id' => $item['penguji_3'],
                    'nama_penguji_3' => $p3->Nama_Dosen,
                ];
            }

            $tanggalPengajuan = now();
            $tanggalTenggat = $tanggalPengajuan->copy()->addDays(3);

            ReqSKPengujiSkripsi::create([
                'Id_Prodi' => $request->prodi_id,
                'Semester' => $request->semester,
                'Tahun_Akademik' => $request->tahun_akademik,
                'Data_Penguji_Skripsi' => $dataPenguji,
                'Id_Dosen_Kaprodi' => $dosenId,
                'Status' => 'Dikerjakan admin',
                'Tanggal-Pengajuan' => $tanggalPengajuan,
                'Tanggal-Tenggat' => $tanggalTenggat,
            ]);

            return redirect()->route('kaprodi.sk.index')->with('success', 'SK Penguji Skripsi berhasil diajukan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display history of SK Penguji Skripsi
     */
    public function historyPengujiSkripsi(Request $request)
    {
        $user = Auth::user();
        $dosenId = $user->dosen ? $user->dosen->Id_Dosen : null;

        if (!$dosenId) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan');
        }

        $query = ReqSKPengujiSkripsi::with(['prodi', 'kaprodi', 'accSKPengujiSkripsi.dekan'])
            ->where('Id_Dosen_Kaprodi', $dosenId);

        if ($request->filled('semester')) {
            $query->where('Semester', $request->semester);
        }

        if ($request->filled('tahun_akademik')) {
            $query->where('Tahun_Akademik', $request->tahun_akademik);
        }

        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        $skList = $query->orderBy('Tanggal-Pengajuan', 'desc')->paginate(15);

        return view('kaprodi.sk.penguji-skripsi.history', compact('skList'));
    }

    /**
     * Download SK Penguji Skripsi as PDF
     */
    public function downloadPengujiSkripsi($id)
    {
        try {
            $user = Auth::user();
            $dosenId = $user->dosen ? $user->dosen->Id_Dosen : null;

            if (!$dosenId) {
                return redirect()->back()->with('error', 'Data dosen tidak ditemukan');
            }

            // Get SK data with related models
            $sk = ReqSKPengujiSkripsi::with(['prodi.jurusan', 'kaprodi', 'accSKPengujiSkripsi.dekan'])
                ->where('No', $id)
                ->where('Id_Dosen_Kaprodi', $dosenId)
                ->firstOrFail();

            // Only allow download if status is Selesai
            if ($sk->Status !== 'Selesai') {
                return redirect()->back()->with('error', 'SK belum selesai ditandatangani');
            }

            $accSK = $sk->accSKPengujiSkripsi;

            if (!$accSK) {
                return redirect()->back()->with('error', 'Data persetujuan SK tidak ditemukan');
            }

            // Get Dekan info
            $dekanName = $accSK->dekan ? $accSK->dekan->Nama_Dosen : 'Dekan Fakultas Teknik';
            $dekanNip = $accSK->dekan ? $accSK->dekan->NIP : '-';

            // Return view for print/download
            return view('kaprodi.sk.penguji-skripsi.download', [
                'sk' => $accSK,
                'prodi' => $sk->prodi,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip,
                'qrCodePath' => $accSK->QR_Code ? asset('storage/' . $accSK->QR_Code) : null
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendownload SK: ' . $e->getMessage());
        }
    }

    /**
     * Get kelas mata kuliah data for management
     */
    public function getKelasMataKuliah(Request $request)
    {
        try {
            $prodiId = $request->query('prodi_id');

            if (!$prodiId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program Studi tidak ditemukan'
                ], 400);
            }

            // Get mata kuliah with their kelas
            $mataKuliahGrouped = MataKuliah::where('Id_Prodi', $prodiId)
                ->select('Nama_Matakuliah', 'Kelas', 'SKS', 'Nomor')
                ->orderBy('Nama_Matakuliah', 'asc')
                ->orderBy('Kelas', 'asc')
                ->get()
                ->groupBy('Nama_Matakuliah');

            $mataKuliahData = $mataKuliahGrouped->map(function ($kelasList, $namaMK) {
                $firstKelas = $kelasList->first();
                return [
                    'nama_matakuliah' => $namaMK,
                    'sks' => $firstKelas->SKS,
                    'jumlah_kelas' => $kelasList->count(),
                    'kelas_list' => $kelasList->pluck('Kelas')->toArray()
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $mataKuliahData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getKelasMataKuliah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kelas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update kelas mata kuliah
     */
    public function updateKelasMataKuliah(Request $request)
    {
        $request->validate([
            'prodi_id' => 'required|exists:Prodi,Id_Prodi',
            'kelas_data' => 'required|array|min:1',
            'kelas_data.*.nama_matakuliah' => 'required|string',
            'kelas_data.*.sks' => 'required|integer|min:1|max:6',
            'kelas_data.*.jumlah_kelas' => 'required|integer|min:1|max:10',
        ]);

        try {
            $prodiId = $request->prodi_id;
            $kelasData = $request->kelas_data;

            foreach ($kelasData as $mkData) {
                $namaMataKuliah = $mkData['nama_matakuliah'];
                $sks = $mkData['sks'];
                $jumlahKelasBaru = $mkData['jumlah_kelas'];

                // Get existing kelas for this mata kuliah
                $existingKelas = MataKuliah::where('Id_Prodi', $prodiId)
                    ->where('Nama_Matakuliah', $namaMataKuliah)
                    ->orderBy('Kelas', 'asc')
                    ->get();

                $jumlahKelasLama = $existingKelas->count();

                // If need to add more kelas
                if ($jumlahKelasBaru > $jumlahKelasLama) {
                    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

                    // Get base name from the first existing kelas
                    $baseName = '';
                    if ($jumlahKelasLama > 0) {
                        $firstKelas = $existingKelas->first()->Kelas;
                        // Extract base name by removing the last character (letter)
                        // Pattern: "IF 5A" -> "IF 5", "Te A" -> "Te "
                        $baseName = preg_replace('/[A-Z]$/', '', $firstKelas);
                    } else {
                        // If no existing kelas, create base name from prodi
                        $prodi = Prodi::find($prodiId);
                        $baseName = substr($prodi->Nama_Prodi, 0, 2) . ' ';
                    }

                    for ($i = $jumlahKelasLama; $i < $jumlahKelasBaru; $i++) {
                        MataKuliah::create([
                            'Id_Prodi' => $prodiId,
                            'Nama_Matakuliah' => $namaMataKuliah,
                            'Kelas' => $baseName . $letters[$i],
                            'SKS' => $sks
                        ]);
                    }
                }
                // If need to remove kelas
                elseif ($jumlahKelasBaru < $jumlahKelasLama) {
                    // Delete the excess kelas (from the end)
                    $kelasToDelete = $existingKelas->slice($jumlahKelasBaru);

                    foreach ($kelasToDelete as $kelas) {
                        // Only delete if not used in any beban mengajar
                        $isUsed = \DB::table('Req_SK_Beban_Mengajar')
                            ->whereRaw("JSON_CONTAINS(Data_Beban_Mengajar, JSON_OBJECT('id_mata_kuliah', ?))", [$kelas->Nomor])
                            ->exists();

                        if (!$isUsed) {
                            $kelas->delete();
                        }
                    }
                }
                // If same amount, just update SKS if needed
                else {
                    MataKuliah::where('Id_Prodi', $prodiId)
                        ->where('Nama_Matakuliah', $namaMataKuliah)
                        ->update(['SKS' => $sks]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Perubahan kelas berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in updateKelasMataKuliah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan perubahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
