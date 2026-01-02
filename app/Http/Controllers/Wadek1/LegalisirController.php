<?php

namespace App\Http\Controllers\Wadek1;

use App\Http\Controllers\Controller;
use App\Models\SuratLegalisir;
use App\Models\Notifikasi;
use App\Helpers\QrCodeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class LegalisirController extends Controller
{
    /**
     * Approve dan tandatangani dokumen legalisir dengan QR code
     */
    public function approve($id)
    {
        $user = Auth::user();
        
        // Pastikan user adalah Wadek1 (Role 8)
        if ($user->Id_Role != 8) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menandatangani dokumen ini.');
        }

        $legalisir = SuratLegalisir::with(['user.mahasiswa', 'tugasSurat'])->findOrFail($id);

        // Cek apakah sudah ditandatangani
        if ($legalisir->isSigned()) {
            return redirect()->back()->with('info', 'Dokumen ini sudah ditandatangani oleh ' . $legalisir->TTD_Oleh . '.');
        }

        // Cek status
        if ($legalisir->Status != 'menunggu_ttd_pimpinan') {
            return redirect()->back()->with('error', 'Status dokumen tidak valid untuk ditandatangani.');
        }

        // Cek apakah file scan ada
        if (!$legalisir->File_Scan_Path || !Storage::disk('public')->exists($legalisir->File_Scan_Path)) {
            return redirect()->back()->with('error', 'File scan tidak ditemukan.');
        }

        try {
            // Generate QR Code dengan informasi penandatangan
            $qrData = [
                'jenis' => 'Legalisir ' . $legalisir->Jenis_Dokumen,
                'mahasiswa' => $legalisir->user->Name_User ?? 'N/A',
                'nim' => $legalisir->user->mahasiswa->NIM ?? 'N/A',
                'ditandatangani_oleh' => $user->Name_User,
                'jabatan' => 'Wakil Dekan I',
                'nip' => $user->pegawai->NIP ?? $user->dosen->NIP ?? 'N/A',
                'tanggal' => now()->format('d-m-Y H:i:s'),
                'id_legalisir' => $legalisir->id_no,
            ];

            $qrCodePath = QrCodeHelper::generateQrCode(json_encode($qrData), 'legalisir_ttd_' . $legalisir->id_no);

            // Overlay QR pada PDF
            $originalPdfPath = storage_path('app/public/' . $legalisir->File_Scan_Path);
            $signedPdfPath = 'legalisir/signed/legalisir_signed_' . $legalisir->id_no . '_' . time() . '.pdf';
            $signedPdfFullPath = storage_path('app/public/' . $signedPdfPath);

            // Buat folder jika belum ada
            $signedFolder = dirname($signedPdfFullPath);
            if (!file_exists($signedFolder)) {
                mkdir($signedFolder, 0755, true);
            }

            // Gunakan FPDI untuk overlay QR pada PDF
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($originalPdfPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Hanya overlay QR di halaman pertama
                if ($pageNo == 1) {
                    // Posisi QR di kanan bawah (koordinat dari kiri-bawah)
                    $qrSize = 40; // mm
                    $marginRight = 15;
                    $marginBottom = 15;
                    
                    $x = $size['width'] - $qrSize - $marginRight;
                    $y = $size['height'] - $qrSize - $marginBottom;

                    $pdf->Image($qrCodePath, $x, $y, $qrSize, $qrSize, 'PNG');

                    // Tambahkan teks "Ditandatangani Digital"
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->SetXY($x - 5, $y + $qrSize + 2);
                    $pdf->Cell($qrSize + 10, 5, 'Ditandatangani Digital', 0, 0, 'C');
                    
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->SetXY($x - 5, $y + $qrSize + 7);
                    $pdf->Cell($qrSize + 10, 4, $user->Name_User, 0, 0, 'C');
                    
                    $pdf->SetXY($x - 5, $y + $qrSize + 11);
                    $pdf->Cell($qrSize + 10, 4, 'Wakil Dekan I', 0, 0, 'C');
                }
            }

            $pdf->Output('F', $signedPdfFullPath);

            // Update record
            $legalisir->update([
                'File_Signed_Path' => $signedPdfPath,
                'TTD_Oleh' => $user->Name_User . ' (Wadek I)',
                'TTD_At' => now(),
                'Status' => 'siap_diambil',
            ]);

            // Notifikasi ke Admin Fakultas (Role 7 = Pegawai_Fakultas)
            $adminFakultas = \App\Models\User::where('Id_Role', 7)->first();
            if ($adminFakultas) {
                Notifikasi::create([
                    'Tipe_Notifikasi' => 'Accepted',
                    'Pesan' => 'Legalisir ' . $legalisir->Jenis_Dokumen . ' untuk ' . ($legalisir->user->Name_User ?? 'mahasiswa') . ' telah ditandatangani oleh Wadek I. Status: Siap Diambil.',
                    'Dest_user' => $adminFakultas->Id_User,
                    'Source_User' => $user->Id_User,
                    'Is_Read' => false,
                    'Data_Tambahan' => json_encode(['entity' => 'legalisir', 'id' => $legalisir->id_no]),
                ]);
            }

            // Notifikasi ke Mahasiswa
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => 'Dokumen legalisir ' . $legalisir->Jenis_Dokumen . ' Anda telah ditandatangani dan siap diambil di administrasi fakultas.',
                'Dest_user' => $legalisir->Id_User,
                'Source_User' => $user->Id_User,
                'Is_Read' => false,
                'Data_Tambahan' => json_encode(['entity' => 'legalisir', 'id_tugas' => $legalisir->Id_Tugas_Surat]),
            ]);

            // Hapus QR code temporary
            if (file_exists($qrCodePath)) {
                unlink($qrCodePath);
            }

            return redirect()->route('wadek1.persetujuan.legalisir')->with('success', 'Dokumen berhasil ditandatangani dengan QR digital. Status: Siap Diambil.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menandatangani dokumen: ' . $e->getMessage());
        }
    }
}
