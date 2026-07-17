<?php

namespace App\Http\Controllers;

use App\Models\SocialPost;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use App\Services\SocialMediaService;

class SocialPostController extends Controller
{
    public function index(Request $request)
    {
        $posts = SocialPost::where('company_id', app('current_company_id'))->latest()->get();
        return view('social.index', compact('posts'));
    }

    public function create()
    {
        return view('social.compose');
    }

    public function store(Request $request, SocialMediaService $socialMediaService)
    {
        $request->validate([
            'content' => 'required_without:media',
            'platform' => 'required|in:facebook,instagram,both',
            'post_type' => 'required|in:feed,reel,story',
            'scheduled_at' => 'nullable|date|after:now',
            'media' => 'nullable|file|mimes:jpeg,png,mp4|max:10240', // 10MB max
        ]);

        $user = $request->user();
        
        $mediaUrls = [];
        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('social_media', 'public');
            $mediaUrls[] = asset('storage/' . $path);
        }

        $status = $request->scheduled_at ? 'scheduled' : 'draft'; // Wait to publish

        $post = SocialPost::create([
            'company_id' => app('current_company_id'),
            'user_id' => $user->id,
            'content' => $request->content,
            'media_urls' => $mediaUrls,
            'platform' => $request->platform,
            'post_type' => $request->post_type,
            'status' => $status,
            'scheduled_at' => $request->scheduled_at,
        ]);

        if (!$request->scheduled_at) {
            // Publish immediately
            $this->publishPost($post, $socialMediaService);
            return redirect()->route('social.index')->with('success', 'Post published successfully!');
        }

        return redirect()->route('social.index')->with('success', 'Post scheduled successfully!');
    }

    public function edit(SocialPost $post)
    {
        if ($post->company_id !== app('current_company_id')) abort(403);
        if ($post->status === 'published') return redirect()->route('social.index')->with('error', 'Cannot edit published posts.');
        
        return view('social.edit', compact('post'));
    }

    public function update(Request $request, SocialPost $post, SocialMediaService $socialMediaService)
    {
        if ($post->company_id !== app('current_company_id')) abort(403);
        if ($post->status === 'published') return redirect()->route('social.index')->with('error', 'Cannot update published posts.');

        $request->validate([
            'content' => 'required_without:media',
            'platform' => 'required|in:facebook,instagram,both',
            'post_type' => 'required|in:feed,reel,story',
            'scheduled_at' => 'nullable|date',
            'media' => 'nullable|file|mimes:jpeg,png,mp4|max:10240',
        ]);

        $mediaUrls = $post->media_urls ?? [];
        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('social_media', 'public');
            $mediaUrls = [asset('storage/' . $path)]; // Replace old media
        }

        $post->update([
            'content' => $request->content,
            'media_urls' => $mediaUrls,
            'platform' => $request->platform,
            'post_type' => $request->post_type,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->scheduled_at ? 'scheduled' : 'draft',
        ]);

        if (!$request->scheduled_at) {
            $this->publishPost($post, $socialMediaService);
            return redirect()->route('social.index')->with('success', 'Post published successfully!');
        }

        return redirect()->route('social.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(SocialPost $post)
    {
        if ($post->company_id !== app('current_company_id')) abort(403);
        $post->delete();
        return redirect()->route('social.index')->with('success', 'Post deleted successfully!');
    }

    private function publishPost(SocialPost $post, SocialMediaService $socialMediaService)
    {
        $account = SocialAccount::where('company_id', $post->company_id)->first();
        if (!$account) {
            $post->update(['status' => 'failed', 'error_message' => 'No social account connected.']);
            return;
        }

        try {
            $fbId = null;
            $igId = null;

            if (in_array($post->platform, ['facebook', 'both'])) {
                $fbId = $socialMediaService->publishToFacebook($account, $post->content, $post->media_urls, $post->post_type);
            }

            if (in_array($post->platform, ['instagram', 'both'])) {
                $igId = $socialMediaService->publishToInstagram($account, $post->content ?? '', $post->media_urls, $post->post_type);
            }

            $post->update([
                'status' => 'published',
                'provider_post_id' => json_encode(['fb' => $fbId, 'ig' => $igId]),
            ]);
        } catch (\Exception $e) {
            $post->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
