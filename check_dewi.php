<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$email = 'dewi.sartika@fakultas.ac.id';
echo "Checking user: $email\n";

$user = DB::table('Users')
    ->leftJoin('Roles', 'Users.Id_Role', '=', 'Roles.Id_Role')
    ->where('email', $email)
    ->select('Users.*', 'Roles.Name_Role')
    ->first();

if ($user) {
    echo "User Found:\n";
    echo "ID: " . $user->Id_User . "\n";
    echo "Name: " . $user->Name_User . "\n";
    echo "Role: " . $user->Name_Role . " (ID: " . $user->Id_Role . ")\n";

    // Check if linked to Pegawai_Prodi or Pegawai_Fakultas
    $pegawaiProdi = DB::table('Pegawai_Prodi')->where('Id_User', $user->Id_User)->first();
    if ($pegawaiProdi) {
        echo "Linked to Pegawai_Prodi:\n";
        $prodi = DB::table('Prodi')->where('Id_Prodi', $pegawaiProdi->Id_Prodi)->first();
        echo "Prodi: " . ($prodi ? $prodi->Nama_Prodi : 'Unknown') . "\n";
    } else {
        echo "Not linked to Pegawai_Prodi\n";
    }

    $pegawaiFakultas = DB::table('Pegawai_Fakultas')->where('Id_User', $user->Id_User)->first();
    if ($pegawaiFakultas) {
        echo "Linked to Pegawai_Fakultas:\n";
        $fakultas = DB::table('Fakultas')->where('Id_Fakultas', $pegawaiFakultas->Id_Fakultas)->first();
        echo "Fakultas: " . ($fakultas ? $fakultas->Nama_Fakultas : 'Unknown') . "\n";
    } else {
        echo "Not linked to Pegawai_Fakultas\n";
    }

} else {
    echo "User not found.\n";
}
