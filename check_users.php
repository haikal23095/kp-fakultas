<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== Checking for Admin Fakultas Users ===\n";
$users = \App\Models\User::whereHas('role', function ($q) {
    $q->where('Name_Role', 'Pegawai_Fakultas');
})->with('role')->get();

echo "Found: " . $users->count() . " user(s) with Pegawai_Fakultas role\n\n";
foreach ($users as $user) {
    echo "ID: " . $user->Id_User . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . ($user->role ? $user->role->Name_Role : 'No Role') . "\n";
    echo "---\n";
}

echo "\n=== Checking All Roles ===\n";
$roles = \App\Models\Role::all();
foreach ($roles as $role) {
    echo "ID: " . $role->Id_Role . " - Name: " . $role->Name_Role . "\n";
}

echo "\n=== Checking All Admin/Pegawai Users ===\n";
$allAdmins = \App\Models\User::whereHas('role', function ($q) {
    $q->whereIn('Name_Role', ['Admin', 'Pegawai_Fakultas', 'Admin Fakultas', 'Admin_Fakultas']);
})->with('role')->get();

echo "Found: " . $allAdmins->count() . " admin/pegawai user(s)\n\n";
foreach ($allAdmins as $user) {
    echo "ID: " . $user->Id_User . " - Name: " . $user->name . " - Role: " . ($user->role ? $user->role->Name_Role : 'No Role') . "\n";
}

echo "\n=== Done ===\n";
