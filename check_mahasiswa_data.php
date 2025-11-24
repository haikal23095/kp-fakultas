<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TugasSurat;
use App\Models\User;
use App\Models\Mahasiswa;

echo "=== CEK DATA MAHASISWA UNTUK SURAT ===\n\n";

// Cek surat dengan ID 1
$suratId = 1;
$surat = TugasSurat::with(['pemberiTugas.mahasiswa.prodi.fakultas'])->find($suratId);

if (!$surat) {
    echo "❌ Surat ID {$suratId} tidak ditemukan!\n";
    exit;
}

echo "✅ Surat ID: {$surat->Id_Tugas_Surat}\n";
echo "   Nomor Surat: {$surat->Nomor_Surat}\n";
echo "   Status: {$surat->Status}\n";
echo "   Id Pemberi Tugas: {$surat->Id_Pemberi_Tugas_Surat}\n\n";

$pemberi = $surat->pemberiTugas;
if ($pemberi) {
    echo "✅ Pemberi Tugas (User):\n";
    echo "   ID: {$pemberi->Id_User}\n";
    echo "   Nama: {$pemberi->Name_User}\n";
    echo "   Email: {$pemberi->Email}\n\n";
    
    // Cek relasi mahasiswa
    $mahasiswa = $pemberi->mahasiswa;
    if ($mahasiswa) {
        echo "✅ Data Mahasiswa ditemukan:\n";
        echo "   ID: {$mahasiswa->Id_Mahasiswa}\n";
        echo "   NIM: {$mahasiswa->NIM}\n";
        echo "   Nama: {$mahasiswa->Nama_Mahasiswa}\n";
        echo "   Alamat: {$mahasiswa->Alamat_Mahasiswa}\n";
        echo "   Id User: {$mahasiswa->Id_User}\n\n";
        
        if ($mahasiswa->prodi) {
            echo "✅ Program Studi:\n";
            echo "   Nama Prodi: {$mahasiswa->prodi->Nama_Prodi}\n";
            
            if ($mahasiswa->prodi->fakultas) {
                echo "   Fakultas: {$mahasiswa->prodi->fakultas->Nama_Fakultas}\n";
            } else {
                echo "   ❌ Fakultas tidak ditemukan\n";
            }
        } else {
            echo "❌ Data Program Studi tidak ditemukan!\n";
        }
    } else {
        echo "❌ Data Mahasiswa tidak ditemukan!\n";
        echo "   Mencoba cek langsung di tabel mahasiswa...\n\n";
        
        // Cek langsung di tabel mahasiswa
        $mhsDirect = Mahasiswa::where('Id_User', $pemberi->Id_User)->first();
        if ($mhsDirect) {
            echo "   ⚠️ Data mahasiswa ADA di database tapi TIDAK ter-load via relasi!\n";
            echo "   Kemungkinan masalah di model User->mahasiswa() relationship\n";
            echo "   Data: NIM={$mhsDirect->NIM}, Nama={$mhsDirect->Nama_Mahasiswa}\n";
        } else {
            echo "   ❌ Data mahasiswa TIDAK ADA di database untuk User ID {$pemberi->Id_User}\n";
        }
    }
} else {
    echo "❌ Pemberi Tugas tidak ditemukan!\n";
}

echo "\n=== CEK SELESAI ===\n";
