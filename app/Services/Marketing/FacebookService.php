<?php

namespace App\Services\Marketing;

use App\Models\Company;
use Illuminate\Support\Facades\Http;

class FacebookService
{
    public function post(Company $company, string $message): bool
    {
        $socialAccount = \App\Models\SocialAccount::where('company_id', $company->id)
                            ->where('provider', 'facebook')
                            ->first();

        $token  = $socialAccount?->page_token ?? $socialAccount?->token;
        $pageId = $socialAccount?->page_id;

        if (!$token || !$pageId) {
            \Log::warning("Facebook post skipped — Meta credentials missing for company {$company->id}");
            return false;
        }

        try {
            $response = Http::post("https://graph.facebook.com/v19.0/{$pageId}/feed", [
                'message'      => $message,
                'access_token' => $token,
            ]);

            if (!$response->successful()) {
                \Log::error("Facebook API Error: " . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            \Log::error("Facebook post failed: " . $e->getMessage());
            return false;
        }
    }
}
