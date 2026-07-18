<?php

namespace App\Services\Marketing;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a template or text message via direct Meta WhatsApp Cloud API.
     */
    public function send(Company $company, string $to, string $body, string $templateName = null, array $templateParams = []): bool
    {
        $accessToken = $company->setting('whatsapp_access_token');
        $phoneId     = $company->setting('whatsapp_phone_number_id');

        if (!$accessToken || !$phoneId) {
            Log::warning("WhatsApp Cloud API message not sent - Credentials missing for company {$company->id}");
            return false;
        }

        // Clean phone number (needs country code, no +, no spaces)
        $cleanPhone = preg_replace('/[^0-9]/', '', $to);

        $url = "https://graph.facebook.com/v19.0/{$phoneId}/messages";

        if ($templateName) {
            $parameters = [];
            foreach ($templateParams as $param) {
                $parameters[] = [
                    'type' => 'text',
                    'text' => $param,
                ];
            }

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $cleanPhone,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => 'en_US',
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => $parameters,
                        ]
                    ]
                ]
            ];
        } else {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $cleanPhone,
                'type' => 'text',
                'text' => [
                    'body' => $body,
                ]
            ];
        }

        try {
            $response = Http::withToken($accessToken)
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp Cloud API message successfully sent to {$to} (Company: {$company->id})");
                return true;
            }

            Log::error("WhatsApp Cloud API send failed: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp Cloud API exception: " . $e->getMessage());
            return false;
        }
    }
}
