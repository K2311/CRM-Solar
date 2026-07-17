<x-app-layout title="Campaign Analysis: {{ $campaign->name }}">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline" style="padding: 0.5rem;"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800;">{{ $campaign->name }}</h1>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <span class="badge" style="background: rgba(255,255,255,0.05); color: white;"><i class="bi {{ \App\Models\Campaign::channelIcons()[$campaign->channel] ?? 'bi-envelope' }}"></i> {{ strtoupper($campaign->channel) }}</span>
                    <span class="badge badge-info">{{ $campaign->segment }}</span>
                </div>
            </div>
        </div>
        @if($campaign->status === 'draft')
        <form action="{{ route('campaigns.send', $campaign) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;"><i class="bi bi-send-fill"></i> Execute Campaign</button>
        </form>
        @endif
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card glass-card">
            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Recipients</div>
            <div style="font-size: 1.5rem; font-weight: 800;">{{ $campaign->contacts->count() }}</div>
        </div>
        <div class="card glass-card">
            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Delivered</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #10b981;">{{ $campaign->sent_count }}</div>
        </div>
        <div class="card glass-card">
            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Failed</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #ef4444;">{{ $campaign->failed_count }}</div>
        </div>
        <div class="card glass-card">
            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Success Rate</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">
                {{ $campaign->contacts->count() > 0 ? round(($campaign->sent_count / $campaign->contacts->count()) * 100) : 0 }}%
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="card">
            <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;">Delivery Log</h4>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Recipient</th>
                        <th>Sent At</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaign->contacts as $contact)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $contact->name ?? 'Unknown' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $contact->email ?? $contact->phone ?? '' }}</div>
                        </td>
                        <td style="font-size: 0.875rem; color: var(--text-muted);">{{ $contact->sent_at ? $contact->sent_at->format('M d, H:i') : 'Pending' }}</td>
                        <td>
                            <span class="badge {{ $contact->status === 'sent' ? 'badge-success' : ($contact->status === 'failed' ? 'badge-danger' : 'badge-warning') }}">
                                {{ $contact->status }}
                            </span>
                        </td>
                        <td style="font-size: 0.75rem; color: var(--text-muted);">{{ $contact->error_message ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>
            <div class="card glass-card" style="margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem; text-transform: uppercase;">Message Content</h4>
                @if($campaign->subject)
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Subject</div>
                    <div style="font-weight: 600;">{{ $campaign->subject }}</div>
                </div>
                @endif
                <div>
                    <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Visual Preview</div>
                    
                    @if(in_array($campaign->channel, ['sms', 'whatsapp']))
                        <div style="background: #0f172a; padding: 1rem; border-radius: 1rem; border: 1px solid var(--border);">
                             <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary); font-size: 0.6rem; display: flex; align-items: center; justify-content: center;">S</div>
                                <span style="font-size: 0.75rem; font-weight: 600;">{{ $campaign->channel === 'sms' ? 'Solar CRM' : 'SolarTech WhatsApp' }}</span>
                             </div>
                             <div style="background: #334155; padding: 0.75rem; border-radius: 1rem 1rem 1rem 0; font-size: 0.85rem; color: white; max-width: 90%; line-height: 1.4;">
                                {!! nl2br(e($campaign->body)) !!}
                             </div>
                        </div>
                    @elseif($campaign->channel === 'email')
                        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; color: #334155;">
                            <div style="padding: 0.75rem 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.75rem;">
                                <span style="color: #64748b;">Subject:</span> <span style="font-weight: 600;">{{ $campaign->subject }}</span>
                            </div>
                            <div style="padding: 1.5rem; font-size: 0.85rem; line-height: 1.6;">
                                {!! nl2br(e($campaign->body)) !!}
                            </div>
                        </div>
                    @else
                        <!-- Social Post -->
                        <div style="background: #1e293b; border-radius: 0.75rem; border: 1px solid var(--border); overflow: hidden;">
                             <div style="padding: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.8rem;"><i class="bi {{ \App\Models\Campaign::channelIcons()[$campaign->channel] }}"></i></div>
                                <div style="font-size: 0.8rem; font-weight: 700;">{{ optional($campaign->company)->name ?? 'Solar CRM' }}</div>
                             </div>
                             <div style="padding: 0 0.75rem 0.75rem 0.75rem; font-size: 0.85rem; color: white;">
                                 {!! nl2br(e($campaign->body)) !!}
                             </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem; text-transform: uppercase;">Campaign Details</h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Created</span>
                        <span>{{ $campaign->created_at->format('M d, Y') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Source Template</span>
                        <span>{{ $campaign->template->name ?? 'Custom' }}</span>
                    </div>
                    @if($campaign->sent_at)
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Sent At</span>
                        <span>{{ $campaign->sent_at->format('M d, H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
