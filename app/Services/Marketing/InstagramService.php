<?php

namespace App\Services\Marketing;

use App\Models\Company;
use Illuminate\Support\Facades\Http;

class InstagramService
{
    public function post(Company $company, string $message): bool
    {
        $token    = $company->setting('meta_access_token');
        $igAccId  = $company->setting('meta_ig_business_id');

        if (!$token || !$igAccId) {
            \Log::warning("Instagram post skipped — Meta IG credentials missing for company {$company->id}");
            return false;
        }

        try {
            // Step 1: Create media container
            $container = Http::post("https://graph.facebook.com/v19.0/{$igAccId}/media", [
                'caption'      => $message,
                'media_type'   => 'TEXT',
                'access_token' => $token,
            ]);

            if (!$container->successful()) return false;

            $creationId = $container->json('id');

            // Step 2: Publish
            $publish = Http::post("https://graph.facebook.com/v19.0/{$igAccId}/media_publish", [
                'creation_id'  => $creationId,
                'access_token' => $token,
            ]);

            return $publish->successful();
        } catch (\Exception $e) {
            \Log::error("Instagram post failed: " . $e->getMessage());
            return false;
        }
    }
}
