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
            // Check session status first to avoid hanging if session is not working
            if (!$this->isSessionWorking()) {
                Log::warning('WAHA: Skipping message send because session is not WORKING', [
                    'session' => $this->session
                ]);
                return false;
            }

            $url = "{$this->baseUrl}/api/sendText";

            Log::info('WAHA: Attempting to send message', [
                'url' => $url,
                'chatId' => $chatId,
                'session' => $this->session
            ]);

            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
            ])->timeout(60)->post($url, [
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
     * Cek apakah sesi WAHA aktif dan siap kirim pesan
     */
    public function isSessionWorking()
    {
        try {
            $url = "{$this->baseUrl}/api/sessions";
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
            ])->timeout(5)->get($url);

            if ($response->successful()) {
                $sessions = $response->json();
                foreach ($sessions as $session) {
                    if ($session['name'] === $this->session) {
                        return $session['status'] === 'WORKING';
                    }
                }
            }
            return false;
        } catch (\Exception $e) {
            Log::error("WAHA Session Check Error: " . $e->getMessage());
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