<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$fakultas = DB::table('Fakultas')->get();
$password = password_hash('password_admin', PASSWORD_BCRYPT);

$adminData = [
    ['nama' => 'Admin Fisib', 'fakultas_id' => 1, 'nip' => '198501012010011001', 'jk' => 'L', 'alamat' => 'Bangkalan'],
    ['nama' => 'Admin Teknik', 'fakultas_id' => 2, 'nip' => '198502022010012002', 'jk' => 'L', 'alamat' => 'Bangkalan'],
    ['nama' => 'Admin FEB', 'fakultas_id' => 3, 'nip' => '198503032010013003', 'jk' => 'P', 'alamat' => 'Bangkalan'],
    ['nama' => 'Admin FKIS', 'fakultas_id' => 4, 'nip' => '198504042010014004', 'jk' => 'L', 'alamat' => 'Bangkalan'],
    ['nama' => 'Admin FP', 'fakultas_id' => 5, 'nip' => '198505052010015005', 'jk' => 'P', 'alamat' => 'Bangkalan'],
    ['nama' => 'Admin FKIP', 'fakultas_id' => 6, 'nip' => '198506062010016006', 'jk' => 'L', 'alamat' => 'Bangkalan'],
];

echo "Mulai insert data admin fakultas...\n\n";

DB::beginTransaction();

try {
    foreach ($adminData as $admin) {
        // Cek apakah fakultas ada
        $fakultas = DB::table('Fakultas')->where('Id_Fakultas', $admin['fakultas_id'])->first();
        if (!$fakultas) {
            echo "⚠ Skip {$admin['nama']}: Fakultas ID {$admin['fakultas_id']} tidak ditemukan\n";
            continue;
        }

        // Generate username dari nama
        $username = strtolower(str_replace(' ', '_', $admin['nama']));
        $email = $username . '@fakultas.ac.id';

        // Get last User ID and increment
        $lastUserId = DB::table('Users')->max('Id_User') ?? 0;
        $newUserId = $lastUserId + 1;

        // Insert ke tabel Users
        DB::table('Users')->insert([
            'Id_User' => $newUserId,
            'Username' => $username,
            'password' => $password,
            'Name_User' => $admin['nama'],
            'email' => $email,
            'Id_Role' => 7, // Admin Fakultas
        ]);

        echo "✓ User created: {$admin['nama']} (ID: {$newUserId})\n";
        echo "  - Username: {$username}\n";
        echo "  - Email: {$email}\n";
        echo "  - Password: password_admin\n";

        // Get last Pegawai ID and increment
        $lastPegawaiId = DB::table('Pegawai_Fakultas')->max('Id_Pegawai') ?? 0;
        $newPegawaiId = $lastPegawaiId + 1;

        // Insert ke tabel Pegawai_Fakultas
        DB::table('Pegawai_Fakultas')->insert([
            'Id_Pegawai' => $newPegawaiId,
            'NIP' => $admin['nip'],
            'Nama_Pegawai' => $admin['nama'],
            'Jenis_Kelamin_Pegawai' => $admin['jk'],
            'Alamat_Pegawai' => $admin['alamat'],
            'Id_User' => $newUserId,
            'Id_Fakultas' => $admin['fakultas_id'],
        ]);

        echo "✓ Pegawai Fakultas created (ID: {$newPegawaiId})\n";
        echo "  - NIP: {$admin['nip']}\n";
        echo "  - Fakultas: {$fakultas->Nama_Fakultas}\n\n";
    }

    DB::commit();
    echo "\n✅ Semua data admin fakultas berhasil ditambahkan!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}
