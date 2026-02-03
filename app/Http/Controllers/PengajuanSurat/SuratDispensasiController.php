<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\SuratDispensasi;
use App\Models\Pejabat;
use App\Models\Mahasiswa;
use App\Models\SuratVerification;
use Carbon\Carbon;

class SuratDispensasiController extends Controller
{
    /**
     * Show the form for creating a new Surat Dispensasi.
     */
    public function create()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->with('prodi')->first();

        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        return view('mahasiswa.pengajuan-surat.form_surat_dispensasi', compact('mahasiswa'));
    }

    /**
     * Store a newly created Surat Dispensasi in storage.
     */
    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'nama_kegiatan' => 'required|string|max:255',
            'instansi_penyelenggara' => 'nullable|string|max:255',
            'tempat_pelaksanaan' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'file_lampiran' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan/alasan wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'file_lampiran.mimes' => 'File lampiran harus berformat PDF, JPG, JPEG, atau PNG.',
            'file_lampiran.max' => 'File lampiran maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->with('prodi.fakultas')->first();

            if (!$mahasiswa) {
                throw new \Exception('Data mahasiswa tidak ditemukan.');
            }

            // Cari Pejabat (Wakil Dekan 3)
            $pejabat = Pejabat::where(function ($query) {
                $query->where('Nama_Jabatan', 'LIKE', '%Wakil Dekan III%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%Wakil Dekan 3%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%Kemahasiswaan%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%WD3%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%WD 3%');
            })->first();

            $idPejabat = $pejabat ? $pejabat->Id_Pejabat : null;

            // Upload file lampiran/bukti pendukung (opsional)
            $fileLampiranPath = null;
            if ($request->hasFile('file_lampiran')) {
                $fileLampiran = $request->file('file_lampiran');

                if (!$fileLampiran->isValid()) {
                    throw new \Exception('File upload gagal. File tidak valid.');
                }

                $extension = $fileLampiran->getClientOriginalExtension();
                $fileLampiranName = 'lampiran_dispen_' . $mahasiswa->NIM . '_' . time() . '.' . $extension;

                $storedPath = Storage::disk('public')->putFileAs(
                    'surat_dispensasi',
                    $fileLampiran,
                    $fileLampiranName
                );

                if (!$storedPath) {
                    throw new \Exception('File upload gagal. Tidak bisa menyimpan ke storage.');
                }

                $fileLampiranPath = $storedPath;
            }

            // Insert langsung ke Surat_Dispensasi
            SuratDispensasi::create([
                'Id_User' => $user->Id_User,
                'Id_Pejabat_Wadek3' => $idPejabat,
                'nama_kegiatan' => $request->nama_kegiatan,
                'instansi_penyelenggara' => $request->instansi_penyelenggara,
                'tempat_pelaksanaan' => $request->tempat_pelaksanaan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'file_lampiran' => $fileLampiranPath,
                'Status' => 'baru',
                'Tanggal_Diberikan' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.riwayat.dispensasi')
                ->with('success', 'Surat Dispensasi berhasil diajukan! Silakan cek status di menu Riwayat.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error store Surat Dispensasi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download / Preview surat (untuk mahasiswa)
     */
    public function downloadSurat($id)
    {
        $user = Auth::user();

        $surat = SuratDispensasi::with([
            'user.mahasiswa.prodi.fakultas',
            'pejabatWadek3'
        ])
            ->where('id', $id)
            ->where('Id_User', $user->Id_User)
            ->firstOrFail();

        $statusLower = strtolower(trim($surat->Status));
        if ($statusLower !== 'selesai' && $statusLower !== 'telah ditandatangani dekan' && $statusLower !== 'success') {
            return redirect()->route('mahasiswa.riwayat.dispensasi')
                ->with('error', 'Surat belum dapat diunduh. Status: ' . $surat->Status);
        }

        $verification = SuratVerification::with(['penandatangan.pegawai', 'penandatangan.dosen'])
            ->where('id_letter', $id)
            ->where('letter_type', 'dispensasi')
            ->first();

        $mahasiswa = $surat->user->mahasiswa;

        return view('mahasiswa.pdf.surat_dispensasi', [
            'surat' => $surat,
            'mahasiswa' => $mahasiswa,
            'verification' => $verification,
            'mode' => 'download'
        ]);
    }
}
