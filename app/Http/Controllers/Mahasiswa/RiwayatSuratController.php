<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TugasSurat;
use App\Models\SuratVerification;
use App\Models\SuratKetAktif;
use App\Models\SuratMagang;
use App\Models\SuratLegalisir;
use App\Models\SuratTidakBeasiswa;
use App\Models\User;
use Carbon\Carbon;

class RiwayatSuratController extends Controller
{
    /**
     * Tampilkan pilihan jenis surat
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->query('type');

        // Hitung jumlah surat per jenis (sesuai dengan method detail masing-masing)
        
        // Count Surat Keterangan Aktif (Id_Jenis_Surat = 1, sesuai riwayatAktif())
        $countAktif = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 1)
            ->count();

        // Count Magang - langsung dari tabel Surat_Magang (sesuai riwayatMagang())
        $countMagang = SuratMagang::whereHas('tugasSurat', function ($query) use ($user) {
            $query->where('Id_Pemberi_Tugas_Surat', $user->Id_User);
        })->count();

        // Count Legalisir - langsung dari tabel Surat_Legalisir (sesuai riwayatLegalisir())
        $countLegalisir = SuratLegalisir::where('Id_User', $user->Id_User)->count();

        // Count Peminjaman Mobil Dinas (Id_Jenis_Surat = 4)
        $countMobilDinas = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 4)
            ->count();
        
        // Count Tidak Menerima Beasiswa (sesuai riwayatTidakBeasiswa())
        $countTidakBeasiswa = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->where('Id_Jenis_Surat', 6)
            ->whereHas('suratTidakBeasiswa')
            ->count();
        
        // Count Dispensasi (sesuai riwayatDispensasi())
        $countDispensasi = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->whereHas('suratDispensasi')
            ->count();
        
        // Count Berkelakuan Baik (sesuai riwayatBerkelakuanBaik())
        $countBerkelakuanBaik = TugasSurat::where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->whereHas('jenisSurat', function($q) {
                $q->where('Nama_Surat', 'LIKE', '%Berkelakuan Baik%');
            })
            ->count();
        
        // Count Surat Tugas (belum tersedia - return 0)
        $countSuratTugas = 0;
        
        // Count yang sudah dihapus dari view (tidak perlu lagi tapi tetap ada untuk backward compatibility)
        $countCekPlagiasi = 0;
        $countMBKM = 0;
        $countPeminjamanGedung = 0;
        $countLembur = 0;
        $countPeminjamanRuang = 0;

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
    public function riwayatDispensasi()
    {
        $user = Auth::user();
        
        // Query dengan relasi suratDispensasi, verification, dan accWadek3
        $riwayatSurat = TugasSurat::with([
            'jenisSurat', 
            'suratDispensasi.accWadek3.dosen',
            'suratDispensasi.accWadek3.pegawaiFakultas',
            'verification'
        ])
            ->where('Id_Pemberi_Tugas_Surat', $user->Id_User)
            ->whereHas('suratDispensasi')
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();

        return view('mahasiswa.riwayat_dispensasi', [
            'riwayatSurat' => $riwayatSurat,
            'title' => 'Riwayat Surat Dispensasi'
        ]);
    }
    
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

    /**
     * Download PDF Surat Dispensasi yang sudah di-ACC Wadek3
     */
    public function downloadDispensasi($id)
    {
        $tugasSurat = TugasSurat::with(['suratDispensasi', 'verification'])->findOrFail($id);
        $surat = $tugasSurat->suratDispensasi;

        // Validasi ownership
        if ($tugasSurat->Id_Pemberi_Tugas_Surat !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh surat ini.');
        }

        // Cek apakah data dispensasi ada
        if (!$surat) {
            return redirect()->back()->with('error', 'Data surat dispensasi tidak ditemukan.');
        }

        // Cek apakah surat sudah selesai (sudah di-ACC Wadek3)
        if (!$surat->acc_wadek3_by) {
            return redirect()->back()->with('error', 'Surat belum disetujui oleh Wadek 3.');
        }

        // Cek apakah file PDF sudah ada di storage
        if ($surat->file_surat_selesai && Storage::disk('public')->exists($surat->file_surat_selesai)) {
            $filePath = storage_path('app/public/' . $surat->file_surat_selesai);
            return response()->download($filePath, 'Surat_Dispensasi_' . ($surat->nomor_surat ?? 'draft') . '.pdf');
        }

        // Jika file tidak ada, generate PDF on-the-fly dari data yang ada
        try {
            \Log::info('Generating PDF on-the-fly for dispensasi', ['id' => $id]);
            
            // Get data untuk PDF
            $mahasiswa = Auth::user()->mahasiswa;
            $verification = $tugasSurat->verification;
            
            if (!$verification || !$verification->qr_path) {
                return redirect()->back()->with('error', 'Data QR Code tidak ditemukan. Harap hubungi admin.');
            }
            
            $penandatangan = User::find($verification->signed_by_user_id);
            if (!$penandatangan) {
                return redirect()->back()->with('error', 'Data penandatangan tidak ditemukan.');
            }
            
            // QR Code path
            $qrAbsolutePath = storage_path('app/public/' . $verification->qr_path);
            
            // Data untuk PDF
            $data = [
                'nomor_surat' => $surat->nomor_surat,
                'tanggal_surat' => Carbon::parse($surat->acc_wadek3_at ?? now())->translatedFormat('d F Y'),
                'nama_mahasiswa' => $mahasiswa->Nama_Mahasiswa,
                'nim' => $mahasiswa->NIM,
                'prodi' => $mahasiswa->prodi->Nama_Prodi ?? '-',
                'angkatan' => $mahasiswa->Angkatan ?? '-',
                'nama_kegiatan' => $surat->nama_kegiatan,
                'instansi_penyelenggara' => $surat->instansi_penyelenggara ?? '-',
                'tempat_pelaksanaan' => $surat->tempat_pelaksanaan ?? '-',
                'tanggal_mulai' => Carbon::parse($surat->tanggal_mulai)->translatedFormat('d F Y'),
                'tanggal_selesai' => Carbon::parse($surat->tanggal_selesai)->translatedFormat('d F Y'),
                'logo_path' => public_path('images/logo_unijoyo.png'),
                'qr_code_path' => $qrAbsolutePath,
                'penandatangan_nama' => $penandatangan->Name_User,
                'penandatangan_nip' => $penandatangan->dosen->NIP ?? $penandatangan->pegawaiFakultas->NIP ?? '-',
            ];
            
            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('wadek3.kemahasiswaan.pdf-dispensasi', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);
            
            // Stream PDF langsung ke browser (seperti print/Ctrl+P)
            return $pdf->stream('Surat_Dispensasi_' . $mahasiswa->NIM . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Error generating PDF on-the-fly', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }
}
