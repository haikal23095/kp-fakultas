<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMagang;
use App\Models\TugasSurat;
use Illuminate\Support\Facades\Storage;

class SuratMagangController extends Controller
{
    /**
     * Menampilkan daftar surat magang yang statusnya Dikerjakan-admin
     */
    public function index()
    {
        $daftarSurat = SuratMagang::with([
            'tugasSurat.pemberiTugas.mahasiswa.prodi',
            'tugasSurat.jenisSurat',
            'koordinator'
        ])
            ->where('Status', 'Dikerjakan-admin')
            ->orderBy('id_no', 'desc')
            ->get();

        return view('admin_fakultas.surat_magang.index', compact('daftarSurat'));
    }

    /**
     * Menampilkan detail surat magang
     */
    public function show($id)
    {
        $surat = SuratMagang::with([
            'tugasSurat.pemberiTugas.mahasiswa.prodi',
            'tugasSurat.jenisSurat',
            'koordinator'
        ])->findOrFail($id);

        // Decode JSON data
        $dataMahasiswa = is_array($surat->Data_Mahasiswa)
            ? $surat->Data_Mahasiswa
            : json_decode($surat->Data_Mahasiswa, true);

        $dataDosenPembimbing = is_array($surat->Data_Dosen_pembiming)
            ? $surat->Data_Dosen_pembiming
            : json_decode($surat->Data_Dosen_pembiming, true);

        return view('admin_fakultas.surat_magang.detail', compact('surat', 'dataMahasiswa', 'dataDosenPembimbing'));
    }

    /**
     * Download proposal magang
     */
    public function downloadProposal($id)
    {
        $surat = SuratMagang::findOrFail($id);

        if (!$surat->Dokumen_Proposal) {
            return redirect()->back()->with('error', 'Dokumen proposal tidak tersedia.');
        }

        $filePath = storage_path('app/public/' . $surat->Dokumen_Proposal);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }

    /**
     * Generate dan assign nomor surat
     */
    public function assignNomorSurat(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:100|unique:Tugas_Surat,Nomor_Surat',
        ], [
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'nomor_surat.unique' => 'Nomor surat sudah digunakan. Gunakan nomor lain.',
        ]);

        $surat = SuratMagang::with('tugasSurat')->findOrFail($id);

        // Update nomor surat di Tugas_Surat
        $surat->tugasSurat->Nomor_Surat = $request->nomor_surat;
        $surat->tugasSurat->save();

        // Update status surat magang
        $surat->Status = 'Diajukan-ke-dekan';
        $surat->save();

        return redirect()->route('admin_fakultas.surat_magang.index')
            ->with('success', 'Nomor surat berhasil diberikan dan surat diteruskan ke Dekan!');
    }
}
