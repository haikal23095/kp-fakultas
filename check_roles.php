<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Semua Role ===\n";
$roles = \App\Models\Role::all();
foreach ($roles as $r) {
    echo "{$r->Name_Role} (ID: {$r->Id_Role})\n";
}

echo "\n=== Semua User dengan Role ===\n";
$users = \App\Models\User::with('role')->get();
foreach ($users as $u) {
    echo "{$u->Name_User} ({$u->email}) - Role: {$u->role->Name_Role}\n";
}
