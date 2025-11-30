<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuratMagang;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class RegenerateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:regenerate {--all : Regenerate all QR codes} {--missing : Only regenerate missing QR codes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate QR codes for Surat Magang';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $onlyMissing = $this->option('missing');
        $all = $this->option('all');

        if (!$onlyMissing && !$all) {
            $this->error('Please specify --all or --missing option');
            return 1;
        }

        $query = SuratMagang::where('Acc_Koordinator', 1);

        if ($onlyMissing) {
            $this->info('Checking for missing QR codes...');
        } else {
            $this->info('Regenerating all QR codes...');
        }

        $surats = $query->get();
        $regenerated = 0;
        $skipped = 0;

        $this->withProgressBar($surats, function ($surat) use ($onlyMissing, &$regenerated, &$skipped) {
            // Check if file exists
            if ($onlyMissing && $surat->Qr_code) {
                $qrPath = storage_path('app/public/' . $surat->Qr_code);
                if (file_exists($qrPath)) {
                    $skipped++;
                    return;
                }
            }

            // Generate QR Code
            $verificationUrl = url('/verify-surat/' . $surat->id_no);
            $qrCodePath = 'qrcodes/surat_magang_' . $surat->id_no . '.png';

            // Create QR code
            $qrCode = QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->generate($verificationUrl);

            // Save to storage
            Storage::disk('public')->put($qrCodePath, $qrCode);

            // Update database
            $surat->Qr_code = $qrCodePath;
            $surat->save();

            $regenerated++;
        });

        $this->newLine(2);
        $this->info("Regenerated: {$regenerated} QR codes");
        if ($skipped > 0) {
            $this->info("Skipped: {$skipped} (files already exist)");
        }

        return 0;
    }
}
