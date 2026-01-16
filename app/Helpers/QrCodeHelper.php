<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeHelper
{
    /**
     * Generate QR Code menggunakan Endroid library
     * Simpan sebagai file PNG di storage dan return path relatif
     * 
     * @param string $data Data yang akan di-encode (URL verifikasi)
     * @param int $boxSize Ukuran pixel per box (default 10, untuk backward compat convert ke size)
     * @return string|null Path relatif file QR code (e.g., 'qr-codes/qr_xxx.png'), atau null jika gagal
     */
    public static function generate($data, $boxSize = 10)
    {
        try {
            // Generate nama file unik
            $filename = 'qr_' . Str::random(16) . '.png';
            $relativePath = 'qr-codes/' . $filename;

            // Path absolut untuk simpan file
            $absolutePath = storage_path('app/public/' . $relativePath);

            // Pastikan direktori ada
            $directory = dirname($absolutePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            // Convert boxSize to pixel size (boxSize 10 = ~200px)
            $size = $boxSize * 20;

            // Generate QR Code dengan Endroid v6.0 (readonly constructor)
            $qrCode = new QrCode(
                data: $data,
                size: $size,
                margin: 10
            );

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Simpan file
            $result->saveToFile($absolutePath);

            // Cek apakah berhasil
            if (file_exists($absolutePath)) {
                // Return path relatif untuk disimpan ke database
                return $relativePath;
            } else {
                \Log::error('Failed to save QR Code file', [
                    'path' => $absolutePath
                ]);
                return null;
            }

        } catch (\Exception $e) {
            \Log::error('QR Code generation exception: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

    /**
     * Generate QR Code dan return absolute file path (untuk PDF overlay)
     * 
     * @param string $data Data yang akan di-encode (biasanya JSON string)
     * @param string $prefix Prefix nama file (opsional)
     * @param int $boxSize Ukuran pixel per box
     * @return string Absolute path file QR code
     */
    public static function generateQrCode($data, $prefix = 'qr', $boxSize = 10)
    {
        try {
            // Generate nama file unik
            $filename = $prefix . '_' . Str::random(16) . '.png';
            $relativePath = 'qr-codes/' . $filename;

            // Path absolut untuk simpan file
            $absolutePath = storage_path('app/public/' . $relativePath);

            // Pastikan direktori ada
            $directory = dirname($absolutePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            // Convert boxSize to pixel size
            $size = $boxSize * 20;

            // Generate QR Code
            $qrCode = new QrCode(
                data: $data,
                size: $size,
                margin: 10
            );

            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $result->saveToFile($absolutePath);

            // Cek berhasil
            if (file_exists($absolutePath)) {
                return $absolutePath; // Return absolute path untuk FPDI/FPDF
            } else {
                \Log::error('Failed to save QR Code file', [
                    'path' => $absolutePath
                ]);
                throw new \Exception('Failed to save QR Code file');
            }

        } catch (\Exception $e) {
            \Log::error('QR Code generation exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate QR Code dan return relative path untuk disimpan di database
     * 
     * @param string $data Data yang akan di-encode
     * @param string $prefix Prefix nama file
     * @param int $boxSize Ukuran pixel per box
     * @return string Relative path file QR code (e.g., 'qr-codes/qr_xxx.png')
     */
    public static function generateAndGetPath($data, $prefix = 'qr', $boxSize = 10)
    {
        try {
            // Generate nama file unik
            $filename = $prefix . '_' . Str::random(16) . '.png';
            $relativePath = 'qr-codes/' . $filename;
            
            // Path absolut untuk simpan file
            $absolutePath = storage_path('app/public/' . $relativePath);
            
            // Pastikan direktori ada
            $directory = dirname($absolutePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            // Convert boxSize to pixel size
            $size = $boxSize * 20;
            
            // Generate QR Code
            $qrCode = new QrCode(
                data: $data,
                size: $size,
                margin: 10
            );
            
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $result->saveToFile($absolutePath);
            
            // Cek berhasil
            if (file_exists($absolutePath)) {
                return $relativePath; // Return relative path untuk database
            } else {
                \Log::error('Failed to save QR Code file', [
                    'path' => $absolutePath
                ]);
                throw new \Exception('Failed to save QR Code file');
            }
            
        } catch (\Exception $e) {
            \Log::error('QR Code generation exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
