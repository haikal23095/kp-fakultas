<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DAFTAR ADMIN FAKULTAS ===\n";
$admins = \App\Models\User::whereHas('role', function($q) {
    $q->where('Name_Role', 'Admin Fakultas');
})->with('pegawaiFakultas.fakultas')->get();

foreach($admins as $admin) {
    echo "User: {$admin->Name_User} | Fakultas: " . ($admin->pegawaiFakultas->fakultas->Nama_Fakultas ?? 'N/A') . " (ID: " . ($admin->pegawaiFakultas->Id_Fakultas ?? 'N/A') . ")\n";
}
