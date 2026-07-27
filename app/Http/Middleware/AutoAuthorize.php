<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AutoAuthorize
{
    /**
     * Map standard route actions to permission actions.
     */
    protected array $actionMap = [
        'index'   => 'view',
        'show'    => 'view',
        'create'  => 'create',
        'store'   => 'create',
        'edit'    => 'edit',
        'update'  => 'edit',
        'destroy' => 'delete',
        // specific mappings
        'send'          => 'send',
        'update-stage'  => 'edit',
        'invite'        => 'invite',
        'permissions'   => 'edit_roles',
        'status'        => 'edit',
        'settings'      => 'edit',
        'milestone'     => 'edit',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        if (!$routeName) {
            return $next($request);
        }

        $parts = explode('.', $routeName);
        if (count($parts) < 2) {
            return $next($request);
        }

        $module = $parts[0];
        $action = end($parts);
        if ($action === $module && count($parts) > 1) {
            $action = $parts[1];
        }

        $moduleAliases = [
            'campaigns' => 'marketing',
            'templates' => 'marketing',
            'social'    => 'marketing',
            'surveys'   => 'installations',
            'invoices'  => 'payments',
        ];

        if (isset($moduleAliases[$module])) {
            $module = $moduleAliases[$module];
        }

        if (count($parts) >= 3 && $module === 'team') {
            $action = $parts[1];
        }

        $permAction = $this->actionMap[$action] ?? null;

        // Specific overrides for team module to match DB permissions
        if ($module === 'team') {
            if (in_array($action, ['edit', 'update'])) {
                $permAction = 'edit_roles';
            } elseif ($action === 'destroy') {
                $permAction = 'remove';
            }
        }

        if ($permAction) {
            $permission = "{$module}.{$permAction}";

            if (array_key_exists($module, \App\Models\Permission::allModules())) {
                if (!auth()->user()->canDo($permission)) {
                    abort(403, "Access Denied: You lack the {$permission} permission.");
                }
            }
        }

        return $next($request);
    }
}
