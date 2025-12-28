<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\TugasSurat;
use App\Models\SuratKelakuanBaik;
use App\Models\JenisSurat;
use App\Models\Pejabat;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class SuratKelakuanBaikController extends Controller
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

        // Ambil ID Jenis Surat
        $jenisSurat = JenisSurat::where('Nama_Surat', 'LIKE', '%Berkelakuan Baik%')
            ->orWhere('Nama_Surat', 'LIKE', '%Kelakuan Baik%')
            ->first();

        if (!$jenisSurat) {
            return redirect()->back()->with('error', 'Jenis surat tidak ditemukan di database. Hubungi admin.');
        }

        return view('mahasiswa.pengajuan-surat.form_surat_kelakuan_baik', compact('mahasiswa', 'jenisSurat'));
    }

    /**
     * Menyimpan pengajuan (Store)
     */
    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'Id_Jenis_Surat' => 'required|integer',
            'keperluan' => 'required|string|max:500',
        ], [
            'keperluan.required' => 'Keperluan surat wajib diisi.',
            'keperluan.max' => 'Keperluan maksimal 500 karakter.',
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
            $pejabat = Pejabat::where(function($query) {
                $query->where('Nama_Jabatan', 'LIKE', '%Wakil Dekan III%')
                      ->orWhere('Nama_Jabatan', 'LIKE', '%Wakil Dekan 3%')
                      ->orWhere('Nama_Jabatan', 'LIKE', '%Kemahasiswaan%')
                      ->orWhere('Nama_Jabatan', 'LIKE', '%WD3%')
                      ->orWhere('Nama_Jabatan', 'LIKE', '%WD 3%');
            })
            ->first();

            $idPejabat = $pejabat ? $pejabat->Id_Pejabat : null;

            // Ambil semester dan tahun akademik otomatis
            // Logika sederhana: bulan 1-6 = genap tahun sebelumnya, 7-12 = ganjil tahun ini
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            
            if ($currentMonth >= 1 && $currentMonth <= 6) {
                $semester = 'Genap';
                $tahunAkademik = ($currentYear - 1) . '/' . $currentYear;
            } else {
                $semester = 'Ganjil';
                $tahunAkademik = $currentYear . '/' . ($currentYear + 1);
            }

            // Insert ke Tabel Induk (Tugas_Surat)
            $tugasSurat = TugasSurat::create([
                'Id_Pemberi_Tugas_Surat' => $user->Id_User,
                'Id_Jenis_Surat' => $request->Id_Jenis_Surat,
                'Judul_Tugas_Surat' => 'Permohonan Surat Keterangan Berkelakuan Baik',
                'Status' => 'baru',
                'Tanggal_Diberikan_Tugas_Surat' => Carbon::now(),
                'Tanggal_Tenggat_Tugas_Surat' => Carbon::now()->addDays(5),
            ]);

            // Insert ke Tabel Child (Surat_Kelakuan_Baik)
            SuratKelakuanBaik::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_User' => $user->Id_User,
                'Id_Pejabat' => $idPejabat,
                'Keperluan' => $request->keperluan,
                'Semester' => $semester,
                'Tahun_Akademik' => $tahunAkademik,
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.pengajuan.create')
                ->with('success', 'Pengajuan surat berhasil dikirim. Silakan cek status di menu Riwayat.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error store Surat Kelakuan Baik: ' . $e->getMessage());
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
        // Ambil TugasSurat yang memiliki relasi suratKelakuanBaik
        $daftarPengajuan = TugasSurat::whereHas('suratKelakuanBaik')
            ->with([
                'suratKelakuanBaik', 
                'pemberiTugas.mahasiswa.prodi'
            ])
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('admin_fakultas.surat_kelakuan_baik.index', compact('daftarPengajuan'));
    }

    /**
     * Download surat yang sudah ditandatangani (untuk Mahasiswa)
     */
    public function downloadSurat($id)
    {
        $user = Auth::user();

        // Ambil surat dengan verifikasi QR
        $tugasSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.mahasiswa.prodi.fakultas',
            'penerimaTugas',
            'suratKelakuanBaik.user.mahasiswa',
            'verification.penandatangan.pegawai',
            'verification.penandatangan.dosen'
        ])
            ->where('Id_Tugas_Surat', $id)
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->firstOrFail();

        // Cek apakah surat sudah selesai
        $statusLower = strtolower(trim($tugasSurat->Status));
        if ($statusLower !== 'selesai' && $statusLower !== 'telah ditandatangani dekan') {
            return redirect()->route('mahasiswa.riwayat.berkelakuan_baik')
                ->with('error', 'Surat belum dapat diunduh. Status: ' . $tugasSurat->Status);
        }

        // Cek apakah ada data surat berkelakuan baik
        if (!$tugasSurat->suratKelakuanBaik) {
            return redirect()->route('mahasiswa.riwayat.berkelakuan_baik')
                ->with('error', 'Data surat tidak ditemukan.');
        }

        $mahasiswa = $tugasSurat->suratKelakuanBaik->user->mahasiswa ?? $tugasSurat->pemberiTugas->mahasiswa;
        $suratKelakuanBaik = $tugasSurat->suratKelakuanBaik;

        // Render PDF view menggunakan template yang sama seperti preview dekan
        return view('mahasiswa.pdf.surat_kelakuan_baik', [
            'surat' => $tugasSurat,
            'mahasiswa' => $mahasiswa,
            'jenisSurat' => $tugasSurat->jenisSurat,
            'suratKelakuanBaik' => $suratKelakuanBaik,
            'verification' => $tugasSurat->verification,
            'mode' => 'download'
        ]);
    }
}
