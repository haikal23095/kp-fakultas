<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use App\Models\TugasSurat;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManajemenSuratController extends Controller
{
    public function index()
    {
        // 1) Update status tugas yang terlambat (delegasi ke model)
        TugasSurat::updateStatusTerlambat();

        // 2) Ambil Id_Fakultas dari user yang login
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Base query dengan filter fakultas (filter berdasarkan PEMBERI tugas = yang mengajukan)
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

        // 3) Ambil statistik surat berdasarkan status dengan filter fakultas
        // Status sekarang ada di tabel spesifik (Surat_Magang)
        $totalSurat = $baseQuery()->has('suratMagang')->count();
        $suratBaru = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->where('Status', 'Diajukan-ke-koordinator');
            })->count();
        $suratDiproses = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->whereIn('Status', ['Dikerjakan-admin', 'Diajukan-ke-dekan']);
            })->count();
        $suratSelesai = $baseQuery()
            ->whereHas('suratMagang', function ($q) {
                $q->where('Status', 'Success');
            })->count();

        // 4) Ambil data surat untuk tabel
        $tugasSurat = $baseQuery()
            ->with([
                'pemberiTugas.role',
                'pemberiTugas.mahasiswa.prodi',
                'pemberiTugas.dosen.prodi',
                'pemberiTugas.pegawai.prodi',
                'jenisSurat',
                'suratMagang'
            ])
            ->has('suratMagang')
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->paginate(15);

        return view('admin_fakultas.manajemen_surat', compact(
            'tugasSurat',
            'totalSurat',
            'suratBaru',
            'suratDiproses',
            'suratSelesai'
        ));
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
