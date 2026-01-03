<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use App\Models\TugasSurat;
use App\Models\Role;
use App\Models\SKDosenWali;
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

        // Hitung untuk SK Dosen Wali - dari tabel Req_SK_Dosen_Wali
        $countSKDosen = SKDosenWali::whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })->count();

        $pendingSKDosen = SKDosenWali::whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })->whereIn('Status', ['Pending', 'Ditolak-Admin', 'Ditolak-Wadek1', 'Ditolak-Dekan'])->count();

        $prosesSKDosen = SKDosenWali::whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })->whereIn('Status', ['Dikerjakan admin', 'Menunggu-Persetujuan-Wadek-1', 'Menunggu-Persetujuan-Dekan'])->count();

        $selesaiSKDosen = SKDosenWali::whereHas('prodi.fakultas', function ($q) use ($fakultasId) {
            $q->where('Id_Fakultas', $fakultasId);
        })->where('Status', 'Selesai')->count();

        // TODO: Counter untuk jenis surat baru (setelah database dibuat)
        $countMobilDinas = 0;
        $countCuti = 0;
        $countTidakBeasiswa = 0;
        $countDispensasi = 0;
        $countBerkelakuanBaik = 0;
        $countSKFakultas = 0;
        $countPeminjamanGedung = 0;
        $countLembur = 0;

        return view('admin_fakultas.manajemen_surat_index', compact(
            'countAktif',
            'pendingAktif',
            'prosesAktif',
            'selesaiAktif',
            'countMagang',
            'pendingMagang',
            'prosesMagang',
            'selesaiMagang',
            'countLegalisir',
            'pendingLegalisir',
            'prosesLegalisir',
            'selesaiLegalisir',
            'countSKDosen',
            'pendingSKDosen',
            'prosesSKDosen',
            'selesaiSKDosen',
            'countMobilDinas',
            'countCuti',
            'countTidakBeasiswa',
            'countDispensasi',
            'countBerkelakuanBaik',
            'countSKFakultas',
            'countPeminjamanGedung',
            'countLembur',
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

    // TODO: Method placeholder untuk jenis surat baru
    public function listMobilDinas()
    {
        $daftarTugas = collect(); // Empty collection untuk sementara
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listCuti()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listTidakBeasiswa()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listDispensasi()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listBerkelakuanBaik()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listSKFakultas()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listPeminjamanGedung()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function listLembur()
    {
        $daftarTugas = collect();
        return view('admin_fakultas.list_surat_general', compact('daftarTugas'));
    }

    public function archive()
    {
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Ambil semua surat yang sudah selesai (tidak peduli jenis surat)
        $arsipTugas = TugasSurat::query()
            ->whereIn('Status', ['Selesai', 'selesai', 'Disetujui', 'disetujui', 'Success'])
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
            ->with(['pemberiTugas.role', 'pemberiTugas.mahasiswa.prodi', 'jenisSurat', 'suratKetAktif', 'suratMagang', 'suratLegalisir'])
            ->orderBy('Tanggal_Diselesaikan', 'desc')
            ->get();

        // Ambil semua jenis surat agar card tetap muncul meskipun kosong
        $allJenisSurat = \App\Models\JenisSurat::all();

        // Grouping manual agar semua jenis surat masuk list
        $arsipByJenis = $allJenisSurat->map(function ($jenis) use ($arsipTugas) {
            return (object) [
                'jenis' => $jenis,
                'items' => $arsipTugas->where('Id_Jenis_Surat', $jenis->Id_Jenis_Surat)
            ];
        });

        return view('admin_fakultas.arsip_surat', compact('arsipTugas', 'arsipByJenis'));
    }

    public function archiveDetail($id)
    {
        $jenisSurat = \App\Models\JenisSurat::findOrFail($id);
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        $arsipTugas = TugasSurat::query()
            ->where('Id_Jenis_Surat', $id)
            ->whereIn('Status', ['Selesai', 'selesai', 'Disetujui', 'disetujui', 'Success'])
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
            ->with(['pemberiTugas.role', 'pemberiTugas.mahasiswa.prodi', 'jenisSurat'])
            ->orderBy('Tanggal_Diselesaikan', 'desc')
            ->get();

        return view('admin_fakultas.arsip_detail', compact('jenisSurat', 'arsipTugas'));
    }
}
