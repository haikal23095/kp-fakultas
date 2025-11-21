<?php
/**
 * DEBUG SCRIPT: Cek alur data Mahasiswa → Admin → Dekan
 * Jalankan dengan: php debug_surat_flow.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TugasSurat;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Prodi;

echo "=== DEBUG ALUR SURAT FAKULTAS ===\n\n";

// 1. CEK SURAT DENGAN STATUS 'menunggu-ttd'
echo "1. SURAT DENGAN STATUS 'menunggu-ttd':\n";
$suratMenunggu = TugasSurat::where('Status', 'menunggu-ttd')
    ->with(['pemberiTugas', 'penerimaTugas'])
    ->get();

if ($suratMenunggu->isEmpty()) {
    echo "   ❌ TIDAK ADA surat dengan status 'menunggu-ttd'\n\n";
} else {
    foreach ($suratMenunggu as $s) {
        echo "   ✅ ID Surat: {$s->Id_Tugas_Surat}\n";
        echo "      Pemberi: " . ($s->pemberiTugas->Name_User ?? 'N/A') . " (ID: {$s->Id_Pemberi_Tugas_Surat})\n";
        echo "      Penerima: " . ($s->penerimaTugas->Name_User ?? 'N/A') . " (ID: {$s->Id_Penerima_Tugas_Surat})\n";
        echo "      Status: {$s->Status}\n";
        echo "      Jenis: " . ($s->jenisSurat->Nama_Surat ?? 'N/A') . "\n\n";
    }
}

// 2. CEK SURAT DENGAN STATUS 'Diterima Admin'
echo "2. SURAT DENGAN STATUS 'Diterima Admin' (Belum Diproses):\n";
$suratAdmin = TugasSurat::where('Status', 'Diterima Admin')
    ->with(['pemberiTugas', 'penerimaTugas'])
    ->get();

if ($suratAdmin->isEmpty()) {
    echo "   ✅ Tidak ada surat pending di Admin (sudah semua diproses)\n\n";
} else {
    foreach ($suratAdmin as $s) {
        echo "   ⚠️  ID Surat: {$s->Id_Tugas_Surat}\n";
        echo "      Pemberi: " . ($s->pemberiTugas->Name_User ?? 'N/A') . "\n";
        echo "      Status: {$s->Status}\n";
        echo "      → Admin perlu memproses surat ini!\n\n";
    }
}

// 3. CEK USER DEKAN
echo "3. CEK USER DEKAN DI DATABASE:\n";
$dekan = User::whereHas('role', function ($q) {
    $q->whereRaw("LOWER(TRIM(Name_Role)) = 'dekan'");
})->first();

if (!$dekan) {
    echo "   ❌ DEKAN TIDAK DITEMUKAN! Ini masalah utama!\n";
    echo "      Solusi: Pastikan ada user dengan role 'Dekan' di database.\n\n";
} else {
    echo "   ✅ Dekan ditemukan:\n";
    echo "      ID User: {$dekan->Id_User}\n";
    echo "      Nama: {$dekan->Name_User}\n";
    echo "      Role: " . ($dekan->role->Name_Role ?? 'N/A') . "\n\n";
    
    // 3a. Cek apakah Dekan punya data Dosen
    $dosenDekan = Dosen::where('Id_User', $dekan->Id_User)->first();
    if (!$dosenDekan) {
        echo "   ⚠️  WARNING: Dekan tidak memiliki data di tabel Dosen!\n";
        echo "      Ini akan menyebabkan query filter fakultas gagal.\n";
        echo "      Solusi: Tambahkan data Dekan ke tabel Dosen dengan Id_Prodi yang sesuai.\n\n";
    } else {
        echo "   ✅ Data Dosen Dekan:\n";
        echo "      ID Dosen: {$dosenDekan->Id_Dosen}\n";
        echo "      NIP: {$dosenDekan->NIP}\n";
        echo "      Id_Prodi: {$dosenDekan->Id_Prodi}\n";
        
        if ($dosenDekan->Id_Prodi) {
            $prodi = Prodi::find($dosenDekan->Id_Prodi);
            if ($prodi) {
                echo "      Prodi: " . ($prodi->Nama_Prodi ?? 'N/A') . "\n";
                echo "      Id_Fakultas: " . ($prodi->Id_Fakultas ?? 'N/A') . "\n\n";
            }
        } else {
            echo "      ⚠️  WARNING: Dekan tidak memiliki Id_Prodi!\n\n";
        }
    }
}

// 4. CEK SURAT YANG DITUJUKAN KE DEKAN
echo "4. SURAT YANG DITUJUKAN KE DEKAN:\n";
if ($dekan) {
    $suratKeDekan = TugasSurat::where('Id_Penerima_Tugas_Surat', $dekan->Id_User)
        ->with(['pemberiTugas', 'penerimaTugas'])
        ->get();
    
    if ($suratKeDekan->isEmpty()) {
        echo "   ⚠️  TIDAK ADA surat yang ditujukan ke Dekan (ID: {$dekan->Id_User})\n";
        echo "      Kemungkinan:\n";
        echo "      - Admin belum memproses surat dari mahasiswa\n";
        echo "      - Admin menggunakan Dekan berbeda saat proses\n\n";
    } else {
        foreach ($suratKeDekan as $s) {
            echo "   ✅ ID Surat: {$s->Id_Tugas_Surat}\n";
            echo "      Status: {$s->Status}\n";
            echo "      Pemberi: " . ($s->pemberiTugas->Name_User ?? 'N/A') . "\n";
            echo "      Jenis: " . ($s->jenisSurat->Nama_Surat ?? 'N/A') . "\n\n";
        }
    }
}

// 5. SUMMARY & REKOMENDASI
echo "=== SUMMARY & REKOMENDASI ===\n";
echo "Agar data muncul di dashboard Dekan, pastikan:\n";
echo "1. ✅ Ada user dengan role 'Dekan' di tabel User\n";
echo "2. ✅ Dekan memiliki record di tabel Dosen dengan Id_Prodi yang valid\n";
echo "3. ✅ Admin sudah klik 'Proses & Ajukan' atau upload draft di halaman detail surat\n";
echo "4. ✅ Status surat berubah dari 'Diterima Admin' → 'menunggu-ttd'\n";
echo "5. ✅ Id_Penerima_Tugas_Surat di-set ke ID user Dekan\n\n";

echo "Cek log Laravel untuk detail lebih lanjut: storage/logs/laravel.log\n";
