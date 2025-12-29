<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\TugasSurat;
use App\Models\SuratIzinKegiatanMalam;
use App\Models\JenisSurat;
use App\Models\Pejabat;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class SuratIzinKegiatanMalamController extends Controller
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
        $jenisSurat = JenisSurat::where('Nama_Surat', 'LIKE', '%Izin%Kegiatan%Malam%')
            ->orWhere('Nama_Surat', 'LIKE', '%Kegiatan Malam%')
            ->first();

        if (!$jenisSurat) {
            return redirect()->back()->with('error', 'Jenis surat tidak ditemukan di database. Hubungi admin.');
        }

        return view('mahasiswa.pengajuan-surat.form_izin_malam', compact('mahasiswa', 'jenisSurat'));
    }

    /**
     * Menyimpan pengajuan (Store)
     */
    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'Id_Jenis_Surat' => 'required|integer',
            'nama_kegiatan' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'lokasi_kegiatan' => 'required|string|max:255',
            'jumlah_peserta' => 'required|integer|min:1',
            'alasan' => 'required|string',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'nama_kegiatan.max' => 'Nama kegiatan maksimal 255 karakter.',
            'waktu_mulai.required' => 'Waktu mulai kegiatan wajib diisi.',
            'waktu_mulai.date' => 'Format waktu mulai tidak valid.',
            'waktu_selesai.required' => 'Waktu selesai kegiatan wajib diisi.',
            'waktu_selesai.date' => 'Format waktu selesai tidak valid.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
            'lokasi_kegiatan.required' => 'Lokasi kegiatan wajib diisi.',
            'lokasi_kegiatan.max' => 'Lokasi kegiatan maksimal 255 karakter.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.integer' => 'Jumlah peserta harus berupa angka.',
            'jumlah_peserta.min' => 'Jumlah peserta minimal 1 orang.',
            'alasan.required' => 'Alasan kegiatan wajib diisi.',
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

            // Insert ke Tabel Induk (Tugas_Surat)
            $tugasSurat = TugasSurat::create([
                'Id_Pemberi_Tugas_Surat' => $user->Id_User,
                'Id_Jenis_Surat' => $request->Id_Jenis_Surat,
                'Judul_Tugas_Surat' => 'Permohonan Izin Kegiatan Malam - ' . $request->nama_kegiatan,
                'Status' => 'baru',
                'Tanggal_Diberikan_Tugas_Surat' => Carbon::now(),
                'Tanggal_Tenggat_Tugas_Surat' => Carbon::now()->addDays(5),
            ]);

            // Insert ke Tabel Child (surat_izin_kegiatan_malams)
            SuratIzinKegiatanMalam::create([
                'id_tugas_surat' => $tugasSurat->Id_Tugas_Surat,
                'id_user' => $user->Id_User,
                'id_pejabat' => $idPejabat,
                'nama_kegiatan' => $request->nama_kegiatan,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'lokasi_kegiatan' => $request->lokasi_kegiatan,
                'jumlah_peserta' => $request->jumlah_peserta,
                'alasan' => $request->alasan,
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.pengajuan.create')
                ->with('success', 'Pengajuan izin kegiatan malam berhasil dikirim. Silakan cek status di menu Riwayat.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error store Surat Izin Kegiatan Malam: ' . $e->getMessage());
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
        // Ambil TugasSurat yang memiliki relasi suratIzinKegiatanMalam
        $daftarPengajuan = TugasSurat::whereHas('suratIzinKegiatanMalam')
            ->with([
                'suratIzinKegiatanMalam', 
                'pemberiTugas.mahasiswa.prodi'
            ])
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('admin_fakultas.surat_izin_kegiatan_malam.index', compact('daftarPengajuan'));
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
            'suratIzinKegiatanMalam.user.mahasiswa',
            'suratIzinKegiatanMalam.pejabat',
            'verification.penandatangan.pegawai',
            'verification.penandatangan.dosen'
        ])
            ->where('Id_Tugas_Surat', $id)
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->firstOrFail();

        // Cek apakah surat sudah selesai
        $statusLower = strtolower(trim($tugasSurat->Status));
        if ($statusLower !== 'selesai' && $statusLower !== 'telah ditandatangani dekan') {
            return redirect()->route('mahasiswa.riwayat.izin_kegiatan_malam')
                ->with('error', 'Surat belum dapat diunduh. Status: ' . $tugasSurat->Status);
        }

        // Cek apakah ada data surat izin kegiatan malam
        if (!$tugasSurat->suratIzinKegiatanMalam) {
            return redirect()->route('mahasiswa.riwayat.izin_kegiatan_malam')
                ->with('error', 'Data surat tidak ditemukan.');
        }

        $mahasiswa = $tugasSurat->suratIzinKegiatanMalam->user->mahasiswa ?? $tugasSurat->pemberiTugas->mahasiswa;
        $suratIzinMalam = $tugasSurat->suratIzinKegiatanMalam;

        // Render PDF view
        return view('mahasiswa.pdf.surat_izin_kegiatan_malam', [
            'surat' => $tugasSurat,
            'mahasiswa' => $mahasiswa,
            'jenisSurat' => $tugasSurat->jenisSurat,
            'suratIzinMalam' => $suratIzinMalam,
            'verification' => $tugasSurat->verification,
            'mode' => 'download'
        ]);
    }
}
