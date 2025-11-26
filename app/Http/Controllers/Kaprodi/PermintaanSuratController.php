<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratMagang;
use App\Models\TugasSurat;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Facades\Storage;

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

        // Ambil ID Kaprodi (dari Dosen atau Pegawai)
        $kaprodiId = $kaprodiDosen?->Id_Dosen ?? $kaprodiPegawai?->Id_Pegawai;

        // Ambil Surat Magang yang:
        // 1. Nama_Koordinator = ID Kaprodi yang sedang login
        // 2. Status = 'Diajukan-ke-koordinator'
        // 3. Acc_Koordinator = false (belum disetujui)
        $daftarSurat = SuratMagang::query()
            ->with([
                'tugasSurat.pemberiTugas.mahasiswa.prodi',
                'tugasSurat.jenisSurat',
                'koordinator' // Load relasi ke Dosen (Koordinator)
            ])
            ->where('Nama_Koordinator', $kaprodiId)
            ->where('Status', 'Diajukan-ke-koordinator')
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
        $user = Auth::user();
        $suratMagang = SuratMagang::with('koordinator')->findOrFail($id);

        // Get nama koordinator yang menyetujui
        $kaprodiDosen = $user->dosen;
        $kaprodiPegawai = $user->pegawai;
        $namaKoordinator = $kaprodiDosen?->Nama_Dosen ?? $kaprodiPegawai?->Nama_Pegawai ?? 'Koordinator';

        // Generate QR Code
        $verificationUrl = route('surat.verify', $suratMagang->id_no);

        $qrCode = new QrCode(
            data: $verificationUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Save QR Code image
        $qrCodeFileName = 'qr_code_' . $suratMagang->id_no . '_' . time() . '.png';
        $qrCodePath = 'qr_codes/' . $qrCodeFileName;
        Storage::disk('public')->put($qrCodePath, $result->getString());

        // Update Acc_Koordinator menjadi true dan simpan QR Code path
        $suratMagang->Acc_Koordinator = true;
        $suratMagang->Status = 'Dikerjakan-admin'; // Update status ke tahap berikutnya
        $suratMagang->Qr_code = $qrCodePath;
        $suratMagang->save();

        // Update Status_KP mahasiswa menjadi Sedang_Melaksanakan
        $this->updateMahasiswaStatusKP($suratMagang, 'Sedang_Melaksanakan');

        return redirect()->route('kaprodi.surat.index')
            ->with('success', 'Surat pengantar magang berhasil disetujui dan QR Code telah dibuat!');
    }

    /**
     * Menolak surat magang
     */
    public function reject(Request $request, $id)
    {
        // Validasi komentar wajib diisi
        $request->validate([
            'komentar' => 'required|string|min:10|max:1000',
        ], [
            'komentar.required' => 'Komentar wajib diisi saat menolak surat.',
            'komentar.min' => 'Komentar minimal 10 karakter.',
            'komentar.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        $suratMagang = SuratMagang::findOrFail($id);

        // Update Status menjadi "Ditolak" dan simpan Komentar di tabel Surat_Magang
        $suratMagang->Status = 'Ditolak';
        $suratMagang->Komentar = $request->komentar;
        $suratMagang->save();

        return redirect()->route('kaprodi.surat.index')
            ->with('success', 'Surat pengantar magang ditolak dengan komentar.');
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

    /**
     * Helper method untuk update Status_KP mahasiswa
     */
    private function updateMahasiswaStatusKP($suratMagang, $status)
    {
        $dataMahasiswa = $suratMagang->Data_Mahasiswa;

        if (!is_array($dataMahasiswa)) {
            return;
        }

        foreach ($dataMahasiswa as $mhs) {
            $nim = $mhs['nim'] ?? null;

            if (!$nim) {
                continue;
            }

            $mahasiswa = \App\Models\Mahasiswa::where('NIM', $nim)->first();

            if ($mahasiswa) {
                $mahasiswa->Status_KP = $status;
                $mahasiswa->save();
            }
        }
    }
}
