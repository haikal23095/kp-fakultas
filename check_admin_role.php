<?php
/**
 * Script untuk cek role admin fakultas
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CEK ROLE & USER ===\n\n";

try {
    // Cek role pegawai fakultas (yang bertindak sebagai admin fakultas)
    $adminRole = DB::table('Roles')
        ->where('Id_Role', 7)
        ->first();
    
    if ($adminRole) {
        echo "✅ Role Admin Fakultas ditemukan:\n";
        echo "   Id_Role: {$adminRole->Id_Role}\n";
        echo "   Name_Role: {$adminRole->Name_Role}\n\n";
        
        // Cek user dengan role ini
        $adminUsers = DB::table('Users')
            ->where('Id_Role', $adminRole->Id_Role)
            ->get();
        
        echo "👤 User dengan role ini:\n";
        if ($adminUsers->isEmpty()) {
            echo "   (Tidak ada user dengan role ini)\n";
        } else {
            foreach ($adminUsers as $user) {
                $email = $user->Email_User ?? $user->email ?? 'N/A';
                echo "   ID: {$user->Id_User} | Name: {$user->Name_User} | Email: {$email}\n";
            }
        }
    } else {
        echo "❌ Role Admin Fakultas tidak ditemukan\n\n";
        
        // Tampilkan semua role
        echo "📋 Semua role yang tersedia:\n";
        $roles = DB::table('Roles')->get();
        foreach ($roles as $role) {
            echo "   ID: {$role->Id_Role} | Name: {$role->Name_Role}\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
