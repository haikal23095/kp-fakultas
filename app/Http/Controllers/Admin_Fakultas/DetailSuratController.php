<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\Mahasiswa;
use App\Models\FileArsip;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DetailSuratController extends Controller
{
    /**
     * Tampilkan detail surat berdasarkan ID.
     */
    public function show($id)
    {
        $tugasSurat = TugasSurat::with([
            'pemberiTugas',
            'penerimasTugas',
            'jenisSurat',
            'suratMagang',
        ])->findOrFail($id);

        // Ambil detail pengaju
        $pemberiTugas = $tugasSurat->pemberiTugas;
        $detailPengaju = null;

        if ($pemberiTugas) {
            if ($pemberiTugas->mahasiswa) {
                $detailPengaju = $pemberiTugas->mahasiswa;
                $detailPengaju->tipe = 'Mahasiswa';
            } elseif ($pemberiTugas->dosen) {
                $detailPengaju = $pemberiTugas->dosen;
                $detailPengaju->tipe = 'Dosen';
            } elseif ($pemberiTugas->pegawai) {
                $detailPengaju = $pemberiTugas->pegawai;
                $detailPengaju->tipe = 'Pegawai';
            }
        }

        return view('admin_fakultas.detail_surat', [
            'surat' => $tugasSurat,
            'detailPengaju' => $detailPengaju,
            'activeMenu' => 'manajemen-surat'
        ]);
    }

    /**
     * Download file pendukung surat.
     */
    public function downloadPendukung($id, Request $request)
    {
        $tugasSurat = TugasSurat::findOrFail($id);
        $fileType = $request->query('type', 'proposal');

        $suratMagang = $tugasSurat->suratMagang;
        if (!$suratMagang) {
            abort(404, 'Data surat magang tidak ditemukan');
        }

        $filePath = null;
        switch ($fileType) {
            case 'proposal':
                $filePath = $suratMagang->Dokumen_Proposal;
                break;
            case 'surat_pengantar':
                $filePath = $suratMagang->Surat_Pengantar_Magang;
                break;
        }

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($filePath);
    }
}
