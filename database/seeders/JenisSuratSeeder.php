<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\JenisSurat;

class JenisSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        Schema::disableForeignKeyConstraints();

        // Empty the table
        JenisSurat::truncate();

        // Enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // Fix Auto Increment if missing (Raw SQL)
        try {
            DB::statement("ALTER TABLE Jenis_Surat MODIFY COLUMN Id_Jenis_Surat INT AUTO_INCREMENT");
        } catch (\Exception $e) {
            // Ignore if it fails (e.g. if already auto increment or different DB driver)
        }

        $surats = [
            ['Id_Jenis_Surat' => 1, 'Nama_Surat' => 'Surat Keterangan Aktif'],
            ['Id_Jenis_Surat' => 2, 'Nama_Surat' => 'Surat Pengantar KP/Magang'],
            ['Id_Jenis_Surat' => 3, 'Nama_Surat' => 'Legalisir Online'],
            ['Id_Jenis_Surat' => 4, 'Nama_Surat' => 'Surat Mobil Dinas'],
            ['Id_Jenis_Surat' => 5, 'Nama_Surat' => 'Surat Cuti'],
            ['Id_Jenis_Surat' => 6, 'Nama_Surat' => 'Surat Keterangan Tidak Menerima Beasiswa'],
            ['Id_Jenis_Surat' => 7, 'Nama_Surat' => 'Surat Dispensasi'],
            ['Id_Jenis_Surat' => 8, 'Nama_Surat' => 'Surat Keterangan Berkelakuan Baik'],
            ['Id_Jenis_Surat' => 9, 'Nama_Surat' => 'SK Fakultas'],
            ['Id_Jenis_Surat' => 10, 'Nama_Surat' => 'Peminjaman Gedung'],
            ['Id_Jenis_Surat' => 11, 'Nama_Surat' => 'Surat Lembur'],
        ];

        foreach ($surats as $surat) {
            JenisSurat::create($surat);
        }
    }
}
