<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // If not logged in, super admin, or doesn't have a company, bypass checks
        if (!$user || $user->is_super_admin || !$user->company) {
            return $next($request);
        }

        $company = $user->company;

        // List of routes allowed even when subscription is expired
        $allowedRoutes = [
            'billing.index',
            'billing.upgrade',
            'billing.expired',
            'logout'
        ];

        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // Check if plan has expired
        if ($company->isPlanExpired()) {
            return redirect()->route('billing.expired');
        }

        return $next($request);
    }
}
