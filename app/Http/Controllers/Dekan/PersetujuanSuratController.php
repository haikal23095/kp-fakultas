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
     * Tampilkan halaman index persetujuan dengan card pilihan jenis surat
     */
    public function index()
    {
        $user = Auth::user();

        // Hitung jumlah surat per jenis yang menunggu persetujuan
        $countAktif = TugasSurat::where('Id_Jenis_Surat', 1)
            ->where(function ($q) {
                $q->where('Status', 'menunggu-ttd')
                    ->orWhereHas('suratKetAktif', function ($subQ) {
                        $subQ->where('Status', 'menunggu-ttd');
                    });
            })
            ->count();

        $countMagang = TugasSurat::where('Id_Jenis_Surat', 2)
            ->whereHas('suratMagang', function ($subQ) {
                $subQ->where('Status', 'Diajukan-ke-dekan');
            })
            ->count();

        $countLegalisir = TugasSurat::where('Id_Jenis_Surat', 14) // Legalisir menggunakan Id_Jenis_Surat = 14
            ->whereHas('suratLegalisir') // Hanya yang punya relasi ke Surat_Legalisir
            ->count();

        // TODO: Implementasi counting untuk jenis surat baru
        // Untuk sementara set 0, bisa diimplementasikan setelah tabel database dibuat
        $countCutiDosen = 0; // TODO: Implementasi dengan Id_Jenis_Surat yang sesuai
        $countTidakBeasiswa = TugasSurat::where('Id_Jenis_Surat', 6)
            ->whereHas('suratTidakBeasiswa')
            ->where('Status', 'menunggu-ttd')
            ->count();
        $countBerkelakuanBaik = TugasSurat::whereHas('jenisSurat', function($q) {
                $q->where('Nama_Surat', 'LIKE', '%Berkelakuan Baik%');
            })
            ->whereHas('suratKelakuanBaik')
            ->where('Status', 'menunggu-ttd')
            ->count();
        $countSKFakultas = 0; // TODO: Implementasi dengan Id_Jenis_Surat yang sesuai
        $countSuratTugas = 0; // TODO: Implementasi dengan Id_Jenis_Surat yang sesuai
        $countMBKM = 0; // TODO: Implementasi dengan Id_Jenis_Surat yang sesuai

        return view('dekan.persetujuan_index', compact(
            'countAktif',
            'countMagang',
            'countLegalisir',
            'countCutiDosen',
            'countTidakBeasiswa',
            'countBerkelakuanBaik',
            'countSKFakultas',
            'countSuratTugas',
            'countMBKM'
        ));
    }

    /**
     * Tampilkan daftar surat keterangan aktif yang menunggu persetujuan
     */
    public function listAktif()
    {
        $user = Auth::user();

        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas',
            'suratKetAktif',
            'pemberiTugas.mahasiswa.prodi'
        ])
            ->where('Id_Jenis_Surat', 1)
            ->where(function ($q) {
                $q->where('Status', 'menunggu-ttd')
                    ->orWhereHas('suratKetAktif', function ($subQ) {
                        $subQ->where('Status', 'menunggu-ttd');
                    });
            })
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar surat pengantar magang yang menunggu persetujuan
     */
    public function listMagang()
    {
        $user = Auth::user();

        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas',
            'suratMagang',
            'pemberiTugas.mahasiswa.prodi'
        ])
            ->where('Id_Jenis_Surat', 2)
            ->whereHas('suratMagang', function ($subQ) {
                $subQ->where('Status', 'Diajukan-ke-dekan');
            })
            ->get();

        return view('dekan.surat_magang.index', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar legalisir yang menunggu TTD Dekan
     */
    public function listLegalisir()
    {
        $user = Auth::user();
        
        // Ambil data legalisir langsung dari tabel Surat_Legalisir dengan status menunggu_ttd_pimpinan
        $daftarLegalisir = \App\Models\SuratLegalisir::with([
                'user.mahasiswa.prodi',
                'user.role',
                'tugasSurat.jenisSurat',
                'pejabat'
            ])
            ->where('Status', 'menunggu_ttd_pimpinan')
            ->orderBy('id_no', 'desc')
            ->get();

        return view('dekan.persetujuan_legalisir', compact('daftarLegalisir'));
    }

    /**
     * Tampilkan daftar surat cuti dosen yang menunggu persetujuan
     * TODO: Implementasi lengkap setelah tabel database dibuat
     */
    public function listCutiDosen()
    {
        $user = Auth::user();

        // TODO: Ganti dengan query yang sesuai setelah tabel surat_cuti_dosen dibuat
        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas'
        ])
            ->where('Id_Jenis_Surat', 99) // TODO: Ganti dengan Id_Jenis_Surat yang benar
            ->where('Status', 'menunggu-ttd')
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar surat keterangan tidak menerima beasiswa yang menunggu persetujuan
     */
    public function listTidakBeasiswa()
    {
        $user = Auth::user();
        
        $daftarSurat = TugasSurat::with([
                'jenisSurat', 
                'pemberiTugas.role', 
                'pemberiTugas.mahasiswa.prodi',
                'penerimaTugas',
                'suratTidakBeasiswa'
            ])
            ->where('Id_Jenis_Surat', 6)
            ->whereHas('suratTidakBeasiswa')
            ->where('Status', 'menunggu-ttd')
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar Surat Keterangan Berkelakuan Baik yang menunggu persetujuan
     */
    public function listBerkelakuanBaik()
    {
        $user = Auth::user();
        
        $daftarSurat = TugasSurat::with([
                'jenisSurat', 
                'pemberiTugas.role', 
                'penerimaTugas',
                'suratKelakuanBaik.user.mahasiswa'
            ])
            ->whereHas('jenisSurat', function($q) {
                $q->where('Nama_Surat', 'LIKE', '%Berkelakuan Baik%');
            })
            ->whereHas('suratKelakuanBaik')
            ->where('Status', 'menunggu-ttd')
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar SK Fakultas yang menunggu persetujuan
     * TODO: Implementasi lengkap setelah tabel database dibuat
     */
    public function listSKFakultas()
    {
        $user = Auth::user();

        // TODO: Ganti dengan query yang sesuai
        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas'
        ])
            ->where('Id_Jenis_Surat', 99) // TODO: Ganti dengan Id_Jenis_Surat yang benar
            ->where('Status', 'menunggu-ttd')
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar Surat Tugas yang menunggu persetujuan
     * TODO: Implementasi lengkap setelah tabel database dibuat
     */
    public function listSuratTugas()
    {
        $user = Auth::user();

        // TODO: Ganti dengan query yang sesuai
        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas'
        ])
            ->where('Id_Jenis_Surat', 99) // TODO: Ganti dengan Id_Jenis_Surat yang benar
            ->where('Status', 'menunggu-ttd')
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar Surat Rekomendasi MBKM yang menunggu persetujuan
     * TODO: Implementasi lengkap setelah tabel database dibuat
     */
    public function listMBKM()
    {
        $user = Auth::user();

        // TODO: Ganti dengan query yang sesuai
        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas'
        ])
            ->where('Id_Jenis_Surat', 99) // TODO: Ganti dengan Id_Jenis_Surat yang benar
            ->where('Status', 'menunggu-ttd')
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }
}
