<x-app-layout title="Plan Upgrade Requests">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.25rem;">Plan Upgrade Requests</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Review and approve company plan upgrade requests.</p>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Requested By</th>
                    <th>Current → Requested</th>
                    <th>Payment Proof</th>
                    <th>Notes</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td>
                        <div style="font-weight: 700;">{{ $req->company->name }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.85rem;">{{ $req->requester->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $req->requester->email }}</div>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(239,68,68,0.1); color: #ef4444; text-transform: capitalize;">{{ $req->current_plan }}</span>
                        <i class="bi bi-arrow-right" style="color: var(--text-muted); margin: 0 0.25rem;"></i>
                        <span class="badge" style="background: rgba(16,185,129,0.1); color: #10b981; text-transform: capitalize;">{{ $req->requested_plan }}</span>
                    </td>
                    <td>
                        @if($req->payment_proof)
                            <a href="{{ Storage::url($req->payment_proof) }}" target="_blank" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">
                                <i class="bi bi-file-earmark-image"></i> View
                            </a>
                        @else
                            <span style="color: var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 0.8rem; color: var(--text-muted); max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $req->notes }}">
                            {{ $req->notes ?? '—' }}
                        </div>
                    </td>
                    <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">
                        {{ $req->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        @if($req->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($req->status === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if($req->status === 'pending')
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <form action="{{ route('admin.upgrade-requests.approve', $req) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="swalConfirm(this, 'Approve Upgrade?', 'This will activate the {{ $req->requested_plan }} plan for {{ $req->company->name }}.', 'Yes, Approve')" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.upgrade-requests.reject', $req) }}" method="POST" x-data>
                                    @csrf
                                    <input type="hidden" name="admin_remarks" value="">
                                    <button type="button" onclick="
                                        CrmSwal.fire({
                                            title: 'Reject Upgrade?',
                                            input: 'textarea',
                                            inputLabel: 'Reason (optional)',
                                            inputPlaceholder: 'e.g. Payment not verified...',
                                            showCancelButton: true,
                                            confirmButtonText: 'Reject',
                                            confirmButtonColor: '#ef4444',
                                        }).then(result => {
                                            if (result.isConfirmed) {
                                                this.closest('form').querySelector('[name=admin_remarks]').value = result.value || '';
                                                this.closest('form').submit();
                                            }
                                        });
                                    " class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; color: #ef4444; border-color: rgba(239,68,68,0.3);">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                </form>
                            </div>
                        @else
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                by {{ $req->reviewer->name ?? 'System' }}<br>
                                {{ $req->reviewed_at?->format('M d, Y H:i') }}
                                @if($req->admin_remarks)
                                    <br><em>"{{ $req->admin_remarks }}"</em>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">No upgrade requests yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $requests->links() }}
    </div>
</x-app-layout>
