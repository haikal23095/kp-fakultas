<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\SuratPeminjamanMobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Notifikasi;
use Carbon\Carbon;

class PeminjamanMobilController extends Controller
{
    /**
     * Tampilkan form pengajuan peminjaman mobil
     */
    public function create()
    {
        return view('mahasiswa.pengajuan.form_peminjaman_mobil');
    }

    /**
     * Simpan pengajuan peminjaman mobil
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'tujuan' => 'required|string',
            'keperluan' => 'required|string',
            'tanggal_pemakaian_mulai' => 'required|date|after_or_equal:today',
            'tanggal_pemakaian_selesai' => 'required|date|after_or_equal:tanggal_pemakaian_mulai',
            'jumlah_penumpang' => 'required|integer|min:1',
        ], [
            'tujuan.required' => 'Tujuan harus diisi',
            'keperluan.required' => 'Keperluan harus diisi',
            'tanggal_pemakaian_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_pemakaian_mulai.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu',
            'tanggal_pemakaian_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_pemakaian_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'jumlah_penumpang.required' => 'Jumlah penumpang harus diisi',
            'jumlah_penumpang.min' => 'Jumlah penumpang minimal 1 orang',
        ]);

        DB::beginTransaction();
        try {
            // Insert langsung ke Surat_Peminjaman_Mobil
            $surat = SuratPeminjamanMobil::create([
                'Id_User' => Auth::id(),
                'tujuan' => $validated['tujuan'],
                'keperluan' => $validated['keperluan'],
                'tanggal_pemakaian_mulai' => $validated['tanggal_pemakaian_mulai'],
                'tanggal_pemakaian_selesai' => $validated['tanggal_pemakaian_selesai'],
                'jumlah_penumpang' => $validated['jumlah_penumpang'],
                'status_pengajuan' => 'Diajukan',
                'Status' => 'baru',
                'Tanggal_Diberikan' => Carbon::now(),
            ]);

            // 3. Kirim notifikasi ke Admin Fakultas (Id_Role = 7)
            $adminFakultas = User::where('Id_Role', 7)->get();
            foreach ($adminFakultas as $admin) {
                Notifikasi::create([
                    'Tipe_Notifikasi' => "Invitation",
                    'Pesan' => "Pengajuan baru: Peminjaman Mobil Dinas dari " . Auth::user()->Name_User,
                    'Dest_user' => $admin->Id_User,
                    'Source_User' => Auth::id(),
                    'Is_Read' => false,
                    'Data_Tambahan' => json_encode([
                        'id_letter' => $surat->id,
                        'letter_type' => 'mobil_dinas',
                        'action_url' => route('admin_fakultas.surat.mobil_dinas'),
                    ]),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('mahasiswa.riwayat.mobil_dinas')
                ->with('success', 'Pengajuan peminjaman mobil dinas berhasil diajukan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal mengajukan peminjaman mobil: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan riwayat pengajuan peminjaman mobil mahasiswa
     */
    public function riwayat()
    {
        $userId = Auth::id();

        $riwayat = SuratPeminjamanMobil::where('Id_User', $userId)
            ->with(['kendaraan', 'pejabat'])
            ->orderBy('id', 'desc')
            ->get();

        return view('mahasiswa.riwayat.mobil_dinas', compact('riwayat'));
    }

    /**
     * Tampilkan detail pengajuan
     * Note: Method ini tidak digunakan, detail ditampilkan via modal di view mobil_dinas.blade.php
     */
    // public function show($id)
    // {
    //     $peminjaman = SuratPeminjamanMobil::with(['tugasSurat', 'kendaraan', 'pejabat', 'user'])
    //         ->where('Id_User', Auth::id())
    //         ->findOrFail($id);
    //
    //     return view('mahasiswa.riwayat.mobil_dinas', compact('peminjaman'));
    // }

    /**
     * Preview surat final (HTML)
     * TODO: Buat view mahasiswa.pdf.peminjaman_mobil untuk preview surat
     */
    public function previewSurat($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['tugasSurat', 'kendaraan', 'pejabat', 'user', 'verification'])
            ->where('Id_User', Auth::id())
            ->findOrFail($id);

        if ($peminjaman->status_pengajuan != 'Selesai') {
            return back()->with('error', 'Surat belum selesai diproses');
        }

        // TODO: Buat view mahasiswa/pdf/peminjaman_mobil.blade.php untuk preview HTML surat
        return redirect()->route('mahasiswa.peminjaman.mobil.download', $id);
        // return view('mahasiswa.pdf.peminjaman_mobil', compact('peminjaman'));
    }

    /**
     * Download file surat final
     */
    public function downloadSurat($id)
    {
        $peminjaman = SuratPeminjamanMobil::with(['tugasSurat', 'kendaraan', 'pejabat', 'user', 'verification'])
            ->where('Id_User', Auth::id())
            ->findOrFail($id);

        if ($peminjaman->status_pengajuan != 'Selesai') {
            return back()->with('error', 'Surat belum selesai diproses');
        }

        // Generate PDF
        $pdf = \PDF::loadView('pdf.surat_peminjaman_mobil', compact('peminjaman'));

        $fileName = 'Surat_Peminjaman_Mobil_' . $peminjaman->nomor_surat . '.pdf';
        $fileName = str_replace('/', '_', $fileName);

        return $pdf->download($fileName);
    }
}
