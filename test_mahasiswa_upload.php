<?php
/**
 * Test script: Simulasi mahasiswa upload legalisir via form
 * Jalankan: php artisan tinker < test_mahasiswa_upload.php
 */

echo "🧪 Testing Mahasiswa Legalisir Upload Flow\n";
echo "==========================================\n\n";

// 1. Cek apakah route mahasiswa ada
try {
    $route = route('mahasiswa.pengajuan.legalisir.create');
    echo "✅ Route Create: $route\n";
    
    $routeStore = route('mahasiswa.pengajuan.legalisir.store');
    echo "✅ Route Store: $routeStore\n\n";
} catch (\Exception $e) {
    echo "❌ Route error: " . $e->getMessage() . "\n";
    exit;
}

// 2. Cek Controller ada dan bisa di-resolve
try {
    $controller = app()->make(\App\Http\Controllers\PengajuanSurat\SuratLegalisirController::class);
    echo "✅ Controller PengajuanSurat\\SuratLegalisirController loaded\n\n";
} catch (\Exception $e) {
    echo "❌ Controller error: " . $e->getMessage() . "\n";
    exit;
}

// 3. Cek Storage public link
$storageLinked = is_link(public_path('storage'));
echo ($storageLinked ? "✅" : "⚠️") . " Storage link exists: " . ($storageLinked ? "Yes" : "No") . "\n";

// 4. Cek folder legalisir/scans
$scanFolder = storage_path('app/public/legalisir/scans');
$folderExists = is_dir($scanFolder);
echo ($folderExists ? "✅" : "⚠️") . " Scan folder exists: $scanFolder\n\n";

// 5. Simulasi request data (untuk debugging, tidak benar-benar submit form)
echo "📋 Form Field Requirements:\n";
echo "   - jenis_dokumen: required|in:Ijazah,Transkrip\n";
echo "   - file_scan: required|file|mimes:pdf|max:10240\n";
echo "   - jumlah_salinan: required|integer|min:1|max:10\n\n";

echo "✅ SIAP DITEST!\n";
echo "Login sebagai mahasiswa (Id_User=201) dan akses:\n";
echo "   → /mahasiswa/pengajuan-surat/legalisir\n";
echo "   → Isi form dan upload PDF\n";
echo "   → Submit dan cek database + storage\n";
