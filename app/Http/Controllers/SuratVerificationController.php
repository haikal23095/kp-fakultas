<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratVerification;
use App\Models\TugasSurat;

class SuratVerificationController extends Controller
{
    /**
     * Halaman public untuk verifikasi dokumen via QR Code
     * Route: GET /verify-surat/{token}
     * 
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function verify($token)
    {
        // Cari verifikasi berdasarkan token
        $verification = SuratVerification::with([
            'tugasSurat.jenisSurat',
            'tugasSurat.pemberiTugas.mahasiswa',
            'tugasSurat.pemberiTugas.dosen',
            'penandatangan.role'
        ])->where('token', $token)->first();

        if (!$verification) {
            return view('public.surat-verification', [
                'status' => 'invalid',
                'message' => 'Dokumen tidak dikenali atau token tidak valid.',
                'verification' => null
            ]);
        }

        // Dokumen valid
        return view('public.surat-verification', [
            'status' => 'valid',
            'message' => 'Dokumen Valid dan Terverifikasi',
            'verification' => $verification,
            'surat' => $verification->tugasSurat
        ]);
    }
    
    /**
     * API endpoint untuk validasi QR (opsional, untuk mobile app dll)
     * Route: GET /api/verify-surat/{token}
     * 
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyApi($token)
    {
        $verification = SuratVerification::with([
            'tugasSurat.jenisSurat',
            'tugasSurat.pemberiTugas',
            'penandatangan'
        ])->where('token', $token)->first();

        if (!$verification) {
            return response()->json([
                'valid' => false,
                'message' => 'Token tidak valid atau dokumen tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Dokumen terverifikasi',
            'data' => [
                'jenis_surat' => $verification->tugasSurat->jenisSurat->Nama_Surat ?? 'N/A',
                'judul' => $verification->tugasSurat->Judul_Tugas_Surat,
                'pengaju' => $verification->tugasSurat->pemberiTugas->Name_User ?? 'N/A',
                'ditandatangani_oleh' => $verification->signed_by,
                'tanggal_ttd' => $verification->signed_at->format('d M Y H:i'),
                'status' => $verification->tugasSurat->Status
            ]
        ], 200);
    }
}
