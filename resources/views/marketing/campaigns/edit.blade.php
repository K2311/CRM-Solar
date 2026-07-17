<x-app-layout title="Edit Campaign">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Edit Campaign</h1>
                <p style="color: var(--text-muted);">Modify your marketing campaign details.</p>
            </div>
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div class="card">
            <form action="{{ route('campaigns.update', $campaign) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label class="form-label">Campaign Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $campaign->name) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Channel</label>
                        <select name="channel" class="form-control" required>
                            @foreach(\App\Models\Campaign::channels() as $channel)
                                <option value="{{ $channel }}" {{ old('channel', $campaign->channel) == $channel ? 'selected' : '' }}>
                                    {{ ucfirst($channel) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Target Segment</label>
                    <select name="segment" class="form-control" required>
                        <option value="all" {{ old('segment', $campaign->segment) == 'all' ? 'selected' : '' }}>All Contacts</option>
                        <option value="leads" {{ old('segment', $campaign->segment) == 'leads' ? 'selected' : '' }}>Leads Only</option>
                        <option value="customers" {{ old('segment', $campaign->segment) == 'customers' ? 'selected' : '' }}>Customers Only</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Subject Line (Email Only)</label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject', $campaign->subject) }}">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Message Body</label>
                    <textarea name="body" class="form-control" rows="10" required>{{ old('body', $campaign->body) }}</textarea>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Available placeholders: {name}, {company}, {email}, {phone}</p>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                    <a href="{{ route('campaigns.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Campaign</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
