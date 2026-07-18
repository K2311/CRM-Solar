<x-app-layout title="Team & Permissions">
    <div x-data="{ showInviteModal: false, editUser: null }">
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
            <button class="btn btn-primary" @click="showInviteModal = true">
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
                            <button @click="editUser = { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}', email: '{{ addslashes($user->email) }}', role: '{{ $user->role }}' }" class="btn btn-outline" style="padding: 0.4rem; font-size: 0.75rem;"><i class="bi bi-pencil"></i></button>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Invite Modal -->
    <template x-teleport="body">
        <div x-show="showInviteModal">
            <div style="display: flex; position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
                <div @click.away="showInviteModal = false" class="card glass-card" style="width: 100%; max-width: 500px; margin: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.25rem; font-weight: 700;">Invite Team Member</h3>
                <button @click="showInviteModal = false" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--text-muted);"><i class="bi bi-x-lg"></i></button>
            </div>
            
            <form action="{{ route('team.invite') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                        <option value="sales">Sales</option>
                        <option value="technician">Technician</option>
                        <option value="accounts">Accounts</option>
                    </select>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" @click="showInviteModal = false">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Invite</button>
                </div>
            </form>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Edit Modal -->
    <template x-teleport="body">
        <div x-show="editUser">
            <div style="display: flex; position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
                <div @click.away="editUser = null" class="card glass-card" style="width: 100%; max-width: 500px; margin: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.25rem; font-weight: 700;">Edit Team Member</h3>
                        <button @click="editUser = null" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--text-muted);"><i class="bi bi-x-lg"></i></button>
                    </div>
                    
                    <form :action="`/team/${editUser?.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" x-model="editUser.name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" x-model="editUser.email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password" minlength="6">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <select name="role" x-model="editUser.role" class="form-control" required>
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                                <option value="sales">Sales</option>
                                <option value="technician">Technician</option>
                                <option value="accounts">Accounts</option>
                            </select>
                        </div>
                        
                        <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                            <button type="button" class="btn btn-outline" @click="editUser = null">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
    
    </div>
</x-app-layout>
