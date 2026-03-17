<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SuratTidakBeasiswa;
use App\Models\Pejabat;
use App\Models\Mahasiswa;
use App\Models\SuratVerification;
use Carbon\Carbon;

class SuratTidakBeasiswaController extends Controller
{
    /**
     * Menampilkan form pengajuan untuk Mahasiswa
     */
    public function create()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('Id_User', $user->Id_User)->with('prodi')->first();

        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        return view('mahasiswa.pengajuan.form_surat_tidak_beasiswa', compact('mahasiswa'));
    }

    /**
     * Menyimpan pengajuan (Store)
     */
    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'nama_orang_tua' => 'required|string|max:255',
            'pekerjaan_orang_tua' => 'required|string|max:255',
            'pendapatan_orang_tua' => 'required|numeric|min:0',
            'nip_orang_tua' => 'nullable|string|max:50',
            'keperluan' => 'required|string|max:255',
            'file_pernyataan' => 'required|file|mimes:pdf|max:2048',
        ], [
            'file_pernyataan.required' => 'Surat pernyataan wajib diunggah.',
            'file_pernyataan.mimes' => 'Format file harus PDF.',
            'file_pernyataan.max' => 'Ukuran file maksimal 2MB.',
            'nama_orang_tua.required' => 'Nama orang tua wajib diisi.',
            'pekerjaan_orang_tua.required' => 'Pekerjaan orang tua wajib diisi.',
            'pendapatan_orang_tua.required' => 'Pendapatan orang tua wajib diisi.',
            'pendapatan_orang_tua.numeric' => 'Pendapatan harus berupa angka.',
            'pendapatan_orang_tua.min' => 'Pendapatan tidak boleh negatif.',
            'keperluan.required' => 'Keperluan surat wajib diisi.',
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

            // Cari Pejabat (Wakil Dekan 3 / Kemahasiswaan)
            $pejabat = Pejabat::where(function ($query) {
                $query->where('Nama_Jabatan', 'LIKE', '%Wakil Dekan III%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%Wakil Dekan 3%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%Kemahasiswaan%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%WD3%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%WD 3%');
            })
                ->first();

            $idPejabat = $pejabat ? $pejabat->Id_Pejabat : null;

            // Upload File
            $path = $request->file('file_pernyataan')->store('surat_pernyataan', 'public');

            // Insert langsung ke Surat_Tidak_Beasiswa
            SuratTidakBeasiswa::create([
                'Id_User' => $user->Id_User,
                'Id_Pejabat' => $idPejabat,
                'Nama_Orang_Tua' => $request->nama_orang_tua,
                'Pekerjaan_Orang_Tua' => $request->pekerjaan_orang_tua,
                'Pendapatan_Orang_Tua' => $request->pendapatan_orang_tua,
                'NIP_Orang_Tua' => $request->nip_orang_tua,
                'Keperluan' => $request->keperluan,
                'File_Pernyataan' => $path,
                'Status' => 'baru',
                'Tanggal_Diberikan' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.riwayat.tidak_beasiswa')
                ->with('success', 'Pengajuan surat berhasil dikirim. Silakan cek status di menu Riwayat.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error store Surat Tidak Beasiswa: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan daftar pengajuan untuk Admin Fakultas
     */
    public function indexAdmin()
    {
        $user = Auth::user()->load(['pegawaiFakultas.fakultas']);
        $fakultasId = $user->pegawaiFakultas?->Id_Fakultas;

        // Ambil SuratTidakBeasiswa langsung
        $daftarTugas = SuratTidakBeasiswa::when($fakultasId, function ($q) use ($fakultasId) {
            // Filter berdasarkan fakultas pengaju (mahasiswa)
            $q->whereHas('user.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
                $subQ->where('Id_Fakultas', $fakultasId);
            });
        })
            ->with([
                'user.mahasiswa.prodi',
                'pejabat'
            ])
            ->orderBy('Tanggal_Diberikan', 'desc')
            ->paginate(15);

        return view('admin_fakultas.surat_tidak_beasiswa.index', compact('daftarTugas'));
    }

    /**
     * Download surat yang sudah ditandatangani dekan (untuk mahasiswa)
     */
    public function downloadSurat($id)
    {
        $user = Auth::user();

        // Ambil surat langsung
        $surat = SuratTidakBeasiswa::with([
            'user.mahasiswa.prodi.fakultas',
            'pejabat'
        ])
            ->where('id', $id)
            ->where('Id_User', $user->Id_User)
            ->firstOrFail();

        // Cek apakah surat sudah selesai
        $statusLower = strtolower(trim($surat->Status));
        if ($statusLower !== 'selesai' && $statusLower !== 'telah ditandatangani dekan' && $statusLower !== 'success') {
            return redirect()->route('mahasiswa.riwayat.tidak_beasiswa')
                ->with('error', 'Surat belum dapat diunduh. Status: ' . $surat->Status);
        }

        // Ambil verification
        $verification = SuratVerification::with(['penandatangan.pegawai', 'penandatangan.dosen'])
            ->where('id_letter', $id)
            ->where('letter_type', 'tidak_beasiswa')
            ->first();

        $mahasiswa = $surat->user->mahasiswa;

        // Render PDF view
        return view('mahasiswa.pdf.surat_tidak_beasiswa', [
            'surat' => $surat,
            'mahasiswa' => $mahasiswa,
            'suratTidakBeasiswa' => $surat,
            'verification' => $verification,
            'mode' => 'download'
        ]);
    }
}
