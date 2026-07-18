<x-app-layout title="Team & Permissions">
    <div x-data="{ tab: '{{ $selectedUser ? 'user' : 'users' }}', showInviteModal: false }">
    <div class="card glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800;">Access Control</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Manage staff roles and granular permission overrides</p>
            </div>
            <div class="tab-container" style="display: flex; gap: 1rem;">
                <button class="tab-pill" :class="{ 'active': tab === 'users' }" @click="tab = 'users'">Team Members</button>
                <button class="tab-pill" :class="{ 'active': tab === 'roles' }" @click="tab = 'roles'">Role Defaults</button>
                @if($selectedUser)
                <button class="tab-pill" :class="{ 'active': tab === 'user' }" @click="tab = 'user'">Overrides: {{ $selectedUser->name }}</button>
                @endif
            </div>
        </div>

        <!-- Team Members Tab -->
        <div x-show="tab === 'users'" class="animate-fade">
            <div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
                <button class="btn btn-primary" @click="showInviteModal = true"><i class="bi bi-person-plus"></i> Invite Member</button>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Permissions</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="{{ $user->avatar_url }}" style="width: 32px; height: 32px; border-radius: 50%;">
                                <div>
                                    <div style="font-weight: 600;">{{ $user->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge {{ $user->role === 'owner' ? 'badge-warning' : 'badge-info' }}">{{ $user->role }}</span></td>
                        <td><span class="badge badge-success">Active</span></td>
                        <td>
                             <a href="{{ route('team.permissions', ['user_id' => $user->id]) }}" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Edit Overrides</a>
                        </td>
                        <td style="text-align: right;">
                             @if($user->id !== auth()->id() && $user->role !== 'owner')
                             <form action="{{ route('team.destroy', $user) }}" method="POST" style="display: inline;">
                                 @csrf @method('DELETE')
                                 <button class="btn" style="color: #ef4444; padding: 0.4rem;"><i class="bi bi-trash"></i></button>
                             </form>
                             @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Role Defaults Tab -->
        <div x-show="tab === 'roles'" class="animate-fade">
             <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
                 @foreach(['admin', 'member', 'sales', 'technician', 'accounts'] as $role)
                 <div class="card">
                     <h3 style="margin-bottom: 1.5rem; text-transform: capitalize;">{{ $role }} Default Permissions</h3>
                     <form action="{{ route('team.permissions.role') }}" method="POST">
                         @csrf
                         <input type="hidden" name="role" value="{{ $role }}">
                         <div style="height: 400px; overflow-y: auto; padding-right: 1rem;">
                             @foreach($permissionsByModule as $module => $perms)
                             <div style="margin-bottom: 1rem;">
                                 <div style="font-size: 0.75rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 0.5rem; border-bottom: 1px solid var(--border);">{{ $module }}</div>
                                 <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                                     @foreach($perms as $p)
                                     <label style="font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                         <input type="checkbox" name="permissions[{{ $p->id }}]" {{ isset($rolePermissions[$role]) && isset($rolePermissions[$role][$p->id]) && $rolePermissions[$role][$p->id] ? 'checked' : '' }}>
                                         {{ $p->action }}
                                     </label>
                                     @endforeach
                                 </div>
                             </div>
                             @endforeach
                         </div>
                         <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">Save {{ $role }} Defaults</button>
                     </form>
                 </div>
                 @endforeach
             </div>
        </div>

        <!-- User Overrides Tab -->
        @if($selectedUser)
        <div x-show="tab === 'user'" class="animate-fade">
             <div class="card">
                 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                     <h3>Custom Permissions for {{ $selectedUser->name }}</h3>
                     <div style="font-size: 0.875rem; color: var(--text-muted);">This user is a <strong>{{ $selectedUser->role }}</strong>. Overrides apply on top of role defaults.</div>
                 </div>

                 <form action="{{ route('team.permissions.user') }}" method="POST">
                     @csrf
                     <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                     <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                         @foreach($permissionsByModule as $module => $perms)
                         <div class="card" style="background: var(--bg-main);">
                             <h4 style="font-size: 0.8rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 1rem;">{{ $module }}</h4>
                             <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                 @foreach($perms as $p)
                                 <div style="display: flex; justify-content: space-between; align-items: center;">
                                     <span style="font-size: 0.875rem;">{{ $p->action }}</span>
                                     <select name="permissions[{{ $p->id }}]" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px; color: inherit; padding: 2px 4px; font-size: 0.7rem;">
                                         <option value="default">Default</option>
                                         <option value="grant" {{ ($userPermissions[$p->id] ?? '') === 'grant' ? 'selected' : '' }}>Grant</option>
                                         <option value="revoke" {{ ($userPermissions[$p->id] ?? '') === 'revoke' ? 'selected' : '' }}>Revoke</option>
                                     </select>
                                 </div>
                                 @endforeach
                             </div>
                         </div>
                         @endforeach
                     </div>
                     <button type="submit" class="btn btn-primary" style="margin-top: 2rem;">Apply Overrides</button>
                     <a href="{{ route('team.permissions') }}" class="btn btn-outline" style="margin-top: 2rem;">Cancel</a>
                 </form>
             </div>
        </div>
        @endif
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

    </div>
</x-app-layout>
