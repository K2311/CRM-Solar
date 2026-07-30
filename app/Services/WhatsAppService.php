<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $accessToken;
    protected string $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = 'https://graph.facebook.com/v19.0/';
        $this->accessToken = \App\Models\CompanySetting::where('key', 'whatsapp_access_token')->value('value') ?: config('services.whatsapp.token', env('WHATSAPP_TOKEN', ''));
        $this->phoneNumberId = \App\Models\CompanySetting::where('key', 'whatsapp_phone_number_id')->value('value') ?: config('services.whatsapp.phone_number_id', env('WHATSAPP_PHONE_NUMBER_ID', ''));
    }

    /**
     * Send a text message via WhatsApp Cloud API.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function sendMessage(string $to, string $message): bool
    {
        $response = Http::withToken($this->accessToken)->post($this->apiUrl . $this->phoneNumberId . '/messages', [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $message,
            ],
        ]);

        if ($response->successful()) {
            return true;
        }

        Log::error('WhatsApp API Error', ['response' => $response->json()]);
        return false;
    }
}
