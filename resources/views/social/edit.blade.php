<x-app-layout title="Edit Post">
    <div class="card glass-card" style="margin-bottom: 2rem; max-width: 800px;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem;">Edit Social Media Post</h3>
        <form action="{{ route('social.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea name="content" rows="4" class="form-control" placeholder="Write your post here...">{{ old('content', $post->content) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Media (Photo/Video)</label>
                @if(!empty($post->media_urls))
                    <div style="margin-bottom: 1rem;">
                        <span style="font-size: 0.875rem; color: var(--text-muted);">Current Media uploaded. Uploading new media will replace it.</span>
                    </div>
                @endif
                <input type="file" name="media" accept="image/*,video/mp4" class="form-control" style="padding: 0.5rem;">
            </div>

            <div class="form-group">
                <label class="form-label">Post Type</label>
                <select name="post_type" class="form-control">
                    <option value="feed" {{ (old('post_type', $post->post_type) === 'feed') ? 'selected' : '' }}>Standard Feed Post</option>
                    <option value="reel" {{ (old('post_type', $post->post_type) === 'reel') ? 'selected' : '' }}>Reel (Video only)</option>
                    <option value="story" {{ (old('post_type', $post->post_type) === 'story') ? 'selected' : '' }}>Story (Image or Video)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Platform</label>
                <select name="platform" class="form-control">
                    <option value="both" {{ (old('platform', $post->platform) === 'both') ? 'selected' : '' }}>Both Facebook & Instagram</option>
                    <option value="facebook" {{ (old('platform', $post->platform) === 'facebook') ? 'selected' : '' }}>Facebook Only</option>
                    <option value="instagram" {{ (old('platform', $post->platform) === 'instagram') ? 'selected' : '' }}>Instagram Only</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Schedule For (Leave blank to publish immediately)</label>
                <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}">
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                <a href="{{ route('social.index') }}" class="btn btn-outline ml-2">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const postTypeSelect = document.querySelector('select[name="post_type"]');
            const platformSelect = document.querySelector('select[name="platform"]');
            const mediaInput = document.querySelector('input[name="media"]');

            function updateFormState() {
                const type = postTypeSelect.value;
                
                // Update media accept attribute
                if (type === 'reel') {
                    mediaInput.setAttribute('accept', 'video/mp4,video/x-m4v,video/*');
                } else {
                    mediaInput.setAttribute('accept', 'image/*,video/mp4,video/x-m4v,video/*');
                }

                // Restrict platforms: Stories are usually IG only in this context
                if (type === 'story') {
                    Array.from(platformSelect.options).forEach(opt => {
                        if (opt.value !== 'instagram') opt.disabled = true;
                    });
                    platformSelect.value = 'instagram';
                } else {
                    Array.from(platformSelect.options).forEach(opt => opt.disabled = false);
                }
            }

            postTypeSelect.addEventListener('change', updateFormState);
            updateFormState();
        });
    </script>
</x-app-layout>
