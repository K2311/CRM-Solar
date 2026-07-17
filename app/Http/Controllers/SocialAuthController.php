<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use App\Services\SocialMediaService;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')
            ->scopes(['pages_show_list', 'pages_read_engagement', 'pages_manage_posts', 'instagram_basic', 'instagram_content_publish'])
            ->redirect();
    }

    public function callback(Request $request, SocialMediaService $socialMediaService)
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            return redirect('/settings/social')->with('error', 'Failed to authenticate with Facebook.');
        }

        $user = $request->user();
        if (!$user || !$user->canDo('marketing.view')) {
            abort(403);
        }

        $socialAccount = SocialAccount::updateOrCreate(
            ['company_id' => app('current_company_id'), 'provider' => 'facebook'],
            [
                'provider_id' => $facebookUser->getId(),
                'token' => $facebookUser->token,
            ]
        );

        // Fetch Pages and auto-select the first one for simplicity, 
        // in a real app, you'd show a UI to select which page to use.
        try {
            $pages = $socialMediaService->getPages($facebookUser->token);
            if (!empty($pages)) {
                $page = $pages[0];
                $socialAccount->page_id = $page['id'];
                $socialAccount->page_token = $page['access_token'];

                // Try to get linked IG account
                $igAccountId = $socialMediaService->getInstagramAccount($page['id'], $page['access_token']);
                if ($igAccountId) {
                    $socialAccount->instagram_account_id = $igAccountId;
                }
                $socialAccount->save();
            }
        } catch (\Exception $e) {
            // Log error, but account is still linked
        }

        return redirect('/settings/social')->with('success', 'Facebook account connected successfully.');
    }
}
