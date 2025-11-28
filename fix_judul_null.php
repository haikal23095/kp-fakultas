<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TugasSurat;
use App\Models\SuratMagang;
use App\Models\SuratKetAktif;
use Illuminate\Support\Facades\DB;

echo "Fixing null Judul_Tugas_Surat...\n";

$tugasSurats = TugasSurat::whereNull('Judul_Tugas_Surat')->orWhere('Judul_Tugas_Surat', '')->get();

foreach ($tugasSurats as $tugas) {
    $judul = "Pengajuan Surat";
    
    if ($tugas->jenisSurat) {
        $judul = $tugas->jenisSurat->Nama_Surat;
    }

    // Cek jika ini surat magang
    $magang = SuratMagang::where('Id_Tugas_Surat', $tugas->Id_Tugas_Surat)->first();
    if ($magang) {
        if (!empty($magang->Nama_Instansi)) {
            $judul .= " ke " . $magang->Nama_Instansi;
        }
    }

    // Cek jika ini surat aktif
    $aktif = SuratKetAktif::where('Id_Tugas_Surat', $tugas->Id_Tugas_Surat)->first();
    if ($aktif) {
        // Tidak ada kolom deskripsi di tabel aktif, jadi gunakan default
        $judul = "Surat Keterangan Aktif Kuliah";
        if ($aktif->Tahun_Akademik) {
            $judul .= " (" . $aktif->Tahun_Akademik . ")";
        }
    }

    $tugas->Judul_Tugas_Surat = $judul;
    $tugas->save();
    echo "Updated ID: " . $tugas->Id_Tugas_Surat . " -> " . $judul . "\n";
}

echo "Done.\n";
