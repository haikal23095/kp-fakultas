<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TugasSurat;
use App\Models\SuratVerification;
use App\Models\SuratKetAktif;
use App\Models\SuratMagang;
use App\Models\SuratLegalisir;
use App\Models\SuratTidakBeasiswa;

class RiwayatSuratController extends Controller
{
    /**
     * Tampilkan pilihan jenis surat
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type');

        // Hitung jumlah surat per jenis (sesuai JenisSuratSeeder)
        $countAktif = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 1)->count(); // Surat Keterangan Aktif
        $countMagang = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 2)->count(); // Surat Pengantar KP/Magang
        $countLegalisir = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 3)->count(); // Legalisir Online
        $countMobilDinas = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 4)->count(); // Surat Mobil Dinas
        $countTidakBeasiswa = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 6)->count(); // Surat Keterangan Tidak Menerima Beasiswa
        $countDispensasi = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 7)->count(); // Surat Dispensasi
        $countBerkelakuanBaik = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 8)->count(); // Surat Keterangan Berkelakuan Baik
        $countPeminjamanGedung = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 10)->count(); // Peminjaman Gedung
        $countLembur = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 11)->count(); // Surat Lembur
        
        // Set nilai 0 untuk jenis surat yang belum ada di seeder
        $countCekPlagiasi = 0; // Belum ada di seeder
        $countSuratTugas = 0; // Belum ada di seeder
        $countMBKM = 0; // Belum ada di seeder
        $countPeminjamanRuang = 0; // Belum ada di seeder

        return view('mahasiswa.riwayat', [
            'countAktif' => $countAktif,
            'countMagang' => $countMagang,
            'countLegalisir' => $countLegalisir,
            'countMobilDinas' => $countMobilDinas,
            'countTidakBeasiswa' => $countTidakBeasiswa,
            'countCekPlagiasi' => $countCekPlagiasi,
            'countDispensasi' => $countDispensasi,
            'countBerkelakuanBaik' => $countBerkelakuanBaik,
            'countSuratTugas' => $countSuratTugas,
            'countMBKM' => $countMBKM,
            'countPeminjamanGedung' => $countPeminjamanGedung,
            'countLembur' => $countLembur,
            'countPeminjamanRuang' => $countPeminjamanRuang,
        ]);
    }

    /**
     * Tampilkan riwayat surat keterangan aktif
     */
    public function riwayatAktif()
    {
        $user = Auth::user();

        // Query surat keterangan aktif dengan relasi ke Surat_Ket_Aktif
        $riwayatSurat = TugasSurat::with([
            'jenisSurat',
            'penerimaTugas',
            'suratKetAktif', // Relasi ke tabel Surat_Ket_Aktif
            'verification'
        ])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 1) // ID untuk Surat Keterangan Aktif
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_aktif', [
            'riwayatSurat' => $riwayatSurat
        ]);
    }

    /**
     * Tampilkan riwayat surat pengantar magang
     */
    public function riwayatMagang()
    {
        $user = Auth::user();

        // Query surat magang dengan relasi ke Surat_Magang
        $riwayatSurat = TugasSurat::with([
            'jenisSurat',
            'penerimaTugas',
            'suratMagang', // Relasi ke tabel Surat_Magang
            'verification'
        ])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 2) // ID untuk Surat Pengantar Magang
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_magang', [
            'riwayatSurat' => $riwayatSurat
        ]);
    }

    /**
     * Tampilkan riwayat pengajuan legalisir
     */
    public function riwayatLegalisir()
    {
        $user = Auth::user();

        // Cari ID Jenis Surat untuk Legalisir
        $jenisSuratLegalisir = \App\Models\JenisSurat::where('Nama_Surat', 'Surat Legalisir')->first();
        
        if (!$jenisSuratLegalisir) {
            return view('mahasiswa.riwayat_legalisir', [
                'riwayatSurat' => collect([])
            ]);
        }

        // Query surat legalisir dengan relasi ke Surat_Legalisir
        $riwayatSurat = TugasSurat::with([
            'jenisSurat',
            'penerimaTugas',
            'suratLegalisir', // Relasi ke tabel Surat_Legalisir
        ])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', $jenisSuratLegalisir->Id_Jenis_Surat)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_legalisir', [
            'riwayatSurat' => $riwayatSurat
        ]);
    }

    /**
     * Preview PDF surat dengan QR Code di browser
     */
    public function downloadSurat($id)
    {
        $user = Auth::user();

        // Ambil surat dengan verifikasi QR
        $tugasSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.mahasiswa.prodi',
            'penerimaTugas',
            'suratMagang',
            'verification.penandatangan.pegawai', // Untuk ambil QR Code + NIP (Pegawai)
            'verification.penandatangan.dosen'    // Untuk ambil QR Code + NIP (Dosen)
        ])
            ->where('Id_Tugas_Surat', $id)
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->firstOrFail();

        // Cek apakah surat sudah selesai
        $statusLower = strtolower(trim($tugasSurat->Status));
        if ($statusLower !== 'selesai' && $statusLower !== 'telah ditandatangani dekan') {
            return redirect()->route('mahasiswa.riwayat')
                ->with('error', 'Surat belum dapat diunduh. Status: ' . $tugasSurat->Status);
        }

        // Get QR Code URL dari database (sudah di-generate saat approve)
        $qrImageUrl = null;
        if ($tugasSurat->verification && $tugasSurat->verification->qr_path) {
            // qr_path berisi URL Google Charts API
            $qrImageUrl = $tugasSurat->verification->qr_path;
        }

        // Render PDF view
        return view('mahasiswa.pdf.surat_dengan_qr', [
            'surat' => $tugasSurat,
            'mahasiswa' => $tugasSurat->pemberiTugas->mahasiswa,
            'jenisSurat' => $tugasSurat->jenisSurat,
            'verification' => $tugasSurat->verification,
            'qrImageUrl' => $qrImageUrl,
            'mode' => 'preview' // Mode preview untuk browser
        ]);
    }

    /**
     * Preview PDF Surat Pengantar (Signed by Kaprodi)
     */
    public function downloadPengantar($id)
    {
        $user = Auth::user();

        // Ambil surat
        $tugasSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.mahasiswa.prodi',
            'suratMagang.koordinator', // Load Kaprodi info
            'suratMagang.dekan' // Load Dekan info
        ])
            ->where('Id_Tugas_Surat', $id)
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->firstOrFail();

        // Cek apakah surat magang ada
        if (!$tugasSurat->suratMagang) {
            return redirect()->route('mahasiswa.riwayat')
                ->with('error', 'Bukan surat magang.');
        }

        // Cek apakah sudah disetujui (koordinator atau dekan)
        // Jika belum ACC Koordinator tapi sudah ACC Dekan (Success), tetap bisa download
        if (!$tugasSurat->suratMagang->Acc_Koordinator && !$tugasSurat->suratMagang->Acc_Dekan) {
            return redirect()->route('mahasiswa.riwayat')
                ->with('error', 'Surat Pengantar belum disetujui.');
        }

        // Render PDF view
        return view('mahasiswa.pdf.surat_pengantar', [
            'surat' => $tugasSurat,
            'magang' => $tugasSurat->suratMagang,
            'mahasiswa' => $tugasSurat->pemberiTugas->mahasiswa,
            'koordinator' => $tugasSurat->suratMagang->koordinator,
            'mode' => 'preview'
        ]);
    }

    /**
     * Helper untuk menampilkan riwayat generic
     */
    private function getGenericRiwayat($idJenisSurat, $title)
    {
        $user = Auth::user();
        $riwayatSurat = TugasSurat::with(['jenisSurat'])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', $idJenisSurat)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_generic', [
            'riwayatSurat' => $riwayatSurat,
            'title' => $title
        ]);
    }

    public function riwayatMobilDinas() { return $this->getGenericRiwayat(4, 'Riwayat Peminjaman Mobil Dinas'); }
    
    public function riwayatTidakBeasiswa() { 
        $user = Auth::user();
        $riwayatSurat = TugasSurat::with(['jenisSurat', 'suratTidakBeasiswa'])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 6)
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_tidak_beasiswa', [
            'riwayatSurat' => $riwayatSurat,
            'title' => 'Riwayat Surat Keterangan Tidak Menerima Beasiswa'
        ]);
    }
    
    // public function riwayatCekPlagiasi() { return $this->getGenericRiwayat(0, 'Riwayat Cek Plagiasi'); } // Belum ada di seeder
    public function riwayatDispensasi() { return $this->getGenericRiwayat(7, 'Riwayat Surat Dispensasi'); }
    
    public function riwayatBerkelakuanBaik() { 
        $user = Auth::user();
        
        // Query dengan relasi suratKelakuanBaik dan verification
        $riwayatSurat = TugasSurat::with(['jenisSurat', 'suratKelakuanBaik', 'verification'])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->whereHas('jenisSurat', function($q) {
                $q->where('Nama_Surat', 'LIKE', '%Berkelakuan Baik%');
            })
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_berkelakuan_baik', [
            'riwayatSurat' => $riwayatSurat,
            'title' => 'Riwayat Surat Keterangan Berkelakuan Baik'
        ]);
    }
    
    // Method di bawah ini untuk jenis surat yang belum ada di seeder - sementara return kosong
    public function riwayatCekPlagiasi() { 
        return view('mahasiswa.riwayat_generic', [
            'riwayatSurat' => collect([]),
            'title' => 'Riwayat Cek Plagiasi'
        ])->with('info', 'Jenis surat ini belum tersedia di sistem.');
    }
    
    public function riwayatSuratTugas() { 
        return view('mahasiswa.riwayat_generic', [
            'riwayatSurat' => collect([]),
            'title' => 'Riwayat Surat Tugas'
        ])->with('info', 'Jenis surat ini belum tersedia di sistem.');
    }
    
    public function riwayatMBKM() { 
        return view('mahasiswa.riwayat_generic', [
            'riwayatSurat' => collect([]),
            'title' => 'Riwayat Surat Rekomendasi MBKM'
        ])->with('info', 'Jenis surat ini belum tersedia di sistem.');
    }
    
    public function riwayatPeminjamanRuang() { 
        return view('mahasiswa.riwayat_generic', [
            'riwayatSurat' => collect([]),
            'title' => 'Riwayat Peminjaman Ruang'
        ])->with('info', 'Jenis surat ini belum tersedia di sistem.');
    }
    
    public function riwayatPeminjamanGedung() { return $this->getGenericRiwayat(10, 'Riwayat Peminjaman Gedung'); }
    public function riwayatLembur() { return $this->getGenericRiwayat(11, 'Riwayat Surat Perintah Lembur'); }
}
