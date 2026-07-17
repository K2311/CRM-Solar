<x-app-layout title="Marketing Templates">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Message Templates</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Create reusable message blueprints for all channels</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline">Back to Campaigns</a>
            @if(auth()->user()->canDo('marketing.create'))
            <a href="{{ route('templates.create') }}" class="btn btn-primary"><i class="bi bi-file-earmark-plus"></i> New Template</a>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        @foreach($templates as $template)
        <div class="card glass-card">
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; align-items: center;">
                <span class="badge" style="background: rgba(255,255,255,0.05); color: white; display: flex; align-items: center; gap: 0.4rem;">
                    <i class="bi {{ \App\Models\Campaign::channelIcons()[$template->channel] ?? 'bi-envelope' }}" style="color: var(--primary);"></i> 
                    <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">{{ $template->channel }}</span>
                </span>
                <span style="font-size: 0.75rem; color: {{ $template->is_active ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: 600;">
                    <i class="bi bi-circle-fill" style="font-size: 0.5rem; margin-right: 0.25rem;"></i>
                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $template->name }}</h3>
            <div style="background: var(--bg-main); border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.875rem; color: var(--text-muted); min-height: 100px; border: 1px solid var(--border);">
                {{ Str::limit($template->body, 150) }}
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                @if(auth()->user()->canDo('marketing.edit'))
                <a href="{{ route('templates.edit', $template) }}" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
                @endif
                <form action="{{ route('templates.destroy', $template) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" class="btn" style="color: #ef4444; padding: 0.4rem;"
                        onclick="swalDelete(this, 'Delete template \'{{ addslashes($template->name) }}\'? This cannot be undone.')">
                        <i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>
