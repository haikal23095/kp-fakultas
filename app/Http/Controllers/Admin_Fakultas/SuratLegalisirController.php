<?php

namespace App\Http\Controllers\Admin_Fakultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratLegalisir;
use App\Models\TugasSurat;
use App\Models\JenisSurat;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\Pejabat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuratLegalisirController extends Controller
{
    /**
     * Membatasi akses hanya untuk Role ID 5 dan 7
     */
    private function checkAccess()
    {
        if (!in_array(Auth::user()->Id_Role, [5, 7])) {
            abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES KE FITUR INI.');
        }
    }

    /**
     * Menampilkan daftar pengajuan legalisir
     */
    public function index()
    {
        $this->checkAccess();

        // HANYA ambil data mahasiswa (bukan dekan/pegawai) dan yang belum selesai
        $daftarSurat = SuratLegalisir::with(['user.mahasiswa', 'tugasSurat'])
            ->whereHas('user.mahasiswa') // FILTER: Hanya yang punya relasi mahasiswa
            ->where('Status', '!=', 'selesai') // EXCLUDE: Data yang sudah selesai
            ->orderBy('id_no', 'desc')
            ->get();

        return view('admin_fakultas.list_legalisir', compact('daftarSurat'));
    }

    /**
     * Form untuk input pengajuan baru oleh admin
     */
    public function create()
    {
        $this->checkAccess();
        
        // Mengambil data mahasiswa beserta user dan prodi untuk dropdown pencarian
        $daftarMahasiswa = Mahasiswa::with(['user', 'prodi'])->get();
        
        return view('admin_fakultas.surat_legalisir.create', compact('daftarMahasiswa'));
    }

    /**
     * Menyimpan data pengajuan baru
     */
    public function store(Request $request)
    {
        $this->checkAccess();

        $request->validate([
            'id_user_mahasiswa' => 'required|exists:Users,Id_User',
            'jenis_dokumen'     => 'required|in:Ijazah,Transkrip',
            'jumlah_salinan'    => 'required|integer|min:1',
            'biaya'             => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Membuat Parent (TugasSurat)
            $tugasSurat = TugasSurat::create([
                'Id_Pemberi_Tugas_Surat'        => Auth::id(),
                'Id_Jenis_Surat'                => 3, // Sesuaikan ID Jenis Surat Legalisir di sistem Anda
                'Tanggal_Diberikan_Tugas_Surat' => Carbon::now(),
                'Judul_Tugas_Surat'             => 'Legalisir ' . $request->jenis_dokumen,
                'Status_Tugas_Surat'            => 'diproses',
            ]);

            // 2. Membuat Child (SuratLegalisir) dengan kolom underscore sesuai DB
            SuratLegalisir::create([
                'Id_Tugas_Surat' => $tugasSurat->Id_Tugas_Surat,
                'Id_User'        => $request->id_user_mahasiswa,
                'Jenis_Dokumen'  => $request->jenis_dokumen,
                'Jumlah_Salinan' => $request->jumlah_salinan,
                'Biaya'          => $request->biaya, // Biaya disimpan di sini
                'Status'         => 'menunggu_pembayaran', // Status awal default
            ]);

            DB::commit();
            return redirect()->route('admin_fakultas.surat_legalisir.index')
                ->with('success', 'Data legalisir mahasiswa berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi Pembayaran (Mencatat Tanggal_Bayar otomatis)
     */
    public function konfirmasiPembayaran(Request $request, $id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::findOrFail($id);

        if ($surat->Status !== 'menunggu_pembayaran') {
            return redirect()->back()->with('error', 'Status berkas tidak valid.');
        }

        $surat->update([
            'Status'        => 'pembayaran_lunas',
            'Tanggal_Bayar' => Carbon::now(),
        ]);

        // Mengirim notifikasi ke mahasiswa
        $this->sendNotification($surat->Id_User, 'Pembayaran Lunas', 'Pembayaran legalisir Anda telah diterima. Berkas diproses ke tahap stempel.', $surat->id_no);

        return redirect()->back()->with('success', 'Pembayaran telah dikonfirmasi sebagai LUNAS.');
    }

    /**
     * Input Nomor Surat Legalisir dan kirim ke Dekan
     */
    public function beriNomorSurat(Request $request, $id)
    {
        $this->checkAccess();

        $request->validate([
            'nomor_surat' => 'required|string|max:100',
        ]);

        $surat = SuratLegalisir::findOrFail($id);

        if ($surat->Status !== 'pembayaran_lunas') {
            return redirect()->back()->with('error', 'Status berkas tidak valid untuk pemberian nomor.');
        }

        // Set Pejabat Dekan otomatis
        $pejabatDekan = Pejabat::where('Nama_Jabatan', 'LIKE', '%Dekan%')->first();
        
        $surat->update([
            'Nomor_Surat_Legalisir' => $request->nomor_surat,
            'Status'                => 'menunggu_ttd_pimpinan',
            'Id_Pejabat'            => $pejabatDekan ? $pejabatDekan->Id_Pejabat : null,
        ]);

        // Notifikasi ke mahasiswa
        $this->sendNotification(
            $surat->Id_User, 
            'Nomor Surat Legalisir', 
            'Nomor surat legalisir Anda: ' . $request->nomor_surat . '. Sedang menunggu tanda tangan Dekan.', 
            $surat->id_no
        );

        return redirect()->back()->with('success', 'Nomor surat berhasil diberikan dan berkas dikirim ke Dekan.');
    }

    /**
     * Memperbarui Progres Berkas (Alur Kerja)
     * ALUR: menunggu_ttd_pimpinan → siap_diambil → selesai
     */
    public function updateProgress(Request $request, $id)
    {
        $this->checkAccess();

        $surat = SuratLegalisir::with('tugasSurat')->findOrFail($id);
        $statusSekarang = $surat->Status;
        $pesanNotif = '';

        switch ($statusSekarang) {
            case 'menunggu_ttd_pimpinan':
                // Dekan sudah TTD, admin kasih stampel manual offline
                $surat->Status = 'siap_diambil';
                $pesanNotif = 'Legalisir selesai. Silakan ambil berkas Anda di loket fakultas.';
                break;

            case 'siap_diambil':
                // Mahasiswa sudah ambil, proses selesai
                $surat->Status = 'selesai';
                if ($surat->tugasSurat) {
                    $surat->tugasSurat->update([
                        'Status_Tugas_Surat'    => 'selesai',
                        'Tanggal_Diselesaikan'  => Carbon::now()
                    ]);
                }
                $pesanNotif = 'Proses legalisir telah selesai sepenuhnya.';
                break;

            default:
                return redirect()->back()->with('error', 'Tidak ada progres lanjutan yang tersedia.');
        }

        $surat->save();

        if ($pesanNotif) {
            $this->sendNotification($surat->Id_User, 'Update Legalisir', $pesanNotif, $surat->id_no);
        }

        return redirect()->back()->with('success', 'Status progres berkas berhasil diperbarui.');
    }

    /**
     * Fungsi pembantu untuk mengirim notifikasi internal
     */
/**
 * Fungsi Kirim Notifikasi ke Mahasiswa
 */
    private function sendNotification($destUser, $title, $message, $idNo)
    {
        // Pastikan 'Tipe_Notifikasi' sesuai dengan pilihan ENUM di database
        // Jika di database adalah 'info', maka jangan gunakan 'Info' (kapital)
        Notifikasi::create([
            'Tipe_Notifikasi' => 'Accepted', // <--- UBAH DARI 'Info' MENJADI 'info'
            'Pesan'           => $message,
            'Dest_user'       => $destUser,
            'Source_User'     => Auth::id(),
            'Is_Read'         => false,
            'Data_Tambahan'   => json_encode(['id' => $idNo, 'type' => 'legalisir'])
        ]);
    }
}