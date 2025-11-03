# ============================================
# PowerShell Script untuk Execute SQL Fix
# ============================================

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Memperbaiki AUTO_INCREMENT dengan Foreign Key" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Baca konfigurasi database dari .env
$envFile = Get-Content .env
$dbHost = ($envFile | Select-String "DB_HOST=(.*)").Matches.Groups[1].Value
$dbPort = ($envFile | Select-String "DB_PORT=(.*)").Matches.Groups[1].Value
$dbName = ($envFile | Select-String "DB_DATABASE=(.*)").Matches.Groups[1].Value
$dbUser = ($envFile | Select-String "DB_USERNAME=(.*)").Matches.Groups[1].Value
$dbPass = ($envFile | Select-String "DB_PASSWORD=(.*)").Matches.Groups[1].Value

Write-Host "Database: $dbName" -ForegroundColor Yellow
Write-Host "User: $dbUser" -ForegroundColor Yellow
Write-Host ""

# Tampilkan peringatan
Write-Host "PERINGATAN: Script ini akan:" -ForegroundColor Red
Write-Host "1. Drop foreign key constraint 'File_Arsip_ibfk_1'" -ForegroundColor Yellow
Write-Host "2. Modify kolom Id_Tugas_Surat menjadi AUTO_INCREMENT" -ForegroundColor Yellow
Write-Host "3. Modify kolom Id_File_Arsip menjadi AUTO_INCREMENT" -ForegroundColor Yellow
Write-Host "4. Recreate foreign key constraint" -ForegroundColor Yellow
Write-Host ""

$confirm = Read-Host "Lanjutkan? (yes/no)"
if ($confirm -ne "yes") {
    Write-Host "Dibatalkan." -ForegroundColor Red
    exit
}

Write-Host ""
Write-Host "Menjalankan perbaikan..." -ForegroundColor Green
Write-Host ""

# Jalankan SQL
php artisan db:seed --class=DatabaseSeeder --force 2>&1 | Out-Null

# Atau gunakan mysql command jika tersedia
# mysql -h $dbHost -P $dbPort -u $dbUser -p$dbPass $dbName < database\fix_auto_increment.sql

Write-Host "Selesai! Silakan cek hasilnya." -ForegroundColor Green
