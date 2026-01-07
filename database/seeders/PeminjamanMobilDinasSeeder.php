<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeminjamanMobilDinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah sudah ada
        $exists = DB::table('Jenis_Surat')
            ->where('Nama_Surat', 'Peminjaman Mobil Dinas')
            ->exists();

        if (!$exists) {
            // Ambil ID terakhir
            $lastId = DB::table('Jenis_Surat')->max('Id_Jenis_Surat') ?? 0;
            
            DB::table('Jenis_Surat')->insert([
                'Id_Jenis_Surat' => $lastId + 1,
                'Nama_Surat' => 'Peminjaman Mobil Dinas',
            ]);

            $this->command->info('✓ Jenis Surat "Peminjaman Mobil Dinas" berhasil ditambahkan!');
        } else {
            $this->command->info('✓ Jenis Surat "Peminjaman Mobil Dinas" sudah ada.');
        }

        // Insert beberapa data kendaraan sample
        $kendaraanExists = DB::table('Kendaraan')->count() > 0;
        
        if (!$kendaraanExists) {
            DB::table('Kendaraan')->insert([
                [
                    'nama_kendaraan' => 'Toyota Avanza',
                    'plat_nomor' => 'N 1234 AB',
                    'kapasitas' => 7,
                    'status_kendaraan' => 'Tersedia',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_kendaraan' => 'Toyota Innova',
                    'plat_nomor' => 'N 5678 CD',
                    'kapasitas' => 8,
                    'status_kendaraan' => 'Tersedia',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_kendaraan' => 'Mitsubishi L300',
                    'plat_nomor' => 'N 9012 EF',
                    'kapasitas' => 12,
                    'status_kendaraan' => 'Maintenance',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $this->command->info('✓ Data sample kendaraan berhasil ditambahkan!');
        } else {
            $this->command->info('✓ Data kendaraan sudah ada.');
        }
    }
}
