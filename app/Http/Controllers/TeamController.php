<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\UserPermission;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    use \App\Traits\HasTenant;

    public function index()
    {
        $company = $this->tenantRequired();
        $users = User::where('company_id', $company->id)->get();
        return view('team.index', compact('users'));
    }

    public function invite(Request $request)
    {
        $company = $this->tenantRequired();
        
        if ($company->hasReachedUserLimit()) {
            return back()->with('error', "Limit reached: Your subscription tier (" . strtoupper($company->plan) . ") allows a maximum of " . $company->plan_details['user_limit'] . " users. Please upgrade your plan.");
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:admin,member,sales,technician,accounts',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return back()->with('success', 'Team member invited.');
    }

    public function permissions(Request $request)
    {
        $company = $this->tenantRequired();
        $companyId = $company->id;
        $users = User::where('company_id', $companyId)->where('role', '!=', 'owner')->get();
        $permissionsByModule = Permission::all()->groupBy('module');
        
        $rolePermissions = RolePermission::where('company_id', $companyId)->get()
            ->groupBy('role')
            ->map(fn($items) => $items->pluck('granted', 'permission_id'));

        $selectedUser = null;
        $userPermissions = collect();
        
        if ($request->user_id) {
            $selectedUser = User::find($request->user_id);
            if ($selectedUser && $selectedUser->company_id === $companyId) {
                $userPermissions = UserPermission::where('user_id', $selectedUser->id)->pluck('type', 'permission_id');
            }
        }

        return view('team.permissions', compact('users', 'permissionsByModule', 'rolePermissions', 'selectedUser', 'userPermissions'));
    }

    public function updateRolePermissions(Request $request)
    {
        $company = $this->tenantRequired();
        $companyId = $company->id;
        $role = $request->role;
        $requestPermissions = $request->permissions ?? [];

        RolePermission::where('company_id', $companyId)->where('role', $role)->delete();

        foreach (Permission::all() as $perm) {
            RolePermission::create([
                'company_id' => $companyId,
                'role' => $role,
                'permission_id' => $perm->id,
                'granted' => isset($requestPermissions[$perm->id]),
            ]);
        }

        return back()->with('success', 'Role permissions updated.');
    }

    public function updateUserPermissions(Request $request)
    {
        $company = $this->tenantRequired();
        $companyId = $company->id;
        $userId = $request->user_id;
        $user = User::findOrFail($userId);
        abort_if($user->company_id !== $companyId, 403);

        UserPermission::where('user_id', $userId)->delete();

        if ($request->permissions) {
            foreach ($request->permissions as $permId => $type) {
                if (in_array($type, ['grant', 'revoke'])) {
                    UserPermission::create([
                        'company_id' => $companyId,
                        'user_id' => $userId,
                        'permission_id' => $permId,
                        'type' => $type,
                    ]);
                }
            }
        }

        app(PermissionService::class)->clearCache($companyId, $userId);

        return back()->with('success', 'User permissions updated.');
    }

    public function update(Request $request, User $user)
    {
        $company = $this->tenantRequired();
        abort_if($user->company_id !== $company->id || $user->role === 'owner', 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,member,sales,technician,accounts',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return back()->with('success', 'Team member updated successfully.');
    }

    public function destroy(User $user)
    {
        $company = $this->tenantRequired();
        abort_if($user->company_id !== $company->id || $user->role === 'owner', 403);
        $user->delete();
        return back()->with('success', 'User removed from team.');
    }
}
