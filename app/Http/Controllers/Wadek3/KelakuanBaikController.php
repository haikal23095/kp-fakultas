<?php

namespace App\Http\Controllers\Wadek3;

use App\Http\Controllers\Controller;
use App\Models\SuratKelakuanBaik;
use App\Models\TugasSurat;
use App\Models\SuratVerification;
use App\Models\Notifikasi;
use App\Models\User;
use App\Helpers\QrCodeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KelakuanBaikController extends Controller
{
    /**
     * Menampilkan daftar surat berkelakuan baik yang menunggu TTD Wadek3
     */
    public function index()
    {
        // Ambil TugasSurat yang memiliki relasi suratKelakuanBaik
        // Status: sudah dibayar dan menunggu TTD
        $daftarSurat = TugasSurat::whereHas('suratKelakuanBaik')
            ->with([
                'suratKelakuanBaik',
                'pemberiTugas.mahasiswa.prodi',
                'jenisSurat'
            ])
            ->whereIn('Status', ['menunggu-ttd', 'Menunggu-ttd'])
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('wadek3.persetujuan_kelakuan_baik', compact('daftarSurat'));
    }

    /**
     * Approve dan tandatangani surat berkelakuan baik dengan QR code
     */
    public function approve($id)
    {
        $user = Auth::user();

        // Pastikan user adalah Wadek3 (Role 10)
        if ($user->Id_Role != 10) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menandatangani surat ini.');
        }

        DB::beginTransaction();
        try {
            $tugasSurat = TugasSurat::with(['suratKelakuanBaik.user.mahasiswa.prodi', 'jenisSurat'])
                ->findOrFail($id);

            $suratKelakuanBaik = $tugasSurat->suratKelakuanBaik;

            if (!$suratKelakuanBaik) {
                throw new \Exception('Data surat berkelakuan baik tidak ditemukan.');
            }

            // Cek apakah sudah ada verifikasi (sudah ditandatangani)
            if (SuratVerification::where('Id_Tugas_Surat', $id)->exists()) {
                return redirect()->back()->with('info', 'Surat ini sudah ditandatangani sebelumnya.');
            }

            // Cek status
            $statusLower = strtolower(trim($tugasSurat->Status));
            if (!in_array($statusLower, ['menunggu_ttd_pimpinan', 'dikirim_ke_wadek3', 'menunggu_ttd_wadek3'])) {
                throw new \Exception('Status surat tidak valid untuk ditandatangani. Status: ' . $tugasSurat->Status);
            }

            // Generate QR Code dengan informasi penandatangan
            $qrData = [
                'jenis_surat' => 'Surat Keterangan Berkelakuan Baik',
                'nomor_surat' => $suratKelakuanBaik->Nomor_Surat ?? 'Belum ada nomor',
                'mahasiswa' => $suratKelakuanBaik->user->Name_User ?? 'N/A',
                'nim' => $suratKelakuanBaik->user->mahasiswa->NIM ?? 'N/A',
                'ditandatangani_oleh' => $user->Name_User,
                'jabatan' => 'Wakil Dekan III',
                'nip' => $user->dosen->NIP ?? $user->pegawaiFakultas->NIP ?? 'N/A',
                'tanggal' => now()->format('d-m-Y H:i:s'),
                'id_tugas_surat' => $tugasSurat->Id_Tugas_Surat,
            ];

            // Generate QR Code dan simpan path-nya
            $qrPath = QrCodeHelper::generateAndGetPath(json_encode($qrData), 'kelakuan_baik_' . $tugasSurat->Id_Tugas_Surat);

            // Buat record verifikasi
            $verification = SuratVerification::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Penandatangan' => $user->Id_User,
                'Tanggal_Tanda_Tangan' => now(),
                'QrCode_Path' => $qrPath,
                'Verification_Token' => \Illuminate\Support\Str::random(32),
            ]);

            // Update status tugas surat
            $tugasSurat->update([
                'Status' => 'Selesai',
                'Id_Penerima_Tugas_Surat' => $user->Id_User,
            ]);

            // Notifikasi ke Admin Fakultas (Role 7)
            $adminFakultas = User::where('Id_Role', 7)->first();
            if ($adminFakultas) {
                Notifikasi::create([
                    'Tipe_Notifikasi' => 'Accepted',
                    'Pesan' => 'Surat Berkelakuan Baik untuk ' . ($suratKelakuanBaik->user->Name_User ?? 'mahasiswa') . ' telah ditandatangani oleh Wakil Dekan III. Status: Selesai.',
                    'Dest_user' => $adminFakultas->Id_User,
                    'Source_User' => $user->Id_User,
                    'Is_Read' => false,
                    'Data_Tambahan' => json_encode(['entity' => 'kelakuan_baik', 'id' => $tugasSurat->Id_Tugas_Surat]),
                ]);
            }

            // Notifikasi ke Mahasiswa
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => 'Surat Keterangan Berkelakuan Baik Anda telah ditandatangani oleh Wakil Dekan III dan siap diambil. Silakan download di menu Riwayat.',
                'Dest_user' => $suratKelakuanBaik->Id_User,
                'Source_User' => $user->Id_User,
                'Is_Read' => false,
                'Data_Tambahan' => json_encode(['entity' => 'kelakuan_baik', 'id' => $tugasSurat->Id_Tugas_Surat]),
            ]);

            DB::commit();

            return redirect()->route('wadek3.persetujuan.kelakuan_baik')->with('success', 'Surat berhasil ditandatangani dengan QR digital. Status: Selesai.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approve Wadek3 Kelakuan Baik: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menandatangani surat: ' . $e->getMessage());
        }
    }

    /**
     * Preview surat berkelakuan baik untuk Wadek3
     */
    public function preview($id)
    {
        $tugasSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.mahasiswa.prodi.fakultas',
            'suratKelakuanBaik.user.mahasiswa',
            'verification.penandatangan.pegawai',
            'verification.penandatangan.dosen'
        ])->findOrFail($id);

        $mahasiswa = $tugasSurat->suratKelakuanBaik->user->mahasiswa ?? $tugasSurat->pemberiTugas->mahasiswa;
        $suratKelakuanBaik = $tugasSurat->suratKelakuanBaik;

        return view('wadek3.preview.surat_kelakuan_baik', [
            'surat' => $tugasSurat,
            'mahasiswa' => $mahasiswa,
            'jenisSurat' => $tugasSurat->jenisSurat,
            'suratKelakuanBaik' => $suratKelakuanBaik,
            'verification' => $tugasSurat->verification,
        ]);
    }
}
