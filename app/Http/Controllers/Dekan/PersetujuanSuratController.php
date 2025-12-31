<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TugasSurat;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Pegawai;
use App\Models\Prodi;
use App\Models\Pejabat;
use App\Helpers\QrCodeHelper;

class PersetujuanSuratController extends Controller
{
    /**
     * Tampilkan halaman index persetujuan dengan card pilihan jenis surat
     */
    public function index()
    {
        $user = Auth::user();

        // Hitung jumlah surat per jenis yang menunggu persetujuan
        $countAktif = TugasSurat::where('Id_Jenis_Surat', 1)
            ->where(function ($q) {
                $q->where('Status', 'menunggu-ttd')
                    ->orWhereHas('suratKetAktif', function ($subQ) {
                        $subQ->where('Status', 'menunggu-ttd');
                    });
            })
            ->count();

        $countMagang = TugasSurat::where('Id_Jenis_Surat', 2)
            ->whereHas('suratMagang', function ($subQ) {
                $subQ->where('Status', 'Diajukan-ke-dekan');
            })
            ->count();

        $countLegalisir = TugasSurat::where('Id_Jenis_Surat', 14) // Legalisir menggunakan Id_Jenis_Surat = 14
            ->whereHas('suratLegalisir') // Hanya yang punya relasi ke Surat_Legalisir
            ->count();

        // TODO: Implementasi counting untuk jenis surat baru
        // Untuk sementara set 0, bisa diimplementasikan setelah tabel database dibuat
        $countCutiDosen = 0; // TODO: Implementasi dengan Id_Jenis_Surat yang sesuai
        $countTidakBeasiswa = 0; // TODO: Implementasi dengan Id_Jenis_Surat yang sesuai
        $countSKFakultas = 0; // TODO: Implementasi dengan Id_Jenis_Surat yang sesuai
        $countSuratTugas = 0; // TODO: Implementasi dengan Id_Jenis_Surat yang sesuai
        $countMBKM = 0; // TODO: Implementasi dengan Id_Jenis_Surat yang sesuai

        // Hitung jumlah SK Dosen yang menunggu persetujuan
        $countSKDosen = \App\Models\AccDekanDosenWali::where('Status', 'Menunggu-Persetujuan-Dekan')->count();

        return view('dekan.persetujuan_index', compact(
            'countAktif',
            'countMagang',
            'countLegalisir',
            'countCutiDosen',
            'countTidakBeasiswa',
            'countSKFakultas',
            'countSuratTugas',
            'countMBKM',
            'countSKDosen'
        ));
    }

