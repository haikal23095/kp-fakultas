<?php

namespace App\Http\Controllers\Wadek3;

use App\Http\Controllers\Controller;
use App\Models\SuratDispensasi;
use App\Models\TugasSurat;
use App\Models\SuratVerification;
use App\Models\Notifikasi;
use App\Models\User;
use App\Helpers\QrCodeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KemahasiswaanController extends Controller
{
    /**
     * Menampilkan daftar dispensasi yang menunggu persetujuan Wadek3
     */
    public function validasiDispensasi()
    {
        // Debug: Tampilkan semua data dispensasi dulu
        $allDispensasi = TugasSurat::whereHas('suratDispensasi')
            ->with([
                'suratDispensasi',
                'pemberiTugas.mahasiswa.prodi',
                'jenisSurat'
            ])
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        // Filter data yang sudah dapat nomor surat (sudah diverifikasi admin) tapi belum ACC wadek3
        $daftarSurat = $allDispensasi->filter(function ($tugas) {
            $surat = $tugas->suratDispensasi;
            // Cek: sudah ada nomor surat DAN belum di-ACC oleh Wadek3
            return $surat && $surat->nomor_surat && !$surat->acc_wadek3_by;
        });

        return view('wadek3.kemahasiswaan.validasi-dispensasi', compact('daftarSurat'));
    }

    /**
     * Detail surat dispensasi untuk preview sebelum ACC
     */
    public function detailDispensasi($id)
    {
        $tugasSurat = TugasSurat::with([
            'suratDispensasi.user.mahasiswa.prodi',
            'suratDispensasi.verifikasiAdmin',
            'jenisSurat'
        ])->findOrFail($id);

        $surat = $tugasSurat->suratDispensasi;

        if (!$surat) {
            abort(404, 'Data dispensasi tidak ditemukan.');
        }

        return view('wadek3.kemahasiswaan.detail-dispensasi', compact('tugasSurat', 'surat'));
    }

    /**
     * Approve dispensasi: Generate QR Code + PDF
     */
    public function approveDispensasi($id)
    {
        $user = Auth::user();

        // Pastikan user adalah Wadek3 (Role 10)
        if ($user->Id_Role != 10) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menyetujui surat ini.');
        }

        DB::beginTransaction();
        try {
            $tugasSurat = TugasSurat::with(['suratDispensasi.user.mahasiswa.prodi', 'jenisSurat'])
                ->findOrFail($id);

            $surat = $tugasSurat->suratDispensasi;

            if (!$surat) {
                throw new \Exception('Data dispensasi tidak ditemukan.');
            }

            // Cek apakah sudah disetujui
            if ($surat->acc_wadek3_by) {
                return redirect()->back()->with('info', 'Surat ini sudah disetujui sebelumnya.');
            }

            // Cek apakah sudah ada nomor surat (sudah diverifikasi admin)
            if (!$surat->nomor_surat) {
                throw new \Exception('Surat belum memiliki nomor. Harap hubungi Admin untuk memberikan nomor surat terlebih dahulu.');
            }

            // Generate QR Code dengan informasi penandatangan (ikuti pola Dekan)
            $qrData = [
                'jenis_surat' => 'Surat Dispensasi Kegiatan',
                'nomor_surat' => $surat->nomor_surat,
                'mahasiswa' => $surat->user->Name_User ?? 'N/A',
                'nim' => $surat->user->mahasiswa->NIM ?? 'N/A',
                'kegiatan' => $surat->nama_kegiatan,
                'ditandatangani_oleh' => $user->Name_User,
                'jabatan' => 'Wakil Dekan III',
                'nip' => $user->dosen->NIP ?? $user->pegawaiFakultas->NIP ?? 'N/A',
                'tanggal' => now()->format('d-m-Y H:i:s'),
                'id_tugas_surat' => $tugasSurat->Id_Tugas_Surat,
            ];

            \Log::info('Wadek3 generating QR Code for Dispensasi', ['data' => $qrData]);

            // Generate QR Code - returns URL like: http://domain/storage/qr-codes/qr_xxx.png
            $qrUrl = QrCodeHelper::generate(json_encode($qrData), 200);

            if (!$qrUrl) {
                \Log::error('QR Code generation failed - empty URL returned');
                throw new \Exception('Gagal generate QR Code.');
            }

            // Extract relative path from URL: qr-codes/qr_xxx.png
            // Parse URL dan ambil path setelah /storage/
            $qrPath = str_replace('/storage/', '', parse_url($qrUrl, PHP_URL_PATH));

            \Log::info('QR Code generated successfully', [
                'url' => $qrUrl,
                'extracted_path' => $qrPath
            ]);

            // Buat record verifikasi
            $verification = SuratVerification::create([
                'id_tugas_surat' => $tugasSurat->Id_Tugas_Surat,
                'signed_by_user_id' => $user->Id_User,
                'signed_by' => $user->Name_User,
                'signed_at' => now(),
                'qr_path' => $qrPath,
                'token' => \Illuminate\Support\Str::random(32),
            ]);

            \Log::info('Verification record created', ['verification_id' => $verification->id]);

            // Generate PDF dengan QR code
            $pdfFileName = $this->generatePDFDispensasi($surat, $surat->user->mahasiswa, $qrPath, $user);

            \Log::info('PDF generated', ['pdf_path' => $pdfFileName]);

            // Update Surat Dispensasi
            $surat->acc_wadek3_by = $user->Id_User;
            $surat->acc_wadek3_at = Carbon::now()->toDateString();
            $surat->file_surat_selesai = $pdfFileName;
            $surat->save();

            // Update status Tugas Surat
            $tugasSurat->update([
                'Status' => 'Selesai',
                'Tanggal_Diselesaikan' => Carbon::now(),
                'Id_Penerima_Tugas_Surat' => $user->Id_User,
            ]);

            // Notifikasi ke Admin Fakultas (Role 7)
            $adminFakultas = User::where('Id_Role', 7)->first();
            if ($adminFakultas) {
                Notifikasi::create([
                    'Tipe_Notifikasi' => 'Accepted',
                    'Pesan' => 'Surat Dispensasi untuk ' . ($surat->user->Name_User ?? 'mahasiswa') . ' telah disetujui oleh Wakil Dekan III.',
                    'Dest_user' => $adminFakultas->Id_User,
                    'Source_User' => $user->Id_User,
                    'Is_Read' => false,
                    'Data_Tambahan' => json_encode(['entity' => 'dispensasi', 'id' => $tugasSurat->Id_Tugas_Surat]),
                ]);
            }

            // Notifikasi ke Mahasiswa
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => 'Surat Dispensasi Anda untuk kegiatan "' . $surat->nama_kegiatan . '" telah disetujui oleh Wakil Dekan III. Silakan download di menu Riwayat.',
                'Dest_user' => $surat->Id_User,
                'Source_User' => $user->Id_User,
                'Is_Read' => false,
                'Data_Tambahan' => json_encode(['entity' => 'dispensasi', 'id' => $tugasSurat->Id_Tugas_Surat]),
            ]);

            DB::commit();

            \Log::info('Dispensasi approval completed successfully', ['id' => $id]);

            return redirect()->route('wadek3.kemahasiswaan.validasi-dispensasi')
                ->with('success', 'Surat Dispensasi berhasil disetujui! QR Code dan PDF telah digenerate.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving dispensasi', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Gagal menyetujui surat: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate PDF untuk dispensasi yang sudah di-ACC (untuk fix file missing)
     */
    public function regeneratePdfDispensasi($id)
    {
        DB::beginTransaction();
        try {
            $tugasSurat = TugasSurat::with(['suratDispensasi.user.mahasiswa.prodi', 'verification'])
                ->findOrFail($id);

            $surat = $tugasSurat->suratDispensasi;

            if (!$surat) {
                throw new \Exception('Data dispensasi tidak ditemukan.');
            }

            // Cek apakah sudah pernah di-ACC
            if (!$surat->acc_wadek3_by) {
                throw new \Exception('Surat belum di-ACC. Tidak bisa regenerate PDF.');
            }

            // Cek apakah ada verification record (QR code)
            $verification = $tugasSurat->verification;
            if (!$verification || !$verification->qr_path) {
                throw new \Exception('QR Code tidak ditemukan. Harap ACC ulang surat ini.');
            }

            // Get penandatangan dari verification record
            $penandatangan = User::find($verification->signed_by_user_id);
            if (!$penandatangan) {
                throw new \Exception('Data penandatangan tidak ditemukan.');
            }

            \Log::info('Regenerating PDF for dispensasi', [
                'id' => $id,
                'qr_path' => $verification->qr_path
            ]);

            // Generate PDF dengan QR code yang sudah ada
            $pdfFileName = $this->generatePDFDispensasi(
                $surat, 
                $surat->user->mahasiswa, 
                $verification->qr_path, 
                $penandatangan
            );

            \Log::info('PDF regenerated successfully', ['pdf_path' => $pdfFileName]);

            // Update file_surat_selesai
            $surat->file_surat_selesai = $pdfFileName;
            $surat->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'PDF berhasil di-generate ulang! Silakan download untuk melihat hasilnya.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error regenerating PDF dispensasi', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF Surat Dispensasi dengan QR Code
     */
    private function generatePDFDispensasi($surat, $mahasiswa, $qrPath, $penandatangan)
    {
        // Convert relative path to absolute path for PDF rendering
        // $qrPath format: "qr-codes/qr_xxxxx.png"
        $qrAbsolutePath = storage_path('app/public/' . $qrPath);

        // Data untuk PDF
        $data = [
            'nomor_surat' => $surat->nomor_surat,
            'tanggal_surat' => Carbon::parse($surat->acc_wadek3_at ?? now())->translatedFormat('d F Y'),
            'nama_mahasiswa' => $mahasiswa->Nama_Mahasiswa,
            'nim' => $mahasiswa->NIM,
            'prodi' => $mahasiswa->prodi->Nama_Prodi ?? '-',
            'angkatan' => $mahasiswa->Angkatan ?? '-',
            'nama_kegiatan' => $surat->nama_kegiatan,
            'instansi_penyelenggara' => $surat->instansi_penyelenggara ?? '-',
            'tempat_pelaksanaan' => $surat->tempat_pelaksanaan ?? '-',
            'tanggal_mulai' => Carbon::parse($surat->tanggal_mulai)->translatedFormat('d F Y'),
            'tanggal_selesai' => Carbon::parse($surat->tanggal_selesai)->translatedFormat('d F Y'),
            'logo_path' => public_path('images/logo_unijoyo.png'),
            'qr_code_path' => $qrAbsolutePath,
            'penandatangan_nama' => $penandatangan->Name_User,
            'penandatangan_nip' => $penandatangan->dosen->NIP ?? $penandatangan->pegawaiFakultas->NIP ?? '-',
        ];

        // Load view PDF
        $pdf = Pdf::loadView('wadek3.kemahasiswaan.pdf-dispensasi', $data);
        
        // Set paper size dan orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Set options untuk gambar
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'chroot' => public_path(),
        ]);
        
        // Generate filename (relative to public disk)
        $fileName = 'surat_dispensasi/dispensasi_' . $mahasiswa->NIM . '_' . time() . '.pdf';
        
        // Pastikan folder ada
        $folderPath = storage_path('app/public/surat_dispensasi');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
            \Log::info('Created surat_dispensasi folder', ['path' => $folderPath]);
        }
        
        try {
            // Save PDF ke storage using public disk
            $pdfOutput = $pdf->output();
            
            \Log::info('PDF output generated', [
                'filename' => $fileName,
                'output_size' => strlen($pdfOutput)
            ]);
            
            $result = Storage::disk('public')->put($fileName, $pdfOutput);
            
            \Log::info('Storage::put result', [
                'result' => $result,
                'filename' => $fileName
            ]);
            
            // Verify file exists
            if (!Storage::disk('public')->exists($fileName)) {
                \Log::error('PDF generation failed - file not saved', [
                    'filename' => $fileName,
                    'expected_path' => storage_path('app/public/' . $fileName),
                    'put_result' => $result
                ]);
                throw new \Exception('Gagal menyimpan PDF ke storage.');
            }
            
            \Log::info('PDF saved successfully', [
                'filename' => $fileName,
                'size' => Storage::disk('public')->size($fileName),
                'full_path' => storage_path('app/public/' . $fileName)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Exception while saving PDF', [
                'filename' => $fileName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        
        return $fileName;
    }

    /**
     * Download file permohonan
     */
    public function downloadPermohonan($id)
    {
        $surat = SuratDispensasi::findOrFail($id);

        $filePath = storage_path('app/public/' . $surat->file_permohonan);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($filePath, 'permohonan_dispensasi_' . $surat->id . '.pdf');
    }

    /**
     * Download file lampiran
     */
    public function downloadLampiran($id)
    {
        $surat = SuratDispensasi::findOrFail($id);

        if (!$surat->file_lampiran) {
            return redirect()->back()->with('error', 'Tidak ada lampiran.');
        }

        $filePath = storage_path('app/public/' . $surat->file_lampiran);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        return response()->download($filePath, 'lampiran_dispensasi_' . $surat->id . '.' . $extension);
    }

    public function validasiKelakuanBaik()
    {
        return view('wadek3.kemahasiswaan.validasi-kelakuan-baik');
    }
}
