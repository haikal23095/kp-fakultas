<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SIMULASI QUERY ARSIP (seperti di controller) ===\n\n";

// Simulasi user admin fakultas ID 2 (Teknik)
$fakultasId = 2;

echo "Fakultas ID: {$fakultasId}\n\n";

echo "--- ARSIP TUGAS (query dari controller) ---\n";
$arsipTugas = \App\Models\TugasSurat::query()
    ->whereNotNull('Nomor_Surat')
    ->where('Nomor_Surat', '!=', '')
    ->where(function ($q) use ($fakultasId) {
        $q->whereHas('pemberiTugas.mahasiswa.prodi.fakultas', function ($subQ) use ($fakultasId) {
            $subQ->where('Id_Fakultas', $fakultasId);
        })
            ->orWhereHas('pemberiTugas.dosen.prodi.fakultas', function ($subQ) use ($fakultasId) {
                $subQ->where('Id_Fakultas', $fakultasId);
            })
            ->orWhereHas('pemberiTugas.pegawai.prodi.fakultas', function ($subQ) use ($fakultasId) {
                $subQ->where('Id_Fakultas', $fakultasId);
            });
    })
    ->with(['pemberiTugas.role', 'pemberiTugas.mahasiswa.prodi', 'jenisSurat'])
    ->get();

echo "Total arsip tugas: " . $arsipTugas->count() . "\n\n";

foreach($arsipTugas as $surat) {
    $jenis = $surat->jenisSurat->Nama_Surat ?? 'N/A';
    $user = $surat->pemberiTugas->Name_User ?? 'N/A';
    $fakultas = $surat->pemberiTugas->mahasiswa->prodi->fakultas->Nama_Fakultas ?? 'N/A';
    
    echo "ID: {$surat->Id_Tugas_Surat} | Jenis: {$jenis} | User: {$user} | Fakultas: {$fakultas} | Nomor: {$surat->Nomor_Surat}\n";
}

echo "\n--- COUNT PER JENIS SURAT ---\n";
$countArsipAktif = $arsipTugas->where('Id_Jenis_Surat', 1)->count();
$countArsipMagang = $arsipTugas->where('Id_Jenis_Surat', 2)->count();
$countArsipSKDosen = $arsipTugas->where('Id_Jenis_Surat', 4)->count();
$countArsipBerkelakuanBaik = $arsipTugas->where('Id_Jenis_Surat', 5)->count();
$countArsipDispensasi = $arsipTugas->where('Id_Jenis_Surat', 6)->count();
$countArsipTidakBeasiswa = $arsipTugas->where('Id_Jenis_Surat', 7)->count();
$countArsipMobilDinas = $arsipTugas->where('Id_Jenis_Surat', 13)->count();

echo "Surat Aktif (1): {$countArsipAktif}\n";
echo "Surat Magang (2): {$countArsipMagang}\n";
echo "SK Dosen (4): {$countArsipSKDosen}\n";
echo "Berkelakuan Baik (5): {$countArsipBerkelakuanBaik}\n";
echo "Dispensasi (6): {$countArsipDispensasi}\n";
echo "Tidak Beasiswa (7): {$countArsipTidakBeasiswa}\n";
echo "Mobil Dinas (13): {$countArsipMobilDinas}\n";

echo "\n--- ARSIP LEGALISIR ---\n";
$arsipLegalisir = \App\Models\SuratLegalisir::with(['user.mahasiswa.prodi'])
    ->where('Status', 'selesai')
    ->whereHas('user.mahasiswa.prodi.fakultas', function($q) use ($fakultasId) {
        $q->where('Id_Fakultas', $fakultasId);
    })
    ->get();
    
echo "Total legalisir selesai: " . $arsipLegalisir->count() . "\n";
