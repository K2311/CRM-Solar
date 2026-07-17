<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\SocialPost;
use App\Models\SocialAccount;
use App\Services\SocialMediaService;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled social media posts';

    /**
     * Execute the console command.
     */
    public function handle(SocialMediaService $socialMediaService)
    {
        $posts = SocialPost::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($posts as $post) {
            $account = SocialAccount::where('company_id', $post->company_id)->first();
            if (!$account) {
                $post->update(['status' => 'failed', 'error_message' => 'No social account connected.']);
                continue;
            }

            try {
                $fbId = null;
                $igId = null;

                if (in_array($post->platform, ['facebook', 'both'])) {
                    $fbId = $socialMediaService->publishToFacebook($account, $post->content, $post->media_urls);
                }

                if (in_array($post->platform, ['instagram', 'both'])) {
                    $igId = $socialMediaService->publishToInstagram($account, $post->content ?? '', $post->media_urls);
                }

                $post->update([
                    'status' => 'published',
                    'provider_post_id' => json_encode(['fb' => $fbId, 'ig' => $igId]),
                ]);
                $this->info("Published post ID: {$post->id}");
            } catch (\Exception $e) {
                $post->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $this->error("Failed post ID: {$post->id}. Error: {$e->getMessage()}");
            }
        }
    }
}
