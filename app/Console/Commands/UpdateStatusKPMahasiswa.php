<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mahasiswa;
use App\Models\SuratMagang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateStatusKPMahasiswa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mahasiswa:update-status-kp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Status_KP mahasiswa menjadi Telah_Melaksanakan jika durasi magang sudah selesai';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai update Status_KP mahasiswa...');

        // Ambil semua surat magang yang:
        // 1. Status = Success
        // 2. Acc_Koordinator = 1 (sudah disetujui Kaprodi)
        // 3. Acc_Dekan = 1 (sudah disetujui Dekan)
        // 4. Tanggal_Selesai sudah lewat
        $suratMagangSelesai = SuratMagang::where('Status', 'Success')
            ->where('Acc_Koordinator', 1)
            ->where('Acc_Dekan', 1)
            ->whereDate('Tanggal_Selesai', '<', Carbon::today())
            ->with('tugasSurat')
            ->get();

        $updated = 0;

        $this->info("Ditemukan {$suratMagangSelesai->count()} surat magang yang memenuhi kriteria.");

        foreach ($suratMagangSelesai as $suratMagang) {
            // Ambil data mahasiswa dari JSON
            $dataMahasiswa = $suratMagang->Data_Mahasiswa;

            if (!is_array($dataMahasiswa)) {
                continue;
            }

            foreach ($dataMahasiswa as $mhs) {
                $nim = $mhs['nim'] ?? null;

                if (!$nim) {
                    continue;
                }

                // Update status mahasiswa
                $mahasiswa = Mahasiswa::where('NIM', $nim)
                    ->where('Status_KP', 'Sedang_Melaksanakan')
                    ->first();

                if ($mahasiswa) {
                    $mahasiswa->Status_KP = 'Telah_Melaksanakan';
                    $mahasiswa->save();
                    $updated++;

                    $this->info("✓ Update mahasiswa: {$mahasiswa->Nama_Mahasiswa} (NIM: {$nim}) → Telah_Melaksanakan");
                }
            }
        }

        $this->info("Selesai! Total mahasiswa yang diupdate: {$updated}");

        return 0;
    }
}
