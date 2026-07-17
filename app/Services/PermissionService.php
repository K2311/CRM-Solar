<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    /**
     * Resolve whether a user has a given permission.
     * Resolution order: user revoke → user grant → role default → deny
     */
    public function check(User $user, string $permissionName): bool
    {
        // Owners and super-admins always have everything
        if ($user->is_super_admin || $user->role === 'owner') return true;

        $cacheKey = "permissions.{$user->company_id}.{$user->id}.{$permissionName}";

        return Cache::remember($cacheKey, 300, function () use ($user, $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if (!$permission) return false;

            // 1. Check per-user override
            $userOverride = UserPermission::where('company_id', $user->company_id)
                ->where('user_id', $user->id)
                ->where('permission_id', $permission->id)
                ->first();

            if ($userOverride) {
                return $userOverride->type === 'grant';
            }

            // 2. Fall back to role default
            $rolePermission = RolePermission::where('company_id', $user->company_id)
                ->where('role', $user->role)
                ->where('permission_id', $permission->id)
                ->first();

            return $rolePermission ? (bool) $rolePermission->granted : false;
        });
    }

    /** Clear cached permissions for a user. */
    public function clearCache(int $companyId, int $userId): void
    {
        $permissions = Permission::all();
        foreach ($permissions as $perm) {
            Cache::forget("permissions.{$companyId}.{$userId}.{$perm->name}");
        }
    }

    /** Get effective permissions for a user as an array [permName => bool]. */
    public function getEffectivePermissions(User $user): array
    {
        $allPermissions = Permission::all();
        $result = [];

        foreach ($allPermissions as $permission) {
            $result[$permission->name] = $this->check($user, $permission->name);
        }

        return $result;
    }
}
