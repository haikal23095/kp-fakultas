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

        $path = $tugasSurat->dokumen_pendukung;

        if (! $path) {
            return response('Dokumen Pendukung tidak ditemukan.', 404);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        return response('Dokumen Pendukung tidak ditemukan.', 404);
    }

    /**
     * Approve (setujui) surat dan beri tanda tangan elektronik.
     * 
     * TODO: Integrasi TTE dengan QR Code
     * - Generate QR Code yang berisi: ID Surat, Nama Penandatangan, Timestamp, Hash
     * - Simpan QR signature data ke kolom 'signature_qr_data' (JSON: {qr_image, qr_url, signed_by, signed_at})
     * - API endpoint untuk verifikasi QR: GET /verify-signature/{qr_token}
     * - Halaman publik untuk scan QR menampilkan identitas penandatangan
     * 
     * Library yang bisa dipakai nanti:
     * - SimpleSoftwareIO/simple-qrcode untuk generate QR
     * - Atau API external seperti QRServer, GoQR
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

        // Update status menjadi 'Telah Ditandatangani Dekan'
        $tugasSurat->Status = 'Telah Ditandatangani Dekan';
        
        // TODO: Simpan data TTE dummy (nanti diganti dengan QR signature)
        // $tugasSurat->signature_qr_data = json_encode([
        //     'signed_by' => $user->Name_User,
        //     'signed_by_id' => $user->Id_User,
        //     'signed_at' => Carbon::now()->toIso8601String(),
        //     'qr_token' => Str::random(32), // token unik untuk verifikasi
        //     'qr_image_url' => null, // nanti diisi path QR image
        // ]);

        $tugasSurat->save();

        return redirect()->route('dekan.persetujuan.index')
            ->with('success', 'Surat berhasil ditandatangani. (TTE QR akan diintegrasikan)');
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
