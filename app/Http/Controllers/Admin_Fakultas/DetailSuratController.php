<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\Mahasiswa;
use App\Models\FileArsip;
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
        $tugasSurat = TugasSurat::with([
            'pemberiTugas',
            'penerimaTugas',
            'jenisSurat',
            'suratMagang',
            'suratKetAktif',
            'suratTidakBeasiswa',
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
        $filePath = null;

        if ($tugasSurat->suratMagang) {
            switch ($fileType) {
                case 'proposal':
                    $filePath = $tugasSurat->suratMagang->Dokumen_Proposal;
                    break;
                case 'surat_pengantar':
                    $filePath = $tugasSurat->suratMagang->Surat_Pengantar_Magang;
                    break;
            }
        } elseif ($tugasSurat->suratKetAktif) {
            // Untuk Surat Keterangan Aktif, file pendukung adalah KRS
            $filePath = $tugasSurat->suratKetAktif->KRS;
        } elseif ($tugasSurat->suratTidakBeasiswa) {
            // Untuk Surat Tidak Beasiswa, file pendukung adalah surat pernyataan
            $filePath = $tugasSurat->suratTidakBeasiswa->File_Pernyataan;
        }

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($filePath);
    }

    /**
     * Preview file pendukung surat di browser.
     */
    public function previewPendukung($id, Request $request)
    {
        $tugasSurat = TugasSurat::findOrFail($id);
        $fileType = $request->query('type', 'proposal');
        $filePath = null;

        if ($tugasSurat->suratMagang) {
            switch ($fileType) {
                case 'proposal':
                    $filePath = $tugasSurat->suratMagang->Dokumen_Proposal;
                    break;
                case 'surat_pengantar':
                    $filePath = $tugasSurat->suratMagang->Surat_Pengantar_Magang;
                    break;
            }
        } elseif ($tugasSurat->suratKetAktif) {
            // Untuk Surat Keterangan Aktif, file pendukung adalah KRS
            $filePath = $tugasSurat->suratKetAktif->KRS;
        } elseif ($tugasSurat->suratTidakBeasiswa) {
            // Untuk Surat Tidak Beasiswa, file pendukung adalah surat pernyataan
            $filePath = $tugasSurat->suratTidakBeasiswa->File_Pernyataan;
        }

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
        $tugas = TugasSurat::findOrFail($id);

        // UPDATE: Status ada di tabel parent (Tugas_Surat) dan child table yang punya kolom Status
        $tugas->Status = 'ditolak';

        // Update status di child table jika ada dan punya kolom Status
        if ($tugas->suratMagang) {
            $tugas->suratMagang->Status = 'ditolak';
            $tugas->suratMagang->save();
        }
        // NOTE: Surat_Ket_Aktif tidak punya kolom Status, skip

        // Update data_spesifik with rejection reason
        $dataSpesifik = $tugas->data_spesifik ?? [];
        $dataSpesifik['alasan_penolakan'] = $request->input('alasan_penolakan');
        $dataSpesifik['tanggal_penolakan'] = now()->toDateTimeString();
        $dataSpesifik['ditolak_oleh'] = $user->Name_User;

        $tugas->data_spesifik = $dataSpesifik;
        $tugas->save();

        // Kirim notifikasi ke mahasiswa
        Notifikasi::create([
            'Tipe_Notifikasi' => 'Rejected',
            'Pesan' => '❌ Pengajuan surat Anda ditolak oleh Admin Fakultas. Alasan: ' . $request->input('alasan_penolakan'),
            'Dest_user' => $tugas->Id_Pemberi_Tugas_Surat,
            'Source_User' => $user->Id_User,
            'Is_Read' => false,
            'Data_Tambahan' => json_encode([
                'id_tugas_surat' => $tugas->Id_Tugas_Surat,
                'jenis_surat' => $this->getJenisSuratSlug($tugas),
                'action_url' => $this->getActionUrlForMahasiswa($tugas),
            ]),
            'created_at' => now(),
        ]);

        return redirect()->route('admin_fakultas.surat.detail', $tugas->Id_Tugas_Surat)
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
        $tugas = TugasSurat::findOrFail($id);
        $tugas->Nomor_Surat = $request->input('nomor_surat');

        // UPDATE: Status ada di tabel parent (Tugas_Surat) dan child table yang punya kolom Status
        $tugas->Status = 'menunggu-ttd';

        // Update status di child table jika ada dan punya kolom Status
        if ($tugas->suratMagang) {
            $tugas->suratMagang->Status = 'menunggu-ttd';
            $tugas->suratMagang->save();
        }
        // NOTE: Surat_Ket_Aktif tidak punya kolom Status, skip

        // Cari user Dekan
        $dekan = User::whereHas('role', function ($q) {
            $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
        })->first();

        if ($dekan) {
            $tugas->Id_Penerima_Tugas_Surat = $dekan->Id_User;

            // Kirim notifikasi ke Dekan
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Invitation',
                'Pesan' => '📝 Surat baru menunggu persetujuan dan tanda tangan Anda. Nomor: ' . $tugas->Nomor_Surat,
                'Dest_user' => $dekan->Id_User,
                'Source_User' => $user->Id_User,
                'Is_Read' => false,
                'Data_Tambahan' => json_encode([
                    'id_tugas_surat' => $tugas->Id_Tugas_Surat,
                    'nomor_surat' => $tugas->Nomor_Surat,
                    'jenis_surat' => $this->getJenisSuratSlug($tugas),
                    'action_url' => route('dekan.surat.pending'),
                ]),
                'created_at' => now(),
            ]);
        }

        $tugas->save();

        return redirect()->route('admin_fakultas.surat.detail', $tugas->Id_Tugas_Surat)
            ->with('success', 'Nomor surat disimpan dan surat diteruskan ke Dekan.');
    }

    /**
     * Helper: Get jenis surat slug from TugasSurat
     */
    private function getJenisSuratSlug($tugasSurat)
    {
        if ($tugasSurat->suratKetAktif) return 'aktif';
        if ($tugasSurat->suratMagang) return 'magang';
        if ($tugasSurat->suratLegalisir) return 'legalisir';
        if ($tugasSurat->suratTidakBeasiswa) return 'tidak_beasiswa';
        if ($tugasSurat->suratDispensasi) return 'dispensasi';
        if ($tugasSurat->suratKelakuanBaik) return 'berkelakuan_baik';
        
        // Fallback berdasarkan Id_Jenis_Surat
        if ($tugasSurat->Id_Jenis_Surat == 1) return 'aktif';
        if ($tugasSurat->Id_Jenis_Surat == 2) return 'magang';
        if ($tugasSurat->Id_Jenis_Surat == 4) return 'mobil_dinas';
        
        return 'general';
    }

    /**
     * Helper: Get action URL for mahasiswa based on jenis surat
     */
    private function getActionUrlForMahasiswa($tugasSurat)
    {
        $jenisSurat = $this->getJenisSuratSlug($tugasSurat);
        
        return match($jenisSurat) {
            'aktif' => route('mahasiswa.riwayat.aktif'),
            'magang' => route('mahasiswa.riwayat.magang'),
            'legalisir' => route('mahasiswa.riwayat.legalisir'),
            'mobil_dinas' => route('mahasiswa.riwayat.mobil_dinas'),
            'tidak_beasiswa' => route('mahasiswa.riwayat.tidak_beasiswa'),
            'dispensasi' => route('mahasiswa.riwayat.dispensasi'),
            'berkelakuan_baik' => route('mahasiswa.riwayat.berkelakuan_baik'),
            default => route('mahasiswa.riwayat'),
        };
    }

    /**
     * Toggle status urgent surat.
     */
    public function toggleUrgent(Request $request, $id)
    {
        $tugasSurat = TugasSurat::with('suratKetAktif')->findOrFail($id);

        if ($tugasSurat->suratKetAktif) {
            $tugasSurat->suratKetAktif->is_urgent = !$tugasSurat->suratKetAktif->is_urgent;

            // Jika di-set urgent, bisa tambahkan alasan default atau ambil dari request
            if ($tugasSurat->suratKetAktif->is_urgent) {
                $tugasSurat->suratKetAktif->urgent_reason = $request->input('urgent_reason', 'Ditandai urgent oleh Admin Fakultas');
            } else {
                $tugasSurat->suratKetAktif->urgent_reason = null;
            }

            $tugasSurat->suratKetAktif->save();

            return redirect()->back()->with('success', 'Status prioritas berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Jenis surat ini tidak mendukung fitur prioritas.');
    }
}
