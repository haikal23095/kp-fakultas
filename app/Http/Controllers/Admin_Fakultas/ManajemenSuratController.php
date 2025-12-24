<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use App\Models\TugasSurat;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManajemenSuratController extends Controller
{
    /**
     * Halaman utama - Card pilihan jenis surat
     */
    public function index()
    {
        // Update status tugas yang terlambat
        TugasSurat::updateStatusTerlambat();
        
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Base query dengan filter fakultas
        $baseQuery = function () use ($fakultasId) {
            return TugasSurat::query()
                ->where(function ($q) use ($fakultasId) {
                    $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    })
                    ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    })
                    ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    });
                });
        };
        
        // Total semua surat
        $totalSemua = $baseQuery()->count();
        
        // Total semua surat
        $totalSemua = $baseQuery()->count();

        // Hitung untuk Surat Keterangan Aktif - GUNAKAN RELASI
        $countAktif = $baseQuery()->has('suratKetAktif')->count();
        $pendingAktif = $baseQuery()
            ->has('suratKetAktif')
            ->whereIn('Status', ['baru', 'pending', 'Diajukan-ke-koordinator'])
            ->count();
        $prosesAktif = $baseQuery()
            ->has('suratKetAktif')
            ->whereIn('Status', ['proses', 'Dikerjakan-admin'])
            ->count();
        $selesaiAktif = $baseQuery()
            ->has('suratKetAktif')
            ->whereIn('Status', ['selesai', 'Success', 'Disetujui'])
            ->count();

        // Hitung untuk Surat Magang - GUNAKAN RELASI
        $countMagang = $baseQuery()->has('suratMagang')->count();
        $pendingMagang = $baseQuery()
            ->has('suratMagang')
            ->whereIn('Status', ['baru', 'pending', 'Diajukan-ke-koordinator'])
            ->count();
        $prosesMagang = $baseQuery()
            ->has('suratMagang')
            ->whereIn('Status', ['proses', 'Dikerjakan-admin', 'Diajukan-ke-dekan'])
            ->count();
        $selesaiMagang = $baseQuery()
            ->has('suratMagang')
            ->whereIn('Status', ['selesai', 'Success'])
            ->count();

        // Hitung untuk Legalisir - GUNAKAN RELASI
        $countLegalisir = $baseQuery()->has('suratLegalisir')->count();
        $pendingLegalisir = $baseQuery()
            ->has('suratLegalisir')
            ->whereIn('Status', ['baru', 'pending'])
            ->count();
        $prosesLegalisir = $baseQuery()
            ->has('suratLegalisir')
            ->whereIn('Status', ['proses', 'Dikerjakan-admin', 'menunggu_pembayaran', 'pembayaran_lunas', 'proses_stempel_paraf'])
            ->count();
        $selesaiLegalisir = $baseQuery()
            ->has('suratLegalisir')
            ->whereIn('Status', ['selesai', 'Success', 'siap_diambil'])
            ->count();

        return view('admin_fakultas.manajemen_surat_index', compact(
            'countAktif', 'pendingAktif', 'prosesAktif', 'selesaiAktif',
            'countMagang', 'pendingMagang', 'prosesMagang', 'selesaiMagang',
            'countLegalisir', 'pendingLegalisir', 'prosesLegalisir', 'selesaiLegalisir',
            'totalSemua'
        ));
    }

    /**
     * List Surat Keterangan Aktif
     */
    public function listAktif()
    {
        TugasSurat::updateStatusTerlambat();

        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $baseQuery = TugasSurat::query()
            ->has('suratKetAktif') // GUNAKAN RELASI BUKAN Id_Jenis_Surat
            ->where(function ($q) use ($fakultasId) {
                $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                });
            });

        $daftarTugas = $baseQuery->with([
            'pemberiTugas.role',
            'pemberiTugas.mahasiswa.prodi',
            'jenisSurat',
            'suratKetAktif'
        ])
        ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
        ->paginate(15);

        return view('admin_fakultas.list_aktif', compact('daftarTugas'));
    }

    /**
     * List Surat Magang
     */
    public function listMagang()
    {
        TugasSurat::updateStatusTerlambat();

        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $baseQuery = TugasSurat::query()
            ->has('suratMagang') // GUNAKAN RELASI BUKAN Id_Jenis_Surat
            ->where(function ($q) use ($fakultasId) {
                $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                });
            });

        $daftarTugas = $baseQuery->with([
            'pemberiTugas.role',
            'pemberiTugas.mahasiswa.prodi',
            'jenisSurat',
            'suratMagang'
        ])
        ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
        ->paginate(15);

        return view('admin_fakultas.list_magang', compact('daftarTugas'));
    }

    public function archive()
    {
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $arsipSurat = TugasSurat::query()
            ->whereHas('suratMagang', function ($q) {
                $q->where('Status', 'Success');
            })
            ->where(function ($q) use ($fakultasId) {
                $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                    $subQ->where('Id_Fakultas', $fakultasId);
                })
                    ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    })
                    ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                        $subQ->where('Id_Fakultas', $fakultasId);
                    });
            })
            ->with(['pemberiTugas', 'jenisSurat'])
            ->orderBy('Tanggal_Diselesaikan', 'desc')
            ->paginate(20);

        return view('admin_fakultas.arsip_surat', compact('arsipSurat'));
    }
}
