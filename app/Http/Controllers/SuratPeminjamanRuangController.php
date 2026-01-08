<?php

namespace App\Http\Controllers;

use App\Models\SuratPeminjamanRuang;
use App\Models\TugasSurat;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratPeminjamanRuangController extends Controller
{
    /**
     * Tampilkan form peminjaman ruang
     */
    public function create()
    {
        return view('mahasiswa.pengajuan-surat.form_peminjaman_ruang');
    }

    /**
     * Simpan pengajuan peminjaman ruang
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jumlah_peserta' => 'required|integer|min:1',
            'file_lampiran' => 'required|file|mimes:pdf|max:2048',
            'keterangan' => 'nullable|string',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan harus diisi',
            'penyelenggara.required' => 'Nama penyelenggara harus diisi',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
            'jumlah_peserta.required' => 'Jumlah peserta harus diisi',
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1 orang',
            'file_lampiran.required' => 'File proposal harus diupload',
            'file_lampiran.mimes' => 'File proposal harus berformat PDF',
            'file_lampiran.max' => 'Ukuran file proposal maksimal 2MB',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            // Upload file lampiran
            $filePath = null;
            if ($request->hasFile('file_lampiran')) {
                $file = $request->file('file_lampiran');
                $fileName = time() . '_' . $user->Id_User . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('surat_peminjaman_ruang', $fileName, 'public');
            }

            // Ambil jenis surat untuk Peminjaman Ruang (buat dulu jika belum ada)
            $jenisSurat = JenisSurat::where('Nama_Surat', 'Peminjaman Ruang')->first();
            if (!$jenisSurat) {
                // Generate ID baru
                $maxId = JenisSurat::max('Id_Jenis_Surat') ?? 0;
                $newId = $maxId + 1;
                
                JenisSurat::create([
                    'Id_Jenis_Surat' => $newId,
                    'Nama_Surat' => 'Peminjaman Ruang',
                ]);
                
                // Ambil ulang dari database
                $jenisSurat = JenisSurat::where('Id_Jenis_Surat', $newId)->first();
            }

            // Insert ke Tugas_Surat
            $tugasSurat = TugasSurat::create([
                'Id_Jenis_Surat' => $jenisSurat->Id_Jenis_Surat,
                'Id_Pemberi_Tugas_Surat' => $user->Id_User, // Mahasiswa yang mengajukan
                'Status' => 'baru',
                'Tanggal_Diberikan_Tugas_Surat' => now(),
            ]);

            // Insert ke Surat_Peminjaman_Ruang
            SuratPeminjamanRuang::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'nama_kegiatan' => $request->nama_kegiatan,
                'penyelenggara' => $request->penyelenggara,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'jumlah_peserta' => $request->jumlah_peserta,
                'file_lampiran' => $filePath,
                'keterangan' => $request->keterangan,
                'status_pengajuan' => 'Diajukan',
            ]);

            DB::commit();

            return redirect()
                ->route('mahasiswa.riwayat.peminjaman_ruang')
                ->with('success', 'Pengajuan peminjaman ruang berhasil dikirim! Silakan tunggu verifikasi dari admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus file jika ada error
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal mengirim pengajuan: ' . $e->getMessage());
        }
    }
}
