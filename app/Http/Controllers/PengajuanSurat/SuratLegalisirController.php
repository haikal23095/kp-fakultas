<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TugasSurat;
use App\Models\SuratLegalisir;
use App\Models\JenisSurat;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SuratLegalisirController extends Controller
{
    /**
     * Menampilkan form pengajuan legalisir
     */
    public function create()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->first();
        
        // Ambil ID jenis surat untuk Legalisir
        // Pastikan nama ini sesuai dengan data di tabel Jenis_Surat
        $jenisSurat = JenisSurat::where('Nama_Surat', 'Surat Legalisir')->first();

        return view('mahasiswa.legalisir', [
            'mahasiswa' => $mahasiswa,
            'jenisSurat' => $jenisSurat
        ]);
    }

    /**
     * Menyimpan pengajuan legalisir
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'jenis_dokumen' => 'required|in:1,2', // 1: Ijazah, 2: Transkrip (sesuai value di blade)
            'file_dokumen' => 'required|file|mimes:pdf|max:5120', // Max 5MB sesuai warning di blade
            'jumlah_salinan' => 'required|integer|min:1|max:10',
        ], [
            'jenis_dokumen.required' => 'Silakan pilih jenis dokumen.',
            'file_dokumen.required' => 'Dokumen asli wajib diunggah.',
            'file_dokumen.mimes' => 'Dokumen harus berformat PDF.',
            'file_dokumen.max' => 'Ukuran dokumen maksimal 5MB.',
            'jumlah_salinan.required' => 'Jumlah salinan wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // 2. Upload File
            // Simpan di folder 'dokumen_legalisir' di disk 'public'
            $path = $request->file('file_dokumen')->store('dokumen_legalisir', 'public');

            // 3. Cari atau Buat Jenis Surat
            $jenisSurat = JenisSurat::firstOrCreate(
                ['Nama_Surat' => 'Surat Legalisir'],
                [
                    'Id_Jenis_Surat' => JenisSurat::max('Id_Jenis_Surat') + 1,
                    'Tipe_Surat' => 'Surat-Keluar'
                ]
            );
            $idJenisSurat = $jenisSurat->Id_Jenis_Surat; 

            // Mapping value jenis dokumen dari form (1/2) ke Enum database (Ijazah/Transkrip)
            $jenisDokumenMap = [
                '1' => 'Ijazah',
                '2' => 'Transkrip'
            ];
            $jenisDokumenLabel = $jenisDokumenMap[$request->jenis_dokumen] ?? 'Dokumen';

            // 4. Buat Parent (TugasSurat)
            $tugasSurat = TugasSurat::create([
                'Id_Pemberi_Tugas_Surat' => Auth::id(),
                'Id_Jenis_Surat' => $idJenisSurat,
                'Tanggal_Diberikan_Tugas_Surat' => Carbon::now(),
                'Judul_Tugas_Surat' => 'Permohonan Legalisir ' . $jenisDokumenLabel,
            ]);

            // 5. Buat Child (SuratLegalisir)
            $suratLegalisir = SuratLegalisir::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_User' => Auth::id(),
                'Jenis_Dokumen' => $jenisDokumenLabel,
                'Path_File' => $path,
                'Jumlah_Salinan' => $request->jumlah_salinan,
                'Status' => 'pending', // Status lokal awal
            ]);

            // 6. Kirim Notifikasi ke Admin Fakultas (Role ID = 7)
            $adminFakultasUsers = User::where('Id_Role', 7)->get();
            
            foreach ($adminFakultasUsers as $admin) {
                Notifikasi::create([
                    'Tipe_Notifikasi' => 'Invitation', // Tipe info/notification
                    'Pesan' => 'Pengajuan Legalisir Baru dari ' . Auth::user()->Name_User,
                    'Dest_user' => $admin->Id_User,
                    'Source_User' => Auth::id(),
                    'Is_Read' => false,
                    'Data_Tambahan' => [
                        'id_surat' => $suratLegalisir->id_no,
                        'type' => 'legalisir'
                    ]
                ]);
            }

            DB::commit();

            return redirect()->route('mahasiswa.riwayat.legalisir')
                ->with('success', 'Pengajuan legalisir berhasil dikirim. Mohon tunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file jika gagal
            if (isset($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
