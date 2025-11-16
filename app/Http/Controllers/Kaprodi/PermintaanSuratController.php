<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratMagang;
use App\Models\TugasSurat;

class PermintaanSuratController extends Controller
{
    /**
     * Menampilkan daftar surat masuk yang perlu persetujuan Kaprodi
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil data Kaprodi (bisa dari Dosen atau Pegawai)
        $kaprodiDosen = $user->dosen;
        $kaprodiPegawai = $user->pegawai;

        // Ambil Id_Prodi dari Kaprodi
        $prodiId = $kaprodiDosen?->Id_Prodi ?? $kaprodiPegawai?->Id_Prodi;

        if (!$prodiId) {
            return view('kaprodi.permintaan_surat', [
                'daftarSurat' => collect([])
            ]);
        }

        // Ambil Surat Magang yang:
        // 1. Diajukan oleh mahasiswa dari prodi yang sama
        // 2. Acc_Koordinator = false (belum disetujui)
        $daftarSurat = SuratMagang::query()
            ->with([
                'tugasSurat.pemberiTugas.mahasiswa.prodi',
                'tugasSurat.jenisSurat'
            ])
            ->whereHas('tugasSurat.pemberiTugas.mahasiswa', function ($q) use ($prodiId) {
                $q->where('Id_Prodi', $prodiId);
            })
            ->where('Acc_Koordinator', false)
            ->orderBy('id_no', 'desc')
            ->get();

        // Ambil NIP Kaprodi untuk preview surat
        $kaprodiNIP = $kaprodiDosen?->NIP ?? $kaprodiPegawai?->NIP ?? null;

        return view('kaprodi.permintaan_surat', compact('daftarSurat', 'kaprodiNIP'));
    }

    /**
     * Menyetujui surat magang
     */
    public function approve($id)
    {
        $suratMagang = SuratMagang::findOrFail($id);

        // Update Acc_Koordinator menjadi true
        $suratMagang->Acc_Koordinator = true;
        $suratMagang->save();

        return redirect()->route('kaprodi.surat.index')
            ->with('success', 'Surat pengantar magang berhasil disetujui!');
    }

    /**
     * Menolak surat magang
     */
    public function reject(Request $request, $id)
    {
        $suratMagang = SuratMagang::findOrFail($id);

        // Acc_Koordinator tetap false (tidak diubah)
        // Hanya update status Tugas_Surat menjadi "Ditolak"
        $tugasSurat = $suratMagang->tugasSurat;
        if ($tugasSurat) {
            $tugasSurat->Status = 'Ditolak';
            $tugasSurat->save();
        }

        return redirect()->route('kaprodi.surat.index')
            ->with('success', 'Surat pengantar magang ditolak. Status tugas diubah menjadi Ditolak.');
    }

    /**
     * Download proposal surat magang
     */
    public function downloadProposal($id)
    {
        $suratMagang = SuratMagang::findOrFail($id);

        // Validasi: pastikan Kaprodi hanya bisa download proposal dari mahasiswa di prodinya
        $user = Auth::user();
        $kaprodiDosen = $user->dosen;
        $kaprodiPegawai = $user->pegawai;
        $prodiId = $kaprodiDosen?->Id_Prodi ?? $kaprodiPegawai?->Id_Prodi;

        $mahasiswa = $suratMagang->tugasSurat->pemberiTugas->mahasiswa ?? null;
        if (!$mahasiswa || $mahasiswa->Id_Prodi != $prodiId) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh dokumen ini.');
        }

        // Cek apakah file proposal ada
        if (!$suratMagang->Dokumen_Proposal) {
            abort(404, 'Dokumen proposal tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $suratMagang->Dokumen_Proposal);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }
}
