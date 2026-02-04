<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratKetAktif;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Notifikasi;
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
        $surat = SuratKetAktif::with([
            'pemberiTugas.mahasiswa',
            'penerimaTugas'
        ])->findOrFail($id);

        // Ambil detail pengaju
        $pemberiTugas = $surat->pemberiTugas;
        $detailPengaju = null;

        if ($pemberiTugas && $pemberiTugas->mahasiswa) {
            $detailPengaju = $pemberiTugas->mahasiswa;
            $detailPengaju->tipe = 'Mahasiswa';
        }

        return view('admin_fakultas.detail_surat', [
            'surat' => $surat,
            'detailPengaju' => $detailPengaju,
            'activeMenu' => 'manajemen-surat'
        ]);
    }

    /**
     * Download file pendukung surat (KRS).
     */
    public function downloadPendukung($id, Request $request)
    {
        $surat = SuratKetAktif::findOrFail($id);
        $filePath = $surat->KRS;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($filePath);
    }

    /**
     * Preview file pendukung surat (KRS) di browser.
     */
    public function previewPendukung($id, Request $request)
    {
        $surat = SuratKetAktif::findOrFail($id);
        $filePath = $surat->KRS;

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->response($filePath, null, ['Content-Disposition' => 'inline']);
    }

    /**
     * Menolak pengajuan surat.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $surat = SuratKetAktif::findOrFail($id);

        $surat->Status = 'Ditolak';
        $surat->save();

        // Kirim notifikasi ke mahasiswa
        Notifikasi::create([
            'Tipe_Notifikasi' => 'Rejected',
            'Pesan' => '❌ Pengajuan surat keterangan aktif Anda ditolak oleh Admin Fakultas. Alasan: ' . $request->input('alasan_penolakan'),
            'Dest_user' => $surat->Id_Pemberi_Tugas,
            'Source_User' => $user->Id_User,
            'Is_Read' => false,
            'Data_Tambahan' => json_encode([
                'id_surat' => $surat->id_no,
                'jenis_surat' => 'aktif',
                'action_url' => route('mahasiswa.riwayat.aktif'),
            ]),
            'created_at' => now(),
        ]);

        return redirect()->route('admin_fakultas.surat.detail', $surat->id_no)
            ->with('success', 'Surat telah ditolak dan notifikasi dikirim ke mahasiswa.');
    }

    /**
     * Beri nomor surat dan teruskan ke Dekan.
     */
    public function forwardToDean(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        $surat = SuratKetAktif::findOrFail($id);

        $surat->Nomor_Surat = $request->input('nomor_surat');
        $surat->Status = 'Menunggu-TTD-Dekan';

        // Cari user Dekan
        $dekan = User::whereHas('role', function ($q) {
            $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
        })->first();

        if ($dekan) {
            $surat->Id_Penerima_Tugas = $dekan->Id_User;

            // Kirim notifikasi ke Dekan
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Invitation',
                'Pesan' => '📝 Surat keterangan aktif baru menunggu persetujuan dan tanda tangan Anda. Nomor: ' . $surat->Nomor_Surat,
                'Dest_user' => $dekan->Id_User,
                'Source_User' => $user->Id_User,
                'Is_Read' => false,
                'Data_Tambahan' => json_encode([
                    'id_surat' => $surat->id_no,
                    'nomor_surat' => $surat->Nomor_Surat,
                    'jenis_surat' => 'aktif',
                    'action_url' => route('dekan.persetujuan.aktif'),
                ]),
                'created_at' => now(),
            ]);
        }

        $surat->save();

        return redirect()->route('admin_fakultas.surat.detail', $surat->id_no)
            ->with('success', 'Nomor surat disimpan dan surat diteruskan ke Dekan.');
    }

    /**
     * Toggle status urgent surat.
     */
    public function toggleUrgent(Request $request, $id)
    {
        $surat = SuratKetAktif::findOrFail($id);

        $surat->is_urgent = !$surat->is_urgent;

        // Jika di-set urgent, ambil alasan dari request
        if ($surat->is_urgent) {
            $surat->urgent_reason = $request->input('urgent_reason', 'Ditandai urgent oleh Admin Fakultas');
        } else {
            $surat->urgent_reason = null;
        }

        $surat->save();

        return redirect()->back()->with('success', 'Status prioritas berhasil diperbarui.');
    }
}
