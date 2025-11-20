<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Cek User sri.wahyuni@fakultas.ac.id:\n";
$user = DB::table('Users')->where('Email', 'sri.wahyuni@fakultas.ac.id')->first();

if ($user) {
    echo "User ditemukan:\n";
    foreach ($user as $key => $value) {
        if ($key !== 'password' && $key !== 'Password_User') {
            echo "- {$key}: {$value}\n";
        }
    }

    // Ambil kolom password (bisa password atau Password_User)
    $passwordHash = $user->password ?? $user->Password_User ?? null;
    echo "- Password Hash: {$passwordHash}\n\n";

    // Test password
    $testPassword = 'password_admin';
    $isValid = password_verify($testPassword, $passwordHash);
    echo "Test password 'password_admin': " . ($isValid ? "✓ VALID" : "✗ INVALID") . "\n\n";

    // Generate hash baru
    echo "Hash baru untuk 'password_admin':\n";
    echo password_hash($testPassword, PASSWORD_BCRYPT) . "\n";
} else {
    echo "User tidak ditemukan!\n";
}
