<?php

namespace App\Services;

use App\Models\SocialAccount;
use Illuminate\Support\Facades\Http;
use Exception;

class SocialMediaService
{
    private const GRAPH_VERSION = 'v19.0';
    private const BASE_URL = 'https://graph.facebook.com/' . self::GRAPH_VERSION;

    public function getPages(string $userAccessToken): array
    {
        $response = Http::get(self::BASE_URL . '/me/accounts', [
            'access_token' => $userAccessToken,
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to fetch pages: ' . $response->body());
        }

        return $response->json('data') ?? [];
    }

    public function getInstagramAccount(string $pageId, string $pageAccessToken): ?string
    {
        $response = Http::get(self::BASE_URL . '/' . $pageId, [
            'fields' => 'instagram_business_account',
            'access_token' => $pageAccessToken,
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to fetch Instagram account: ' . $response->body());
        }

        return $response->json('instagram_business_account.id');
    }

    public function publishToFacebook(SocialAccount $account, ?string $message, ?array $mediaUrls = null, string $postType = 'feed'): string
    {
        if (!$account->page_id || !$account->page_token) {
            throw new Exception('No Facebook Page connected.');
        }

        $endpoint = '/feed';
        $payload = [
            'message' => $message ?? '',
            'access_token' => $account->page_token,
        ];

        if (!empty($mediaUrls)) {
            $mediaUrl = $mediaUrls[0];
            $isVideo = preg_match('/\.mp4$/i', $mediaUrl);
            
            if ($postType === 'story') {
                if ($isVideo) {
                    $endpoint = '/video_stories';
                    $payload['video_url'] = $mediaUrl;
                } else {
                    $endpoint = '/photo_stories';
                    $payload['url'] = $mediaUrl;
                }
                unset($payload['message']); // FB Stories don't use standard message fields in API
            } elseif ($postType === 'reel' || $isVideo) {
                // Video post or Reel
                $endpoint = '/videos';
                $payload['file_url'] = $mediaUrl;
                $payload['description'] = $message ?? '';
                unset($payload['message']);
            } else {
                // Photo feed post
                $endpoint = '/photos';
                $payload['url'] = $mediaUrl;
            }
        } elseif ($postType === 'story' || $postType === 'reel') {
            throw new Exception("Facebook {$postType}s require a media file.");
        }

        $response = Http::post(self::BASE_URL . '/' . $account->page_id . $endpoint, $payload);

        if ($response->failed()) {
            throw new Exception('Facebook publish failed: ' . $response->body());
        }

        return $response->json('id');
    }

    public function publishToInstagram(SocialAccount $account, ?string $caption, array $mediaUrls, string $postType = 'feed'): string
    {
        if (!$account->instagram_account_id || !$account->page_token) {
            throw new Exception('No Instagram Account connected.');
        }

        if (empty($mediaUrls)) {
            throw new Exception('Instagram requires media (image or video).');
        }

        $mediaUrl = $mediaUrls[0];
        $isVideo = preg_match('/\.mp4$/i', $mediaUrl);

        $payload = [
            'access_token' => $account->page_token,
            'caption' => $caption ?? '',
        ];

        // Set media_type depending on post_type and file type
        if ($postType === 'reel') {
            $payload['media_type'] = 'REELS';
            $payload['video_url'] = $mediaUrl;
        } elseif ($postType === 'story') {
            $payload['media_type'] = 'STORIES';
            if ($isVideo) {
                $payload['video_url'] = $mediaUrl;
            } else {
                $payload['image_url'] = $mediaUrl;
            }
            unset($payload['caption']); // Stories usually don't support traditional captions in the same way via API
        } else {
            // Standard feed post
            if ($isVideo) {
                $payload['media_type'] = 'VIDEO';
                $payload['video_url'] = $mediaUrl;
            } else {
                $payload['image_url'] = $mediaUrl;
            }
        }

        // Step 1: Create Container
        $containerResponse = Http::post(self::BASE_URL . '/' . $account->instagram_account_id . '/media', $payload);

        if ($containerResponse->failed()) {
            throw new Exception('Instagram container creation failed: ' . $containerResponse->body());
        }

        $creationId = $containerResponse->json('id');

        // Step 2: Publish Container
        $publishResponse = Http::post(self::BASE_URL . '/' . $account->instagram_account_id . '/media_publish', [
            'creation_id' => $creationId,
            'access_token' => $account->page_token,
        ]);

        if ($publishResponse->failed()) {
            throw new Exception('Instagram publish failed: ' . $publishResponse->body());
        }

        return $publishResponse->json('id');
    }
}
