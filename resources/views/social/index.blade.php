<x-app-layout title="Social Media Posts">
    <div class="card glass-card" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.1rem; font-weight: 700;">Published & Scheduled Posts</h3>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('social.settings') }}" class="btn btn-outline">
                    <i class="bi bi-gear"></i> Settings
                </a>
                <a href="{{ route('social.create') }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Compose New
                </a>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Content</th>
                    <th>Platform</th>
                    <th>Status</th>
                    <th>Scheduled For</th>
                    <th>Created</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                    <tr>
                        <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $post->content ?: 'Media Only' }}
                        </td>
                        <td style="text-transform: capitalize;">{{ $post->platform }}</td>
                        <td>
                            @php
                                $badgeClass = match($post->status) {
                                    'published' => 'badge-success',
                                    'scheduled' => 'badge-warning',
                                    'failed'    => 'badge-danger',
                                    default     => 'badge-info'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($post->status) }}</span>
                        </td>
                        <td style="color: var(--text-muted);">{{ $post->scheduled_at ? $post->scheduled_at->format('M d, Y H:i') : '-' }}</td>
                        <td style="color: var(--text-muted);">{{ $post->created_at->format('M d, Y') }}</td>
                        <td style="text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                @if(in_array($post->status, ['draft', 'scheduled', 'failed']))
                                    <a href="{{ route('social.edit', $post) }}" class="btn btn-outline" style="padding: 0.4rem; border-radius: 0.5rem;" title="Edit Post"><i class="bi bi-pencil"></i></a>
                                @endif
                                <form action="{{ route('social.destroy', $post) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline" style="padding: 0.4rem; border-radius: 0.5rem; color: #ef4444; border-color: rgba(239, 68, 68, 0.2);" onclick="swalDelete(this)" title="Delete Post"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No posts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
