<?php

namespace App\Services\Marketing;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS-style message via WhatsApp Cloud API (replaces Twilio).
     * Falls back to WhatsApp text message since we no longer use Twilio.
     */
    public function send(Company $company, string $to, string $message): bool
    {
        $accessToken = $company->setting('whatsapp_access_token');
        $phoneId     = $company->setting('whatsapp_phone_number_id');

        if (!$accessToken || !$phoneId) {
            Log::warning("WhatsApp SMS not sent - API credentials missing for company {$company->id}");
            return false;
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $to);
        $url = "https://graph.facebook.com/v19.0/{$phoneId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $cleanPhone,
            'type' => 'text',
            'text' => [
                'body' => $message,
            ]
        ];

        try {
            $response = Http::withToken($accessToken)->post($url, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp text message sent to {$to} (Company: {$company->id})");
                return true;
            }

            Log::error("WhatsApp text message failed: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp text message exception: " . $e->getMessage());
            return false;
        }
    }
}
