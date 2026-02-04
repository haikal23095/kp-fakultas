<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    /**
     * Tampilkan halaman pilihan jenis surat (card view)
     */
    public function index()
    {
        // Definisikan surat apa saja yang boleh diajukan Mahasiswa
        $jenis_surats = [
            (object) ['Id_Jenis_Surat' => 1, 'Nama_Surat' => 'Surat Keterangan Aktif'],
            (object) ['Id_Jenis_Surat' => 2, 'Nama_Surat' => 'Surat Pengantar KP/Magang'],
            (object) ['Id_Jenis_Surat' => 3, 'Nama_Surat' => 'Surat Rekomendasi'],
        ];

        return view('mahasiswa.pilih_jenis_surat', [
            'jenis_surats' => $jenis_surats
        ]);
    }

    /**
     * Tampilkan form pengajuan Surat Rekomendasi
     */
    public function rekomendasi()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();

        $prodi = null;
        if ($mahasiswa && $mahasiswa->Id_Prodi) {
            $prodi = Prodi::find($mahasiswa->Id_Prodi);
        }

        return view('mahasiswa.pengajuan.form_surat_rekomendasi', [
            'mahasiswa' => $mahasiswa,
            'prodi' => $prodi
        ]);
    }
}
