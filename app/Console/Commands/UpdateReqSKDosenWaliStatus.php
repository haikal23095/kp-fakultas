<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateReqSKDosenWaliStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sk:update-req-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status Req_SK_Dosen_Wali berdasarkan status Acc_SK_Dosen_Wali';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai update status Req_SK_Dosen_Wali...');

        try {
            // Get all Acc_SK_Dosen_Wali yang statusnya Selesai
            $accSelesai = DB::table('Acc_SK_Dosen_Wali')
                ->where('Status', 'Selesai')
                ->pluck('No');

            $this->info("Ditemukan {$accSelesai->count()} SK yang sudah Selesai di Acc_SK_Dosen_Wali");

            if ($accSelesai->isEmpty()) {
                $this->warn('Tidak ada SK dengan status Selesai di Acc_SK_Dosen_Wali');
                return 0;
            }

            // Update Req_SK_Dosen_Wali yang terhubung
            $updated = DB::table('Req_SK_Dosen_Wali')
                ->whereIn('Id_Acc_SK_Dosen_Wali', $accSelesai)
                ->where('Status', '!=', 'Selesai')
                ->update(['Status' => 'Selesai']);

            $this->info("Berhasil update {$updated} record di Req_SK_Dosen_Wali menjadi Selesai");

            // Show detail
            $this->newLine();
            $this->info('Detail update per Acc_SK:');
            foreach ($accSelesai as $accNo) {
                $count = DB::table('Req_SK_Dosen_Wali')
                    ->where('Id_Acc_SK_Dosen_Wali', $accNo)
                    ->where('Status', 'Selesai')
                    ->count();

                $this->line("  Acc_SK No. {$accNo}: {$count} Req_SK updated");
            }

            $this->newLine();
            $this->info('✓ Update selesai!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
