<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaService
{
    protected $baseUrl;
    protected $session;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('WAHA_URL');
        $this->session = env('WAHA_SESSION', 'default');
        $this->apiKey = env('WAHA_API_KEY');
    }

    /**
     * Kirim pesan teks via WAHA
     */
    public function sendMessage($phoneNumber, $message)
    {
        $chatId = $this->formatPhoneNumber($phoneNumber);

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
            ])->timeout(10)->post("{$this->baseUrl}/api/sendText", [
                        'session' => $this->session,
                        'chatId' => $chatId,
                        'text' => $message,
                    ]);

            if (!$response->successful()) {
                Log::error("WAHA Failed Response: " . $response->body());
                echo "WAHA Error Body: " . $response->body() . "\n";
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WAHA Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format nomor HP ke format chatId WhatsApp (62xxx@c.us)
     */
    private function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }
        return $number . '@c.us';
    }
}