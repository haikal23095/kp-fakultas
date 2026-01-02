<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use App\Models\SuratLegalisir;
use App\Models\TugasSurat;
use App\Models\Notifikasi;
use App\Models\JenisSurat;
use App\Models\PegawaiFakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratLegalisirController extends Controller
{
    /**
     * Tampilkan form pengajuan legalisir untuk mahasiswa
     */
    public function create()
    {
        return view('mahasiswa.form_legalisir');
    }

    /**
     * Menyimpan pengajuan legalisir baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_dokumen' => 'required|in:Ijazah,Transkrip',
            'file_scan' => 'required|file|mimes:pdf|max:10240', // Max 10MB
            'jumlah_salinan' => 'required|integer|min:1|max:10',
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $jenisDokumen = $request->jenis_dokumen;

            // Upload File Scan PDF (selalu simpan file baru yang di-upload)
            $filePath = $request->file('file_scan')->store('legalisir/scans', 'public');

            // Cari ID Jenis Surat untuk 'Surat Legalisir'
            $jenisSurat = JenisSurat::where('Nama_Surat', 'Surat Legalisir')->first();
            $idJenisSurat = $jenisSurat ? $jenisSurat->Id_Jenis_Surat : 3; 

            // Buat Tugas Surat (Parent)
            $tugasSurat = TugasSurat::create([
                'Id_Pemberi_Tugas_Surat' => $userId,
                'Id_Jenis_Surat' => $idJenisSurat,
                'Tanggal_Diberikan_Tugas_Surat' => now(),
                'Judul_Tugas_Surat' => 'Permohonan Legalisir ' . $jenisDokumen,
                'Status' => 'Proses',
            ]);

            // Hitung Biaya (Rp 5.000 per lembar)
            $biaya = $request->jumlah_salinan * 5000;

            // Buat Surat Legalisir dengan file baru
            SuratLegalisir::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_User' => $userId,
                'Jenis_Dokumen' => $jenisDokumen,
                'Jumlah_Salinan' => $request->jumlah_salinan,
                'Biaya' => $biaya,
                'File_Scan_Path' => $filePath,
                'Is_Verified' => false, // Admin akan verifikasi manual
                'Status' => 'menunggu_pembayaran',
            ]);

            // Kirim notifikasi ke mahasiswa
            Notifikasi::create([
                'Tipe_Notifikasi' => 'Accepted',
                'Pesan' => 'Pengajuan legalisir ' . $jenisDokumen . ' berhasil dikirim. Menunggu verifikasi admin.',
                'Dest_user' => $userId,
                'Source_User' => $userId,
                'Is_Read' => false,
                'Data_Tambahan' => json_encode(['entity' => 'legalisir', 'id_tugas' => $tugasSurat->Id_Tugas_Surat]),
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.riwayat.legalisir')->with('success', 'Permohonan legalisir berhasil diajukan! Silakan cek status di bawah.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file jika gagal simpan database
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui status pengajuan legalisir.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,verifikasi_berkas,menunggu_pembayaran,pembayaran_lunas,proses_stempel_paraf,menunggu_ttd_pimpinan,siap_diambil,selesai,ditolak',
            'komentar' => 'nullable|string',
        ]);

        $surat = SuratLegalisir::findOrFail($id);
        $tugasSurat = $surat->tugasSurat;
        $user = Auth::user();

        // LOGIC KHUSUS: Cek Role untuk status 'pembayaran_lunas'
        if ($request->status === 'pembayaran_lunas') {
            // Cek apakah user adalah Pegawai Fakultas
            // Menggunakan relasi di model User: public function pegawaiFakultas()
            $isPegawaiFakultas = $user->pegawaiFakultas()->exists();
            
            if (!$isPegawaiFakultas) {
                return response()->json(['message' => 'Unauthorized. Hanya Pegawai Fakultas yang dapat memverifikasi pembayaran.'], 403);
            }
        }

        // Update Status Surat Legalisir
        $surat->Status = $request->status;
        if ($request->has('komentar')) {
            $surat->Komentar = $request->komentar;
        }
        $surat->save();

        // Sinkronisasi Status Global di TugasSurat
        $this->syncGlobalStatus($tugasSurat, $request->status);

        // NOTIFIKASI: Kirim notifikasi ke Mahasiswa
        $this->sendNotificationToMahasiswa($tugasSurat->Id_Pemberi_Tugas_Surat, $request->status, $surat->Komentar);

        return response()->json(['message' => 'Status berhasil diperbarui.']);
    }

    /**
     * Helper untuk sinkronisasi status global
     */
    private function syncGlobalStatus($tugasSurat, $localStatus)
    {
        switch ($localStatus) {
            case 'selesai':
                $tugasSurat->Status = 'Selesai';
                $tugasSurat->Tanggal_Diselesaikan = now();
                break;
            case 'ditolak':
                $tugasSurat->Status = 'Ditolak';
                break;
            case 'siap_diambil':
                $tugasSurat->Status = 'Selesai'; // Atau status lain jika ada
                break;
            default:
                $tugasSurat->Status = 'Proses';
                break;
        }
        $tugasSurat->save();
    }

    /**
     * Helper untuk mengirim notifikasi
     */
    private function sendNotificationToMahasiswa($userId, $status, $komentar = null)
    {
        $pesan = "Status pengajuan legalisir Anda berubah menjadi: " . str_replace('_', ' ', ucfirst($status));
        if ($komentar) {
            $pesan .= ". Catatan: " . $komentar;
        }

        // Tentukan tipe notifikasi
        $tipe = 'Caution'; // Default
        if (in_array($status, ['selesai', 'siap_diambil', 'pembayaran_lunas'])) {
            $tipe = 'Accepted';
        } elseif ($status === 'ditolak') {
            $tipe = 'Rejected';
        }

        Notifikasi::create([
            'Tipe_Notifikasi' => $tipe,
            'Pesan' => $pesan,
            'Dest_user' => $userId, // Sesuai $fillable di model Notifikasi
            'Source_User' => Auth::id(),
            'Is_Read' => false,
            'Data_Tambahan' => ['status' => $status, 'entity' => 'legalisir'],
        ]);
    }
}
