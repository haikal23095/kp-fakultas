<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'Username' => 'admin',
            'email' => 'admin@fakultas.ac.id',
            'password' => Hash::make('password123'),
            'Name_User' => 'Administrator',
            'Id_Role' => 1, // Asumsi role admin memiliki ID 1
        ]);

        User::create([
            'Username' => 'dosen',
            'email' => 'dosen@fakultas.ac.id',
            'password' => Hash::make('password123'),
            'Name_User' => 'Dr. Contoh Dosen',
            'Id_Role' => 2, // Asumsi role dosen memiliki ID 2
        ]);

        User::create([
            'Username' => 'mahasiswa',
            'email' => 'mahasiswa@fakultas.ac.id',
            'password' => Hash::make('password123'),
            'Name_User' => 'Mahasiswa Contoh',
            'Id_Role' => 3, // Asumsi role mahasiswa memiliki ID 3
        ]);
    }
}