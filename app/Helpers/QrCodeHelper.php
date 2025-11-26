<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeHelper
{
    /**
     * Generate QR Code menggunakan Python script
     * Simpan sebagai file PNG di storage dan return URL publik
     * 
     * @param string $data Data yang akan di-encode (URL verifikasi)
     * @param int $boxSize Ukuran pixel per box (default 10, ignored for backward compat)
     * @return string|null URL publik file QR code, atau null jika gagal
     */
    public static function generate($data, $boxSize = 10)
    {
        try {
            // Generate nama file unik
            $filename = 'qr_' . Str::random(16) . '.png';
            $relativePath = 'qr-codes/' . $filename;
            
            // Path absolut untuk simpan file
            $absolutePath = storage_path('app/public/' . $relativePath);
            
            // Path ke Python script
            $scriptPath = base_path('generate_qr.py');
            
            // Escape arguments untuk command line
            $dataEscaped = escapeshellarg($data);
            $pathEscaped = escapeshellarg($absolutePath);
            
            // Execute Python script
            $command = "python \"{$scriptPath}\" {$dataEscaped} {$pathEscaped} {$boxSize}";
            $output = [];
            $returnCode = 0;
            
            exec($command . ' 2>&1', $output, $returnCode);
            
            // Cek apakah berhasil
            if ($returnCode === 0 && file_exists($absolutePath)) {
                // Return URL publik untuk akses file
                return asset('storage/' . $relativePath);
            } else {
                \Log::error('Failed to generate QR Code', [
                    'command' => $command,
                    'output' => implode("\n", $output),
                    'return_code' => $returnCode
                ]);
                return null;
            }
            
        } catch (\Exception $e) {
            \Log::error('QR Code generation exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate QR Code dan return path file relatif (untuk disimpan di database)
     * 
     * @param string $data Data yang akan di-encode
     * @param int $boxSize Ukuran pixel per box
     * @return string|null Path relatif file QR code (qr-codes/xxx.png)
     */
    public static function generateAndGetPath($data, $boxSize = 10)
    {
        try {
            // Generate nama file unik
            $filename = 'qr_' . Str::random(16) . '.png';
            $relativePath = 'qr-codes/' . $filename;
            
            // Path absolut untuk simpan file
            $absolutePath = storage_path('app/public/' . $relativePath);
            
            // Path ke Python script
            $scriptPath = base_path('generate_qr.py');
            
            // Escape arguments
            $dataEscaped = escapeshellarg($data);
            $pathEscaped = escapeshellarg($absolutePath);
            
            // Execute Python script
            $command = "python \"{$scriptPath}\" {$dataEscaped} {$pathEscaped} {$boxSize}";
            $output = [];
            $returnCode = 0;
            
            exec($command . ' 2>&1', $output, $returnCode);
            
            // Cek berhasil
            if ($returnCode === 0 && file_exists($absolutePath)) {
                return $relativePath; // Return path relatif untuk DB
            } else {
                \Log::error('Failed to generate QR Code', [
                    'command' => $command,
                    'output' => implode("\n", $output),
                    'return_code' => $returnCode
                ]);
                return null;
            }
            
        } catch (\Exception $e) {
            \Log::error('QR Code generation exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate QR Code sebagai HTML img tag
     * 
     * @param string $data Data yang akan di-encode
     * @param int $size Ukuran QR code
     * @param string $alt Alt text
     * @return string HTML img tag
     */
    public static function html($data, $size = 300, $alt = 'QR Code')
    {
        $url = self::generate($data, 10);
        if ($url) {
            return "<img src=\"{$url}\" alt=\"{$alt}\" width=\"{$size}\" height=\"{$size}\" />";
        }
        return "<p>QR Code generation failed</p>";
    }
}
