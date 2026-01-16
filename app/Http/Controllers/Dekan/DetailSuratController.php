<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\FileArsip;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DetailSuratController extends Controller
{
    /**
     * Tampilkan detail surat untuk monitoring Dekan.
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $with = [
            'pemberiTugas.role',
            'jenisSurat',
            'penerimaTugas.role',
            'suratMagang',      // Tambahkan ini
            'suratKetAktif'     // Tambahkan ini
            // NOTE: suratKelakuanBaik dikelola oleh Wadek3
        ];

        if (method_exists(TugasSurat::class, 'fileArsip')) {
            $with[] = 'fileArsip';
        }

        $tugasSurat = TugasSurat::with($with)->find($id);

        if (!$tugasSurat) {
            abort(404, 'Surat tidak ditemukan');
        }

        // Ambil detail pengaju jika pemberiTugas adalah Mahasiswa
        $detailPengaju = null;
        $pemberi = $tugasSurat->pemberiTugas;
        $roleName = optional($pemberi->role)->Name_Role;

        if ($pemberi && is_string($roleName) && strtolower(trim($roleName)) === 'mahasiswa') {
            if (method_exists($pemberi, 'mahasiswa')) {
                $detailPengaju = $pemberi->mahasiswa;
            }
        }

        return view('dekan.detail_surat', [
            'surat' => $tugasSurat,
            'detailPengaju' => $detailPengaju,
        ]);
    }

    /**
     * Download dokumen pendukung yang diupload oleh pengaju.
     *
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPendukung($id)
    {
        $tugasSurat = TugasSurat::findOrFail($id);

        // Path disimpan di data_spesifik['dokumen_pendukung']
        $dataSpesifik = $tugasSurat->data_spesifik;
        $path = $dataSpesifik['dokumen_pendukung'] ?? null;

        if (!$path) {
            return response('Dokumen Pendukung tidak ditemukan.', 404);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        return response('Dokumen Pendukung tidak ditemukan.', 404);
    }

    /**
     * Approve (setujui) surat dan beri tanda tangan elektronik dengan QR Code.
     * 
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $tugasSurat = TugasSurat::findOrFail($id);

        // Validasi: hanya bisa approve jika status 'menunggu-ttd'
        if (strtolower(trim($tugasSurat->Status)) !== 'menunggu-ttd') {
            return redirect()->back()->with('error', 'Surat tidak dalam status menunggu tanda tangan.');
        }

        try {
            // 1. Generate token unik untuk verifikasi
            $token = \Str::random(64);

            // 2. Simpan data verifikasi ke database
            $verification = \App\Models\SuratVerification::create([
                'id_tugas_surat' => $tugasSurat->Id_Tugas_Surat,
                'token' => $token,
                'signed_by' => $user->Name_User,
                'signed_by_user_id' => $user->Id_User,
                'signed_at' => Carbon::now(),
            ]);

            // 3. Generate QR Code URL untuk verifikasi surat
            $verifyUrl = route('surat.verify', $token);

            // Generate QR Code dengan box_size 10 (ukuran optimal ~150x150px)
            $qrCodeUrl = \App\Helpers\QrCodeHelper::generate($verifyUrl, 10);

            // Simpan URL QR ke database
            $verification->qr_path = $qrCodeUrl;
            $verification->save();

            // 4. Update status surat - mapping ke enum valid di child
            // Child enum: 'Draft','Diajukan-ke-koordinator','Dikerjakan-admin','Diajukan-ke-dekan','Success','Ditolak'
            if ($tugasSurat->suratMagang) {
                try {
                    // Get data Dekan yang login
                    $dekan = Auth::user();

                    // Generate QR Code untuk Dekan dan simpan ke Qr_code_dekan
                    $qrContentDekan = url("/verify-surat-magang/{$tugasSurat->suratMagang->id_no}");
                    $qrFileNameDekan = 'qr_codes/dekan_' . $tugasSurat->suratMagang->id_no . '_' . time() . '.png';
                    $qrPathDekan = storage_path('app/public/' . $qrFileNameDekan);

                    // Pastikan direktori ada
                    if (!file_exists(dirname($qrPathDekan))) {
                        mkdir(dirname($qrPathDekan), 0777, true);
                    }

                    // Generate QR Code khusus untuk Dekan dengan Endroid v6.0 (readonly constructor)
                    $qrCodeDekan = new \Endroid\QrCode\QrCode(
                        data: $qrContentDekan,
                        size: 200,
                        margin: 10
                    );
                    $writerDekan = new \Endroid\QrCode\Writer\PngWriter();
                    $resultDekan = $writerDekan->write($qrCodeDekan);
                    $resultDekan->saveToFile($qrPathDekan);

                    // Update Surat Magang
                    $tugasSurat->suratMagang->Qr_code_dekan = $qrFileNameDekan;
                    $tugasSurat->suratMagang->Status = 'Success';
                    $tugasSurat->suratMagang->Acc_Dekan = 1;

                    // Simpan Nama dan NIP Dekan
                    if ($dekan->dosen) {
                        $tugasSurat->suratMagang->Nama_Dekan = $dekan->dosen->Nama_Dosen;
                        $tugasSurat->suratMagang->Nip_Dekan = $dekan->dosen->NIP;
                    } elseif ($dekan->pegawaiFakultas) {
                        $tugasSurat->suratMagang->Nama_Dekan = $dekan->pegawaiFakultas->Nama_Pegawai;
                        $tugasSurat->suratMagang->Nip_Dekan = $dekan->pegawaiFakultas->Nip_Pegawai;
                    } else {
                        $tugasSurat->suratMagang->Nama_Dekan = $dekan->Name_User;
                        $tugasSurat->suratMagang->Nip_Dekan = '-';
                    }

                    $tugasSurat->suratMagang->save();
                } catch (\Throwable $e) {
                    // Jika gagal (misal enum belum dimigrasi), log dan lanjutkan tanpa blokir
                    \Log::warning('Gagal update status child Surat_Magang ke Success: ' . $e->getMessage());
                }
            }

            // NOTE: Surat Kelakuan Baik dikelola oleh Wadek3 (Role 10), bukan Dekan
            // NOTE: Surat_Ket_Aktif tidak punya kolom Status, skip

            // PENTING: Update juga status di tabel parent
            $tugasSurat->Status = 'Selesai';
            $tugasSurat->Tanggal_Diselesaikan = Carbon::now();

            // Simpan info QR ke Tugas_Surat (hanya jika kolom tersedia agar tidak error sebelum migrate)
            if (\Schema::hasColumn('Tugas_Surat', 'qr_image_path')) {
                $tugasSurat->qr_image_path = $qrCodeUrl;
            }
            if (\Schema::hasColumn('Tugas_Surat', 'signature_qr_data')) {
                $tugasSurat->signature_qr_data = json_encode([
                    'signed_by' => $user->Name_User,
                    'signed_by_id' => $user->Id_User,
                    'signed_at' => Carbon::now()->toIso8601String(),
                    'qr_token' => $token,
                    'qr_image_url' => $qrCodeUrl,
                    'verify_url' => $verifyUrl
                ]);
            }

            $tugasSurat->save();

            // 6. Kirim notifikasi ke mahasiswa
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => '✅ Surat Anda telah disetujui dan ditandatangani oleh Dekan. Silakan download di halaman riwayat.',
                'Dest_user' => $tugasSurat->Id_Pemberi_Tugas_Surat,
                'Source_User' => $user->Id_User,
                'Is_Read' => false,
                'created_at' => now(),
            ]);

            // 7. Kirim notifikasi ke admin fakultas
            $adminFakultas = User::whereHas('role', function ($q) {
                $q->whereRaw("LOWER(TRIM(Name_Role)) = 'admin fakultas'");
            })->get();

            foreach ($adminFakultas as $admin) {
                Notifikasi::create([
                    'Tipe_Notifikasi' => 'Accepted',
                    'Pesan' => '📄 Surat ' . $tugasSurat->Nomor_Surat . ' telah disetujui dan masuk ke arsip.',
                    'Dest_user' => $admin->Id_User,
                    'Source_User' => $user->Id_User,
                    'Is_Read' => false,
                    'created_at' => now(),
                ]);
            }

            return redirect()->route('dekan.persetujuan.index')
                ->with('success', 'Surat berhasil ditandatangani dan siap didownload mahasiswa!');

        } catch (\Exception $e) {
            \Log::error('Error saat approve surat dengan QR: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses persetujuan: ' . $e->getMessage());
        }
    }

    /**
     * Reject (tolak) surat dengan komentar/alasan.
     *
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'komentar' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $tugasSurat = TugasSurat::findOrFail($id);

        // Validasi: hanya bisa reject jika status 'menunggu-ttd'
        if (strtolower(trim($tugasSurat->Status)) !== 'menunggu-ttd') {
            return redirect()->back()->with('error', 'Surat tidak dalam status menunggu tanda tangan.');
        }

        // Update status menjadi 'Ditolak'
        // Update child dengan enum valid ('Ditolak')
        if ($tugasSurat->suratMagang) {
            try {
                $tugasSurat->suratMagang->Status = 'Ditolak';
                $tugasSurat->suratMagang->save();
            } catch (\Throwable $e) {
                \Log::warning('Gagal update status child Surat_Magang ke Ditolak: ' . $e->getMessage());
            }
        }
        // NOTE: Surat_Ket_Aktif tidak punya kolom Status, skip

        // PENTING: Update juga status di tabel parent
        $tugasSurat->Status = 'ditolak';

        // Simpan komentar penolakan ke kolom data_spesifik
        $dataSpesifik = $tugasSurat->data_spesifik ?? [];
        $dataSpesifik['alasan_penolakan'] = $validated['komentar'] ?? 'Tidak memenuhi persyaratan';
        $dataSpesifik['ditolak_oleh'] = $user->Name_User;
        $dataSpesifik['tanggal_penolakan'] = Carbon::now()->format('d M Y H:i');

        $tugasSurat->data_spesifik = $dataSpesifik;
        $tugasSurat->save();

        // Kirim notifikasi ke mahasiswa
        Notifikasi::create([
            'Tipe_Notifikasi' => 'Rejected',
            'Pesan' => '❌ Surat Anda ditolak oleh Dekan. Alasan: ' . ($validated['komentar'] ?? 'Tidak memenuhi persyaratan'),
            'Dest_user' => $tugasSurat->Id_Pemberi_Tugas_Surat,
            'Source_User' => $user->Id_User,
            'Is_Read' => false,
            'created_at' => now(),
        ]);

        // Kirim notifikasi ke admin fakultas
        $adminFakultas = User::whereHas('role', function ($q) {
            $q->whereRaw("LOWER(TRIM(Name_Role)) = 'admin fakultas'");
        })->get();

        foreach ($adminFakultas as $admin) {
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Rejected',
                'Pesan' => '📋 Surat ' . $tugasSurat->Nomor_Surat . ' ditolak oleh Dekan.',
                'Dest_user' => $admin->Id_User,
                'Source_User' => $user->Id_User,
                'Is_Read' => false,
                'created_at' => now(),
            ]);
        }

        return redirect()->route('dekan.persetujuan.index')
            ->with('success', 'Surat telah ditolak dan notifikasi dikirim.');
    }

    /**
     * Preview draft surat di browser (generate on-the-fly atau dari arsip).
     *
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function previewDraft($id)
    {
        $tugasSurat = TugasSurat::with([
            'fileArsip',
            'jenisSurat',
            'pemberiTugas.mahasiswa.prodi.fakultas',
            'penerimaTugas',
            'suratMagang',
            'suratKetAktif',
            'suratTidakBeasiswa',
            // NOTE: suratKelakuanBaik dikelola oleh Wadek3
            'verification.penandatangan.pegawai',
            'verification.penandatangan.dosen'
        ])->findOrFail($id);

        // Jika sudah ada file arsip (surat sudah selesai), tampilkan dari arsip
        if ($tugasSurat->fileArsip && $tugasSurat->fileArsip->Path_File) {
            $path = $tugasSurat->fileArsip->Path_File;
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->response($path, 'draft_surat.pdf', ['Content-Disposition' => 'inline']);
            }
        }

        // Jika belum ada arsip, generate preview dari view
        // Ambil data mahasiswa dari pemberi tugas
        $mahasiswa = $tugasSurat->pemberiTugas->mahasiswa ?? null;

        // Render view preview sesuai jenis surat
        $jenisSurat = $tugasSurat->jenisSurat;

        // Untuk surat aktif
        if ($tugasSurat->suratKetAktif) {
            return view('dekan.preview.surat_aktif', [
                'surat' => $tugasSurat,
                'mahasiswa' => $mahasiswa,
                'jenisSurat' => $jenisSurat,
                'verification' => $tugasSurat->verification,
                'mode' => 'preview'
            ]);
        }

        // Untuk surat magang
        if ($tugasSurat->suratMagang) {
            return view('dekan.preview.surat_magang', [
                'surat' => $tugasSurat,
                'mahasiswa' => $mahasiswa,
                'jenisSurat' => $jenisSurat,
                'verification' => $tugasSurat->verification,
                'mode' => 'preview'
            ]);
        }

        // Untuk surat tidak beasiswa
        if ($tugasSurat->suratTidakBeasiswa) {
            return view('dekan.preview.surat_tidak_beasiswa', [
                'surat' => $tugasSurat,
                'mahasiswa' => $mahasiswa,
                'jenisSurat' => $jenisSurat,
                'verification' => $tugasSurat->verification,
                'suratTidakBeasiswa' => $tugasSurat->suratTidakBeasiswa,
                'mode' => 'preview'
            ]);
        }

        // NOTE: Surat berkelakuan baik dikelola oleh Wadek3 (Role 10)

        return response('Preview tidak tersedia untuk jenis surat ini.', 404);
    }
}
