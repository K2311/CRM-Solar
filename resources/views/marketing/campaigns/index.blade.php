<x-app-layout title="Marketing Campaigns">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Marketing Center</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Create and dispatch multi-channel marketing campaigns</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('templates.index') }}" class="btn btn-outline"><i class="bi bi-file-earmark-richtext"></i> Manage Templates</a>
            @if(auth()->user()->canDo('marketing.create'))
            <a href="{{ route('campaigns.create') }}" class="btn btn-primary"><i class="bi bi-megaphone"></i> New Campaign</a>
            @endif
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="card glass-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Campaign Name</th>
                    <th>Channel</th>
                    <th>Segment</th>
                    <th>Status</th>
                    <th>Performance</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $campaign)
                <tr>
                    <td>
                        <div style="font-weight: 700;">{{ $campaign->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $campaign->created_at->format('M d, Y') }}</div>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(255,255,255,0.05); color: white; display: flex; align-items: center; gap: 0.4rem; width: fit-content;">
                            <i class="bi {{ \App\Models\Campaign::channelIcons()[$campaign->channel] ?? 'bi-envelope' }}"></i>
                            {{ strtoupper($campaign->channel) }}
                        </span>
                    </td>
                    <td><span class="badge badge-info">{{ $campaign->segment }}</span></td>
                    <td>
                        @php
                            $sClass = match($campaign->status) {
                                'sent'    => 'badge-success',
                                'sending' => 'badge-warning',
                                'failed'  => 'badge-danger',
                                default   => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge {{ $sClass }}">{{ $campaign->status }}</span>
                    </td>
                    <td>
                        @if($campaign->status === 'sent')
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 0.75rem;">
                                <span style="color: #10b981;">{{ $campaign->sent_count }} sent</span> / 
                                <span style="color: #ef4444;">{{ $campaign->failed_count }} failed</span>
                            </div>
                        </div>
                        @else
                        <span style="color: var(--text-muted); font-size: 0.75rem;">Pending Dispatch</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline" style="padding: 0.4rem;"><i class="bi bi-graph-up"></i></a>
                            @if($campaign->status === 'draft')
                            <form action="{{ route('campaigns.send', $campaign) }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;"
                                    onclick="swalConfirm(this, 'Dispatch Campaign', 'Send this campaign to all recipients now?', 'Yes, Send Now!')">
                                    <i class="bi bi-send"></i> Send</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 1.5rem;">
            {{ $campaigns->links() }}
        </div>
    </div>
</x-app-layout>
