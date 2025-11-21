<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$hash = '$2y$12$vbyK8WrszhLmjgDtn5yus.eOGQHA9TO.oMe7rboTL2LcQOQ6kwK7i';

echo "Testing password hash...\n";
echo "Hash: $hash\n\n";

$passwords = [
    'admin',
    'admin123', 
    'password',
    '123456',
    'admin@123',
    'Admin123',
    'Password123',
    'fakultas',
    'fakultas123'
];

echo "Testing common passwords:\n";
foreach ($passwords as $pwd) {
    if (password_verify($pwd, $hash)) {
        echo "✓ FOUND: $pwd\n";
        exit(0);
    } else {
        echo "✗ Not: $pwd\n";
    }
}

echo "\nPassword not found in common list.\n";
echo "Let me check which user has this hash...\n\n";

// Check which user has this password
$users = DB::table('Users')->select('Id_User', 'Username', 'email', 'Name_User', 'password', 'Id_Role')->get();

foreach ($users as $user) {
    if ($user->password === $hash) {
        echo "User found:\n";
        echo "  ID: {$user->Id_User}\n";
        echo "  Username: {$user->Username}\n";
        echo "  Email: {$user->email}\n";
        echo "  Name: {$user->Name_User}\n";
        echo "  Role ID: {$user->Id_Role}\n";
    }
}
