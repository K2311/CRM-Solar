<x-app-layout title="Team & Permissions">
    <div x-data="{ tab: 'users' }" class="card glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800;">Access Control</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Manage staff roles and granular permission overrides</p>
            </div>
            <div style="display: flex; gap: 2rem; border-bottom: 1px solid var(--border);">
                <button class="btn" style="border-radius: 0; padding: 1rem 0; font-weight: 700;" :style="tab === 'users' ? 'color: var(--primary); border-bottom: 2px solid var(--primary)' : 'color: var(--text-muted)'" @click="tab = 'users'">Team Members</button>
                <button class="btn" style="border-radius: 0; padding: 1rem 0; font-weight: 700;" :style="tab === 'roles' ? 'color: var(--primary); border-bottom: 2px solid var(--primary)' : 'color: var(--text-muted)'" @click="tab = 'roles'">Role Defaults</button>
                @if($selectedUser)
                <button class="btn" style="border-radius: 0; padding: 1rem 0; font-weight: 700;" :style="tab === 'user' ? 'color: var(--primary); border-bottom: 2px solid var(--primary)' : 'color: var(--text-muted)'" @click="tab = 'user'">Overrides: {{ $selectedUser->name }}</button>
                @endif
            </div>
        </div>

        <!-- Team Members Tab -->
        <div x-show="tab === 'users'" class="animate-fade">
            <div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
                <button class="btn btn-primary" x-data @click="$dispatch('open-invite-modal')"><i class="bi bi-person-plus"></i> Invite Member</button>
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
             <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                 @foreach(['admin', 'member'] as $role)
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
                                     <select name="permissions[{{ $p->id }}]" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px; color: white; padding: 2px 4px; font-size: 0.7rem;">
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
</x-app-layout>
