<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use App\Models\SuratMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class SuratMagangController extends Controller
{
    public function index()
    {
        // Ambil surat magang yang statusnya "Diajukan-ke-dekan"
        $daftarSurat = SuratMagang::with([
            'tugasSurat.pemberiTugas.mahasiswa.prodi',
            'tugasSurat.jenisSurat',
            'koordinator'
        ])
            ->where('Status', 'Diajukan-ke-dekan')
            ->orderBy('id_no', 'desc')
            ->get();

        return view('dekan.surat_magang.index', compact('daftarSurat'));
    }

    public function show($id)
    {
        $surat = SuratMagang::with([
            'tugasSurat.pemberiTugas.mahasiswa.prodi.fakultas',
            'tugasSurat.jenisSurat',
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

        // Simpan Nama dan NIP Dekan
        if ($dekan->dosen) {
            $surat->Nama_Dekan = $dekan->dosen->Nama_Dosen;
            $surat->Nip_Dekan = $dekan->dosen->NIP;
        } elseif ($dekan->pegawaiFakultas) {
            $surat->Nama_Dekan = $dekan->pegawaiFakultas->Nama_Pegawai;
            $surat->Nip_Dekan = $dekan->pegawaiFakultas->Nip_Pegawai;
        } else {
            $surat->Nama_Dekan = $dekan->Name_User;
            $surat->Nip_Dekan = '-';
        }

        // Generate QR Code untuk Dekan
        $qrContent = url("/verify-surat-magang/{$surat->id_no}");
        $qrFileName = 'qr_codes/dekan_' . $surat->id_no . '_' . time() . '.png';
        $qrPath = storage_path('app/public/' . $qrFileName);

        // Pastikan direktori ada
        if (!file_exists(dirname($qrPath))) {
            mkdir(dirname($qrPath), 0777, true);
        }

        // Generate QR Code dengan Endroid v6.0 (readonly constructor)
        $qrCode = new QrCode(
            data: $qrContent,
            size: 200,
            margin: 10
        );
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $result->saveToFile($qrPath);

        $surat->Qr_code_dekan = $qrFileName;
        $surat->save();

        return redirect()->route('dekan.surat_magang.index')
            ->with('success', 'Surat magang berhasil disetujui dan ditandatangani.');
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
