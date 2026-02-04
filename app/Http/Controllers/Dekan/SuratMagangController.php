<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use App\Models\SuratMagang;
use App\Models\Notifikasi;
use App\Models\Mahasiswa;
use App\Services\WahaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SuratMagangController extends Controller
{
    protected $waha;

    public function __construct(WahaService $waha)
    {
        $this->waha = $waha;
    }
    public function index()
    {
        // Ambil surat magang yang statusnya "Diajukan-ke-dekan" (Menunggu Persetujuan)
        $daftarSurat = SuratMagang::with([
            'pemberiTugas.mahasiswa.prodi',
            'koordinator'
        ])
            ->where('Status', 'Diajukan-ke-dekan')
            ->orderBy('id_no', 'desc')
            ->get();

        // Ambil riwayat surat yang sudah disetujui (Status = Success)
        $riwayatSurat = SuratMagang::with([
            'pemberiTugas.mahasiswa.prodi',
            'koordinator'
        ])
            ->where('Status', 'Success')
            ->where('Acc_Dekan', true)
            ->orderBy('id_no', 'desc')
            ->get();

        return view('dekan.surat_magang.index', compact('daftarSurat', 'riwayatSurat'));
    }

    public function show($id)
    {
        $surat = SuratMagang::with([
            'pemberiTugas.mahasiswa.prodi.fakultas',
            'koordinator'
        ])->findOrFail($id);

        // Decode JSON data
        $dataMahasiswa = is_array($surat->Data_Mahasiswa)
            ? $surat->Data_Mahasiswa
            : json_decode($surat->Data_Mahasiswa, true);

        $dataDosenPembimbing = is_array($surat->Data_Dosen_pembiming)
            ? $surat->Data_Dosen_pembiming
            : json_decode($surat->Data_Dosen_pembiming, true);

        return view('dekan.surat_magang.detail', compact('surat', 'dataMahasiswa', 'dataDosenPembimbing'));
    }

    public function approve($id)
    {
        $surat = SuratMagang::findOrFail($id);

        // Get data Dekan yang login
        $dekan = Auth::user();

        // Update status
        $surat->Acc_Dekan = 1;
        $surat->Status = 'Success';

        // Simpan ID Dekan (INTEGER) bukan nama (STRING) karena kolom Nama_Dekan adalah foreign key
        if ($dekan->dosen) {
            $surat->Nama_Dekan = $dekan->dosen->Id_Dosen; // Simpan ID, bukan nama
            $surat->Nip_Dekan = $dekan->dosen->NIP;
        } elseif ($dekan->pegawaiFakultas) {
            // Jika pegawai fakultas, kita tidak bisa menyimpan ke Nama_Dekan karena foreign key ke Dosen
            // Skip atau set null
            $surat->Nama_Dekan = null;
            $surat->Nip_Dekan = $dekan->pegawaiFakultas->Nip_Pegawai;
        } else {
            $surat->Nama_Dekan = null;
            $surat->Nip_Dekan = '-';
        }

        // Generate QR Code untuk Dekan menggunakan QrCodeHelper
        $qrContent = url("/verify-surat-magang/{$surat->id_no}");
        $qrCodePath = \App\Helpers\QrCodeHelper::generateAndGetPath($qrContent, 10);

        if ($qrCodePath) {
            $surat->Qr_code_dekan = $qrCodePath;
        }

        $surat->save();

        // Update Status_KP mahasiswa menjadi "Sedang_Melaksanakan"
        $dataMahasiswa = is_array($surat->Data_Mahasiswa)
            ? $surat->Data_Mahasiswa
            : json_decode($surat->Data_Mahasiswa, true);

        $notificationsSent = 0;
        if ($dataMahasiswa && is_array($dataMahasiswa)) {
            foreach ($dataMahasiswa as $mhs) {
                if (isset($mhs['nim'])) {
                    // Update Status_KP
                    Mahasiswa::where('NIM', $mhs['nim'])
                        ->update(['Status_KP' => 'Sedang_Melaksanakan']);

                    // Kirim notifikasi ke setiap mahasiswa yang mengajukan
                    $mahasiswa = Mahasiswa::with('user')->where('NIM', $mhs['nim'])->first();
                    if ($mahasiswa && $mahasiswa->Id_User) {
                        // Notifikasi internal
                        Notifikasi::create([
                            'Dest_user' => $mahasiswa->Id_User,
                            'Source_User' => Auth::id(),
                            'Tipe_Notifikasi' => 'Accepted',
                            'Pesan' => 'Surat pengantar magang Anda dengan nomor ' . ($surat->Nomor_Surat ?? 'N/A') . ' telah disetujui dan ditandatangani oleh Dekan. Anda dapat melihat dan mengunduh surat di halaman riwayat surat.',
                            'Is_Read' => false
                        ]);

                        // Kirim notifikasi WhatsApp
                        if ($mahasiswa->user && $mahasiswa->user->No_WA) {
                            try {
                                $pesanWA = "✅ *SURAT MAGANG DISETUJUI*\n\nHalo {$mahasiswa->Nama_Mahasiswa},\n\nSurat pengantar magang Anda telah disetujui dan ditandatangani oleh Dekan.\n\n*Nomor Surat:* " . ($surat->Nomor_Surat ?? '-') . "\n*Instansi:* {$surat->Nama_Instansi}\n\nSilakan unduh surat Anda di halaman riwayat surat.\n\n_Sistem SIFAKULTAS_";
                                $this->waha->sendMessage($mahasiswa->user->No_WA, $pesanWA);
                                $notificationsSent++;
                                Log::info('WhatsApp notification sent to mahasiswa', [
                                    'nim' => $mhs['nim'],
                                    'nama' => $mahasiswa->Nama_Mahasiswa,
                                    'no_wa' => $mahasiswa->user->No_WA
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Failed to send WhatsApp to mahasiswa', [
                                    'nim' => $mhs['nim'],
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                }
            }
        }

        Log::info('Surat Magang approved', [
            'surat_id' => $surat->id_no,
            'nomor_surat' => $surat->Nomor_Surat,
            'wa_notifications_sent' => $notificationsSent
        ]);

        return redirect()->route('dekan.surat_magang.index')
            ->with('success', 'Surat magang berhasil disetujui dan ditandatangani. Notifikasi telah dikirim ke ' . $notificationsSent . ' mahasiswa.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'komentar' => 'required|string|min:10'
        ]);

        $surat = SuratMagang::findOrFail($id);
        $surat->Status = 'Ditolak';
        $surat->Acc_Dekan = 0;
        $surat->Komentar = $request->komentar;
        $surat->save();

        return redirect()->route('dekan.surat_magang.index')
            ->with('success', 'Surat magang telah ditolak.');
    }

    public function download($id)
    {
        $surat = SuratMagang::findOrFail($id);

        if (!$surat->Dokumen_Proposal) {
            return back()->with('error', 'Dokumen proposal tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $surat->Dokumen_Proposal);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }
}