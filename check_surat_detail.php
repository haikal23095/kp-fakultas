<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TugasSurat;

echo "=== CEK DETAIL SURAT DAN JENIS SURAT ===\n\n";

$suratId = 1;
$surat = TugasSurat::with([
    'fileArsip',
    'jenisSurat',
    'pemberiTugas.mahasiswa.prodi.fakultas',
    'penerimaTugas',
    'suratMagang',
    'suratKetAktif',
    'verification'
])->find($suratId);

if (!$surat) {
    echo "❌ Surat ID {$suratId} tidak ditemukan!\n";
    exit;
}

echo "✅ TUGAS SURAT:\n";
echo "   ID: {$surat->Id_Tugas_Surat}\n";
echo "   Nomor: {$surat->Nomor_Surat}\n";
echo "   Status: {$surat->Status}\n";
echo "   Id Jenis Surat: {$surat->Id_Jenis_Surat}\n\n";

echo "✅ JENIS SURAT:\n";
if ($surat->jenisSurat) {
    echo "   ID: {$surat->jenisSurat->Id_Jenis_Surat}\n";
    echo "   Nama: {$surat->jenisSurat->Nama_Surat}\n";
} else {
    echo "   ❌ Jenis Surat tidak ditemukan!\n";
}
echo "\n";

echo "📄 CEK RELASI CHILD TABLE:\n";

// Cek Surat Magang
if ($surat->suratMagang) {
    echo "   ✅ suratMagang ADA:\n";
    echo "      ID: {$surat->suratMagang->Id_Surat_Magang}\n";
    echo "      Status: {$surat->suratMagang->Status}\n";
} else {
    echo "   ❌ suratMagang: TIDAK ADA\n";
}

// Cek Surat Ket Aktif
if ($surat->suratKetAktif) {
    echo "   ✅ suratKetAktif ADA:\n";
    echo "      ID: {$surat->suratKetAktif->Id_Surat_Ket_Aktif}\n";
    if (isset($surat->suratKetAktif->Status)) {
        echo "      Status: {$surat->suratKetAktif->Status}\n";
    } else {
        echo "      (Tabel ini tidak punya kolom Status)\n";
    }
} else {
    echo "   ❌ suratKetAktif: TIDAK ADA\n";
}

echo "\n";

echo "📁 FILE ARSIP:\n";
if ($surat->fileArsip) {
    echo "   ✅ File arsip ADA: {$surat->fileArsip->Path_File}\n";
} else {
    echo "   ❌ File arsip TIDAK ADA (belum di-generate)\n";
}

echo "\n";

echo "👤 DATA MAHASISWA:\n";
$mahasiswa = $surat->pemberiTugas->mahasiswa ?? null;
if ($mahasiswa) {
    echo "   ✅ Mahasiswa ADA:\n";
    echo "      NIM: {$mahasiswa->NIM}\n";
    echo "      Nama: {$mahasiswa->Nama_Mahasiswa}\n";
    echo "      Prodi: " . ($mahasiswa->prodi->Nama_Prodi ?? 'null') . "\n";
    echo "      Fakultas: " . ($mahasiswa->prodi->fakultas->Nama_Fakultas ?? 'null') . "\n";
} else {
    echo "   ❌ Mahasiswa TIDAK ADA\n";
}

echo "\n";

echo "🔑 DATA SPESIFIK (JSON):\n";
if ($surat->data_spesifik) {
    echo "   " . json_encode($surat->data_spesifik, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "   ❌ Tidak ada data_spesifik\n";
}

echo "\n=== KESIMPULAN ===\n";
if (!$surat->suratMagang && !$surat->suratKetAktif) {
    echo "⚠️  MASALAH DITEMUKAN: Surat ini TIDAK punya data di tabel child!\n";
    echo "    - suratMagang: TIDAK ADA\n";
    echo "    - suratKetAktif: TIDAK ADA\n";
    echo "    Ini yang menyebabkan pesan 'Preview tidak tersedia untuk jenis surat ini.'\n\n";
    echo "💡 SOLUSI: Data surat harus ada di salah satu tabel child!\n";
} else {
    echo "✅ Data surat lengkap dan seharusnya bisa di-preview.\n";
}

echo "\n";
