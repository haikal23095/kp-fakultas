<?php

namespace App\Http\Controllers\PengajuanSurat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SuratKelakuanBaik;
use App\Models\Pejabat;
use App\Models\Mahasiswa;
use App\Models\SuratVerification;
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

        return view('mahasiswa.pengajuan-surat.form_surat_kelakuan_baik', compact('mahasiswa'));
    }

    /**
     * Menyimpan pengajuan (Store)
     */
    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
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
            $pejabat = Pejabat::where(function ($query) {
                $query->where('Nama_Jabatan', 'LIKE', '%Wakil Dekan III%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%Wakil Dekan 3%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%Kemahasiswaan%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%WD3%')
                    ->orWhere('Nama_Jabatan', 'LIKE', '%WD 3%');
            })
                ->first();

            $idPejabat = $pejabat ? $pejabat->Id_Pejabat : null;

            // Ambil semester dan tahun akademik otomatis
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            if ($currentMonth >= 1 && $currentMonth <= 6) {
                $semester = 'Genap';
                $tahunAkademik = ($currentYear - 1) . '/' . $currentYear;
            } else {
                $semester = 'Ganjil';
                $tahunAkademik = $currentYear . '/' . ($currentYear + 1);
            }

            // Insert langsung ke tabel Surat_Kelakuan_Baik
            SuratKelakuanBaik::create([
                'Id_User' => $user->Id_User,
                'Id_Pejabat' => $idPejabat,
                'Keperluan' => $request->keperluan,
                'Semester' => $semester,
                'Tahun_Akademik' => $tahunAkademik,
                'Status' => 'baru',
                'Tanggal_Diberikan' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.riwayat.berkelakuan_baik')
                ->with('success', 'Pengajuan surat berhasil dikirim. Silakan cek status di bawah.');

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
        // Ambil SuratKelakuanBaik langsung
        $daftarPengajuan = SuratKelakuanBaik::with([
            'user.mahasiswa.prodi'
        ])
            ->orderBy('Tanggal_Diberikan', 'desc')
            ->get();

        return view('admin_fakultas.surat_kelakuan_baik.index', compact('daftarPengajuan'));
    }

    /**
     * Beri nomor surat oleh Admin Fakultas
     */
    public function beriNomor(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:100'
        ]);

        DB::beginTransaction();
        try {
            $surat = SuratKelakuanBaik::findOrFail($id);

            // Update nomor surat
            $surat->update([
                'Nomor_Surat' => $request->nomor_surat
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Nomor surat berhasil diberikan. Silakan kirim ke Wadek3.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memberikan nomor surat: ' . $e->getMessage());
        }
    }

    /**
     * Kirim surat ke Wadek3 untuk ditandatangani
     */
    public function kirimKeWadek3($id)
    {
        DB::beginTransaction();
        try {
            $surat = SuratKelakuanBaik::findOrFail($id);

            if (!$surat->Nomor_Surat) {
                throw new \Exception('Nomor surat belum diberikan.');
            }

            // Update status
            $surat->update([
                'Status' => 'menunggu-ttd'
            ]);

            // Notifikasi ke Wadek3 (Role 10)
            $wadek3 = \App\Models\User::where('Id_Role', 10)->first();
            if ($wadek3) {
                \App\Models\Notifikasi::create([
                    'Tipe_Notifikasi' => 'New',
                    'Pesan' => 'Surat Berkelakuan Baik dari mahasiswa ' . ($surat->user->Name_User ?? '-') . ' siap untuk ditandatangani.',
                    'Dest_user' => $wadek3->Id_User,
                    'Source_User' => auth()->user()->Id_User,
                    'Is_Read' => false,
                    'Data_Tambahan' => json_encode(['entity' => 'kelakuan_baik', 'id' => $surat->id]),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Surat berhasil dikirim ke Wadek3 untuk ditandatangani.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengirim ke Wadek3: ' . $e->getMessage());
        }
    }

    /**
     * Download surat yang sudah ditandatangani (untuk Mahasiswa)
     */
    public function downloadSurat($id)
    {
        $user = Auth::user();

        // Ambil surat langsung
        $surat = SuratKelakuanBaik::with([
            'user.mahasiswa.prodi.fakultas',
            'pejabat'
        ])
            ->where('id', $id)
            ->where('Id_User', $user->Id_User)
            ->firstOrFail();

        // Cek apakah surat sudah selesai
        $statusLower = strtolower(trim($surat->Status));
        if ($statusLower !== 'selesai' && $statusLower !== 'telah ditandatangani dekan' && $statusLower !== 'success') {
            return redirect()->route('mahasiswa.riwayat.berkelakuan_baik')
                ->with('error', 'Surat belum dapat diunduh. Status: ' . $surat->Status);
        }

        // Ambil verification menggunakan ID child
        $verification = SuratVerification::with(['penandatangan.pegawai', 'penandatangan.dosen'])
            ->where('id_tugas_surat', $id)
            ->first();

        $mahasiswa = $surat->user->mahasiswa;

        // Render PDF view
        return view('mahasiswa.pdf.surat_kelakuan_baik', [
            'surat' => $surat,
            'mahasiswa' => $mahasiswa,
            'suratKelakuanBaik' => $surat,
            'verification' => $verification,
            'mode' => 'download'
        ]);
    }
}
