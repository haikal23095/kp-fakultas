<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ALL ADMIN USERS (Role ID = 1) ===\n\n";

$admins = DB::table('Users')->where('Id_Role', 1)->get();

foreach ($admins as $admin) {
    echo "ID: {$admin->Id_User}\n";
    echo "Username: {$admin->Username}\n";
    echo "Email: {$admin->email}\n";
    echo "Name: {$admin->Name_User}\n";
    echo "---\n";
}

echo "\nTotal: " . count($admins) . " admin(s)\n";
