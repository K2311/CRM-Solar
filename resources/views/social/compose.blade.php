<x-app-layout title="Compose Post">
    <div style="max-width: 1200px; margin: 0 auto;" x-data="socialCreator()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Create Social Media Post</h1>
                <p style="color: var(--text-muted);">Publish updates to your Facebook and Instagram pages.</p>
            </div>
            <a href="{{ route('social.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem; align-items: start;">
            <!-- Form -->
            <div class="card">
                <form action="{{ route('social.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div style="margin-bottom: 1.5rem;">
                        <label class="form-label">Platform</label>
                        <select name="platform" class="form-control" x-model="platform" required>
                            <option value="both">Both Facebook & Instagram</option>
                            <option value="facebook">Facebook Only</option>
                            <option value="instagram">Instagram Only</option>
                        </select>
                    </div>

                    <!-- Post Type -->
                    <label class="form-label">Post Type</label>
                    <div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <label
                            :style="postType === 'feed'
                                ? 'flex:1; display:flex; align-items:center; gap:0.6rem; padding:0.75rem 1rem; border-radius:0.6rem; cursor:pointer; border:2px solid var(--primary); background:rgba(var(--primary-rgb,16,185,129),0.08);'
                                : 'flex:1; display:flex; align-items:center; gap:0.6rem; padding:0.75rem 1rem; border-radius:0.6rem; cursor:pointer; border:2px solid var(--border); background:transparent;'">
                            <input type="radio" name="post_type" value="feed" x-model="postType" style="display:none;">
                            <span style="font-size:1.4rem;">📄</span>
                            <span>
                                <strong style="display:block; font-size:0.9rem;">Feed Post</strong>
                                <small style="color:var(--text-muted); font-size:0.75rem;">Standard post</small>
                            </span>
                        </label>
                        <label
                            :style="postType === 'reel'
                                ? 'flex:1; display:flex; align-items:center; gap:0.6rem; padding:0.75rem 1rem; border-radius:0.6rem; cursor:pointer; border:2px solid var(--primary); background:rgba(var(--primary-rgb,16,185,129),0.08);'
                                : 'flex:1; display:flex; align-items:center; gap:0.6rem; padding:0.75rem 1rem; border-radius:0.6rem; cursor:pointer; border:2px solid var(--border); background:transparent;'">
                            <input type="radio" name="post_type" value="reel" x-model="postType" style="display:none;">
                            <span style="font-size:1.4rem;">🎬</span>
                            <span>
                                <strong style="display:block; font-size:0.9rem;">Reel</strong>
                                <small style="color:var(--text-muted); font-size:0.75rem;">Short video format</small>
                            </span>
                        </label>
                        <label
                            :style="postType === 'story'
                                ? 'flex:1; display:flex; align-items:center; gap:0.6rem; padding:0.75rem 1rem; border-radius:0.6rem; cursor:pointer; border:2px solid var(--primary); background:rgba(var(--primary-rgb,16,185,129),0.08);'
                                : 'flex:1; display:flex; align-items:center; gap:0.6rem; padding:0.75rem 1rem; border-radius:0.6rem; cursor:pointer; border:2px solid var(--border); background:transparent;'">
                            <input type="radio" name="post_type" value="story" x-model="postType" style="display:none;">
                            <span style="font-size:1.4rem;">⚡</span>
                            <span>
                                <strong style="display:block; font-size:0.9rem;">Story</strong>
                                <small style="color:var(--text-muted); font-size:0.75rem;">24-hour disappearing</small>
                            </span>
                        </label>
                    </div>

                    <!-- Media Upload -->
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                        <label class="form-label" style="margin:0;">Media</label>
                        <span x-show="platform === 'instagram' || platform === 'both' || postType === 'story' || postType === 'reel'"
                            style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; padding:2px 8px; border-radius:20px; background:rgba(239,68,68,0.12); color:#ef4444; border:1px solid rgba(239,68,68,0.2);">
                            Required
                        </span>
                        <span x-show="platform === 'facebook' && postType === 'feed'"
                            style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; padding:2px 8px; border-radius:20px; background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.2);">
                            Optional
                        </span>
                    </div>

                    <!-- Drop zone -->
                    <div
                        id="media-dropzone"
                        style="border-radius: 1rem; cursor: pointer; transition: all 0.25s ease; position: relative; overflow: hidden;"
                        :style="dragOver
                            ? 'border: 2px dashed var(--primary); background: rgba(16,185,129,0.06); box-shadow: 0 0 0 4px rgba(16,185,129,0.08);'
                            : mediaPreview
                                ? 'border: 2px solid var(--border); background: var(--surface, #1e293b);'
                                : 'border: 2px dashed var(--border); background: transparent;'"
                        @dragover.prevent="dragOver = true"
                        @dragleave="dragOver = false"
                        @drop.prevent="handleDrop($event)"
                        @click="!mediaPreview && $refs.mediaInput.click()">

                        <!-- File Selected State -->
                        <template x-if="mediaPreview">
                            <div style="display: flex; align-items: stretch; min-height: 110px;">
                                <div style="width: 120px; flex-shrink: 0; position:relative; overflow:hidden; border-radius: 0.8rem 0 0 0.8rem;">
                                    <img :src="mediaPreview"
                                        style="width:100%; height:100%; object-fit:cover; display:block; min-height:110px;">
                                </div>
                                <div style="flex:1; padding: 1rem 1rem 1rem 1.25rem; display:flex; flex-direction:column; justify-content:center; gap:0.4rem;">
                                    <div>
                                        <span x-text="mediaFileType"
                                            style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; padding:2px 8px; border-radius:20px; background:rgba(99,102,241,0.15); color:#818cf8; border:1px solid rgba(99,102,241,0.2);">
                                        </span>
                                    </div>
                                    <div x-text="mediaFileName"
                                        style="font-size:0.85rem; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;">
                                    </div>
                                    <div x-text="mediaFileSize"
                                        style="font-size:0.75rem; color:var(--text-muted);">
                                    </div>
                                    <div style="display:flex; gap:0.5rem; margin-top:0.25rem;">
                                        <button type="button"
                                            style="font-size:0.75rem; padding:4px 12px; border-radius:6px; border:1px solid var(--border); background:transparent; color:var(--text-muted); cursor:pointer;"
                                            @click.stop="$refs.mediaInput.click()">
                                            <i class="bi bi-arrow-repeat"></i> Replace
                                        </button>
                                        <button type="button"
                                            style="font-size:0.75rem; padding:4px 12px; border-radius:6px; border:1px solid rgba(239,68,68,0.3); background:rgba(239,68,68,0.05); color:#ef4444; cursor:pointer;"
                                            @click.stop="removeMedia()">
                                            <i class="bi bi-trash3"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty / Drag-Over State -->
                        <template x-if="!mediaPreview">
                            <div style="padding: 2.5rem 2rem; text-align:center; display:flex; flex-direction:column; align-items:center; gap:0.75rem;">
                                <div :style="dragOver ? 'transform:scale(1.15) translateY(-4px); transition:transform 0.2s ease;' : 'transform:scale(1); transition:transform 0.2s ease;'">
                                    <div style="width:56px; height:56px; border-radius:1rem; background:rgba(16,185,129,0.1); border:1.5px solid rgba(16,185,129,0.25); display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                        <i class="bi bi-cloud-arrow-up-fill" style="font-size:1.6rem; color:var(--primary);"></i>
                                    </div>
                                </div>
                                <div>
                                    <p style="margin:0; font-size:0.9rem; font-weight:600; color:var(--text);">
                                        <span x-show="dragOver">Drop your file here</span>
                                        <span x-show="!dragOver">Drag &amp; drop your media here</span>
                                    </p>
                                    <p style="margin:0.25rem 0 0; font-size:0.8rem; color:var(--text-muted);">
                                        or <span style="color:var(--primary); font-weight:600; text-decoration:underline; text-underline-offset:2px;">click to browse</span>
                                    </p>
                                </div>
                                <div style="display:flex; gap:0.4rem; flex-wrap:wrap; justify-content:center; margin-top:0.25rem;">
                                    <span style="font-size:0.7rem; padding:2px 8px; border-radius:20px; background:rgba(255,255,255,0.05); border:1px solid var(--border); color:var(--text-muted);">JPG</span>
                                    <span style="font-size:0.7rem; padding:2px 8px; border-radius:20px; background:rgba(255,255,255,0.05); border:1px solid var(--border); color:var(--text-muted);">PNG</span>
                                    <span style="font-size:0.7rem; padding:2px 8px; border-radius:20px; background:rgba(255,255,255,0.05); border:1px solid var(--border); color:var(--text-muted);">GIF</span>
                                    <span style="font-size:0.7rem; padding:2px 8px; border-radius:20px; background:rgba(255,255,255,0.05); border:1px solid var(--border); color:var(--text-muted);">MP4</span>
                                    <span style="font-size:0.7rem; padding:2px 8px; border-radius:20px; background:rgba(255,255,255,0.05); border:1px solid var(--border); color:var(--text-muted);">Max 50 MB</span>
                                </div>
                            </div>
                        </template>

                        <input
                            type="file"
                            name="media"
                            x-ref="mediaInput"
                            :accept="postType === 'reel' ? 'video/mp4,video/quicktime' : 'image/jpeg,image/png,image/gif,video/mp4,video/quicktime'"
                            style="display:none;"
                            @change="handleFileSelect($event)">
                    </div>

                    <div style="margin-bottom: 1.5rem; margin-top: 1.5rem;">
                        <label class="form-label">Content / Caption</label>
                        <textarea name="content" class="form-control" rows="6" x-model="content" placeholder="Write your post here..."></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Schedule For (Leave blank to publish immediately)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" x-model="scheduledAt">
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                        <a href="{{ route('social.index') }}" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary" x-html="scheduledAt ? '<i class=\'bi bi-calendar-check\'></i> Schedule Post' : '<i class=\'bi bi-send\'></i> Publish Now'"></button>
                    </div>
                </form>
            </div>

            <!-- Preview Sidebar -->
            <div style="position: sticky; top: 2rem;">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem;">Live Preview</h3>

                <div class="animate-fade">
                    <!-- STORY preview -->
                    <div x-show="postType === 'story'" x-transition style="margin: 0 auto; width: 220px;">
                        <div style="width:220px; height:390px; background: linear-gradient(135deg,#1e293b 0%,#0f172a 100%); border-radius:1.5rem; overflow:hidden; position:relative; box-shadow: 0 20px 40px rgba(0,0,0,0.4); border:2px solid #334155;">
                            <!-- Story ring gradient -->
                            <div style="position:absolute; inset:0; border-radius:1.5rem; border:3px solid transparent; background:linear-gradient(#0f172a,#0f172a) padding-box, linear-gradient(135deg,#f59e0b,#ef4444,#8b5cf6) border-box; pointer-events:none; z-index:10;"></div>

                            <!-- Story header -->
                            <div style="position:absolute; top:0; left:0; right:0; padding:1rem; z-index:5; background:linear-gradient(to bottom,rgba(0,0,0,0.6),transparent);">
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <div style="width:32px; height:32px; border-radius:50%; background: linear-gradient(135deg,#f59e0b,#ef4444); border:2px solid white; display:flex; align-items:center; justify-content:center; color:white; font-size:0.7rem; font-weight:700;">
                                        <i :class="platform === 'instagram' ? 'bi bi-instagram' : (platform === 'facebook' ? 'bi bi-facebook' : 'bi bi-share')"></i>
                                    </div>
                                    <div>
                                        <div style="font-size:0.7rem; font-weight:700; color:white;">{{ optional(auth()->user()->company)->name ?? 'Solar CRM' }}</div>
                                        <div style="font-size:0.6rem; color:rgba(255,255,255,0.7);">Just now</div>
                                    </div>
                                </div>
                                <!-- Progress bar -->
                                <div style="height:2px; background:rgba(255,255,255,0.3); border-radius:1px; margin-top:0.75rem;">
                                    <div style="width:40%; height:100%; background:white; border-radius:1px;"></div>
                                </div>
                            </div>

                            <!-- Media area / placeholder -->
                            <template x-if="mediaPreview">
                                <img :src="mediaPreview" style="width:100%; height:100%; object-fit:cover; position:absolute; inset:0;">
                            </template>
                            <template x-if="!mediaPreview">
                                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:0.5rem;">
                                    <i class="bi bi-image" style="font-size:3rem; color:rgba(255,255,255,0.15);"></i>
                                    <span style="font-size:0.7rem; color:rgba(255,255,255,0.3);">Upload an image</span>
                                </div>
                            </template>

                            <!-- Caption overlay at bottom -->
                            <div x-show="content" style="position:absolute; bottom:0; left:0; right:0; padding:1rem; background:linear-gradient(to top,rgba(0,0,0,0.8),transparent); z-index:5;">
                                <div x-text="content.length > 80 ? content.slice(0,80) + '…' : content" style="font-size:0.75rem; color:white; line-height:1.4;"></div>
                            </div>
                        </div>
                        <p style="text-align:center; font-size:0.7rem; color:var(--text-muted); margin-top:0.75rem;">
                            <i :class="platform === 'instagram' ? 'bi bi-instagram' : (platform === 'facebook' ? 'bi bi-facebook' : 'bi bi-share')"></i>
                            Story Preview
                        </p>
                    </div>

                    <!-- FEED / REEL POST preview -->
                    <div x-show="postType === 'feed' || postType === 'reel'" x-transition>
                        <div style="background: #1e293b; border-radius: 0.75rem; overflow: hidden; border: 1px solid var(--border);">
                            <!-- Post header -->
                            <div style="padding: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white;">
                                    <i :class="platform === 'instagram' ? 'bi bi-instagram' : (platform === 'facebook' ? 'bi bi-facebook' : 'bi bi-share')"></i>
                                </div>
                                <div>
                                    <div style="font-size: 0.875rem; font-weight: 700; color: white;">{{ optional(auth()->user()->company)->name ?? 'Solar CRM' }}</div>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">
                                        Just now •
                                        <i class="bi bi-globe"></i>
                                        <span x-show="postType === 'feed'" style="margin-left:4px; font-size:0.65rem; background:rgba(99,102,241,0.2); color:#818cf8; padding:1px 6px; border-radius:4px; font-weight:600;">Feed Post</span>
                                        <span x-show="postType === 'reel'" style="margin-left:4px; font-size:0.65rem; background:rgba(244,63,94,0.2); color:#fb7185; padding:1px 6px; border-radius:4px; font-weight:600;">Reel</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Caption text -->
                            <div style="padding: 0 1rem 0.75rem 1rem; font-size: 0.875rem; color: white; line-height: 1.5;">
                                <div x-text="content || 'Your caption will appear here...'"></div>
                            </div>

                            <!-- Media preview or placeholder -->
                            <template x-if="mediaPreview">
                                <img :src="mediaPreview" style="width:100%; max-height:300px; object-fit:cover; display:block;">
                            </template>
                            <template x-if="!mediaPreview">
                                <div style="aspect-ratio: 4/3; background: #334155; display: flex; align-items: center; justify-content: center; color: var(--text-muted); flex-direction:column; gap:0.5rem;">
                                    <i class="bi bi-image" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <span style="font-size:0.75rem; opacity:0.5;">No media attached</span>
                                </div>
                            </template>

                            <!-- Action bar -->
                            <div style="padding: 0.75rem 1rem; border-top: 1px solid var(--border); display: flex; gap: 1.5rem; color: var(--text-muted); font-size: 0.875rem;">
                                <span><i class="bi bi-hand-thumbs-up"></i> Like</span>
                                <span><i class="bi bi-chat"></i> Comment</span>
                                <span><i class="bi bi-share"></i> Share</span>
                            </div>
                        </div>

                        <!-- Instagram warning -->
                        <div x-show="(platform === 'instagram' || platform === 'both') && !mediaPreview"
                            style="margin-top:0.75rem; padding:0.75rem; background:rgba(234,179,8,0.1); border:1px solid rgba(234,179,8,0.3); border-radius:0.5rem; display:flex; gap:0.5rem; align-items:flex-start;">
                            <i class="bi bi-exclamation-triangle-fill" style="color:#eab308; flex-shrink:0; margin-top:2px;"></i>
                            <span style="font-size:0.75rem; color:#ca8a04; line-height:1.4;">Instagram posts require at least one image/video. Please upload a media file.</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('socialCreator', () => ({
                platform:      '{{ old('platform', 'both') }}',
                postType:      '{{ old('post_type', 'feed') }}',
                content:       '{{ old('content', '') }}',
                scheduledAt:   '{{ old('scheduled_at', '') }}',
                mediaPreview:  null,
                mediaFileName: '',
                mediaFileSize: '',
                mediaFileType: '',
                dragOver:      false,

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) this.previewFile(file);
                },

                handleDrop(event) {
                    this.dragOver = false;
                    const file = event.dataTransfer.files[0];
                    if (file) {
                        // Sync to the file input so the form submits it
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        this.$refs.mediaInput.files = dt.files;
                        this.previewFile(file);
                    }
                },

                previewFile(file) {
                    if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) return;

                    // File metadata
                    this.mediaFileName = file.name;
                    const kb = file.size / 1024;
                    this.mediaFileSize = kb < 1024
                        ? kb.toFixed(1) + ' KB'
                        : (kb / 1024).toFixed(2) + ' MB';
                    this.mediaFileType = file.type.split('/')[1].toUpperCase();

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => { this.mediaPreview = e.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        // For video show a placeholder colour block
                        this.mediaPreview = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="120" height="110"><rect width="120" height="110" fill="%231e293b"/><text x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-size="28" fill="%23475569">▶</text></svg>';
                    }
                },

                removeMedia() {
                    this.mediaPreview  = null;
                    this.mediaFileName = '';
                    this.mediaFileSize = '';
                    this.mediaFileType = '';
                    this.$refs.mediaInput.value = '';
                },
            }));
        });
    </script>
</x-app-layout>
