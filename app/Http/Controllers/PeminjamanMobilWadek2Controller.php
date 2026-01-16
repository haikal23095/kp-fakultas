<?php

namespace App\Http\Controllers;

use App\Models\SuratPeminjamanMobil;
use App\Models\TugasSurat;
use App\Models\Pejabat;
use App\Models\SuratVerification;
use App\Helpers\QrCodeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PeminjamanMobilWadek2Controller extends Controller
{
    /**
     * Tampilkan daftar pengajuan yang perlu disetujui Wadek2 (Status: Diverifikasi_Admin)
     */
    public function index()
    {
        $pengajuan = SuratPeminjamanMobil::diverifikasiAdmin()
            ->with(['user', 'kendaraan', 'tugasSurat'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('wadek2.sarpras.peminjaman_mobil.index', compact('pengajuan'));
    }

    /**
     * Tampilkan detail pengajuan untuk approval
     */
    public function show($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['user', 'kendaraan', 'tugasSurat'])
            ->findOrFail($id);

        // Ambil pejabat Wadek 2 yang sedang login melalui Dosen
        $dosen = \App\Models\Dosen::where('Id_User', Auth::id())->first();
        $pejabat = $dosen ? $dosen->pejabat : null;

        return view('wadek2.sarpras.peminjaman_mobil.show', compact('peminjaman', 'pejabat'));
    }

    /**
     * Setujui pengajuan (nomor surat sudah ada dari admin)
     */
    public function setujui(Request $request, $id)
    {
        $request->validate([
            'catatan_wadek2' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = SuratPeminjamanMobil::findOrFail($id);
            $user = Auth::user();

            // Validasi nomor surat sudah ada (dari admin)
            if (!$peminjaman->nomor_surat) {
                throw new \Exception('Nomor surat belum diberikan oleh admin. Hubungi admin fakultas.');
            }

            // Ambil pejabat yang sedang login melalui Dosen
            $dosen = \App\Models\Dosen::where('Id_User', Auth::id())->first();
            $pejabat = $dosen ? $dosen->pejabat : null;

            // Generate QR Code dengan informasi penandatangan
            $qrData = [
                'jenis_surat' => 'Surat Peminjaman Mobil Dinas',
                'nomor_surat' => $peminjaman->nomor_surat,
                'peminjam' => $peminjaman->user->Name_User ?? 'N/A',
                'tujuan' => $peminjaman->tujuan,
                'kendaraan' => $peminjaman->kendaraan->nama_kendaraan ?? 'N/A',
                'ditandatangani_oleh' => $user->Name_User,
                'jabatan' => 'Wakil Dekan II',
                'nip' => $pejabat->pegawai->NIP ?? 'N/A',
                'tanggal' => now()->format('d-m-Y H:i:s'),
                'id_tugas_surat' => $peminjaman->tugasSurat->Id_Tugas_Surat ?? null,
            ];

            // Generate QR Code dan simpan path-nya
            $qrPath = QrCodeHelper::generateAndGetPath(
                json_encode($qrData), 
                'peminjaman_mobil_' . $peminjaman->id
            );

            // Buat record verifikasi jika ada tugas surat
            if ($peminjaman->tugasSurat) {
                SuratVerification::create([
                    'id_tugas_surat' => $peminjaman->tugasSurat->Id_Tugas_Surat,
                    'signed_by' => $user->Name_User,
                    'signed_by_user_id' => $user->Id_User,
                    'signed_at' => now(),
                    'qr_path' => $qrPath,
                    'token' => \Illuminate\Support\Str::random(32),
                ]);
            }

            // Update peminjaman dengan approval Wadek2 dan QR Path
            $peminjaman->update([
                'Id_Pejabat' => $pejabat ? $pejabat->Id_Pejabat : null,
                'catatan_wadek2' => $request->catatan_wadek2,
                'qr_code_path' => $qrPath,
                'status_pengajuan' => 'Selesai', // Langsung Selesai setelah ACC
            ]);

            // Update Tugas_Surat status Selesai
            if ($peminjaman->tugasSurat) {
                $peminjaman->tugasSurat->update([
                    'Status_Surat' => 'Selesai',
                    'Tanggal_Diselesaikan' => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('wadek2.sarpras.peminjaman_mobil.index')
                ->with('success', 'Pengajuan berhasil disetujui dengan nomor surat: ' . $peminjaman->nomor_surat);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyetujui pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pengajuan (opsional - jika Wadek2 ingin menolak setelah verifikasi admin)
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = SuratPeminjamanMobil::findOrFail($id);

            // Update peminjaman
            $peminjaman->update([
                'alasan_penolakan' => $request->alasan_penolakan,
                'status_pengajuan' => 'Ditolak',
            ]);

            // Update Tugas_Surat
            if ($peminjaman->tugasSurat) {
                $peminjaman->tugasSurat->update([
                    'Status_Surat' => 'Ditolak',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('wadek2.sarpras.peminjaman_mobil.index')
                ->with('success', 'Pengajuan telah ditolak');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Preview Draft Surat untuk Wadek2
     */
    public function previewDraft($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['user.mahasiswa', 'kendaraan', 'pejabat', 'tugasSurat'])
            ->findOrFail($id);

        // Generate PDF draft surat
        $pdf = \PDF::loadView('pdf.surat_peminjaman_mobil_draft', compact('peminjaman'));
        
        return $pdf->stream('Draft_Surat_Peminjaman_Mobil_' . $peminjaman->id . '.pdf');
    }

    /**
     * Tampilkan arsip peminjaman mobil yang sudah disetujui
     */
    public function arsip()
    {
        $arsip = SuratPeminjamanMobil::arsip()
            ->with(['user', 'kendaraan', 'pejabat', 'tugasSurat'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('wadek2.sarpras.peminjaman_mobil.arsip', compact('arsip'));
    }

    /**
     * Download file surat final
     */
    public function downloadSurat($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['user.mahasiswa', 'kendaraan', 'pejabat', 'tugasSurat'])
            ->findOrFail($id);

        if ($peminjaman->status_pengajuan !== 'Selesai') {
            return back()->with('error', 'Surat belum disetujui');
        }

        // Generate PDF surat final dengan QR Code
        $pdf = \PDF::loadView('pdf.surat_peminjaman_mobil', compact('peminjaman'));
        
        return $pdf->download('Surat_Peminjaman_Mobil_' . $peminjaman->nomor_surat . '.pdf');
    }
}
