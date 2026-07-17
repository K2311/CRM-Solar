<?php

namespace App\Services\Marketing;

use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppService
{
    public function send(Company $company, string $to, string $message): bool
    {
        $sid   = $company->setting('twilio_sid');
        $token = $company->setting('twilio_token');
        $from  = $company->setting('twilio_whatsapp_from', 'whatsapp:+14155238886');

        if (!$sid || !$token) {
            Log::warning("WhatsApp not sent — Twilio credentials missing for company {$company->id}");
            return false;
        }

        try {
            $client = new Client($sid, $token);
            $client->messages->create('whatsapp:' . $to, [
                'from' => $from,
                'body' => $message,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("WhatsApp send failed: " . $e->getMessage());
            return false;
        }
    }
}
