<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeHelper
{
    /**
     * Generate QR Code dan simpan sebagai file PNG di storage
     * Return file path relatif untuk disimpan di database
     * 
     * @param string $data Data yang akan di-encode
     * @param int $size Ukuran QR code dalam pixel (default 200)
     * @return string|null Path relatif file QR code, atau null jika gagal
     */
    public static function generate($data, $size = 200)
    {
        try {
            // Generate nama file unik
            $filename = 'qr_' . Str::random(16) . '_' . time() . '.png';
            $relativePath = 'qr-codes/' . $filename;

            // Path absolut untuk simpan file
            $absolutePath = storage_path('app/public/' . $relativePath);

            // Pastikan direktori exists
            $dir = dirname($absolutePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Generate QR Code dengan constructor baru (Endroid v6.0)
            $qrCode = new QrCode(
                data: $data,
                size: $size,
                margin: 10
            );

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Simpan file
            $result->saveToFile($absolutePath);

            return $relativePath;

        } catch (\Exception $e) {
            \Log::error('QR Code generation exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate QR Code dan return URL publik
     * 
     * @param string $data Data yang akan di-encode
     * @param int $size Ukuran QR code dalam pixel (default 200)
     * @return string|null URL publik file QR code, atau null jika gagal
     */
    public static function generateUrl($data, $size = 200)
    {
        $path = self::generate($data, $size);
        if ($path) {
            return asset('storage/' . $path);
        }
        return null;
    }

    /**
     * Generate QR Code sebagai HTML img tag dengan src URL
     * 
     * @param string $data Data yang akan di-encode
     * @param int $size Ukuran QR code
     * @param string $alt Alt text
     * @return string HTML img tag
     */
    public static function html($data, $size = 200, $alt = 'QR Code')
    {
        $url = self::generateUrl($data, $size);
        if ($url) {
            return "<img src=\"{$url}\" alt=\"{$alt}\" width=\"{$size}\" height=\"{$size}\" />";
        }
        return "<p>QR Code generation failed</p>";
    }
}
