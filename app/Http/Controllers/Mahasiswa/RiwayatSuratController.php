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

        // Hitung jumlah surat per jenis
        $countAktif = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 3)->count();

        // Ambil jumlah magang langsung dari tabel Surat_Magang via relasi TugasSurat
        $countMagang = SuratMagang::whereHas('tugasSurat', function ($query) use ($user) {
            $query->where('Id_Pemberi_Tugas_Surat', $user->Id_User);
        })->count();

        // Cari ID Jenis Surat untuk Legalisir
        $jenisSuratLegalisir = \App\Models\JenisSurat::where('Nama_Surat', 'Surat Legalisir')->first();
        $countLegalisir = 0;
        if ($jenisSuratLegalisir) {
            $countLegalisir = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
                ->where('Id_Jenis_Surat', $jenisSuratLegalisir->Id_Jenis_Surat)
                ->count();
        }

        // Hitung jenis surat lainnya
        $countMobilDinas = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 15)->count();
        $countTidakBeasiswa = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 6)
            ->whereHas('suratTidakBeasiswa')
            ->count();
        $countCekPlagiasi = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 17)->count();
        $countDispensasi = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 18)->count();
        $countBerkelakuanBaik = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->whereHas('jenisSurat', function($q) {
                $q->where('Nama_Surat', 'LIKE', '%Berkelakuan Baik%');
            })
            ->count();
        $countSuratTugas = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 20)->count();
        $countMBKM = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 21)->count();
        $countPeminjamanGedung = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 22)->count();
        $countLembur = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 23)->count();
        $countPeminjamanRuang = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)->where('Id_Jenis_Surat', 24)->count();

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

        // Query langsung dari Surat_Magang dan filter berdasarkan mahasiswa
        $riwayatSurat = SuratMagang::with([
            'tugasSurat.jenisSurat',
            'tugasSurat.penerimaTugas',
            'tugasSurat.verification',
            'koordinator'
        ])
            ->whereHas('tugasSurat', function ($query) use ($user) {
                $query->where('Id_Pemberi_Tugas_Surat', $user->Id_User);
            })
            ->orderBy('id_no', 'desc')
            ->get();

        return view('mahasiswa.magang.riwayat_magang', [
            'riwayatSurat' => $riwayatSurat
        ]);
    }

    /**
     * Tampilkan riwayat pengajuan legalisir
     */
    public function riwayatLegalisir()
    {
        $user = Auth::user();

        // Ambil data legalisir langsung dari tabel Surat_Legalisir (tidak perlu Jenis_Surat)
        $daftarRiwayat = SuratLegalisir::with(['tugasSurat', 'user.mahasiswa.prodi'])
            ->where('Id_User', $user->Id_User)
            ->orderBy('id_no', 'desc')
            ->get();

        return view('mahasiswa.riwayat_legalisir', [
            'daftarRiwayat' => $daftarRiwayat
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
     * Preview Form Surat Pengantar (TTD Mahasiswa & QR Kaprodi)
     */
    public function previewFormPengantar($id)
    {
        $user = Auth::user();

        // Ambil surat
        $tugasSurat = TugasSurat::with([
            'jenisSurat',
            'pemberiTugas.mahasiswa.prodi.jurusan',
            'suratMagang.koordinator'
        ])
            ->where('Id_Tugas_Surat', $id)
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->firstOrFail();

        // Cek apakah surat magang ada
        if (!$tugasSurat->suratMagang) {
            return redirect()->route('mahasiswa.riwayat')
                ->with('error', 'Bukan surat magang.');
        }

        // Render PDF view form pengantar
        return view('mahasiswa.pdf.form_pengantar', [
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
        $riwayatSurat = TugasSurat::with(['jenisSurat', 'suratTidakBeasiswa', 'verification'])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 6)
            ->whereHas('suratTidakBeasiswa')
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
