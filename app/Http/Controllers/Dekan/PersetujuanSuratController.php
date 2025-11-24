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

        // Query surat yang menunggu tanda tangan dekan
        // Simplifikasi: ambil semua surat yang ditujukan ke dekan ini dengan status menunggu-ttd
        $daftarSurat = TugasSurat::with([
                'jenisSurat', 
                'pemberiTugas.role', 
                'penerimaTugas', 
                'suratMagang', 
                'suratKetAktif',
                'pemberiTugas.mahasiswa.prodi' // Untuk validasi fakultas
            ])
            ->where(function ($q) {
                // Cek status di Tugas_Surat atau di tabel child (suratMagang, suratKetAktif)
                $q->where('Status', 'menunggu-ttd')
                  ->orWhereHas('suratMagang', function ($subQ) {
                      $subQ->where('Status', 'menunggu-ttd');
                  })
                  ->orWhereHas('suratKetAktif', function ($subQ) {
                      $subQ->where('Status', 'menunggu-ttd');
                  });
            })
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User) // Hanya surat yang ditujukan ke dekan ini
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }
}
