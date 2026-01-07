<?php

namespace App\Http\Controllers;

use App\Models\SuratPeminjamanMobil;
use App\Models\Kendaraan;
use App\Models\TugasSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanMobilAdminController extends Controller
{
    /**
     * Tampilkan daftar pengajuan peminjaman mobil yang masuk (Status: Diajukan)
     */
    public function index()
    {
        $pengajuan = SuratPeminjamanMobil::diajukan()
            ->with(['user', 'tugasSurat'])
            ->orderBy('created_at', 'desc')
            ->get();

        $kendaraan = Kendaraan::orderBy('nama_kendaraan')->get();

        return view('admin_fakultas.peminjaman_mobil.index', compact('pengajuan', 'kendaraan'));
    }

    /**
     * Tampilkan detail pengajuan untuk validasi
     */
    public function show($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['user', 'tugasSurat', 'kendaraan'])
            ->findOrFail($id);

        // Ambil kendaraan tersedia yang tidak bentrok tanggal
        $kendaraanTersedia = Kendaraan::tersedia()
            ->get()
            ->filter(function($kendaraan) use ($peminjaman) {
                return !SuratPeminjamanMobil::isTanggalBentrok(
                    $kendaraan->id,
                    $peminjaman->tanggal_pemakaian_mulai,
                    $peminjaman->tanggal_pemakaian_selesai
                );
            });

        return view('admin_fakultas.peminjaman_mobil.show', compact('peminjaman', 'kendaraanTersedia'));
    }

    /**
     * Verifikasi dan assign kendaraan (Terima)
     */
    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'Id_Kendaraan' => 'required|exists:Kendaraan,id',
            'nomor_surat' => 'required|string|max:100|unique:Surat_Peminjaman_Mobil,nomor_surat',
            'rekomendasi_admin' => 'nullable|string',
        ], [
            'Id_Kendaraan.required' => 'Silakan pilih kendaraan',
            'Id_Kendaraan.exists' => 'Kendaraan tidak valid',
            'nomor_surat.required' => 'Nomor surat harus diisi',
            'nomor_surat.unique' => 'Nomor surat sudah digunakan',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = SuratPeminjamanMobil::findOrFail($id);

            // Cek apakah kendaraan bentrok
            if (SuratPeminjamanMobil::isTanggalBentrok(
                $request->Id_Kendaraan,
                $peminjaman->tanggal_pemakaian_mulai,
                $peminjaman->tanggal_pemakaian_selesai,
                $id
            )) {
                throw new \Exception('Kendaraan tidak tersedia pada tanggal yang dipilih');
            }

            // Update peminjaman dengan kendaraan dan nomor surat
            $peminjaman->update([
                'Id_Kendaraan' => $request->Id_Kendaraan,
                'nomor_surat' => $request->nomor_surat,
                'rekomendasi_admin' => $request->rekomendasi_admin,
                'status_pengajuan' => 'Diverifikasi_Admin',
            ]);

            // Update Tugas_Surat dengan nomor surat
            if ($peminjaman->tugasSurat) {
                $peminjaman->tugasSurat->update([
                    'Nomor_Surat' => $request->nomor_surat,
                    'Status_Surat' => 'Proses',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin_fakultas.surat.mobil_dinas')
                ->with('success', 'Pengajuan berhasil diverifikasi dengan nomor surat: ' . $request->nomor_surat);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memverifikasi pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pengajuan
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
                ->route('admin_fakultas.surat.mobil_dinas')
                ->with('success', 'Pengajuan telah ditolak');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Preview/Download Draft Surat Peminjaman Mobil
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
     * Download Surat Final Peminjaman Mobil (untuk arsip)
     */
    public function downloadSurat($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['user.mahasiswa', 'kendaraan', 'pejabat', 'tugasSurat'])
            ->findOrFail($id);

        if (!$peminjaman->tugasSurat || !$peminjaman->tugasSurat->Nomor_Surat) {
            return back()->with('error', 'Surat belum diberi nomor');
        }

        // Generate PDF surat final dengan nomor surat
        $pdf = \PDF::loadView('pdf.surat_peminjaman_mobil', compact('peminjaman'));
        
        return $pdf->download('Surat_Peminjaman_Mobil_' . $peminjaman->tugasSurat->Nomor_Surat . '.pdf');
    }

    /**
     * Tampilkan arsip peminjaman mobil
     */
    public function arsip()
    {
        $arsip = SuratPeminjamanMobil::arsip()
            ->with(['user', 'kendaraan', 'pejabat', 'tugasSurat'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin_fakultas.peminjaman_mobil.arsip', compact('arsip'));
    }

    /**
     * Tampilkan daftar yang ditolak
     */
    public function ditolak()
    {
        $ditolak = SuratPeminjamanMobil::ditolak()
            ->with(['user', 'tugasSurat'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin_fakultas.peminjaman_mobil.ditolak', compact('ditolak'));
    }

    /**
     * Management Kendaraan - Index
     */
    public function kendaraanIndex()
    {
        $kendaraan = Kendaraan::orderBy('nama_kendaraan')->get();
        return view('admin_fakultas.kendaraan.index', compact('kendaraan'));
    }

    /**
     * Management Kendaraan - Store
     */
    public function kendaraanStore(Request $request)
    {
        $request->validate([
            'nama_kendaraan' => 'required|string|max:255',
            'plat_nomor' => 'required|string|unique:Kendaraan,plat_nomor',
            'kapasitas' => 'required|integer|min:1',
            'status_kendaraan' => 'required|in:Tersedia,Maintenance',
        ]);

        Kendaraan::create($request->all());

        return redirect()
            ->route('admin_fakultas.kendaraan.index')
            ->with('success', 'Kendaraan berhasil ditambahkan');
    }

    /**
     * Management Kendaraan - Update
     */
    public function kendaraanUpdate(Request $request, $id)
    {
        $request->validate([
            'nama_kendaraan' => 'required|string|max:255',
            'plat_nomor' => 'required|string|unique:Kendaraan,plat_nomor,' . $id,
            'kapasitas' => 'required|integer|min:1',
            'status_kendaraan' => 'required|in:Tersedia,Maintenance',
        ]);

        $kendaraan = Kendaraan::findOrFail($id);
        $kendaraan->update($request->all());

        return redirect()
            ->route('admin_fakultas.kendaraan.index')
            ->with('success', 'Kendaraan berhasil diperbarui');
    }

    /**
     * Management Kendaraan - Delete
     */
    public function kendaraanDestroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        
        // Cek apakah kendaraan masih digunakan
        if ($kendaraan->peminjamanMobil()->exists()) {
            return back()->with('error', 'Kendaraan tidak dapat dihapus karena masih terdapat riwayat peminjaman');
        }

        $kendaraan->delete();

        return redirect()
            ->route('admin_fakultas.kendaraan.index')
            ->with('success', 'Kendaraan berhasil dihapus');
    }
}
