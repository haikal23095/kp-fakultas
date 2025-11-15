<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TugasSurat;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Pegawai;
use App\Models\Prodi;

class PersetujuanSuratController extends Controller
{
    /**
     * Tampilkan daftar surat yang menunggu persetujuan TTE Dekan.
     * Hanya surat dari fakultas yang sama dengan Dekan yang login.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        // Langkah 1: Cari Fakultas Dekan yang login
        // Asumsi: Dekan juga tercatat di tabel Dosen (atau bisa Pegawai, sesuaikan jika perlu)
        $dosen = Dosen::where('Id_User', $user->Id_User)->first();

        if (!$dosen || !$dosen->Id_Prodi) {
            // Jika dekan tidak punya prodi, return kosong atau error
            return view('dekan.persetujuan_surat', ['daftarSurat' => collect()]);
        }

        // Ambil Id_Fakultas dari Prodi dekan
        $prodi = Prodi::find($dosen->Id_Prodi);
        if (!$prodi || !$prodi->Id_Fakultas) {
            return view('dekan.persetujuan_surat', ['daftarSurat' => collect()]);
        }

        $idFakultas = $prodi->Id_Fakultas;

        // Langkah 2: Kumpulkan semua Prodi di fakultas yang sama
        $prodiIds = Prodi::where('Id_Fakultas', $idFakultas)
            ->pluck('Id_Prodi')
            ->toArray();

        // Kumpulkan semua Id_User dari Dosen, Mahasiswa, dan Pegawai di prodi-prodi tersebut
        $userIdsDosen = Dosen::whereIn('Id_Prodi', $prodiIds)->pluck('Id_User')->toArray();
        $userIdsMahasiswa = Mahasiswa::whereIn('Id_Prodi', $prodiIds)->pluck('Id_User')->toArray();
        $userIdsPegawai = Pegawai::whereIn('Id_Prodi', $prodiIds)->pluck('Id_User')->toArray();

        // Gabung semua user IDs dan buang duplikat
        $allUserIds = array_unique(array_merge($userIdsDosen, $userIdsMahasiswa, $userIdsPegawai));

        // Langkah 3: Query surat dengan filter fakultas dan status
        $daftarSurat = TugasSurat::with(['jenisSurat', 'pemberiTugas.role', 'penerimaTugas'])
            ->where('Status', 'menunggu-ttd')
            ->where(function ($query) use ($allUserIds) {
                $query->whereIn('Id_Pemberi_Tugas_Surat', $allUserIds)
                      ->orWhereIn('Id_Penerima_Tugas_Surat', $allUserIds);
            })
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }
}
