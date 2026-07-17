<x-app-layout title="Team & Permissions">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Team Management</h1>
            <p style="color: var(--text-muted);">Manage users and their access levels for your company.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('team.permissions') }}" class="btn btn-outline">
                <i class="bi bi-shield-check"></i> Global Permissions
            </a>
            @endif
            @if(auth()->user()->isOwner())
            <button class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Invite Member
            </button>
            @endif
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <img src="{{ $user->avatar_url }}" style="width: 32px; height: 32px; border-radius: 50%;">
                            <span style="font-weight: 600;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background: {{ $user->isOwner() ? '#8b5cf6' : ($user->isAdmin() ? '#3b82f6' : '#6b7280') }}; color: white;">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span style="color: #10b981; font-size: 0.875rem;">Active</span>
                    </td>
                    <td style="text-align: right;">
                        @if(auth()->user()->isAdmin() && !$user->isOwner())
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button class="btn btn-outline" style="padding: 0.4rem; font-size: 0.75rem;"><i class="bi bi-pencil"></i></button>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
