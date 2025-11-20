<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\Mahasiswa;
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
        $with = ['pemberiTugas.role', 'jenisSurat', 'penerimaTugas.role'];

        if (method_exists(TugasSurat::class, 'fileArsip')) {
            $with[] = 'fileArsip';
        }

        $tugasSurat = TugasSurat::with($with)->find($id);

        if (! $tugasSurat) {
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

        if (! $path) {
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

            // 3. Generate QR Code (jika library sudah terinstall)
            if (class_exists('\SimpleSoftwareIO\QrCode\Generator')) {
                // URL verifikasi public
                $verifyUrl = route('surat.verify', $token);
                
                // Generate QR Code image
                $qrGenerator = new \SimpleSoftwareIO\QrCode\Generator();
                $qrCode = $qrGenerator->format('png')
                    ->size(300)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($verifyUrl);
                
                // Simpan QR image ke storage
                $qrFilename = 'qr_codes/' . $token . '.png';
                \Storage::disk('public')->put($qrFilename, $qrCode);
                
                // Update path di database
                $verification->qr_path = $qrFilename;
                $verification->save();
                
                // Simpan info QR ke kolom signature_qr_data di Tugas_Surat (opsional)
                $tugasSurat->signature_qr_data = json_encode([
                    'signed_by' => $user->Name_User,
                    'signed_by_id' => $user->Id_User,
                    'signed_at' => Carbon::now()->toIso8601String(),
                    'qr_token' => $token,
                    'qr_image_path' => $qrFilename,
                    'verify_url' => $verifyUrl
                ]);
                
                $tugasSurat->qr_image_path = $qrFilename;
            } else {
                \Log::warning('QR Code library not installed. Install: composer require simplesoftwareio/simple-qrcode');
            }
            
            // 4. Update status surat - SET STATUS FINAL = 'Selesai'
            $tugasSurat->Status = 'Selesai';
            $tugasSurat->Tanggal_Diselesaikan = Carbon::now();
            $tugasSurat->save();

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
        $tugasSurat->Status = 'Ditolak';
        
        // Simpan komentar penolakan ke kolom data_spesifik
        if (!empty($validated['komentar'])) {
            $tugasSurat->data_spesifik = json_encode([
                'komentar_penolakan' => $validated['komentar'],
                'rejected_by' => $user->Name_User,
                'rejected_by_id' => $user->Id_User,
                'rejected_at' => Carbon::now()->toIso8601String(),
            ]);
        }

        $tugasSurat->save();

        return redirect()->route('dekan.persetujuan.index')
            ->with('success', 'Surat telah ditolak.');
    }
}
