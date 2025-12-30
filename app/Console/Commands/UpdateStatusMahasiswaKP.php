<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuratMagang;
use App\Models\Mahasiswa;
use Carbon\Carbon;

class UpdateStatusMahasiswaKP extends Command
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
    protected $description = 'Update status KP mahasiswa menjadi Telah_Melaksanakan jika tanggal selesai magang sudah tercapai';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mengecek status magang mahasiswa...');

        // Ambil semua surat magang yang sudah disetujui dan tanggal selesainya sudah lewat
        $suratMagang = SuratMagang::where('Status', 'Success')
            ->where('Acc_Dekan', 1)
            ->whereNotNull('Tanggal_Selesai')
            ->whereDate('Tanggal_Selesai', '<=', Carbon::today())
            ->get();

        $updated = 0;

        foreach ($suratMagang as $surat) {
            // Decode data mahasiswa
            $dataMahasiswa = is_array($surat->Data_Mahasiswa)
                ? $surat->Data_Mahasiswa
                : json_decode($surat->Data_Mahasiswa, true);

            if ($dataMahasiswa && is_array($dataMahasiswa)) {
                foreach ($dataMahasiswa as $mhs) {
                    if (isset($mhs['nim'])) {
                        // Update status mahasiswa yang masih "Sedang_Melaksanakan"
                        $result = Mahasiswa::where('NIM', $mhs['nim'])
                            ->where('Status_KP', 'Sedang_Melaksanakan')
                            ->update(['Status_KP' => 'Telah_Melaksanakan']);

                        if ($result > 0) {
                            $this->info("✓ Updated NIM: {$mhs['nim']} - Status: Telah_Melaksanakan");
                            $updated++;
                        }
                    }
                }
            }
        }

        $this->info("Selesai! Total mahasiswa yang diupdate: {$updated}");

        return Command::SUCCESS;
    }
}
