<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$email = 'sri.wahyuni@fakultas.ac.id';
$newPassword = 'password_admin';
$newHash = password_hash($newPassword, PASSWORD_BCRYPT);

echo "Updating password untuk {$email}...\n";
echo "Hash baru: {$newHash}\n\n";

$updated = DB::table('Users')
    ->where('email', $email)
    ->update(['password' => $newHash]);

if ($updated) {
    echo "✓ Password berhasil diupdate!\n\n";

    // Verify
    $user = DB::table('Users')->where('email', $email)->first();
    $isValid = password_verify($newPassword, $user->password);
    echo "Verifikasi password: " . ($isValid ? "✓ VALID" : "✗ INVALID") . "\n";
} else {
    echo "✗ Gagal update password\n";
}
