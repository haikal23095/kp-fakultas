<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AccDekanDosenWali;
use App\Models\Dosen;
use App\Helpers\QrCodeHelper;

class SKDosenWaliController extends Controller
{
    /**
     * Tampilkan daftar SK Dosen Wali yang menunggu persetujuan Dekan
     */
    public function index()
    {
        $daftarSK = AccDekanDosenWali::with(['reqSKDosenWali.prodi', 'reqSKDosenWali.kaprodi.user'])
            ->where('Status', 'Menunggu-Persetujuan-Dekan')
            ->orderBy('Tanggal-Pengajuan', 'desc')
            ->get();

        return view('dekan.sk_dosen_wali', compact('daftarSK'));
    }

    /**
     * Ambil detail SK Dosen Wali untuk preview
     */
    public function detail($id)
    {
        try {
            $sk = AccDekanDosenWali::with(['reqSKDosenWali.prodi'])
                ->findOrFail($id);

            // Ambil data Dekan dari tabel Dosen dengan Id_Pejabat = 1 (Dekan)
            $dekan = Dosen::where('Id_Pejabat', 1)->first();

            if (!$dekan) {
                Log::error('Dekan tidak ditemukan di tabel Dosen dengan Id_Pejabat=1');
                return response()->json([
                    'success' => false,
                    'message' => 'Data Dekan tidak ditemukan'
                ], 404);
            }

            $dekanName = $dekan->Nama_Dosen ?? 'Nama Dekan';
            $dekanNip = $dekan->NIP ?? '-';

            Log::info('SK Detail - Dekan found', [
                'dekan_name' => $dekanName,
                'dekan_nip' => $dekanNip,
                'id_pejabat' => $dekan->Id_Pejabat
            ]);

            return response()->json([
                'success' => true,
                'sk' => $sk,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching SK detail', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Setujui SK Dosen Wali dan generate QR Code
     */
    public function approve($id)
    {
        try {
            Log::info('Starting SK approval', ['sk_id' => $id]);

            $sk = AccDekanDosenWali::findOrFail($id);

            Log::info('SK found', [
                'no' => $sk->No,
                'status' => $sk->Status
            ]);

            // Generate QR Code untuk verifikasi
            $qrContent = url("/verify-sk-dosen-wali/{$sk->No}");
            Log::info('Generating QR Code', ['content' => $qrContent]);

            $qrPath = QrCodeHelper::generate($qrContent, 200);

            if (!$qrPath) {
                Log::error('QR Code generation failed - empty path returned');
                throw new \Exception('Gagal generate QR Code - path kosong');
            }

            Log::info('QR Code generated', ['path' => $qrPath]);

            // Get URL untuk ditampilkan di preview
            $qrUrl = asset('storage/' . $qrPath);
            Log::info('QR Code URL', ['url' => $qrUrl]);

            // Update status dan simpan QR code path
            $sk->Status = 'Selesai'; // Status ENUM: Menunggu-Persetujuan-Wadek-1, Menunggu-Persetujuan-Dekan, Selesai, Ditolak
            $sk->QR_Code = $qrPath; // Simpan path relatif ke database
            $sk->{'Tanggal-Persetujuan-Dekan'} = now();
            $sk->Id_Dekan = Auth::user()->Id_User;
            $sk->save();

            Log::info('SK updated successfully', [
                'status' => $sk->Status,
                'qr_path' => $sk->QR_Code
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SK Dosen Wali berhasil disetujui dan ditandatangani',
                'qr_code' => $qrUrl  // Return URL untuk ditampilkan di HTML
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('SK not found', [
                'sk_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'SK tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error approving SK', [
                'sk_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui SK: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan history SK Dosen Wali yang sudah selesai ditandatangani
     */
    public function history()
    {
        try {
            $history = AccDekanDosenWali::with(['reqSKDosenWali.prodi', 'reqSKDosenWali.kaprodi.user'])
                ->where('Status', 'Selesai')
                ->whereNotNull('QR_Code')
                ->orderBy('Tanggal-Persetujuan-Dekan', 'desc')
                ->get();

            // Get Dekan name for display
            $dekan = Dosen::where('Id_Pejabat', 1)->first();
            $dekanName = $dekan ? $dekan->Nama_Dosen : 'Dr. Budi Hartono, S.Kom., M.Kom.';

            // Add dekan name to each SK
            $history->transform(function ($sk) use ($dekanName) {
                $sk->dekan_name = $dekanName;
                return $sk;
            });

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching history', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat history: ' . $e->getMessage()
            ], 500);
        }
    }
}