    /**
     * Tampilkan daftar surat keterangan aktif yang menunggu persetujuan
     */
    public function listAktif()
    {
        $user = Auth::user();

        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas',
            'suratKetAktif',
            'pemberiTugas.mahasiswa.prodi'
        ])
            ->where('Id_Jenis_Surat', 1)
            ->where(function ($q) {
                $q->where('Status', 'menunggu-ttd')
                    ->orWhereHas('suratKetAktif', function ($subQ) {
                        $subQ->where('Status', 'menunggu-ttd');
                    });
            })
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar surat pengantar magang yang menunggu persetujuan
     */
    public function listMagang()
    {
        $user = Auth::user();

        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas',
            'suratMagang',
            'pemberiTugas.mahasiswa.prodi'
        ])
            ->where('Id_Jenis_Surat', 2)
            ->whereHas('suratMagang', function ($subQ) {
                $subQ->where('Status', 'Diajukan-ke-dekan');
            })
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar legalisir yang menunggu persetujuan
     */
    public function listLegalisir()
    {
        $user = Auth::user();

        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas',
            'suratLegalisir.user.role', // Eager load user (pemohon) dari Surat_Legalisir
            'pemberiTugas.mahasiswa.prodi'
        ])
            ->where('Id_Jenis_Surat', 14) // Legalisir menggunakan Id_Jenis_Surat = 14
            ->whereHas('suratLegalisir') // Hanya yang punya relasi ke Surat_Legalisir
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar surat cuti dosen yang menunggu persetujuan
     * TODO: Implementasi lengkap setelah tabel database dibuat
     */
    public function listCutiDosen()
    {
        $user = Auth::user();

        // TODO: Ganti dengan query yang sesuai setelah tabel surat_cuti_dosen dibuat
        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas'
        ])
            ->where('Id_Jenis_Surat', 99) // TODO: Ganti dengan Id_Jenis_Surat yang benar
            ->where('Status', 'menunggu-ttd')
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar surat keterangan tidak menerima beasiswa yang menunggu persetujuan
     * TODO: Implementasi lengkap setelah tabel database dibuat
     */
    public function listTidakBeasiswa()
    {
        $user = Auth::user();

        // TODO: Ganti dengan query yang sesuai
        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas'
        ])
            ->where('Id_Jenis_Surat', 99) // TODO: Ganti dengan Id_Jenis_Surat yang benar
            ->where('Status', 'menunggu-ttd')
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar SK Fakultas yang menunggu persetujuan
     * TODO: Implementasi lengkap setelah tabel database dibuat
     */
    public function listSKFakultas()
    {
        $user = Auth::user();

        // TODO: Ganti dengan query yang sesuai
        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas'
        ])
            ->where('Id_Jenis_Surat', 99) // TODO: Ganti dengan Id_Jenis_Surat yang benar
            ->where('Status', 'menunggu-ttd')
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar Surat Tugas yang menunggu persetujuan
     * TODO: Implementasi lengkap setelah tabel database dibuat
     */
    public function listSuratTugas()
    {
        $user = Auth::user();

        // TODO: Ganti dengan query yang sesuai
        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas'
        ])
            ->where('Id_Jenis_Surat', 99) // TODO: Ganti dengan Id_Jenis_Surat yang benar
            ->where('Status', 'menunggu-ttd')
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan daftar Surat Rekomendasi MBKM yang menunggu persetujuan
     * TODO: Implementasi lengkap setelah tabel database dibuat
     */
    public function listMBKM()
    {
        $user = Auth::user();

        // TODO: Ganti dengan query yang sesuai
        $daftarSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.role',
            'penerimaTugas'
        ])
            ->where('Id_Jenis_Surat', 99) // TODO: Ganti dengan Id_Jenis_Surat yang benar
            ->where('Status', 'menunggu-ttd')
            ->where('Id_Penerima_Tugas_Surat', $user->Id_User)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('dekan.persetujuan_surat', compact('daftarSurat'));
    }

    /**
     * Tampilkan halaman pilihan SK Dosen dengan beberapa card jenis SK
     */
    public function listSKDosen()
    {
        $user = Auth::user();

        // Hitung jumlah per jenis SK Dosen yang menunggu persetujuan
        $skDosenWaliCount = \App\Models\AccDekanDosenWali::where('Status', 'Menunggu-Persetujuan-Dekan')
            ->count();

        $skDosenWaliTotal = \App\Models\AccDekanDosenWali::count();

        // TODO: Implementasi counting untuk jenis SK lainnya
        $skBebanMengajarCount = 0;
        $skPembimbingSkripsiCount = 0;
        $skPengujiSkripsiCount = 0;

        // Hitung total SK Dosen untuk badge di menu utama
        $countSKDosen = $skDosenWaliCount + $skBebanMengajarCount + $skPembimbingSkripsiCount + $skPengujiSkripsiCount;

        return view('dekan.sk_dosen', compact(
            'skDosenWaliCount',
            'skDosenWaliTotal',
            'skBebanMengajarCount',
            'skPembimbingSkripsiCount',
            'skPengujiSkripsiCount'
        ));
    }

    /**
     * Tampilkan daftar SK Dosen Wali yang menunggu persetujuan Dekan
     */
    public function listSKDosenWali()
    {
        $user = Auth::user();

        // Ambil SK Dosen Wali dari tabel Acc_SK_Dosen_Wali yang menunggu persetujuan Dekan
        $daftarSK = \App\Models\AccDekanDosenWali::with([
            'reqSKDosenWali.prodi',
            'reqSKDosenWali.kaprodi.user'
        ])
            ->where('Status', 'Menunggu-Persetujuan-Dekan')
            ->orderBy('Tanggal-Pengajuan', 'desc')
            ->get();

        return view('dekan.sk_dosen_wali', compact('daftarSK'));
    }

    /**
     * Tampilkan detail SK Dosen Wali untuk preview (JSON)
     */
    public function showSKDosenWaliDetail($id)
    {
        try {
            $sk = \App\Models\AccDekanDosenWali::findOrFail($id);

            // Ambil data dekan dengan try-catch terpisah
            $dekanName = 'Nama Dekan';
            $dekanNip = '1234567890';

            try {
                // Cari Dosen yang memiliki Id_Pejabat = 1 (Dekan)
                $dekanDosen = Dosen::where('Id_Pejabat', 1)
                    ->with('user')
                    ->first();

                if ($dekanDosen) {
                    // Prioritas: ambil dari Nama_Dosen (lebih lengkap dengan gelar)
                    if ($dekanDosen->Nama_Dosen) {
                        $dekanName = $dekanDosen->Nama_Dosen;
                    } elseif ($dekanDosen->user) {
                        $dekanName = $dekanDosen->user->name;
                    }

                    if ($dekanDosen->NIP) {
                        $dekanNip = $dekanDosen->NIP;
                    }
                }
            } catch (\Exception $dekanError) {
                \Log::warning('Could not load Dekan data: ' . $dekanError->getMessage());
                // Continue dengan default values
            }

            return response()->json([
                'success' => true,
                'sk' => $sk,
                'dekanName' => $dekanName,
                'dekanNip' => $dekanNip,
                'debug' => [
                    'sk_id' => $sk->No,
                    'semester' => $sk->Semester,
                    'tahun_akademik' => $sk->Tahun_Akademik
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'SK Dosen Wali tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error in showSKDosenWaliDetail: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail SK: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Approve SK Dosen Wali dan generate QR Code untuk tanda tangan
     */
    public function approveSKDosenWali($id)
    {
        try {
            $sk = \App\Models\AccDekanDosenWali::findOrFail($id);

            // Generate QR Code untuk verifikasi
            $qrContent = url("/verify-sk-dosen-wali/{$sk->No}");
            $qrPath = QrCodeHelper::generate($qrContent, 200);

            if (!$qrPath) {
                throw new \Exception('Gagal generate QR Code');
            }

            // Get URL untuk ditampilkan di preview
            $qrUrl = asset('storage/' . $qrPath);

            // Update status dan simpan QR code path
            $sk->Status = 'Disetujui Dekan';
            $sk->QR_Code = $qrPath; // Simpan path relatif ke database
            $sk->Tanggal_Persetujuan_Dekan = now();
            $sk->Id_Dekan = Auth::user()->Id_User;
            $sk->save();

            return response()->json([
                'success' => true,
                'message' => 'SK Dosen Wali berhasil disetujui dan ditandatangani',
                'qr_code' => $qrUrl  // Return URL untuk ditampilkan di HTML
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui SK: ' . $e->getMessage()
            ], 500);
        }
    }
}
