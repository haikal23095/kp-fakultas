<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\Mahasiswa;
use App\Models\FileArsip;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request as HttpRequest;

class DetailSuratController extends Controller
{
    /**
     * Tampilkan detail surat berdasarkan ID.
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Eager load relations yang dibutuhkan. Be defensif jika beberapa relasi belum didefinisikan.
        $with = ['pemberiTugas.role', 'jenisSurat', 'penerimaTugas'];

        if (method_exists(TugasSurat::class, 'fileArsip')) {
            $with[] = 'fileArsip';
        }

        $tugasSurat = TugasSurat::with($with)->find($id);

        // Jika data tidak ditemukan, return 404
        if (!$tugasSurat) {
            abort(404);
        }

        // Coba ambil detail pengaju jika pemberiTugas adalah Mahasiswa
        $detailPengaju = null;
        $pemberi = $tugasSurat->pemberiTugas;
        $roleName = optional($pemberi->role)->Name_Role;

        if ($pemberi && is_string($roleName) && strtolower(trim($roleName)) === 'mahasiswa') {
            // Prefer mengambil via relasi 'mahasiswa' di model User jika ada
            if (method_exists($pemberi, 'mahasiswa')) {
                $detailPengaju = $pemberi->mahasiswa;
            } else {
                // Relasi tidak ada, set null sesuai instruksi
                $detailPengaju = null;
            }
        }

        return view('admin.detail_surat', [
            'surat' => $tugasSurat,
            'detailPengaju' => $detailPengaju,
            'activeMenu' => 'manajemen-surat' // ← Tambahkan ini untuk highlight menu
        ]);
    }

    /**
     * Download dokumen pendukung yang diupload oleh mahasiswa.
     *
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPendukung($id)
    {
        $tugasSurat = TugasSurat::with(['suratMagang'])->findOrFail($id);

        // Cek apakah ada data surat magang
        $suratMagang = $tugasSurat->suratMagang;
        
        if ($suratMagang && $suratMagang->Dokumen_Proposal) {
            $path = $suratMagang->Dokumen_Proposal;
        } else {
            // Path disimpan di data_spesifik['dokumen_pendukung']
            $dataSpesifik = $tugasSurat->data_spesifik;
            $path = $dataSpesifik['dokumen_pendukung'] ?? null;
        }

        if (!$path) {
            return response('Dokumen Pendukung tidak ditemukan.', 404);
        }

        // Gunakan disk 'public' (storage/app/public)
        if (Storage::disk('public')->exists($path)) {
            // Menggunakan Storage::download() untuk memaksa download
            return Storage::disk('public')->download($path);
        }

        return response('Dokumen Pendukung tidak ditemukan di storage.', 404);
    }


    /**
     * Proses upload draft final atau langsung mengajukan ke Dekan.
     * - Jika ada file 'draft_surat' akan disimpan ke tabel File_Arsip.
     * - Jika action == 'proses_ajukan_dekan' tanpa file, hanya ubah status.
     */
    public function processDraft(HttpRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->Id_Role != 1) {
            abort(403);
        }

        $tugas = TugasSurat::findOrFail($id);

        // Jika hanya tombol header diklik untuk langsung proses tanpa upload
        if ($request->input('action') === 'proses_ajukan_dekan') {
            // Cari user Dekan
            $dekan = User::whereHas('role', function ($q) {
                $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
            })->first();

            if ($dekan) {
                $tugas->Id_Penerima_Tugas_Surat = $dekan->Id_User;
                \Log::info('Admin: Mengajukan surat ke Dekan', [
                    'id_surat' => $tugas->Id_Tugas_Surat,
                    'id_dekan' => $dekan->Id_User,
                    'status' => 'menunggu-ttd'
                ]);
            } else {
                \Log::warning('Admin: Dekan tidak ditemukan saat proses ajukan');
            }

            $tugas->Status = 'menunggu-ttd';
            $tugas->save();

            return redirect()->route('admin.surat.detail', $tugas->Id_Tugas_Surat)
                ->with('success', 'Tugas telah diajukan ke Dekan.');
        }

        // Validasi upload draft final (wajib)
        $validated = $request->validate([
            'draft_surat' => 'required|file|mimes:pdf|max:5048',
        ]);

        $file = $request->file('draft_surat');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        // Simpan file di storage/app/public/surat_drafts/{Id_Tugas_Surat}/
        $path = $file->store("surat_drafts/{$tugas->Id_Tugas_Surat}", 'public');

        // Perbarui/masukkan pada tabel File_Arsip
        $fileArsip = FileArsip::where('Id_Tugas_Surat', $tugas->Id_Tugas_Surat)->first();
        if (!$fileArsip) {
            $fileArsip = new FileArsip();
            $fileArsip->Id_Tugas_Surat = $tugas->Id_Tugas_Surat;
        }
        $fileArsip->Path_File = $path;
        $fileArsip->Keterangan = 'Draft Final';
        $fileArsip->Id_Pemberi_Tugas_Surat = $user->Id_User;

        // Cari user Dekan dan set sebagai penerima tugas jika ada
        $dekan = User::whereHas('role', function ($q) {
            $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
        })->first();

        if ($dekan) {
            $fileArsip->Id_Penerima_Tugas_Surat = $dekan->Id_User;
            $tugas->Id_Penerima_Tugas_Surat = $dekan->Id_User;
            \Log::info('Admin: Upload draft dan ajukan ke Dekan', [
                'id_surat' => $tugas->Id_Tugas_Surat,
                'id_dekan' => $dekan->Id_User,
                'draft_path' => $path,
                'status' => 'menunggu-ttd'
            ]);
        } else {
            // fallback: tetap gunakan penerima yang ada
            $fileArsip->Id_Penerima_Tugas_Surat = $tugas->Id_Penerima_Tugas_Surat ?? $user->Id_User;
            \Log::warning('Admin: Dekan tidak ditemukan saat upload draft');
        }

        $fileArsip->save();

        // Update status untuk mengajukan ke Dekan
        $tugas->Status = 'menunggu-ttd';
        $tugas->save();

        return redirect()->route('admin.surat.detail', $tugas->Id_Tugas_Surat)
            ->with('success', 'Draft final berhasil diupload dan diajukan ke Dekan.');
    }

    /**
     * Menolak pengajuan surat.
     */
    public function reject(HttpRequest $request, $id)
    {
        $user = Auth::user();
        if (!$user || $user->Id_Role != 1) {
            abort(403);
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        $tugas = TugasSurat::findOrFail($id);
        $tugas->Status = 'Ditolak';
        
        // Update data_spesifik with rejection reason
        $dataSpesifik = $tugas->data_spesifik ?? [];
        $dataSpesifik['alasan_penolakan'] = $request->input('alasan_penolakan');
        $dataSpesifik['tanggal_penolakan'] = now()->toDateTimeString();
        $dataSpesifik['ditolak_oleh'] = $user->Name_User;
        
        $tugas->data_spesifik = $dataSpesifik;
        $tugas->save();

        return redirect()->route('admin.surat.detail', $tugas->Id_Tugas_Surat)
            ->with('success', 'Surat telah ditolak.');
    }
}
