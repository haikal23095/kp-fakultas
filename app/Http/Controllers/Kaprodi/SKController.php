<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prodi;
use App\Models\Dosen;
use App\Models\SKDosenWali;
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
     * Show the form for creating SK Beban Mengajar
     */
    public function createBebanMengajar()
    {
        // TODO: Implement form for SK Beban Mengajar
        return view('kaprodi.sk.beban-mengajar.create');
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
                'Tanggal-Tenggat' => $tanggalTenggat
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
