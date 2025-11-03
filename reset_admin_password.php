<?php
/**
 * Script untuk reset password admin
 * Email: joko.susanto@fakultas.ac.id
 * New Password: password_admin
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "========================================\n";
echo "Reset Password Admin\n";
echo "========================================\n\n";

$email = 'joko.susanto@fakultas.ac.id';
$newPassword = 'password_admin';

// Cek user dulu
$user = DB::table('Users')->where('email', $email)->first();

if (!$user) {
    echo "ERROR: User dengan email '$email' tidak ditemukan!\n";
    exit(1);
}

echo "User ditemukan:\n";
echo "- ID: {$user->Id_User}\n";
echo "- Name: {$user->Name_User}\n";
echo "- Email: {$user->email}\n";
echo "- Role ID: {$user->Id_Role}\n";
echo "\n";

// Generate hash password baru
$hashedPassword = Hash::make($newPassword);

echo "Password baru (hashed): $hashedPassword\n\n";

// Update password
$updated = DB::table('Users')
    ->where('email', $email)
    ->update(['password' => $hashedPassword]);

if ($updated) {
    echo "✅ SUCCESS: Password berhasil diubah!\n";
    echo "   Email: $email\n";
    echo "   Password baru: $newPassword\n";
    echo "\n";
    echo "Sekarang Anda bisa login dengan:\n";
    echo "   Username: {$user->Username}\n";
    echo "   Password: $newPassword\n";
} else {
    echo "❌ ERROR: Gagal mengubah password!\n";
}

echo "\n========================================\n";
