<?php

namespace App\Http\Controllers;

use App\Models\SuratMagang;
use Illuminate\Http\Request;

class QrCodeVerificationController extends Controller
{
    /**
     * Halaman verifikasi surat berdasarkan QR Code
     */
    public function verify($id)
    {
        $surat = SuratMagang::with(['koordinator', 'tugasSurat.pemberiTugas.mahasiswa'])
            ->where('id_no', $id)
            ->first();

        if (!$surat) {
            abort(404, 'Surat tidak ditemukan');
        }

        if (!$surat->Acc_Koordinator) {
            return view('public.qr_verification', [
                'status' => 'pending',
                'message' => 'Surat ini belum disetujui oleh Koordinator.'
            ]);
        }

        // Decode data mahasiswa dan dosen
        $dataMahasiswa = is_array($surat->Data_Mahasiswa)
            ? $surat->Data_Mahasiswa
            : json_decode($surat->Data_Mahasiswa, true);

        $dataDosenPembimbing = is_array($surat->Data_Dosen_pembiming)
            ? $surat->Data_Dosen_pembiming
            : json_decode($surat->Data_Dosen_pembiming, true);

        return view('public.qr_verification', [
            'status' => 'approved',
            'surat' => $surat,
            'dataMahasiswa' => $dataMahasiswa,
            'dataDosenPembimbing' => $dataDosenPembimbing,
            'koordinatorName' => $surat->koordinator->Nama_Dosen ?? 'N/A',
            'koordinatorNIP' => $surat->koordinator->NIP ?? 'N/A'
        ]);
    }
}
