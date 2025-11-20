<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuratVerification;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Generator;

class GenerateMissingQR extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:generate-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR Code untuk surat verification yang belum punya QR';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Mencari verification tanpa QR Code...');
        $this->newLine();

        $verifications = SuratVerification::whereNull('qr_path')
            ->orWhere('qr_path', '')
            ->get();

        $this->info("📊 Ditemukan: {$verifications->count()} verification");
        $this->newLine();

        if ($verifications->isEmpty()) {
            $this->comment('✨ Semua verification sudah memiliki QR Code!');
            return 0;
        }

        $bar = $this->output->createProgressBar($verifications->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($verifications as $v) {
            try {
                // Generate QR Code
                $verifyUrl = route('surat.verify', $v->token);
                $qrGenerator = new Generator;
                $qrCode = $qrGenerator->format('png')
                    ->size(300)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($verifyUrl);
                
                // Save to storage
                $qrFilename = 'qr_codes/' . $v->token . '.png';
                Storage::disk('public')->put($qrFilename, $qrCode);
                
                // Update database
                $v->qr_path = $qrFilename;
                $v->save();
                
                $success++;
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Error ID {$v->id}: " . $e->getMessage());
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Berhasil: {$success}");
        if ($failed > 0) {
            $this->error("❌ Gagal: {$failed}");
        }
        
        $this->newLine();
        $this->comment('✨ Selesai!');

        return 0;
    }
}
