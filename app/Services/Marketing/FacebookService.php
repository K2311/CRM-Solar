<?php

namespace App\Services\Marketing;

use App\Models\Company;
use Illuminate\Support\Facades\Http;

class FacebookService
{
    public function post(Company $company, string $message): bool
    {
        $token  = $company->setting('meta_access_token');
        $pageId = $company->setting('meta_page_id');

        if (!$token || !$pageId) {
            \Log::warning("Facebook post skipped — Meta credentials missing for company {$company->id}");
            return false;
        }

        try {
            $response = Http::post("https://graph.facebook.com/v19.0/{$pageId}/feed", [
                'message'      => $message,
                'access_token' => $token,
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            \Log::error("Facebook post failed: " . $e->getMessage());
            return false;
        }
    }
}
