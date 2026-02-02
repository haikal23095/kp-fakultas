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
        // Remove trailing slash from base URL to prevent double slashes
        $this->baseUrl = rtrim(env('WAHA_URL'), '/');
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
            $url = "{$this->baseUrl}/api/sendText";

            Log::info('WAHA: Attempting to send message', [
                'url' => $url,
                'chatId' => $chatId,
                'session' => $this->session
            ]);

            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
            ])->timeout(30)->post($url, [
                        'session' => $this->session,
                        'chatId' => $chatId,
                        'text' => $message,
                    ]);

            if ($response->successful()) {
                Log::info('WAHA: Message sent successfully', [
                    'chatId' => $chatId,
                    'status' => $response->status()
                ]);
                return true;
            } else {
                Log::error("WAHA Failed Response", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'chatId' => $chatId
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error("WAHA Error", [
                'message' => $e->getMessage(),
                'chatId' => $chatId
            ]);
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